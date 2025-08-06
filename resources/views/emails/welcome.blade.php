<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¡Bienvenido a B-MaiA!</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            padding: 20px;
            line-height: 1.6;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            position: relative;
        }

        .header {
            background: linear-gradient(135deg, #ff8f00 0%, #f57c00 50%, #e65100 100%);
            padding: 40px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><polygon fill="rgba(255,255,255,0.05)" points="50,0 93.3,25 93.3,75 50,100 6.7,75 6.7,25"/></svg>') repeat;
            background-size: 60px 60px;
            animation: float 20s linear infinite;
        }

        @keyframes float {
            0% { transform: translateX(0) translateY(0); }
            100% { transform: translateX(-60px) translateY(-60px); }
        }

        .logo-section {
            position: relative;
            z-index: 2;
        }

        .bee-icon {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.4); }
            50% { transform: scale(1.05); box-shadow: 0 0 0 20px rgba(255, 255, 255, 0); }
        }

        .bee-emoji {
            font-size: 40px;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
        }

        .header h1 {
            color: #ffffff;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .highlight {
            color: #fff3e0;
            font-weight: 800;
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
        }

        .header p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 16px;
            font-weight: 400;
        }

        .content {
            padding: 40px 30px;
        }

        .welcome-message {
            text-align: center;
            margin-bottom: 40px;
        }

        .welcome-message h2 {
            color: #1e293b;
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .welcome-message p {
            color: #64748b;
            font-size: 16px;
            line-height: 1.7;
        }

        .features-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 30px 0;
        }

        .feature-item {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            padding: 20px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .feature-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #ff8f00 0%, #f57c00 100%);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            color: #ffffff;
            font-size: 20px;
        }

        .feature-item h3 {
            color: #1e293b;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .feature-item p {
            color: #64748b;
            font-size: 12px;
            line-height: 1.5;
        }

        .cta-section {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            margin: 30px 0;
            border: 1px solid #e0f2fe;
        }

        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #ff8f00 0%, #f57c00 100%);
            color: #ffffff;
            text-decoration: none;
            padding: 15px 30px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 16px;
            margin-top: 15px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255, 143, 0, 0.3);
        }

        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 143, 0, 0.4);
        }

        .stats-section {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
            margin: 30px 0;
            text-align: center;
        }

        .stat-item {
            padding: 20px;
        }

        .stat-number {
            color: #ff8f00;
            font-size: 32px;
            font-weight: 800;
            display: block;
            margin-bottom: 5px;
        }

        .stat-label {
            color: #64748b;
            font-size: 12px;
            font-weight: 500;
        }

        .footer {
            background: #1e293b;
            padding: 30px;
            text-align: center;
            color: #94a3b8;
        }

        .footer-logo {
            color: #ffffff;
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .footer p {
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .social-links {
            margin-top: 20px;
        }

        .social-link {
            display: inline-block;
            width: 40px;
            height: 40px;
            background: rgba(255, 143, 0, 0.1);
            border-radius: 50%;
            margin: 0 10px;
            line-height: 40px;
            color: #ff8f00;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .social-link:hover {
            background: #ff8f00;
            color: #ffffff;
            transform: translateY(-2px);
        }

        .divider {
            height: 1px;
            background: linear-gradient(90deg, transparent 0%, #e2e8f0 50%, transparent 100%);
            margin: 30px 0;
        }

        /* Responsive */
        @media (max-width: 600px) {
            .email-container {
                margin: 10px;
                border-radius: 12px;
            }

            .header {
                padding: 30px 20px;
            }

            .content {
                padding: 30px 20px;
            }

            .features-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .stats-section {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .header h1 {
                font-size: 24px;
            }

            .welcome-message h2 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <div class="logo-section">
                <div class="bee-icon">
                    <span class="bee-emoji">🐝</span>
                </div>
                <h1>¡Bienvenido a B-Ma<span class="highlight">iA</span>!</h1>
                <p>Ecosistema de Apicultura Inteligente</p>
            </div>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="welcome-message">
                <h2>¡Hola {{ $user->name }}!</h2>
                <p>Nos alegra enormemente tenerte como parte de la comunidad B-MaiA. Has dado el primer paso hacia una gestión apícola más inteligente, eficiente y profesional.</p>
            </div>

            <!-- Features Grid -->
            <div class="features-grid">
                <div class="feature-item">
                    <div class="feature-icon">📊</div>
                    <h3>Trazabilidad</h3>
                    <p>Control completo de tus colmenas</p>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">📋</div>
                    <h3>Cuaderno Digital</h3>
                    <p>Cumple con normativas SAG</p>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">📅</div>
                    <h3>Planificación</h3>
                    <p>Organiza tus inspecciones</p>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">🌤️</div>
                    <h3>Clima en Tiempo Real</h3>
                    <p>Monitoreo meteorológico</p>
                </div>
            </div>

            <!-- Stats Section -->
            <div class="stats-section">
                <div class="stat-item">
                    <span class="stat-number">50%</span>
                    <span class="stat-label">Ahorro de tiempo</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">30%</span>
                    <span class="stat-label">Reducción de mortandad</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">100%</span>
                    <span class="stat-label">Trazabilidad</span>
                </div>
            </div>

            <div class="divider"></div>

            <!-- CTA Section -->
            <div class="cta-section">
                <h3 style="color: #1e293b; margin-bottom: 15px; font-size: 20px;">¡Comienza tu experiencia!</h3>
                <p style="color: #64748b; margin-bottom: 20px;">Accede a tu cuenta y descubre todas las herramientas que tenemos preparadas para optimizar tu gestión apícola.</p>
                <a href="{{ url('/login') }}" class="cta-button">Iniciar Sesión</a>
            </div>

            <div class="divider"></div>

            <div style="text-align: center; color: #64748b;">
                <p><strong>¿Necesitas ayuda?</strong></p>
                <p>Nuestro equipo de soporte está disponible para acompañarte en cada paso. No dudes en contactarnos si tienes preguntas.</p>
                <p style="margin-top: 20px;">
                    📧 <a href="mailto:contacto@bmaia.cl" style="color: #ff8f00; text-decoration: none;">contacto@bmaia.cl</a><br>
                    📱 <a href="https://wa.me/56977632303" style="color: #ff8f00; text-decoration: none;">+56 9 7763 2303</a>
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-logo">Bee Fractal SpA</div>
            <p>Nuestra misión es contribuir a la creación de una apicultura más eficiente y resiliente, integrando tecnología con respeto por el entorno natural.</p>
            
            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #334155; font-size: 12px;">
                <p>&copy; {{ date('Y') }} B-MaiA. Todos los derechos reservados.</p>
                <p>Talca, Chile - Región del Maule</p>
            </div>
        </div>
    </div>
</body>
</html>