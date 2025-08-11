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

        $token = $request->input('token_ws');
        try {
            $response = (new Transaction())->commit($token);
        } catch (Exception $e) {
            \Log::error('Error al procesar la transacción: ' . $e->getMessage());
            $request->session()->put('payment_failed', true);
            return redirect()->route('payment.failed')->with('error', 'Error al procesar la transacción');
        }

        $payment = Payment::where('transaction_id', $token)->first();
        if (!$payment) {
            \Log::error('No se encontró el pago para el token: ' . $token);
            $request->session()->put('payment_failed', true);
            return redirect()->route('payment.failed')->with('error', 'No se encontró el pago asociado.');
        }

        if ($response->isApproved()) {
            $payment->update(['status' => 'paid']);

            $user = \App\Models\User::find(Auth::id());
            if (!$user) {
                \Log::error('Usuario no encontrado para el pago.');
                $request->session()->put('payment_failed', true);
                return redirect()->route('payment.failed')->with('error', 'Usuario no encontrado.');
            }

            // Plan y vencimiento (anual)
            $plan = $payment->plan;
            $user->plan = $plan;
            $user->fecha_vencimiento = now()->addYear();
            $user->webpay_status = 'pagado';
            $user->save();

            $request->session()->put('payment_success', true);
            return redirect()->route('payment.success');
        } else {
            $payment->update(['status' => 'failed']);
            $request->session()->put('payment_failed', true);
            return redirect()->route('payment.failed');
        }
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

        $payment = Payment::where('user_id', Auth::id())
            ->where('status', 'paid')
            ->latest()
            ->with('datosFacturacion')
            ->first();

        return view('payment.success', compact('payment'));
    }

    public function showFailed(Request $request)
    {
        if (!$request->session()->pull('payment_failed')) {
            return redirect()->route('home');
        }
        return view('payment.failed');
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
