<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tu prueba gratuita ha terminado - B-Maia</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8fafc;
            padding: 20px;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            padding: 40px 30px;
            text-align: center;
            color: #fff;
        }

        .bee-icon {
            width: 70px;
            height: 70px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            margin: 0 auto 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 35px;
            border: 3px solid rgba(255, 255, 255, 0.3);
        }

        .header h1 {
            font-size: 26px;
            margin-bottom: 10px;
        }

        .header p {
            font-size: 16px;
            opacity: 0.9;
        }

        .content {
            padding: 30px;
            color: #1e293b;
        }

        .content p {
            font-size: 16px;
            line-height: 1.7;
            margin-bottom: 20px;
        }

        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: #fff;
            text-decoration: none;
            padding: 15px 25px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 16px;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
            transition: all 0.3s ease;
        }

        .cta-button:hover {
            background: linear-gradient(135deg, #facc15, #ca8a04);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(245, 158, 11, 0.4);
        }

        .footer {
            background: #1e293b;
            color: #cbd5e1;
            text-align: center;
            padding: 25px 20px;
            font-size: 13px;
        }

        .footer a {
            color: #facc15;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <div class="bee-icon">🐝</div>
            <h1>Fin de tu Prueba Gratuita</h1>
            <p>{{ $user->name }}, tu período de prueba terminó el {{ $user->fecha_vencimiento->format('d/m/Y') }}</p>
        </div>

        <!-- Content -->
        <div class="content">
            <p>Esperamos que hayas disfrutado de todas las funcionalidades del plan <strong>Drone</strong> durante tu prueba gratuita.</p>

            <p>Para continuar utilizando todas las herramientas inteligentes de B-Maia, te invitamos a contratar uno de nuestros planes de suscripción.</p>

            <p style="text-align: center;">
                <a href="{{ route('planes') }}" class="cta-button">Ver Planes de Suscripción</a>
            </p>

            <p>Si tienes dudas o necesitas ayuda para elegir el plan que mejor se adapte a ti, no dudes en contactarnos.</p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>B-Maia</strong> – Ecosistema de Apicultura Inteligente</p>
            <p>¿Tienes dudas? Escríbenos a <a href="mailto:contacto@bmaia.cl">contacto@bmaia.cl</a></p>
            <p>&copy; {{ now()->year }} Bee Fractal SpA. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
