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

    public function initiatePayment(Request $request)
    {
        // Validar que el plan esté presente en la solicitud
        $request->validate([
            'plan' => 'required|in:afc,me,ge',
        ]);
        $user = Auth::user();
        $plan = $request->input('plan');
        $transaction = new Transaction();

        // Asignar el monto según el plan seleccionado
        switch ($plan) {
            case 'afc':
                $amount = 69900;
                break;
            case 'me':
                $amount = 87900;
                break;
            case 'ge':
                $amount = 150900;
                break;
            default:
                return redirect()->back()->with('error', 'Plan no válido');
        }

        // DESCUENTO DEL 30% SOLO EN AGOSTO
        $now = now();
        if ($now->month == 8) { // Agosto
            $amount = intval(round($amount * 0.7));
        }

        $response = $transaction->create(
            uniqid(),
            uniqid(),
            $amount,
            route('payment.response')
        );

        Payment::create([
            'user_id' => $user->id,
            'transaction_id' => $response->getToken(),
            'status' => 'pending',
            'amount' => $amount,
            'plan' => $plan,
        ]);

        return redirect($response->getUrl() . '?token_ws=' . $response->getToken());
    }

    public function paymentResponse(Request $request)
    {
        $transaction = new Transaction();
        $token = $request->input('token_ws');
        try {
            $response = $transaction->commit($token);
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

            // Obtener el usuario y el plan
            $user = \App\Models\User::find(Auth::id());
            if (!$user) {
                \Log::error('Usuario no encontrado para el pago.');
                $request->session()->put('payment_failed', true);
                return redirect()->route('payment.failed')->with('error', 'Usuario no encontrado.');
            }
            $plan = $payment->plan; // Asumimos que el plan se guardó en el pago

            // Determinar la fecha de expiración
            $fechaVencimiento = now()->addMonths($plan === 'mensual' ? 1 : 12);

            // Actualizar el usuario con el plan y la fecha de expiración
            $user->plan = $plan;
            $user->fecha_vencimiento = $fechaVencimiento;
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

        // Buscar el último registro de prueba gratuita (plan drone)
        $dronePayment = Payment::where('user_id', $user->id)
            ->where('plan', 'drone')
            ->orderByDesc('created_at')
            ->first();

        // Si ya tiene una prueba activa (menos de 16 días), no permitir otra
        if ($dronePayment && now()->diffInDays($dronePayment->created_at) < 16) {
            return redirect()->route('home')->with('error', 'Ya tienes una prueba gratuita activa.');
        }

        // Si ya usó la prueba y pasaron los 16 días, no permitir otra
        if ($dronePayment && now()->diffInDays($dronePayment->created_at) >= 16) {
            // Cuando detectes que debe ver el required
            $request->session()->put('payment_required', true);
            return redirect()->route('payment.required')->with('error', 'Ya usaste tu prueba gratuita.');
        }

        // Crear el registro de prueba gratuita
        Payment::create([
            'user_id' => $user->id,
            'transaction_id' => 'trial-' . uniqid(),
            'status' => 'paid',
            'amount' => 0,
            'plan' => 'drone',
        ]);

        // Actualizar campo de vencimiento en tabla users
        $user->fecha_vencimiento = now()->addDays(16);
        $user->save();

        // Enviar correo de activación de prueba
        $htmlContent = View::make('emails.free-trial-activated', ['user' => $user])->render();

        $email = new \SendGrid\Mail\Mail();
        $email->setFrom("soporte@bmaia.cl", "B-MaiA - Prueba Gratuita");
        $email->setSubject("¡Prueba gratuita activada en B-MaiA!");
        $email->addTo($user->email, $user->name);
        $email->addContent(
            "text/plain",
            "¡Hola {$user->name}! Tu prueba gratuita ha sido activada por 16 días."
        );
        $email->addContent(
            "text/html",
            $htmlContent
        );

        $sendgrid = new SendGrid(config('services.sendgrid.api_key'));
        try {
            $response = $sendgrid->send($email);
            \Log::info('SendGrid trial response', [
                'status' => $response->statusCode(),
                'body' => $response->body(),
                'headers' => $response->headers(),
            ]);
        } catch (Exception $e) {
            \Log::error('SendGrid trial error: ' . $e->getMessage());
        }

        return redirect()->route('home')->with('success', 'Prueba gratuita activada por 16 días.');
    }

    public function showSuccess(Request $request)
    {
        // Solo permite acceso si existe el flag en sesión
        if (!$request->session()->pull('payment_success')) {
            return redirect()->route('home');
        }
        return view('payment.success');
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

        // Si el usuario NO tiene plan activo o el plan está vencido, mostrar la vista
        if (!$user->plan || ($user->fecha_vencimiento && now()->greaterThan($user->fecha_vencimiento))) {
            return view('payment.required');
        }

        // Si tiene plan activo, redirigir al HOME
        return redirect()->route('home');
    }
}
