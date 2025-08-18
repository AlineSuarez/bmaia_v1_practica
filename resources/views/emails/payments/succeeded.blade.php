@component('mail::message')
# Pago recibido

Hola {{ $p->user->name }}, tu pago fue **aprobado**.

**Plan:** {{ strtoupper($p->plan) }}  
**Monto:** ${{ number_format($p->amount, 0, ',', '.') }}  
**Fecha:** {{ $p->created_at->format('d/m/Y H:i') }}

@component('mail::button', ['url' => route('user.settings')])
Ver mi suscripción
@endcomponent
@endcomponent
