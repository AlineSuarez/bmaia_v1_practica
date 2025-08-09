<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Prueba gratuita activada – B-MaiA</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8fafc;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: auto;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #f59e0b 0%, #e65100 100%);
            padding: 40px 30px;
            text-align: center;
            color: #fff;
            position: relative;
        }

        .header h1 {
            margin-bottom: 10px;
            font-size: 28px;
        }

        .header p {
            font-size: 16px;
            color: #fffde7;
        }

        .bee-icon {
            font-size: 48px;
            margin-bottom: 10px;
            animation: buzz 2s infinite ease-in-out;
        }

        @keyframes buzz {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(5deg); }
            75% { transform: rotate(-5deg); }
        }

        .content {
            padding: 30px;
            color: #1e293b;
        }

        .content h2 {
            font-size: 22px;
            margin-bottom: 15px;
        }

        .content p {
            font-size: 16px;
            line-height: 1.7;
            color: #475569;
        }

        .cta-button {
            display: inline-block;
            margin-top: 30px;
            padding: 14px 30px;
            background: linear-gradient(135deg, #ff8f00, #f57c00);
            color: white;
            font-weight: 600;
            text-decoration: none;
            border-radius: 30px;
            box-shadow: 0 6px 18px rgba(245, 124, 0, 0.3);
            transition: transform 0.2s ease;
        }

        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(245, 124, 0, 0.4);
        }

        .footer {
            background: #1e293b;
            padding: 25px;
            color: #cbd5e1;
            text-align: center;
            font-size: 14px;
        }

        .footer a {
            color: #fbbf24;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Encabezado -->
        <div class="header">
            <div class="bee-icon">🐝</div>
            <h1>¡Prueba Gratuita Activada!</h1>
            <p>Bienvenido al Plan Drone de B-MaiA</p>
        </div>

        <!-- Contenido -->
        <div class="content">
            <h2>Hola {{ $user->name }},</h2>
            <p>Nos complace informarte que tu prueba gratuita de <strong>16 días</strong> ha sido activada correctamente.</p>

            <p>Desde hoy puedes acceder a todas las funciones del plan <strong>Drone</strong> sin restricciones, hasta el día <strong>{{ $user->fecha_vencimiento->format('d/m/Y') }}</strong>.</p>

            <p>Aprovecha al máximo esta experiencia para mejorar la gestión de tus apiarios, digitalizar tus registros y ahorrar tiempo valioso.</p>

            <a href="{{ route('home') }}" class="cta-button">Ir al Panel</a>
        </div>

        <!-- Pie de página -->
        <div class="footer">
            <p>¿Tienes dudas o necesitas ayuda?</p>
            <p>Contáctanos a <a href="mailto:contacto@bmaia.cl">contacto@bmaia.cl</a> o vía WhatsApp al <a href="https://wa.me/56977632303">+56 9 7763 2303</a></p>
            <p>&copy; {{ date('Y') }} B-MaiA – Bee Fractal SpA</p>
        </div>
    </div>
</body>
</html>
