@extends('layouts.app')

@section('title', 'Maia - Pago Aprobado')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/components/home-user/settings.css') }}">
@endpush

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="settings-card">
                <div class="card-header text-center">
                    <div class="success-icon mb-3">
                        <i class="fas fa-check-circle" style="font-size: 4rem; color: var(--success-color);"></i>
                    </div>
                    <h3 class="text-success">Transacción Completada</h3>
                    <p>Tu pago se ha procesado correctamente</p>
                </div>
                
                <div class="card-body">
                    <div class="payment-details mb-4">
                        <h5 class="text-center mb-3" style="color: var(--primary-dark);">Detalles de la Transacción</h5>
                        <div class="row">
                            <div class="col-6">
                                <small class="text-muted">Estado:</small>
                                <p class="mb-2"><span class="plan-badge plan-premium">Aprobado</span></p>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Fecha:</small>
                                <p class="mb-2">{{ date('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="next-steps mb-4">
                        <h6 style="color: var(--primary-dark);">¿Qué sigue ahora?</h6>
                        <ul class="utility-features">
                            <li>Ya puedes acceder a todas las funciones premium</li>
                            <li>Revisa tu configuración de cuenta actualizada</li>
                            <li>Explora las nuevas herramientas disponibles</li>
                        </ul>
                    </div>

                    <div class="action-buttons text-center">
                        <a href="{{ url('/user/settings') }}" class="btn btn-primary me-2">
                            <i class="fas fa-cog me-1"></i>
                            Ir a Configuración
                        </a>
                        <a href="{{ url('/home') }}" class="btn btn-outline-primary">
                            <i class="fas fa-home me-1"></i>
                            Volver al Inicio
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animación de entrada para la tarjeta
    const card = document.querySelector('.settings-card');
    if (card) {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.6s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 200);
    }

    // Animación para el ícono de éxito
    const successIcon = document.querySelector('.success-icon i');
    if (successIcon) {
        setTimeout(() => {
            successIcon.style.animation = 'pulse 1.5s ease-in-out';
        }, 800);
    }
});
</script>

<style>
@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

.success-icon {
    animation: fadeInScale 0.8s ease-out;
}

@keyframes fadeInScale {
    0% {
        opacity: 0;
        transform: scale(0.5);
    }
    100% {
        opacity: 1;
        transform: scale(1);
    }
}

.payment-details {
    background-color: var(--lighter-bg);
    border-radius: 8px;
    padding: 1.25rem;
    border: 1px solid var(--border-color);
}

.next-steps {
    background-color: rgba(123, 168, 64, 0.05);
    border-radius: 8px;
    padding: 1.25rem;
    border-left: 4px solid var(--success-color);
}

.action-buttons .btn {
    min-width: 160px;
    margin-bottom: 0.5rem;
}

@media (max-width: 767px) {
    .action-buttons .btn {
        display: block;
        width: 100%;
        margin-bottom: 1rem;
    }
    
    .action-buttons .btn:last-child {
        margin-bottom: 0;
    }
    
    .success-icon i {
        font-size: 3rem !important;
    }
}
</style>
@endpush