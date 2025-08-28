<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Transbank\Webpay\WebpayPlus;
use Transbank\Webpay\WebpayPlus\Transaction;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
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
use App\Services\PlanService;
use App\Services\PaymentMailer;
use Carbon\Carbon;
use Illuminate\Session\Store;


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
        $allowTestPlan = $request->boolean('prod_test');
        $allowedPlans  = $allowTestPlan ? 'afc,me,ge,test' : 'afc,me,ge';
        // Validar plan
        $request->validateWithBag('plans', [
            'plan'     => "required|in:{$allowedPlans}",
            'doc_type' => 'required|in:boleta,factura',
        ]);

        $user = Auth::user();
        $plan = $request->input('plan');
        $docType = $request->input('doc_type');

        // ---- Validar datos si pidió FACTURA ----
        $df = $user->datosFacturacion;
        if ($docType === 'factura') {
            if (!$df || !$df->razon_social || !$df->rut || !$df->correo_envio_dte) {
                return back()->with(
                    'error',
                    'Para factura, completa Razón Social, RUT y Correo de envío DTE en tus Datos de Facturación.'
                );
            }
        }
        // 1) Verificar datos de facturación mínimos
        $df = $user->datosFacturacion;
        $billingSnapshot = null;
        $datoFacturacionId = null;

        if ($docType === 'factura') {
            if (!$df || !$df->razon_social || !$df->rut || !$df->correo_envio_dte) {
                return back()->with(
                    'error',
                    'Para factura, completa Razón Social, RUT y Correo de envío DTE en tus Datos de Facturación.'
                );
            }
        }
        // Monto
        $amount = match ($plan) {
            'afc' => 69900,
            'me' => 87900,
            'ge' => 150900,
            'test' => 50,
            default => 0,
        };
        if (now()->month == 8)
            $amount = (int) round($amount * 0.7);
        
        // ---- OVERRIDE solo si es PRODUCCIÓN y está habilitado ----
        $prodOverride = (env('WEBPAY_ENVIRONMENT') === 'PRODUCTION')
            && filter_var(env('WEBPAY_PROD_TEST_MODE', false), FILTER_VALIDATE_BOOLEAN)
            && $request->boolean('prod_test');

        $skipIva = false;

        if ($prodOverride) {
            $allowed = array_filter(array_map('trim', explode(',', (string) env('WEBPAY_PROD_TEST_ALLOWED'))));
            if (empty($allowed) || in_array(strtolower($user->email), array_map('strtolower', $allowed), true)) {
                $amount  = (int) env('WEBPAY_PROD_TEST_AMOUNT', 50); // monto final con IVA incluido
                $plan    = 'test';
                $docType = 'boleta'; // evitar facturas reales en prueba
                $skipIva = false;
            }
        }

        // ---- Aplica IVA solo si no es override ----
        if (!$skipIva) {
            $amount = (int) round($amount * 1.19);
        }
        
        // Aplica IVA 19% (total final)
        //$amount = $amount * 1.19;
        // Redondear a entero
        //$amount = (int) round($amount);
        // Transacción
        $buyOrder = uniqid('ORDER_');
        $sessionId = session()->getId();
        $response = (new Transaction())->create($buyOrder, $sessionId, $amount, route('payment.response'));

        // ⚠️ Solo setear snapshot si es FACTURA
        $billingSnapshot = null;
        $datoFacturacionId = null;
        if ($docType === 'factura' && $df) {
            $billingSnapshot = [
                'razon_social' => $df->razon_social,
                'rut' => $df->rut,
                'giro' => $df->giro,
                'direccion_comercial' => $df->direccion_comercial,
                'region_id' => $df->region_id,
                'comuna_id' => $df->comuna_id,
                'ciudad' => $df->ciudad,
                'telefono' => $df->telefono,
                'correo' => $df->correo,
                'autorizacion_envio_dte' => (bool) $df->autorizacion_envio_dte,
                'correo_envio_dte' => $df->correo_envio_dte,
            ];
            $datoFacturacionId = $df->id;
        }
        // 5) Guardar Payment (dejamos el token inmediatamente)
        Payment::create([
            'user_id' => $user->id,
            'dato_facturacion_id' => $datoFacturacionId,  
            'transaction_id' => $response->getToken(),
            'status' => 'pending',
            'amount' => $amount,
            'plan' => $plan,
            'doc_type' => $docType,
            'billing_snapshot' => $billingSnapshot,  
            'buy_order' => $buyOrder,
            'session_id' => $sessionId,
        ]);
        return redirect($response->getUrl() . '?token_ws=' . $response->getToken());
    }

    public function paymentResponse(Request $request)
    {
        $this->configureWebpay();

        // Acepta GET o POST (algunos entornos retornan por GET)
        $tbkToken = $request->input('TBK_TOKEN') ?? $request->query('TBK_TOKEN');
        $token = $request->input('token_ws') ?? $request->query('token_ws');

        // === ABORTO / ANULACIÓN DESDE WEBPAY (flujo TBK) ===
        if (empty($token) || !empty($tbkToken)) {
            // Webpay suele enviar también:
            $tbkOrder = $request->input('TBK_ORDEN_COMPRA') ?? $request->query('TBK_ORDEN_COMPRA');
            $tbkSession = $request->input('TBK_ID_SESION') ?? $request->query('TBK_ID_SESION');

            $p = null;
            if (!empty($token)) {
                $p = Payment::where('transaction_id', $token)->latest()->first();
            }
            if (!$p && !empty($tbkOrder)) {
                $p = Payment::where('buy_order', $tbkOrder)->latest()->first();
            }
            if (!$p && !empty($tbkSession)) {
                $p = Payment::where('session_id', $tbkSession)->latest()->first();
            }

            if ($p) {
                $p->update(['status' => 'voided']);
                //PaymentMailer::sendVoided($p);
            }
            $user = Auth::user();
            \Storage::disk('public')->makeDirectory('comprobantes/' . $user->id);
            $request->session()->put('payment_failed', true);
            return redirect()->route('payment.failed')->with('error', 'La compra fue cancelada por el usuario.');
        }

        // Guarda el token para mostrarlo en success/failed
        $request->session()->put('payment_token', $token);

        // === COMMIT NORMAL EN TRANSBANK ===
        try {
            $commit = (new Transaction())->commit($token);
        } catch (\Throwable $e) {
            \Log::error('[TBK] commit() exception: ' . $e->getMessage(), ['ex' => $e]);
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
                'token_ws' => $token,
                'buy_order' => method_exists($commit, 'getBuyOrder') ? $commit->getBuyOrder() : null,
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
            \Log::warning('[TBK] No se pudieron guardar campos de diagnóstico en payments: ' . $e->getMessage());
        }

        // === LECTURA RESULTADO ===
        $responseCode = (int) ($commit->getResponseCode() ?? 999);   // 0 = aprobado
        $status = strtoupper($commit->getStatus() ?? '');      // 'AUTHORIZED' = aprobado
        $paymentType = strtoupper($commit->getPaymentTypeCode() ?? '');
        \Log::info('[TBK] Resultado commit', compact('responseCode', 'status', 'paymentType'));

        // Regla correcta de aprobación: responseCode === 0 y status === 'AUTHORIZED'.
        if ($responseCode !== 0 || $status !== 'AUTHORIZED') {
            $payment->update(['status' => 'failed']);
            // Mail de pago rechazado
            PaymentMailer::sendFailed($payment);
            $request->session()->put('payment_failed', true);
            session()->flash('error', 'El pago fue rechazado por el emisor.');
            return redirect()->route('payment.failed');
        }

        // === Pago aprobado ===
        $start = $payment->created_at ?? now();
        $durationDays = (int) config('plans.duration_days', 365);
        $payment->forceFill([
            'status' => 'paid',
            'expires_at' => (clone $start)->addDays($durationDays),
        ])->save();

        /** @var \App\Models\User $user */
        $user = $payment->user ?? User::find(Auth::id());
        if (!$user) {
            \Log::error('[TBK] Usuario no encontrado para pago aprobado (payment_id=' . $payment->id . ')');
            $request->session()->put('payment_failed', true);
            return redirect()->route('payment.failed')->with('error', 'Usuario no encontrado.');
        }

        // Actualiza plan/fecha de vencimiento
        $user->plan = $payment->plan;
        $user->fecha_vencimiento = $payment->expires_at instanceof Carbon ? $payment->expires_at : Carbon::parse($payment->expires_at);
        $user->webpay_status = 'pagado';
        $user->save();
        $user->refresh();
        // Enviar correos
        PaymentMailer::sendSucceeded($payment);
        PaymentMailer::sendPlanActivated($user, $payment->plan);

        // === Documento según elección ===
        if ($payment->doc_type === 'factura') {
            // === Crear factura + PDF (si falla, no bloquea el success) ===
            try {
                // amount ES EL TOTAL COBRADO (con IVA). Prorrateamos a neto + IVA
                $porcIva = 19;
                $montoTot = (int) $payment->amount;                        // total cobrado
                $montoNeto = (int) round($montoTot / (1 + $porcIva / 100));  // prorrateo 19%
                $montoIva = $montoTot - $montoNeto;
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
                        'razon_social' => $df->razon_social,
                        'rut' => $df->rut,
                        'giro' => $df->giro,
                        'direccion_comercial' => $df->direccion_comercial,
                        'region_id' => $df->region_id,
                        'comuna_id' => $df->comuna_id,
                        'ciudad' => $df->ciudad,
                        'telefono' => $df->telefono,
                        'correo' => $df->correo,
                        'autorizacion_envio_dte' => (bool) $df->autorizacion_envio_dte,
                        'correo_envio_dte' => $df->correo_envio_dte,
                        'region' => $regionNombre,
                        'region_nombre' => $regionNombre,
                        'comuna' => $comunaNombre,
                        'comuna_nombre' => $comunaNombre,
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
                        $billingSnapshot['region'] = $billingSnapshot['region'] ?? $regionNombre;
                        $billingSnapshot['region_nombre'] = $billingSnapshot['region_nombre'] ?? $regionNombre;
                        $billingSnapshot['comuna'] = $billingSnapshot['comuna'] ?? $comunaNombre;
                        $billingSnapshot['comuna_nombre'] = $billingSnapshot['comuna_nombre'] ?? $comunaNombre;
                    }
                }
                $numeroFactura = now()->format('Ymd') . '-' . strtoupper(\Str::random(4));
                $facturaPdf = Pdf::loadView('user.factura', [
                    'user' => $user,
                    'payment' => $payment,
                    'montoNeto' => $montoNeto,
                    'montoIva' => $montoIva,
                    'montoTotal' => $montoTot,
                    'numero' => $numeroFactura,
                    'snapshot' => $billingSnapshot,
                ]);

                $pdfFilename = 'facturas/' . $user->id . '/' . $numeroFactura . '.pdf';
                \Storage::disk('public')->put($pdfFilename, $facturaPdf->output());
                $factura = Factura::create([
                    'user_id' => $user->id,
                    'payment_id' => $payment->id,
                    'numero' => $numeroFactura,
                    'folio' => null,
                    'sii_track_id' => null,
                    'estado' => 'emitida',
                    'monto_neto' => $montoNeto,
                    'monto_iva' => $montoIva,
                    'monto_total' => $montoTot,
                    'porcentaje_iva' => $porcIva,
                    'moneda' => 'CLP',
                    'fecha_emision' => now(),
                    'fecha_vencimiento' => now()->addDays(30),
                    'pdf_path' => $pdfFilename,
                    'xml_path' => null,
                    'pdf_url' => null,
                    'xml_url' => null,
                    'datos_facturacion_snapshot' => $billingSnapshot,
                    'plan' => $payment->plan,
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
        } else {
            // === COMPROBANTE / BOLETA (voucher interno) ===
            $items = [
                [
                    'desc' => 'Suscripción plan ' . strtoupper($payment->plan) . ' por 12 meses',
                    'qty' => 1,
                    'price' => (int) $payment->amount,
                ]
            ];

            $paymentTypeLabel = match (strtoupper($payment->payment_type ?? '')) {
                'VD' => 'Débito',
                'VN', 'VC', 'SI', 'S2' => 'Crédito',
                default => 'Tarjeta',
            };

            $payment->forceFill([
                'receipt_number' => now()->format('Ymd-His') . '-' . $payment->id,
                'receipt_issued_at' => now(),
                'receipt_payment_method' => $paymentTypeLabel,
                'receipt_items' => $items,
            ])->save();

            // ✅ crear carpeta y guardar PDF
            \Storage::disk('public')->makeDirectory('comprobantes/' . $user->id);

            // PDF del comprobante
            $receiptPdf = Pdf::loadView('payment.receipt', [
                'user' => $user,
                'payment' => $payment,
                'empresa' => [
                    'razon' => config('app.company_name', 'Bee Fractal SpA'),
                    'rut' => config('app.company_rut', 'XX.XXX.XXX-X'),
                ],
            ]);

            $receiptName = 'comprobantes/' . $user->id . '/COMP-' . $payment->receipt_number . '.pdf';
            \Storage::disk('public')->put($receiptName, $receiptPdf->output());
            $payment->update(['receipt_pdf_path' => $receiptName]);

            PaymentMailer::sendReceipt($payment);
        }


        // Si ya hay factura, continúa a success (después de enviar correos)
        if (Factura::where('payment_id', $payment->id)->exists()) {
            $request->session()->put('payment_success', true);
            return redirect()->route('payment.success');
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
            'user_id' => $user->id,
            'transaction_id' => 'trial-' . uniqid(),
            'status' => 'paid',
            'amount' => 0,
            'plan' => 'drone',
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
                'body' => $sgResp->body(),
            ]);
        } catch (Exception $e) {
            \Log::error('SendGrid trial error: ' . $e->getMessage());
        }

        return redirect()->route('home')->with('success', 'Prueba gratuita activada por 16 días.');
    }

    public function showSuccess(Request $request)
    {
        // Solo permite mostrar si viene de un commit exitoso
        $fromCommit = (bool) $request->session()->pull('payment_success', false);

        if (!$fromCommit) {
            return redirect()->route('home')
                ->with('error', 'No se encontró un pago aprobado reciente para mostrar.');
        }

        $payment = Payment::where('user_id', Auth::id())
            ->where('status', 'paid')
            ->latest()
            ->with(['datosFacturacion', 'factura', 'user'])
            ->first();

        // URL de comprobante si es boleta
        $receiptUrl = null;
        if ($payment && $payment->receipt_pdf_path && \Storage::disk('public')->exists($payment->receipt_pdf_path)) {
            $receiptUrl = asset('storage/' . $payment->receipt_pdf_path);
        }

        // URL de factura si existe
        $facturaUrl = null;
        if ($payment && $payment->factura && $payment->factura->pdf_path) {
            if (\Storage::disk('public')->exists($payment->factura->pdf_path)) {
                $facturaUrl = asset('storage/' . $payment->factura->pdf_path);
            }
        }
        if (!$facturaUrl && !empty($payment?->factura_pdf_url)) {
            $facturaUrl = $payment->factura_pdf_url;
        }

        // clave para la vista
        $isFactura = ($payment?->doc_type === 'factura');

        // Para BOLETA: datos desde el perfil del usuario
        $buyer = null;
        if (!$isFactura) {
            $u = $payment->user;
            $buyer = [
                'nombre' => trim(($u->name ?? '') . ' ' . ($u->last_name ?? '')) ?: ($u->name ?? $u->email),
                'rut' => $u->rut,
                'telefono' => $u->telefono,
                'correo' => $u->email,
            ];
        }

        // >>> FALTABA PASAR isFactura y buyer <<<
        return view('payment.success', compact('payment', 'facturaUrl', 'receiptUrl', 'isFactura', 'buyer'));
    }


    public function showFailed(Request $request)
    {
        $canShow = $request->session()->get('payment_failed')
            || $request->boolean('aborted')
            || $request->boolean('rejected');

        if (!$canShow) {
            return redirect()->route('home');
        }

        $errorMessage = session('error') ?: 'No pudimos procesar tu pago. No se realizó ningún cargo.';

        // Limpia cualquier resto sensible de la sesión
        $request->session()->forget([
            'payment_failed',
            'payment_token',
            'payment_idSesion',
            'payment_ordenCompra',
            'tbk_debug'
        ]);

        return view('payment.failed', compact('errorMessage'));
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