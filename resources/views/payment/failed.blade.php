<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>B-MaiA - Pago Fallido</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/components/home-user/payment/failed.css') }}">
</head>
<body>
<div class="payment-failed-container">
    <div class="container-fluid h-100">
        <div class="row h-100 justify-content-center align-items-center">
            <div class="col-lg-6 col-md-8 col-sm-10 text-center">

                <div class="error-animation-wrapper mb-4">
                    <div class="error-circle"><div class="error-icon"><i class="fas fa-times"></i></div></div>
                    <div class="pulse-ring"></div>
                    <div class="pulse-ring-2"></div>
                    <div class="pulse-ring-3"></div>
                </div>

                <div class="error-content">
                    <h1 class="error-title">No pudimos procesar tu pago</h1>
                    <h2 class="error-subtitle">La transacción fue rechazada o cancelada</h2>
                    <p class="error-description">
                        No se realizó ningún cargo. Puedes intentarlo nuevamente o hablar con nuestro equipo de soporte.
                    </p>

                    @if(!empty($errorMessage))
                        <div class="alert alert-warning mt-3" role="alert">
                            <i class="fas fa-info-circle me-2"></i>{{ $errorMessage }}
                        </div>
                    @endif

                    <div class="action-buttons mt-4">
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-home me-2"></i>Volver al inicio
                        </a>
                        
                    </div>

                    <div class="help-link mt-4">
                        <p class="mb-2">¿Necesitas ayuda?</p>
                        <a href="{{ route('contacto.form') }}"  class="support-link">
                            <i class="fas fa-headset me-2"></i>Contactar soporte
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
    }, 15000);
});
</script>
</body>
</html>
