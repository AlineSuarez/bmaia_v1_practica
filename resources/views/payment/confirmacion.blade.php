@extends('layouts.app')

@section('content')
<div class="container text-center py-5">
    <h2>¡Pago realizado con éxito! 🎉</h2>
    <p>Tu factura ha sido generada por un monto de <strong>${{ number_format($factura->monto_total, 0, ',', '.') }}</strong>.</p>
    <p>¿Quieres que enviemos una copia de la factura a tu correo electrónico <strong>{{ auth()->user()->email }}</strong>?</p>

    <form action="{{ route('facturas.enviarCorreo', $factura) }}" method="POST" style="display:inline-block;">
        @csrf
        <button type="submit" class="btn btn-primary">Sí, enviar</button>
    </form>

    <a href="{{ route('user.settings') }}" class="btn btn-secondary">No, gracias</a>
</div>
@endsection
