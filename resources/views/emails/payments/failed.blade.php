@component('mail::message')
# Pago rechazado

No pudimos procesar tu pago del plan **{{ strtoupper($p->plan) }}**.

Por favor intenta nuevamente o contáctanos.
@endcomponent
