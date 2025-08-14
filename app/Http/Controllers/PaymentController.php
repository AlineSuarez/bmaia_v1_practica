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
use \App\Models\Factura;
use App\Models\Region;
use App\Models\Comuna;


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
        $df = $user->datosFacturacion;
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
            route('payment.response') // <- vuelve a paymentResponse
        );

        // 5) Guardar Payment (dejamos el token inmediatamente)
        Payment::create([
            'user_id'             => $user->id,
            'dato_facturacion_id' => $df->id,
            'transaction_id'      => $response->getToken(), // token_ws
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
            'buy_order'  => $buyOrder,
            'session_id' => $sessionId,
        ]);

        return redirect($response->getUrl() . '?token_ws=' . $response->getToken());
    }

    public function paymentResponse(Request $request)
    {
        $this->configureWebpay();

        // Acepta GET o POST (algunos entornos retornan por GET)
        $tbkToken = $request->input('TBK_TOKEN') ?? $request->query('TBK_TOKEN');
        $token    = $request->input('token_ws')  ?? $request->query('token_ws');

        // === ABORTO / ANULACIÓN DESDE WEBPAY (flujo TBK) ===
        if (empty($token) || !empty($tbkToken)) {
            // Guarda en sesión para que failed.blade pueda leerlo
            $request->session()->put('payment_failed', true);
            $request->session()->put('payment_token', $tbkToken);
            $request->session()->put('payment_idSesion', $request->input('TBK_ID_SESION') ?? $request->query('TBK_ID_SESION'));
            $request->session()->put('payment_ordenCompra', $request->input('TBK_ORDEN_COMPRA') ?? $request->query('TBK_ORDEN_COMPRA'));

            \Log::info('[TBK] Cancelación/anulación recibida', [
                'TBK_TOKEN'        => $tbkToken,
                'TBK_ID_SESION'    => $request->input('TBK_ID_SESION') ?? $request->query('TBK_ID_SESION'),
                'TBK_ORDEN_COMPRA' => $request->input('TBK_ORDEN_COMPRA') ?? $request->query('TBK_ORDEN_COMPRA'),
            ]);

            return redirect()->route('payment.failed')
                ->with('error', 'La compra fue anulada desde el formulario de Transbank.')
                ->with('tbk_debug', [
                    'responseCode' => 'aborted',
                    'status'       => 'FAILED',
                    'paymentType'  => null,
                ]);
        }

        // Guarda el token para mostrarlo en success/failed
        $request->session()->put('payment_token', $token);

        // === COMMIT NORMAL EN TRANSBANK ===
        try {
            $commit = (new Transaction())->commit($token);
        } catch (\Throwable $e) {
            \Log::error('[TBK] commit() exception: '.$e->getMessage(), ['ex' => $e]);
            $request->session()->put('payment_failed', true);
            return redirect()->route('payment.failed')
                ->with('error', 'Error al procesar la transacción.');
        }

        // Busca el Payment por token guardado en initiate (transaction_id = token_ws)
        $payment = Payment::where('transaction_id', $token)->latest()->first();

        // (fallback opcional) si no lo encuentra por token, intenta por buy_order si tu tabla lo tiene
        if (!$payment && method_exists($commit, 'getBuyOrder')) {
            $payment = Payment::where('buy_order', $commit->getBuyOrder())->latest()->first();
        }

        if (!$payment) {
            \Log::error('[TBK] No se encontró Payment para token/buy_order', [
                'token_ws'   => $token,
                'buy_order'  => method_exists($commit, 'getBuyOrder') ? $commit->getBuyOrder() : null,
            ]);
            $request->session()->put('payment_failed', true);
            return redirect()->route('payment.failed')->with('error', 'No se encontró el pago asociado.');
        }

        // === Guarda trazas/diagnóstico SOLO si existen las columnas ===
        try {
            $updates = [];
            if (\Schema::hasColumn('payments', 'response_code')) {
                $updates['response_code'] = (int) ($commit->getResponseCode() ?? 999);
            }
            if (\Schema::hasColumn('payments', 'tbk_status')) {
                $updates['tbk_status'] = strtoupper($commit->getStatus() ?? '');
            }
            if (\Schema::hasColumn('payments', 'payment_type')) {
                $updates['payment_type'] = strtoupper($commit->getPaymentTypeCode() ?? '');
            }
            if (\Schema::hasColumn('payments', 'auth_code') && method_exists($commit, 'getAuthorizationCode')) {
                $updates['auth_code'] = $commit->getAuthorizationCode();
            }
            if (\Schema::hasColumn('payments', 'card_detail') && method_exists($commit, 'getCardDetail')) {
                $updates['card_detail'] = $commit->getCardDetail(); // json/text
            }
            if (!empty($updates)) {
                $payment->forceFill($updates)->save();
            }
        } catch (\Throwable $e) {
            \Log::warning('[TBK] No se pudieron guardar campos de diagnóstico en payments: '.$e->getMessage());
        }

        // === LECTURA RESULTADO ===
        $responseCode = (int) ($commit->getResponseCode() ?? 999);   // 0 = aprobado
        $status       = strtoupper($commit->getStatus() ?? '');      // 'AUTHORIZED' = aprobado
        $paymentType  = strtoupper($commit->getPaymentTypeCode() ?? '');

        \Log::info('[TBK] Resultado commit', compact('responseCode','status','paymentType'));

        // Regla correcta de aprobación: responseCode === 0 y status === 'AUTHORIZED'.
        // NO filtramos por tipo de medio de pago (crédito/débito/prepago).
        if ($responseCode !== 0 || $status !== 'AUTHORIZED') {
            $payment->update(['status' => 'failed']);
            $request->session()->put('payment_failed', true);

            // datos para depurar en la vista
            session()->flash('tbk_debug', [
                'responseCode' => $responseCode,
                'status'       => $status,
                'paymentType'  => $paymentType,
            ]);

            return redirect()->route('payment.failed')
                ->with('error', 'El pago fue rechazado.');
        }

        // === Pago aprobado ===
        $start = $payment->created_at ?? now();
        $durationDays = (int) config('plans.duration_days', 365);
        $payment->forceFill([
            'status'     => 'paid',
            'expires_at' => (clone $start)->addDays($durationDays),
        ])->save();

        /** @var \App\Models\User $user */
        $user = $payment->user ?? User::find(Auth::id());
        if (!$user) {
            \Log::error('[TBK] Usuario no encontrado para pago aprobado (payment_id='.$payment->id.')');
            $request->session()->put('payment_failed', true);
            return redirect()->route('payment.failed')->with('error', 'Usuario no encontrado.');
        }

        // Actualiza plan/fecha de vencimiento
        $user->plan              = $payment->plan;
        $user->fecha_vencimiento = $payment->expires_at; 
        $user->webpay_status     = 'pagado';
        $user->save();

        if (Factura::where('payment_id', $payment->id)->exists()) {
            return redirect()->route('payment.success');
        }
        // === Crear factura + PDF (si falla, no bloquea el success) ===
        try {
            // amount ES EL TOTAL COBRADO (con IVA). Prorrateamos a neto + IVA
            $porcIva   = 19;
            $montoTot  = (int) $payment->amount;                        // total cobrado
            $montoNeto = (int) round($montoTot / (1 + $porcIva / 100));  // prorrateo 19%
            $montoIva  = $montoTot - $montoNeto;
            $billingSnapshot = $payment->billing_snapshot ?? [];
            $df = $payment->datosFacturacion()->first();
            if (empty($billingSnapshot) && $df) {
                $regionNombre = null;
                $comunaNombre = null;
                try {
                    $regionNombre = method_exists($df, 'region') && $df->relationLoaded('region')
                        ? optional($df->region)->nombre
                        : (Region::find($df->region_id)->nombre ?? null);
                    $comunaNombre = method_exists($df, 'comuna') && $df->relationLoaded('comuna')
                        ? optional($df->comuna)->nombre
                        : (Comuna::find($df->comuna_id)->nombre ?? null);
                } catch (\Throwable $e) {
                    // silencioso
                }
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
                    'region'                 => $regionNombre,
                    'region_nombre'          => $regionNombre,
                    'comuna'                 => $comunaNombre,
                    'comuna_nombre'          => $comunaNombre,
                ];
            } else {
                if ($df && (!isset($billingSnapshot['region_nombre']) || !isset($billingSnapshot['comuna_nombre']))) {
                    try {
                        $regionNombre = Region::find($df->region_id)->nombre ?? null;
                        $comunaNombre = Comuna::find($df->comuna_id)->nombre ?? null;
                    } catch (\Throwable $e) {
                        $regionNombre = $billingSnapshot['region_nombre'] ?? null;
                        $comunaNombre = $billingSnapshot['comuna_nombre'] ?? null;
                    }
                    $billingSnapshot['region']         = $billingSnapshot['region']         ?? $regionNombre;
                    $billingSnapshot['region_nombre']  = $billingSnapshot['region_nombre']  ?? $regionNombre;
                    $billingSnapshot['comuna']         = $billingSnapshot['comuna']         ?? $comunaNombre;
                    $billingSnapshot['comuna_nombre']  = $billingSnapshot['comuna_nombre']  ?? $comunaNombre;
                }
            }
            $numeroFactura = now()->format('Ymd') . '-' . strtoupper(\Str::random(4));
            $facturaPdf = Pdf::loadView('user.factura', [
                'user'        => $user,
                'payment'     => $payment,
                'montoNeto'   => $montoNeto,
                'montoIva'    => $montoIva,
                'montoTotal'  => $montoTot,
                'numero'      => $numeroFactura,
                'snapshot'    => $billingSnapshot,
            ]);

            $pdfFilename = 'facturas/' . $user->id . '/' . $numeroFactura . '.pdf';
            \Storage::disk('public')->put($pdfFilename, $facturaPdf->output());
            $factura = Factura::create([
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
                'plan'                       => $payment->plan,
            ]);
            if ($df && $df->autorizacion_envio_dte) {
                \Mail::to($df->correo_envio_dte)
                    ->queue(new \App\Mail\FacturaGeneradaMail($factura));
            }
            \Log::info('[TBK] Factura creada', [
                'factura_id' => $factura->id,
                'payment_id' => $payment->id
            ]);
        } catch (\Throwable $e) {
            \Log::error('Error al generar factura tras el pago: ' . $e->getMessage(), ['ex' => $e]);
            // No bloqueamos el success del pago si la factura falla.
        }
        // Marca success para la vista
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
        // Permite entrar si venimos del commit o si hay un pago paid reciente
        $fromCommit = (bool) $request->session()->pull('payment_success', false);
        $token = $request->session()->pull('payment_token') ?? $request->query('token');
        $paymentQuery = Payment::query()
            ->where('user_id', Auth::id())
            ->with('datosFacturacion','factura');
        $payment = null;
        if ($token) {
            $payment = $paymentQuery->where('transaction_id', $token)->latest()->first();
        }
        if (!$payment) {
            $payment = $paymentQuery->where('status', 'paid')->latest()->first();
        }
        if (!$payment && !$fromCommit) {
            return redirect()->route('home')->with('error', 'No se encontró un pago aprobado reciente para mostrar.');
        }
        if (!$token && $payment) {
            $token = $payment->transaction_id;
        }
        // construir URL pública del PDF (usa el disco donde guardas el PDF)
        $facturaUrl = null;
        if ($payment?->factura?->pdf_path) {
            $facturaUrl = Storage::disk('public')->url($payment->factura->pdf_path);
        } elseif (!empty($payment?->factura_pdf_url)) {
            $facturaUrl = $payment->factura_pdf_url;
        }
        return view('payment.success', compact('payment', 'token','facturaUrl'));
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
        // Permite mostrar la página si:
        // - hay flag en sesión, o
        // - venimos marcados por query (?aborted=1 / ?rejected=1), o
        // - Transbank nos devolvió TBK_* o al menos un token por query
        $canShow = $request->session()->get('payment_failed')
            || $request->boolean('aborted')
            || $request->boolean('rejected')
            || $request->hasAny(['TBK_TOKEN', 'TBK_ID_SESION', 'TBK_ORDEN_COMPRA', 'token']);

        if (!$canShow) {
            return redirect()->route('home');
        }

        // Toma datos desde sesión o, si no existen, desde la querystring
        $token       = $request->session()->get('payment_token')
                        ?? $request->query('token')
                        ?? $request->query('TBK_TOKEN');
        $idSesion    = $request->session()->get('payment_idSesion')
                        ?? $request->query('TBK_ID_SESION');
        $ordenCompra = $request->session()->get('payment_ordenCompra')
                        ?? $request->query('TBK_ORDEN_COMPRA');

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