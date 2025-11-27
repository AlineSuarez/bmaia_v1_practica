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

        // 🔎 Saber si la ruta actual pertenece a Hoja de Ruta
        $isHojaRutaRoute = $request->routeIs('hojaruta.*') || $request->is('hoja-de-ruta/*');

        // 1. Permitir acceso si tiene plan activo y no vencido
        if ($user->plan && (!$user->fecha_vencimiento || now()->lessThanOrEqualTo($user->fecha_vencimiento))) {

            // 🐝 Regla extra SOLO para Hoja de Ruta:
            // si la ruta es de hoja de ruta y el usuario no tiene el módulo contratado,
            // lo mandamos a la pantalla de pago.
            if ($isHojaRutaRoute && !$user->has_hoja_ruta) {
                return redirect()
                    ->route('payment.required')
                    ->with('error', 'Debes contratar el módulo Hoja de Ruta para acceder a esta sección.');
            }

            return $next($request);
        }

        // 2. Permitir acceso por prueba gratuita solo si nunca tuvo plan pago
        $hasPaidPlan = Payment::where('user_id', $user->id)
            ->whereIn('plan', ['afc', 'me', 'ge']) // agrega aquí todos los planes pagos
            ->where('status', 'paid')
            ->exists();

        $dronePayment = Payment::where('user_id', $user->id)
            ->where('plan', 'drone')
            ->where('status', 'paid')
            ->orderByDesc('created_at')
            ->first();

        if (
            !$hasPaidPlan &&
            $dronePayment &&
            now()->lessThan($dronePayment->created_at->addDays(16))
        ) {
            // 👇 Durante la prueba gratuita dejamos pasar a todo, incluida Hoja de Ruta
            return $next($request);
        }

        // 3. Si no cumple ninguna condición, redirigir
        return redirect()
            ->route('payment.required')
            ->with('error', 'Debes completar el pago para acceder a esta sección.');
    }
}
