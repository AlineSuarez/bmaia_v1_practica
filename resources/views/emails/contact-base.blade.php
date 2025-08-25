<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo mensaje de contacto</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
            background-color: #f5f7fa;
        }

        .container {
            max-width: 700px;
            margin: 0 auto;
            background: #ffffff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f1c40f;
        }

        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .tagline {
            color: #7f8c8d;
            font-size: 14px;
            margin: 0;
        }

        h2 {
            color: #2c3e50;
            margin-bottom: 25px;
            font-size: 22px;
        }

        .field {
            margin-bottom: 18px;
            display: flex;
            align-items: flex-start;
        }

        .label {
            font-weight: 600;
            color: #555;
            min-width: 140px;
            margin-right: 10px;
        }

        .value {
            color: #333;
            flex: 1;
        }

        .message-box {
            background: #f8f9fa;
            padding: 25px;
            border-left: 4px solid #3498db;
            margin: 25px 0;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .about-section {
            background: linear-gradient(135deg, #fff9e6, #fef3c7);
            padding: 25px;
            border-radius: 8px;
            margin: 30px 0;
            border: 1px solid #f1c40f;
        }

        .about-title {
            color: #b8860b;
            font-weight: 600;
            margin-bottom: 15px;
            font-size: 16px;
        }

        .about-content {
            color: #555;
            font-size: 14px;
            line-height: 1.7;
        }

        .services {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 15px;
        }

        .service-item {
            background: rgba(255, 255, 255, 0.6);
            padding: 12px;
            border-radius: 5px;
            font-size: 13px;
            text-align: center;
            color: #7c6d07;
            font-weight: 500;
        }

        .footer {
            margin-top: 35px;
            padding-top: 25px;
            border-top: 1px solid #e0e6ed;
            text-align: center;
        }

        .footer-info {
            color: #666;
            font-size: 13px;
            margin-bottom: 10px;
        }

        .footer-links {
            font-size: 12px;
            color: #888;
        }

        .footer-links a {
            color: #3498db;
            text-decoration: none;
        }

        @media (max-width: 600px) {
            .container {
                padding: 25px;
                margin: 10px;
            }

            .field {
                flex-direction: column;
            }

            .label {
                min-width: auto;
                margin-bottom: 5px;
            }

            .services {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="logo">B-MaiA</div>
            <p class="tagline">Ecosistema de Apicultura Inteligente</p>
        </div>

        <h2>Nuevo mensaje de contacto</h2>

        <div class="field">
            <span class="label">Nombre:</span>
            <span class="value">{{ $datos['nombre'] }}</span>
        </div>

        <div class="field">
            <span class="label">Email:</span>
            <span class="value">{{ $datos['email'] }}</span>
        </div>

        @if(!empty($datos['telefono']))
            <div class="field">
                <span class="label">Teléfono:</span>
                <span class="value">{{ $datos['telefono'] }}</span>
            </div>
        @endif

        @if(!empty($datos['empresa']))
            <div class="field">
                <span class="label">Empresa:</span>
                <span class="value">{{ $datos['empresa'] }}</span>
            </div>
        @endif

        @if(!empty($datos['tipo']))
            <div class="field">
                <span class="label">Tipo de consulta:</span>
                <span class="value">{{ ucfirst($datos['tipo']) }}</span>
            </div>
        @endif

        <div class="message-box">
            <strong>Mensaje:</strong><br><br>
            {{ $datos['mensaje'] }}
        </div>

        <div class="about-section">
            <div class="about-title">Acerca de B-MaiA</div>
            <div class="about-content">
                B-MaiA es el primer ecosistema de inteligencia artificial especializado en apicultura de Chile.
                Desarrollamos herramientas innovadoras que permiten a los apicultores optimizar sus operaciones,
                aumentar la productividad y cuidar mejor la salud de sus colmenas.
            </div>
        </div>

        <div class="footer">
            <div class="footer-info">
                Enviado desde el formulario de contacto de <strong>bmaia.cl</strong><br>
                {{ now()->format('d/m/Y H:i:s') }}
            </div>
            <div class="footer-links">
                <a href="https://bmaia.cl">Visitar sitio web</a> |
                <a href="mailto:contacto@bmaia.cl">contacto@bmaia.cl</a>
            </div>
        </div>
    </div>
</body>

</html>