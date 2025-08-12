<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Transbank\Webpay\WebpayPlus;
use Transbank\Webpay\WebpayPlus\Transaction;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\FreeTrialActivatedMail;
use SendGrid;
use Illuminate\Support\Facades\View;
use Exception;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /** Configura Webpay para TEST o PRODUCCIÓN según .env */
    private function configureWebpay(): void
    {
        $env = env('WEBPAY_ENVIRONMENT', 'TEST'); // TEST | PRODUCTION

        if ($env === 'PRODUCTION') {
            WebpayPlus::configureForProduction(
                env('WEBPAY_COMMERCE_CODE'),
                env('WEBPAY_API_KEY')
            );
        } else {
            WebpayPlus::configureForTesting(); // usa llaves de integración
        }
    }

    public function initiatePayment(Request $request)
    {
        $this->configureWebpay();

        // Validar plan
        $request->validate([
            'plan' => 'required|in:afc,me,ge',
        ]);

        $user = Auth::user();
        $plan = $request->input('plan');

        // 1) Verificar datos de facturación mínimos
        $df = $user->datosFacturacion; // relación hasOne en User
        if (!$df || !$df->razon_social || !$df->rut || !$df->correo_envio_dte) {
            return back()->with('error',
                'Debes completar Razón Social, RUT y Correo de envío DTE en tus Datos de Facturación antes de pagar.');
        }

        // 2) Monto por plan
        switch ($plan) {
            case 'afc': $amount = 69900; break;
            case 'me':  $amount = 87900; break;
            case 'ge':  $amount = 150900; break;
            default:    return back()->with('error', 'Plan no válido');
        }

        // 3) Descuento de Agosto (-30%)
        if (now()->month == 8) {
            $amount = (int) round($amount * 0.7);
        }

        // 4) Crear transacción Webpay
        $buyOrder  = uniqid('ORDER_');
        $sessionId = session()->getId();

        $response = (new Transaction())->create(
            $buyOrder,
            $sessionId,
            $amount,
            route('payment.response')
        );

        // 5) Guardar Payment con relación a datos de facturación + snapshot
        Payment::create([
            'user_id'             => $user->id,
            'dato_facturacion_id' => $df->id,
            'transaction_id'      => $response->getToken(),
            'status'              => 'pending',
            'amount'              => $amount,
            'plan'                => $plan,
            'billing_snapshot'    => [
                'razon_social'           => $df->razon_social,
                'rut'                    => $df->rut,
                'giro'                   => $df->giro,
                'direccion_comercial'    => $df->direccion_comercial,
                'region_id'              => $df->region_id,
                'comuna_id'              => $df->comuna_id,
                'ciudad'                 => $df->ciudad,
                'telefono'               => $df->telefono,
                'correo'                 => $df->correo,
                'autorizacion_envio_dte' => (bool) $df->autorizacion_envio_dte,
                'correo_envio_dte'       => $df->correo_envio_dte,
            ],
        ]);

        return redirect($response->getUrl() . '?token_ws=' . $response->getToken());
    }

    public function paymentResponse(Request $request)
    {
        $this->configureWebpay();

        /* =========================
        * 1) CANCELACIÓN / ANULACIÓN (flujo TBK)
        *    TBK envía TBK_TOKEN, TBK_ID_SESION, TBK_ORDEN_COMPRA
        *    ¡NO llamar commit() aquí!
        * ========================= */
        if ($request->has('TBK_TOKEN')) {
            $tbkToken      = $request->input('TBK_TOKEN');
            $tbkIdSesion   = $request->input('TBK_ID_SESION');
            $tbkOrdenCompra= $request->input('TBK_ORDEN_COMPRA');

            // Guarda en sesión para mostrarlos en failed.blade
            $request->session()->put('payment_failed', true);
            $request->session()->put('payment_token', $tbkToken);
            $request->session()->put('TBK_ID_SESION', $tbkIdSesion);
            $request->session()->put('TBK_ORDEN_COMPRA', $tbkOrdenCompra);

            \Log::info('[TBK] Cancelación/anulación recibida', [
                'TBK_TOKEN'        => $tbkToken,
                'TBK_ID_SESION'    => $tbkIdSesion,
                'TBK_ORDEN_COMPRA' => $tbkOrdenCompra,
            ]);

            return redirect()->route('payment.failed')
                ->with('error', 'La compra fue anulada desde el formulario de Transbank.');
        }

        /* =========================
        * 2) TRANSACCIÓN NORMAL (token_ws)
        * ========================= */
        if (!$request->has('token_ws')) {
            \Log::warning('[TBK] Sin token_ws ni TBK_TOKEN -> FAILED');
            $request->session()->put('payment_failed', true);
            $request->session()->put('payment_token', null);
            return redirect()->route('payment.failed')
                ->with('error', 'No se recibió token de Transbank.');
        }

        $token = $request->input('token_ws');
        $request->session()->put('payment_token', $token); // para mostrar en vistas

        // Commit en Transbank
        try {
            $tx = (new Transaction())->commit($token);
        } catch (\Throwable $e) {
            \Log::error('[TBK] commit() exception: '.$e->getMessage());
            $request->session()->put('payment_failed', true);
            return redirect()->route('payment.failed')->with('error', 'Error al procesar la transacción.');
        }

        // Buscar el Payment por token
        $payment =Payment::where('transaction_id', $token)->first();
        if (!$payment) {
            \Log::error('[TBK] No se encontró Payment para token: '.$token);
            $request->session()->put('payment_failed', true);
            return redirect()->route('payment.failed')->with('error', 'No se encontró el pago asociado.');
        }

        // Evaluación estricta: SOLO crédito permitido
        $responseCode = method_exists($tx,'getResponseCode') ? $tx->getResponseCode() : null;
        $status       = strtoupper(method_exists($tx,'getStatus') ? $tx->getStatus() : '');
        $paymentType  = strtoupper(method_exists($tx,'getPaymentTypeCode') ? $tx->getPaymentTypeCode() : '');

        $allowedCreditTypes = ['VN','VC','SI','S2','S3','NC','ND']; // crédito
        $isCredit           = in_array($paymentType, $allowedCreditTypes, true);

        \Log::info('[TBK] Resultado commit', compact('responseCode','status','paymentType'));

        if ($responseCode !== 0 || $status !== 'AUTHORIZED' || !$isCredit) {
            // Rechazo (incluye VD / VP)
            $payment->update(['status' => 'failed']);
            $request->session()->put('payment_failed', true);

            // (opcional) datos para depurar en la vista
            session()->flash('tbk_debug', [
                'responseCode' => $responseCode,
                'status'       => $status,
                'paymentType'  => $paymentType,
            ]);

            return redirect()->route('payment.failed')
                ->with('error', 'El pago fue rechazado.');
        }

        // === Pago aprobado ===
        $payment->update(['status' => 'paid']);

        /** @var \App\Models\User $user */
        $user = $payment->user ?? User::find(Auth::id());
        if (!$user) {
            \Log::error('[TBK] Usuario no encontrado para pago aprobado (payment_id='.$payment->id.')');
            $request->session()->put('payment_failed', true);
            return redirect()->route('payment.failed')->with('error', 'Usuario no encontrado.');
        }

        $plan = $payment->plan;
        $user->plan = $plan;
        $user->fecha_vencimiento = now()->addYear();
        $user->webpay_status = 'pagado';
        $user->save();

        // === Crear factura + PDF (igual que tenías) ===
        try {
            $montoNeto = (int) $payment->amount;
            $porcIva   = 19;
            $montoIva  = (int) round($montoNeto * ($porcIva / 100));
            $montoTot  = $montoNeto + $montoIva;

            $billingSnapshot = $payment->billing_snapshot ?? [];
            $df = $payment->datosFacturacion()->first();

            if (empty($billingSnapshot) && $df) {
                $billingSnapshot = [
                    'razon_social'           => $df->razon_social,
                    'rut'                    => $df->rut,
                    'giro'                   => $df->giro,
                    'direccion_comercial'    => $df->direccion_comercial,
                    'region_id'              => $df->region_id,
                    'comuna_id'              => $df->comuna_id,
                    'ciudad'                 => $df->ciudad,
                    'telefono'               => $df->telefono,
                    'correo'                 => $df->correo,
                    'autorizacion_envio_dte' => (bool) $df->autorizacion_envio_dte,
                    'correo_envio_dte'       => $df->correo_envio_dte,
                ];
            }

            $numeroFactura = now()->format('Ymd') . '-' . strtoupper(Str::random(4));

            $facturaPdf = Pdf::loadView('pdfs.factura-oficial', [
                'user'        => $user,
                'payment'     => $payment,
                'montoNeto'   => $montoNeto,
                'montoIva'    => $montoIva,
                'montoTotal'  => $montoTot,
                'numero'      => $numeroFactura,
                'snapshot'    => $billingSnapshot,
            ]);

            $pdfFilename = 'facturas/' . $user->id . '/' . $numeroFactura . '.pdf';
            Storage::disk('local')->put($pdfFilename, $facturaPdf->output());

            $factura = \App\Models\Factura::create([
                'user_id'                    => $user->id,
                'payment_id'                 => $payment->id,
                'numero'                     => $numeroFactura,
                'folio'                      => null,
                'sii_track_id'               => null,
                'estado'                     => 'emitida',
                'monto_neto'                 => $montoNeto,
                'monto_iva'                  => $montoIva,
                'monto_total'                => $montoTot,
                'porcentaje_iva'             => $porcIva,
                'moneda'                     => 'CLP',
                'fecha_emision'              => now(),
                'fecha_vencimiento'          => now()->addDays(30),
                'pdf_path'                   => $pdfFilename,
                'xml_path'                   => null,
                'pdf_url'                    => null,
                'xml_url'                    => null,
                'datos_facturacion_snapshot' => $billingSnapshot,
                'plan'                       => $plan,
            ]);

            if ($df && $df->autorizacion_envio_dte) {
                Mail::to($df->correo_envio_dte)
                    ->queue(new \App\Mail\FacturaGeneradaMail($factura));
            }

            \Log::info('[TBK] Factura creada', ['factura_id' => $factura->id, 'payment_id' => $payment->id]);
        } catch (\Throwable $tx) {
            \Log::error('Error al generar factura tras el pago: ' . $tx->getMessage());
            // No bloqueamos el success del pago si la factura falla.
        }

        $request->session()->put('payment_success', true);
        return redirect()->route('payment.success');
}



    public function startTrial(Request $request)
    {
        $user = Auth::user();

        $dronePayment = Payment::where('user_id', $user->id)
            ->where('plan', 'drone')
            ->orderByDesc('created_at')
            ->first();

        if ($dronePayment && now()->diffInDays($dronePayment->created_at) < 16) {
            return redirect()->route('home')->with('error', 'Ya tienes una prueba gratuita activa.');
        }

        if ($dronePayment && now()->diffInDays($dronePayment->created_at) >= 16) {
            $request->session()->put('payment_required', true);
            return redirect()->route('payment.required')->with('error', 'Ya usaste tu prueba gratuita.');
        }

        Payment::create([
            'user_id'        => $user->id,
            'transaction_id' => 'trial-' . uniqid(),
            'status'         => 'paid',
            'amount'         => 0,
            'plan'           => 'drone',
        ]);

        $user->fecha_vencimiento = now()->addDays(16);
        $user->save();

        // Envío con SDK SendGrid (opcional)
        $htmlContent = View::make('emails.free-trial-activated', ['user' => $user])->render();
        $email = new \SendGrid\Mail\Mail();
        $email->setFrom("soporte@bmaia.cl", "B-MaiA - Prueba Gratuita");
        $email->setSubject("¡Prueba gratuita activada en B-MaiA!");
        $email->addTo($user->email, $user->name);
        $email->addContent("text/plain", "¡Hola {$user->name}! Tu prueba gratuita ha sido activada por 16 días.");
        $email->addContent("text/html", $htmlContent);

        $sendgrid = new SendGrid(config('services.sendgrid.api_key'));
        try {
            $sgResp = $sendgrid->send($email);
            \Log::info('SendGrid trial response', [
                'status' => $sgResp->statusCode(),
                'body'   => $sgResp->body(),
            ]);
        } catch (Exception $e) {
            \Log::error('SendGrid trial error: ' . $e->getMessage());
        }

        return redirect()->route('home')->with('success', 'Prueba gratuita activada por 16 días.');
    }

    public function showSuccess(Request $request)
    {
        if (!$request->session()->pull('payment_success')) {
            return redirect()->route('home');
        }
        $token = $request->session()->pull('payment_token');
        $payment = Payment::where('user_id', Auth::id())
            ->where('status', 'paid')
            ->latest()
            ->with('datosFacturacion')
            ->first();

        return view('payment.success', compact('payment','token'));
    }

    public function success(Request $request)
    {
        // ... tu lógica actual de validación de pago
        $factura = $this->generarFactura($request); // tu método para crear el registro

        // Preguntar si quiere envío por correo (puedes pasar la factura a la vista)
        return view('pagos.confirmacion', compact('factura'));
    }

    public function showFailed(Request $request)
    {
        if ($request->isMethod('post')) {
            // Guardar en sesión
            $request->session()->put('payment_token',       $request->input('TBK_TOKEN'));
            $request->session()->put('payment_idSesion',    $request->input('TBK_ID_SESION'));
            $request->session()->put('payment_ordenCompra', $request->input('TBK_ORDEN_COMPRA'));
            $request->session()->put('payment_failed', true);
        }
        if (!$request->session()->get('payment_failed')) {
            return redirect()->route('home');
        }
        $token       = $request->session()->get('payment_token');
        $idSesion    = $request->session()->get('payment_idSesion');
        $ordenCompra = $request->session()->get('payment_ordenCompra');

        return view('payment.failed', compact('token', 'idSesion', 'ordenCompra'));
    }

    public function showRequired(Request $request)
    {
        $user = Auth::user();

        if (!$user->plan || ($user->fecha_vencimiento && now()->greaterThan($user->fecha_vencimiento))) {
            return view('payment.required');
        }
        return redirect()->route('home');
    }
}
