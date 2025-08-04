<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Payment;

class CheckPaymentStatus
{
    /**
     * Manejar una solicitud entrante.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Permitir acceso si tiene un plan pagado
        $hasPaidPlan = Payment::where('user_id', $user->id)
            ->where('status', 'paid')
            ->whereIn('plan', ['afc', 'me', 'ge'])
            ->exists();

        if ($hasPaidPlan) {
            return $next($request);
        }

        // Permitir acceso si tiene prueba gratuita activa (menos de 16 días)
        $dronePayment = Payment::where('user_id', $user->id)
            ->where('plan', 'drone')
            ->where('status', 'paid')
            ->orderByDesc('created_at')
            ->first();

        if ($dronePayment && now()->diffInDays($dronePayment->created_at) < 16) {
            return $next($request);
        }

        // Si no tiene pago ni prueba activa, redirigir
        return redirect()->route('payment.required')->with('error', 'Debes completar el pago para acceder a esta sección.');
    }
}
