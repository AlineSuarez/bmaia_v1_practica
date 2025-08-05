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
                                            Tu prueba gratuita está activa - <strong>{{ $daysRemaining }} días restantes</strong>
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
                                        <button type="submit" 
                                                class="btn btn-trial btn-lg px-4 fw-bold shadow"
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
                <h2 class="fw-bold mb-2" style="color: #FF8C00;">Planes de Suscripción</h2>
                <p class="text-muted">Elige el plan que mejor se adapte a tu operación apícola</p>
            </div>

            <div class="row g-4">
                <!-- Plan AFC -->
                <div class="col-lg-4 col-md-6">
                    <div class="card plan-card h-100 border-0 shadow-sm">
                        <div class="plan-header afc-header">
                            <div class="plan-icon">
                                <i class="fas fa-cogs"></i>
                            </div>
                            <h4 class="plan-title">WorkerBee AFC</h4>
                        </div>
                        <div class="card-body p-4">
                            <div class="plan-features">
                                <div class="feature-item">
                                    <i class="fas fa-check feature-check"></i>
                                    <span>1 Usuario Administrador</span>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-check feature-check"></i>
                                    <span>Hasta 299 colmenas</span>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-check feature-check"></i>
                                    <span>Funcionalidades básicas</span>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-check feature-check"></i>
                                    <span>Soporte técnico estándar</span>
                                </div>
                            </div>
                            <div class="plan-pricing">
                                <div class="price-main">$69.900 + IVA</div>
                                <div class="price-details">
                                    <small>Mensual: $5.825 + IVA</small>
                                    <small>Por colmena: $234/año</small>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0 p-4">
                            <form method="POST" action="{{ route('payment.initiate') }}">
                                @csrf
                                <button name="plan" value="afc" class="btn btn-plan-primary w-100 fw-bold">
                                    <i class="fas fa-credit-card me-2"></i>Seleccionar AFC
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Plan ME -->
                <div class="col-lg-4 col-md-6">
                    <div class="card plan-card featured h-100 border-0 shadow-lg position-relative">
                        <div class="plan-header me-header">
                            <div class="plan-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <h4 class="plan-title">WorkerBee ME</h4>
                        </div>
                        <div class="card-body p-4">
                            <div class="plan-features">
                                <div class="feature-item">
                                    <i class="fas fa-check feature-check"></i>
                                    <span>Todo de AFC incluido</span>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-check feature-check"></i>
                                    <span>Hasta 799 colmenas</span>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-check feature-check"></i>
                                    <span>Analytics avanzados</span>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-check feature-check"></i>
                                    <span>Soporte prioritario</span>
                                </div>
                            </div>
                            <div class="plan-pricing">
                                <div class="price-main">$87.900 + IVA</div>
                                <div class="price-details">
                                    <small>Mensual: $7.325 + IVA</small>
                                    <small>Por colmena: $110/año</small>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0 p-4">
                            <form method="POST" action="{{ route('payment.initiate') }}">
                                @csrf
                                <button name="plan" value="me" class="btn btn-plan-featured w-100 fw-bold">
                                    <i class="fas fa-credit-card me-2"></i>Seleccionar ME
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Plan GE -->
                <div class="col-lg-4 col-md-6">
                    <div class="card plan-card h-100 border-0 shadow-sm">
                        <div class="plan-header ge-header">
                            <div class="plan-icon">
                                <i class="fas fa-crown"></i>
                            </div>
                            <h4 class="plan-title">WorkerBee GE</h4>
                        </div>
                        <div class="card-body p-4">
                            <div class="plan-features">
                                <div class="feature-item">
                                    <i class="fas fa-check feature-check"></i>
                                    <span>Todo de ME incluido</span>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-check feature-check"></i>
                                    <span>Colmenas ilimitadas</span>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-check feature-check"></i>
                                    <span>Soporte 24/7 dedicado</span>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-check feature-check"></i>
                                    <span>Personalización completa</span>
                                </div>
                            </div>
                            <div class="plan-pricing">
                                <div class="price-main">$150.900 + IVA</div>
                                <div class="price-details">
                                    <small>Mensual: $12.575 + IVA</small>
                                    <small>Por colmena: $86/año</small>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0 p-4">
                            <form method="POST" action="{{ route('payment.initiate') }}">
                                @csrf
                                <button name="plan" value="ge" class="btn btn-plan-premium w-100 fw-bold">
                                    <i class="fas fa-credit-card me-2"></i>Seleccionar GE
                                </button>
                            </form>
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

<style>

</style>
@endsection