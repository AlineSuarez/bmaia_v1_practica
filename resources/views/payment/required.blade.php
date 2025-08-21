@extends('layouts.app')

@section('title', 'B-MaiA - Planes de Suscripción')

@section('content')

<head>
    <link rel="stylesheet" href="{{ asset('css/components/home-user/payment/required.css') }}">
</head>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header Section -->
            <div class="text-center mb-5">
                <h1 class="display-4 fw-bold mb-3" style="color: #FF8C00;">Elige tu Plan Perfecto</h1>
                <p class="lead text-muted">Selecciona el plan que mejor se adapte a tus necesidades</p>
            </div>

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @php
                $user = auth()->user();
                $dronePayment = \App\Models\Payment::where('user_id', $user->id)
                ->where('plan', 'drone')
                ->orderByDesc('created_at')
                ->first();

                $droneActive = false;
                $daysRemaining = 0;
                if ($dronePayment) {
                $daysSincePayment = now()->diffInDays($dronePayment->created_at);
                $droneActive = $daysSincePayment < 16;
                $daysRemaining = intval(16 - $daysSincePayment);
                }

                $hasPaidPlan = \App\Models\Payment::where('user_id', $user->id)
                ->where('status', 'paid')
                ->whereIn('plan', ['afc', 'me', 'ge'])
                ->exists();
            @endphp

            <!-- Trial Section -->
            @if(!$dronePayment || $droneActive)
                @if(!$hasPaidPlan)
                    <div class="card border-0 shadow-lg mb-5 trial-card">
                        <div class="card-body p-4 text-white position-relative overflow-hidden">
                            <div class="trial-pattern"></div>
                            <div class="row align-items-center position-relative">
                                <div class="col-md-8">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="trial-icon-wrapper me-4">
                                            <i class="fas fa-rocket fs-2"></i>
                                        </div>
                                        <div>
                                            <h3 class="mb-1 fw-bold">¡Prueba Gratuita Disponible!</h3>
                                            <div class="trial-badge">
                                                <i class="fas fa-gift me-2"></i>16 días GRATIS
                                            </div>
                                        </div>
                                    </div>
                                    @if($droneActive)
                                        <div class="trial-status active">
                                            <i class="fas fa-clock me-2"></i>
                                            Tu prueba gratuita está activa, una vez que finalice, podrás elegir un plan de pago.
                                        </div>
                                        <div class="progress mt-2" style="height: 6px;">
                                            <div class="progress-bar bg-warning" role="progressbar" 
                                                    style="width: {{ ($daysRemaining / 16) * 100 }}%"></div>
                                        </div>
                                    @else
                                        <p class="mb-0 trial-description">
                                            <i class="fas fa-star me-2"></i>
                                            Accede a todas las funciones del plan Drone por 16 días completamente gratis
                                        </p>
                                    @endif
                                </div>
                                <div class="col-md-4 text-md-end">
                                    <form method="POST" action="{{ route('trial.start') }}">
                                        @csrf
                                        <button type="submit" class="btn btn-trial btn-lg px-4 fw-bold shadow"
                                                @if($droneActive) disabled @endif>
                                            @if($droneActive)
                                                <i class="fas fa-check-circle me-2"></i>Activa
                                            @else
                                                <i class="fas fa-play me-2"></i>Iniciar Prueba
                                            @endif
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            <!-- Plans Section -->
            <div class="text-center mb-4">
                <h2 class="fw-bold mb-2" style="color: #FF8C00;">Proceso de Suscripción</h2>
                <p class="text-muted">Elige cómo deseas realizar tu suscripción</p>
            </div>

            <div class="alert-info d-flex align-items-center justify-content-center w-auto mx-auto mb-3"
                style="font-size:1rem; gap: 1rem;">
                <div>
                    <i class="fas fa-info-circle" style="font-size:1.5rem;"></i>
                </div>
                <div>
                    <span>
                        <strong>Importante:</strong> Para revisar en detalle todos los planes de suscripción disponibles, así como
                        sus características y precios, dirígete a la pestaña <strong>“Plan”</strong> ubicada en "Mi Cuenta" → "Configuración de cuenta" de 
                        la plataforma. Desde allí podrás comparar las distintas opciones y seleccionar la que mejor se adapte a tus
                        necesidades.
                    </span>
                </div>
            </div>

            <!-- Payment Process Cards -->
            <div class="payment-process-section">
                <div class="row g-4">
                    <!-- Persona Natural Card -->
                    <div class="col-lg-6">
                        <div class="process-card natural-card">
                            <div class="process-header">
                                <div class="process-icon natural-icon">
                                    <i class="fas fa-user"></i>
                                </div>
                                <h3 class="process-title">Persona Natural</h3>
                                <p class="process-subtitle">Proceso simplificado para usuarios individuales</p>
                            </div>

                            <div class="process-body">
                                <div class="process-steps">
                                    <div class="step-item">
                                        <div class="step-number">1</div>
                                        <div class="step-content">
                                            <h4>Datos Personales</h4>
                                            <p>Completa tu información básica: nombre, email y teléfono</p>
                                        </div>
                                    </div>

                                    <div class="step-item">
                                        <div class="step-number">2</div>
                                        <div class="step-content">
                                            <h4>Seleccionar Plan (AFC, ME o GE)</h4>
                                            <p>Elige entre nuestros planes: AFC, ME o GE según tus necesidades</p>
                                        </div>
                                    </div>

                                    <div class="step-item">
                                        <div class="step-number">3</div>
                                        <div class="step-content">
                                            <h4>Método de Pago</h4>
                                            <p>Realiza el pago seguro mediante WebPay Plus</p>
                                        </div>
                                    </div>

                                    <div class="step-item">
                                        <div class="step-number">4</div>
                                        <div class="step-content">
                                            <h4>¡Listo!</h4>
                                            <p>Accede inmediatamente a tu plan seleccionado</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="process-benefits">
                                    <div class="benefit-tag">
                                        <i class="fas fa-clock"></i>
                                        <span>Proceso rápido</span>
                                    </div>
                                    <div class="benefit-tag">
                                        <i class="fas fa-shield-alt"></i>
                                        <span>100% Seguro</span>
                                    </div>
                                </div>
                            </div>

                            <div class="process-footer">
                                <button class="btn btn-process-natural w-100">
                                    <i class="fas fa-arrow-right me-2"></i>
                                    Continuar como Persona Natural
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Facturación Card -->
                    <div class="col-lg-6">
                        <div class="process-card business-card">
                            <div class="process-header">
                                <div class="process-icon business-icon">
                                    <i class="fas fa-building"></i>
                                </div>
                                <h3 class="process-title">Con Facturación</h3>
                                <p class="process-subtitle">Para empresas que requieren factura</p>
                            </div>

                            <div class="process-body">
                                <div class="process-steps">
                                    <div class="step-item">
                                        <div class="step-number">1</div>
                                        <div class="step-content">
                                            <h4>Datos de Facturación</h4>
                                            <p>RUT, razón social, dirección y giro comercial</p>
                                        </div>
                                    </div>

                                    <div class="step-item">
                                        <div class="step-number">2</div>
                                        <div class="step-content">
                                            <h4>Información de Contacto</h4>
                                            <p>Email corporativo y teléfono de la empresa</p>
                                        </div>
                                    </div>

                                    <div class="step-item">
                                        <div class="step-number">3</div>
                                        <div class="step-content">
                                            <h4>Seleccionar Plan (AFC, ME o GE)</h4>
                                            <p>Elige el plan empresarial que mejor se adapte</p>
                                        </div>
                                    </div>

                                    <div class="step-item">
                                        <div class="step-number">4</div>
                                        <div class="step-content">
                                            <h4>Pago y Factura</h4>
                                            <p>Realiza el pago y recibe tu factura electrónica</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="process-benefits">
                                    <div class="benefit-tag">
                                        <i class="fas fa-file-invoice"></i>
                                        <span>Factura incluida</span>
                                    </div>
                                    <div class="benefit-tag">
                                        <i class="fas fa-certificate"></i>
                                        <span>Válido para empresas</span>
                                    </div>
                                </div>
                            </div>

                            <div class="process-footer">
                                <button class="btn btn-process-business w-100">
                                    <i class="fas fa-arrow-right me-2"></i>
                                    Continuar con Facturación
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Info Section -->
                <div class="process-info-section mt-4">
                    <div class="info-card">
                        <div class="info-icon">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <div class="info-content">
                            <h4>¿Necesitas ayuda para decidir?</h4>
                            <p>Si eres una persona natural que realiza actividades comerciales de apicultura, puedes usar cualquiera de las dos opciones. La facturación es especialmente útil si necesitas el documento para fines contables o tributarios.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Trust Section -->
            <div class="trust-section mt-5">
                <div class="text-center mb-4">
                    <h3 class="fw-bold" style="color: #FF8C00;">¿Por qué elegir B-MaiA?</h3>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="trust-item">
                            <div class="trust-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <h5 class="trust-title">Pago Seguro</h5>
                            <p class="trust-description">Transacciones protegidas con SSL y WebPay Plus</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="trust-item">
                            <div class="trust-icon">
                                <i class="fas fa-headset"></i>
                            </div>
                            <h5 class="trust-title">Soporte 24/7</h5>
                            <p class="trust-description">Estamos aquí para ayudarte cuando lo necesites</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="trust-item">
                            <div class="trust-icon">
                                <i class="fas fa-sync-alt"></i>
                            </div>
                            <h5 class="trust-title">Actualizaciones</h5>
                            <p class="trust-description">Mejoras continuas incluidas en tu plan</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
            document.addEventListener('DOMContentLoaded', function () {
    // Botón Persona Natural
    const btnNatural = document.querySelector('.btn-process-natural');
            if (btnNatural) {
                btnNatural.addEventListener('click', function () {
                    window.location.href = "{{ route('user.settings') }}#user-data";
                });
    }

            // Botón Facturación
            const btnBusiness = document.querySelector('.btn-process-business');
            if (btnBusiness) {
                btnBusiness.addEventListener('click', function () {
                    window.location.href = "{{ route('user.settings') }}#billing";
                });
    }
});
</script>
@endsection