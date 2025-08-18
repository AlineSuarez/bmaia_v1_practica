@component('mail::message')
# ¡Plan activado!

Hola {{ $user->name }}, activamos tu plan **{{ strtoupper($plan) }}** por 12 meses.

@php
    use Carbon\Carbon;
    $venc = $user->fecha_vencimiento instanceof Carbon
        ? $user->fecha_vencimiento
        : ($user->fecha_vencimiento ? Carbon::parse($user->fecha_vencimiento) : null);
@endphp

Vence el {{ $venc ? $venc->format('d/m/Y') : '—' }}.

@component('mail::button', ['url' => route('user.settings')])
Ver mi plan
@endcomponent
@endcomponent
