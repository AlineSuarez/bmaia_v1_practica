<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Transbank\Webpay\WebpayPlus;
use Transbank\Webpay\WebpayPlus\Transaction;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
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
        try{
            $response = $transaction->commit($token);
        }catch(\Exception $e){
            \Log::error('Error al procesar la transacción: ' . $e->getMessage());
            return redirect()->route('payment.failed')->with('error', 'Error al procesar la transacción');
        }

        $payment = Payment::where('transaction_id', $token)->first();

        if ($response->isApproved()) {
            $payment->update(['status' => 'paid']);

            // Obtener el usuario y el plan
            $user = \App\Models\User::find(Auth::id());
            $plan = $payment->plan; // Asumimos que el plan se guardó en el pago

            // Determinar la fecha de expiración
            $fechaVencimiento = now()->addMonths($plan === 'mensual' ? 1 : 12);

            // Actualizar el usuario con el plan y la fecha de expiración
            $user->plan = $plan;
            $user->fecha_vencimiento = $fechaVencimiento;
            $user->webpay_status = 'pagado';
            $user->save();

            return redirect()->route('payment.success');
        } else {
            $payment->update(['status' => 'failed']);
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
            return redirect()->route('dashboard')->with('error', 'Ya tienes una prueba gratuita activa.');
        }

        // Si ya usó la prueba y pasaron los 16 días, no permitir otra
        if ($dronePayment && now()->diffInDays($dronePayment->created_at) >= 16) {
            return redirect()->route('payment.required')->with('error', 'Ya usaste tu prueba gratuita.');
        }

        // Crear el registro de prueba gratuita
        Payment::create([
            'user_id' => $user->id,
            'transaction_id' => 'trial-'.uniqid(),
            'status' => 'paid',
            'amount' => 0,
            'plan' => 'drone',
        ]);

        return redirect()->route('dashboard')->with('success', 'Prueba gratuita activada por 16 días.');
    }

}
