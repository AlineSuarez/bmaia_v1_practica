<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>B-MaiA - Pago Fallido</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/components/home-user/payment/failed.css') }}">
</head>
<body>
    <div class="payment-failed-container">
        <div class="container-fluid h-100">
            <div class="row h-100 justify-content-center align-items-center">
                <div class="col-lg-6 col-md-8 col-sm-10 text-center">
                    <div class="error-animation-wrapper mb-4">
                        <div class="error-circle">
                            <div class="error-icon">
                                <i class="fas fa-times"></i>
                            </div>
                        </div>
                        <div class="pulse-ring"></div>
                        <div class="pulse-ring-2"></div>
                        <div class="pulse-ring-3"></div>
                    </div>

                    <div class="error-content">
                        <h1 class="error-title">¡Oops! Algo salió mal</h1>
                        <h2 class="error-subtitle">El pago no pudo ser procesado</h2>
                        <p class="error-description">
                            No te preocupes, no se realizó ningún cargo a tu cuenta. 
                            Puedes intentar nuevamente o contactar con nuestro soporte.
                        </p>

                        @if(session('error_message'))
                            <div class="error-details">
                                <div class="error-code">
                                    <i class="fas fa-info-circle me-2"></i>
                                    {{ session('error_message') }}
                                </div>
                            </div>
                        @endif

                        @if(isset($token) && $token)
                            <div class="mt-4">
                                <p class="text-muted small">Token de la transacción:</p>
                                <code class="text-danger">{{ $token }}</code>
                            </div>
                        @endif


                        <!-- Action Buttons -->
                        <div class="action-buttons mt-4">
                            <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-home me-2"></i>
                                Volver al Inicio
                            </a>
                        </div>

                        <!-- Help Link -->
                        <div class="help-link mt-4">
                            <p class="mb-2">¿Necesitas ayuda?</p>
                            <a href="tel:+56977632303" class="support-link">
                                <i class="fas fa-headset me-2"></i>
                                Contactar Soporte
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                window.location.href = "{{ route('payment.required') }}";
            }, 30000);
            
            const errorCircle = document.querySelector('.error-circle');
            if (errorCircle) {
                errorCircle.addEventListener('click', function() {
                    this.style.animation = 'none';
                    setTimeout(() => {
                        this.style.animation = 'errorBounce 0.5s ease-out';
                    }, 10);
                });
            }

            document.querySelectorAll('.btn, .support-link').forEach(function(element) {
                element.style.pointerEvents = 'auto';
                element.style.zIndex = '100';
                element.style.position = 'relative';
            });
        });
    </script>
</body>
</html>