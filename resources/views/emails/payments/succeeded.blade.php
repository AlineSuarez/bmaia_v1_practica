<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Pago recibido</title>
</head>

<body>
    <h1>Pago recibido</h1>
    <p>
        Hola {{ $payment->user->name }}, tu pago fue <strong>aprobado</strong>.
    </p>
    <ul>
        <li><strong>Plan:</strong> {{ strtoupper($payment->plan) }}</li>
        <li><strong>Monto:</strong> ${{ number_format($payment->amount, 0, ',', '.') }}</li>
        <li><strong>Fecha:</strong> {{ $payment->created_at->format('d/m/Y H:i') }}</li>
    </ul>
    <p>
        <a href="{{ route('user.settings') }}"
            style="display:inline-block;padding:10px 20px;background:#007bff;color:#fff;text-decoration:none;border-radius:4px;">
            Ver mi suscripción
        </a>
    </p>
</body>

</html>