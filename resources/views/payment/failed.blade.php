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

                    @php
                        $err = session('error') ?? session('error_message') ?? request()->query('error_message');
                        $token = $token ?? request()->query('token') ?? request()->query('TBK_TOKEN');
                        $idSesion = $idSesion ?? request()->query('TBK_ID_SESION');
                        $ordenCompra = $ordenCompra ?? request()->query('TBK_ORDEN_COMPRA');
                        $tbkDebug = session('tbk_debug'); // array con responseCode/status/paymentType si viene del commit
                    @endphp
                    

                    <div class="error-content">
                        <h1 class="error-title">¡Oops! Algo salió mal</h1>
                        <h2 class="error-subtitle">El pago no pudo ser procesado</h2>
                        <p class="error-description">
                            No te preocupes, no se realizó ningún cargo a tu cuenta. 
                            Puedes intentar nuevamente o contactar con nuestro soporte.
                        </p>

                        @if($err)
                        <div class="error-details">
                            <div class="error-code">
                            <i class="fas fa-info-circle me-2"></i>{{ $err }}
                            </div>
                        </div>
                        @endif

                        @if($tbkDebug)
                        <div class="mt-3 small text-muted">
                            <div>responseCode: <code>{{ $tbkDebug['responseCode'] }}</code></div>
                            <div>status:       <code>{{ $tbkDebug['status'] }}</code></div>
                            <div>paymentType:  <code>{{ $tbkDebug['paymentType'] }}</code></div>
                        </div>
                        @endif


                        @if(isset($token) && $token)
                            <div class="mt-4">
                                <p class="text-muted small">Token de la transacción:</p>
                                <code class="text-danger">{{ $token }}</code>
                            </div>
                        @endif

                        @if(isset($idSesion))
                            <div class="mt-2">
                                <p class="text-muted small">ID de Sesión:</p>
                                <code class="text-primary">{{ $idSesion }}</code>
                            </div>
                        @endif

                        @if(isset($ordenCompra))
                            <div class="mt-2">
                                <p class="text-muted small">Orden de Compra:</p>
                                <code class="text-success">{{ $ordenCompra }}</code>
                            </div>
                        @endif


                        @if(session('tbk_debug'))
                            @php $d = session('tbk_debug'); @endphp
                            <div class="mt-3 small text-muted">
                                <div>responseCode: <code>{{ $d['responseCode'] }}</code></div>
                                <div>status:       <code>{{ $d['status'] }}</code></div>
                                <div>paymentType:  <code>{{ $d['paymentType'] }}</code></div>
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