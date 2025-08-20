<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>¡Plan activado!</title>
</head>

<body>
    <h1>¡Plan activado!</h1>
    <p>
        Hola {{ $user->name }}, activamos tu plan <strong>{{ strtoupper($plan) }}</strong> por 12 meses.
    </p>
    @php
        use Carbon\Carbon;
        $venc = $user->fecha_vencimiento instanceof Carbon
            ? $user->fecha_vencimiento
            : ($user->fecha_vencimiento ? Carbon::parse($user->fecha_vencimiento) : null);
    @endphp
    <p>
        Vence el <strong>{{ $venc ? $venc->format('d/m/Y') : '—' }}</strong>.
    </p>
    <p>
        <a href="{{ route('user.settings') }}"
            style="display:inline-block;padding:10px 20px;background:#007bff;color:#fff;text-decoration:none;border-radius:4px;">
            Ver mi plan
        </a>
    </p>
</body>

</html>