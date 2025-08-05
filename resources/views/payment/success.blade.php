<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>B-MaiA - Pago Exitoso</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/components/home-user/payment/success.css') }}">
</head>
<body>
    <div class="payment-success-container">
        <div class="container-fluid h-100">
            <div class="row h-100 justify-content-center align-items-center">
                <div class="col-lg-6 col-md-8 col-sm-10 text-center">
                    <div class="success-animation-wrapper mb-4">
                        <div class="success-circle">
                            <div class="success-icon">
                                <i class="fas fa-check"></i>
                            </div>
                        </div>
                        <div class="success-ring"></div>
                        <div class="success-ring-2"></div>
                        <div class="success-ring-3"></div>
                    </div>

                    <div class="success-content">
                        <h1 class="success-title">¡Pago Exitoso!</h1>
                        <h2 class="success-subtitle">Tu transacción se ha completado correctamente</h2>
                        <p class="success-description">
                            ¡Felicidades! Ahora tienes acceso completo a todas las funciones premium de B-MaiA.
                        </p>

                        <div class="transaction-details">
                            <div class="detail-card">
                                <div class="detail-icon">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                                <div class="detail-info">
                                    <small>Fecha de transacción</small>
                                    <strong>{{ date('d/m/Y H:i') }}</strong>
                                </div>
                            </div>
                            <div class="detail-card">
                                <div class="detail-icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="detail-info">
                                    <small>Estado</small>
                                    <strong class="text-success">Aprobado</strong>
                                </div>
                            </div>
                        </div>

                        <div class="next-steps">
                            <h5 class="steps-title">¿Qué sigue ahora?</h5>
                            <div class="steps-list">
                                <div class="step-item">
                                    <i class="fas fa-star"></i>
                                    <span>Ya puedes acceder a todas las funciones premium</span>
                                </div>
                                <div class="step-item">
                                    <i class="fas fa-cog"></i>
                                    <span>Revisa tu configuración de cuenta actualizada</span>
                                </div>
                                <div class="step-item">
                                    <i class="fas fa-rocket"></i>
                                    <span>Explora las nuevas herramientas disponibles</span>
                                </div>
                            </div>
                        </div>

                        <div class="action-buttons mt-4">
                            <a href="{{ route('user.settings') }}" class="btn btn-primary btn-lg me-3">
                                <i class="fas fa-cog me-2"></i>
                                Ir a Configuración
                            </a>
                            <a href="{{ route('home') }}" class="btn btn-outline-success btn-lg">
                                <i class="fas fa-home me-2"></i>
                                Volver al Inicio
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
            const successCircle = document.querySelector('.success-circle');
            if (successCircle) {
                successCircle.addEventListener('click', function() {
                    createConfettiBurst();
                });
            }

            document.querySelectorAll('.btn, .support-link').forEach(function(element) {
                element.style.pointerEvents = 'auto';
                element.style.zIndex = '100';
                element.style.position = 'relative';
            });

            setTimeout(function() {
                createConfettiBurst();
            }, 2000);
        });

        function createConfettiBurst() {
            const container = document.querySelector('.confetti-container');
            if (!container) return;

            for (let i = 0; i < 15; i++) {
                const confetti = document.createElement('div');
                confetti.className = 'confetti';
                confetti.style.left = Math.random() * 100 + '%';
                confetti.style.background = Math.random() > 0.5 ? '#28a745' : '#ffd700';
                confetti.style.animationDelay = Math.random() * 2 + 's';
                confetti.style.animationDuration = (Math.random() * 2 + 2) + 's';
                
                container.appendChild(confetti);
                
                setTimeout(() => {
                    if (confetti.parentNode) {
                        confetti.parentNode.removeChild(confetti);
                    }
                }, 4000);
            }
        }
    </script>
</body>
</html>