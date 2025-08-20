<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Pago rechazado</title>
</head>

<body>
    <h1>Pago rechazado</h1>
    <p>
        No pudimos procesar tu pago del plan <strong>{{ strtoupper($p->plan) }}</strong>.
    </p>
    <p>
        Por favor intenta nuevamente o contáctanos.
    </p>
</body>

</html>