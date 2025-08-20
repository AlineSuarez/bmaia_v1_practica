<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>B-MaiA - Pago Fallido</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/components/home-user/payment/failed.css') }}">
    <script src="https://unpkg.com/@lottiefiles/dotlottie-wc@0.6.2/dist/dotlottie-wc.js" type="module"></script>
</head>

<body>
    <div class="payment-failed-container">
        <!-- ===========================================
             SECCIÓN IZQUIERDA - ANIMACIÓN Y TÍTULO
             =========================================== -->
        <div class="left-section">
            <div class="error-animation-wrapper">
                <dotlottie-wc src="https://lottie.host/0e712568-8981-4507-89ca-58c250b3f8c0/K0TVD8xrLf.lottie"
                    class="error-lottie" speed="1" autoplay>
                </dotlottie-wc>
            </div>

            <div class="error-content">
                <h1 class="error-title">No pudimos procesar tu pago</h1>
                <p class="error-subtitle">La transacción fue rechazada o cancelada</p>
                <div class="error-badge">
                    <i class="fas fa-times-circle"></i>
                    <span>Pago Rechazado</span>
                </div>
            </div>
        </div>

        <!-- ===========================================
             SECCIÓN DERECHA - DETALLES DEL ERROR
             =========================================== -->
        <div class="right-section">
            <div class="details-container">
                <!-- Encabezado de Error -->
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h2 class="section-title">Detalles del Error</h2>
                </div>

                <!-- Información del Error -->
                <div class="error-info">
                    <p class="error-description">
                        No se realizó ningún cargo a tu cuenta. Puedes intentarlo nuevamente o contactar con nuestro
                        equipo de soporte para obtener ayuda.
                    </p>

                    @if(!empty($errorMessage))
                        <div class="error-message">
                            <div class="error-message-text">
                                <i class="fas fa-info-circle"></i>
                                <span>{{ $errorMessage }}</span>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- ===========================================
                     PRÓXIMOS PASOS
                     =========================================== -->
                <div class="next-steps-section">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="fas fa-list-check"></i>
                        </div>
                        <h2 class="section-title">¿Qué puedes hacer?</h2>
                    </div>

                    <ul class="steps-list">
                        <li class="step-item">
                            <div class="step-icon">
                                <i class="fas fa-credit-card"></i>
                            </div>
                            <span>Verificar que tu tarjeta tenga fondos suficientes y esté activa</span>
                        </li>
                        <li class="step-item">
                            <div class="step-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <span>Confirmar que los datos de la tarjeta sean correctos</span>
                        </li>
                        <li class="step-item">
                            <div class="step-icon">
                                <i class="fas fa-redo"></i>
                            </div>
                            <span>Intentar nuevamente con el mismo método de pago</span>
                        </li>
                        <li class="step-item">
                            <div class="step-icon">
                                <i class="fas fa-headset"></i>
                            </div>
                            <span>Contactar a soporte si el problema persiste</span>
                        </li>
                    </ul>
                </div>

                <!-- ===========================================
                     SECCIÓN DE AYUDA
                     =========================================== -->
                <div class="help-section">
                    <h3 class="help-title">¿Necesitas ayuda?</h3>
                    <p class="error-description"
                        style="margin-bottom: var(--spacing-4); font-size: var(--font-size-sm);">
                        Nuestro equipo de soporte está disponible para ayudarte con cualquier problema de pago.
                    </p>
                    <a href="{{ route('contacto.form') }}" class="support-link">
                        <i class="fas fa-headset"></i>
                        <span>Contactar Soporte</span>
                    </a>
                </div>

                <!-- ===========================================
                     BOTONES DE ACCIÓN FINALES
                     =========================================== -->
                <div class="action-buttons">
                    <a href="{{ route('payment.required') }}" class="btn btn-primary">
                        <i class="fas fa-redo"></i>
                        <span>Intentar Nuevamente</span>
                    </a>
                    <a href="{{ route('home') }}" class="btn btn-secondary">
                        <i class="fas fa-home"></i>
                        <span>Volver al Inicio</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Redirección automática después de 15 segundos
        document.addEventListener('DOMContentLoaded', function () {
            setTimeout(function () {
                window.location.href = "{{ route('home') }}";
            }, 15000);
        });
    </script>
</body>

</html>