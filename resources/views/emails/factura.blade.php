<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura N° {{ $factura->numero_mostrar }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
            color: #333333;
            background-color: #f6f6f6;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #fbbf24;
            color: #000;
            text-align: center;
            padding: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
        }
        .content {
            padding: 20px;
            line-height: 1.5;
        }
        .content h2 {
            color: #111827;
            font-size: 18px;
            margin-top: 0;
        }
        .factura-info {
            background: #f9fafb;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .factura-info p {
            margin: 4px 0;
        }
        .footer {
            text-align: center;
            padding: 15px;
            font-size: 12px;
            color: #6b7280;
            background-color: #f3f4f6;
        }
        .btn {
            display: inline-block;
            padding: 10px 16px;
            background-color: #fbbf24;
            color: #000;
            text-decoration: none;
            font-weight: bold;
            border-radius: 4px;
            margin-top: 15px;
        }
        .btn:hover {
            background-color: #d97706;
            color: #fff;
        }
    </style>
</head>
<body>

    <div class="container">
        <!-- ENCABEZADO -->
        <div class="header">
            <h1>Factura N° {{ $factura->numero_mostrar }}</h1>
        </div>

        <!-- CONTENIDO -->
        <div class="content">
            <p>Hola <strong>{{ $usuario->name ?? 'Cliente' }}</strong>,</p>
            <p>Adjunto encontrarás tu factura correspondiente al pago realizado.</p>

            <div class="factura-info">
                <p><strong>Monto total:</strong> ${{ number_format($factura->monto_total, 0, ',', '.') }}</p>
                <p><strong>Fecha de emisión:</strong> {{ optional($factura->fecha_emision)->format('d/m/Y H:i') }}</p>
                <p><strong>Estado:</strong> {{ ucfirst($factura->estado) }}</p>
            </div>

            <p>Si tienes cualquier duda sobre esta factura, por favor contáctanos respondiendo este correo o a través de nuestro soporte.</p>

            <a href="{{ config('app.url') }}" class="btn">Ir a mi cuenta</a>
        </div>

        <!-- PIE -->
        <div class="footer">
            <p>Este es un mensaje automático, por favor no respondas directamente a este correo.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Todos los derechos reservados.</p>
        </div>
    </div>

</body>
</html>
