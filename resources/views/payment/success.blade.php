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
                <div class="col-lg-8 col-md-10 col-sm-12">

                    <div class="text-center mb-4">
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

                        <h1 class="success-title">¡Pago Exitoso!</h1>
                        <h2 class="success-subtitle">Tu transacción se ha completado correctamente</h2>
                        <p class="success-description">
                            ¡Felicidades! Ahora tienes acceso completo a todas las funciones premium de B‑MaiA.
                        </p>
                    </div>

                    {{-- Detalle de la transacción --}}
                    <div class="card shadow-sm mb-4">
                        <div class="card-header d-flex align-items-center">
                            <i class="fas fa-receipt me-2"></i>
                            <strong>Detalle de la transacción</strong>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="detail-card p-3 border rounded h-100">
                                        <div class="detail-icon mb-2">
                                            <i class="fas fa-calendar-check"></i>
                                        </div>
                                        <small class="text-muted d-block">Fecha de transacción</small>
                                        <strong>{{ date('d/m/Y H:i') }}</strong>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="detail-card p-3 border rounded h-100">
                                        <div class="detail-icon mb-2">
                                            <i class="fas fa-tag"></i>
                                        </div>
                                        <small class="text-muted d-block">Plan</small>
                                        <strong>
                                            @if(isset($payment))
                                                {{ strtoupper($payment->plan) }}
                                            @else
                                                —
                                            @endif
                                        </strong>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="detail-card p-3 border rounded h-100">
                                        <div class="detail-icon mb-2">
                                            <i class="fas fa-dollar-sign"></i>
                                        </div>
                                        <small class="text-muted d-block">Monto</small>
                                        <strong>
                                            @if(isset($payment))
                                                ${{ number_format($payment->amount ?? 0, 0, ',', '.') }} + IVA
                                            @else
                                                —
                                            @endif
                                        </strong>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="detail-card p-3 border rounded">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            <small class="text-muted me-2">Estado</small>
                                            <strong class="text-success">Aprobado</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Datos de facturación utilizados en el pago --}}
                    <div class="card shadow-sm mb-4">
                        <div class="card-header d-flex align-items-center">
                            <i class="fas fa-building me-2"></i>
                            <strong>Datos de facturación utilizados</strong>
                        </div>

                        @php
                            $bs = isset($payment) ? ($payment->billing_snapshot ?? null) : null;
                            $df = isset($payment) ? ($payment->datosFacturacion ?? null) : null;
                        @endphp

                        <div class="card-body">
                            @if(isset($payment))
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-1"><small class="text-muted">Razón social</small><br>
                                            <strong>{{ $bs['razon_social'] ?? ($df->razon_social ?? '—') }}</strong>
                                        </p>
                                        <p class="mb-1"><small class="text-muted">RUT</small><br>
                                            <strong>{{ $bs['rut'] ?? ($df->rut ?? '—') }}</strong>
                                        </p>
                                        <p class="mb-1"><small class="text-muted">Giro</small><br>
                                            <span>{{ $bs['giro'] ?? ($df->giro ?? '—') }}</span>
                                        </p>
                                        <p class="mb-1"><small class="text-muted">Correo envío DTE</small><br>
                                            <span>{{ $bs['correo_envio_dte'] ?? ($df->correo_envio_dte ?? '—') }}</span>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1"><small class="text-muted">Dirección comercial</small><br>
                                            <span>{{ $bs['direccion_comercial'] ?? ($df->direccion_comercial ?? '—') }}</span>
                                        </p>
                                        <p class="mb-1"><small class="text-muted">Ciudad</small><br>
                                            <span>{{ $bs['ciudad'] ?? ($df->ciudad ?? '—') }}</span>
                                        </p>
                                        <p class="mb-1"><small class="text-muted">Teléfono</small><br>
                                            <span>{{ $bs['telefono'] ?? ($df->telefono ?? '—') }}</span>
                                        </p>
                                        <p class="mb-1"><small class="text-muted">Autorización envío DTE</small><br>
                                            @php
                                                $authDte = $bs['autorizacion_envio_dte'] ?? ($df->autorizacion_envio_dte ?? null);
                                            @endphp
                                            <span class="{{ $authDte ? 'text-success' : 'text-muted' }}">
                                                {{ $authDte ? 'Sí' : 'No' }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                <small class="text-muted d-block mt-2">
                                    *Estos son los datos que se usaron al momento del pago (snapshot). Si luego editas tu información, este comprobante no cambia.
                                </small>
                            @else
                                <div class="alert alert-info mb-0">
                                    No encontramos los detalles del pago en esta vista. Si recargaste o entraste directo,
                                    vuelve desde el flujo de pago o revisa tu historial.
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Siguientes pasos --}}
                    <div class="card shadow-sm">
                        <div class="card-header d-flex align-items-center">
                            <i class="fas fa-shoe-prints me-2"></i>
                            <strong>¿Qué sigue ahora?</strong>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <i class="fas fa-star me-2"></i> Ya puedes acceder a todas las funciones premium.
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-cog me-2"></i> Revisa tu configuración de cuenta actualizada.
                                </li>
                                <li class="mb-0">
                                    <i class="fas fa-rocket me-2"></i> Explora las nuevas herramientas disponibles.
                                </li>
                            </ul>

                            <div class="mt-4 d-flex flex-wrap gap-2">
                                <a href="{{ route('user.settings') }}" class="btn btn-primary">
                                    <i class="fas fa-cog me-2"></i> Ir a Configuración
                                </a>
                                <a href="{{ route('home') }}" class="btn btn-outline-success">
                                    <i class="fas fa-home me-2"></i> Volver al Inicio
                                </a>
                            </div>
                        </div>
                    </div>

                </div> <!-- /col -->
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
            setTimeout(createConfettiBurst, 1200);
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
                setTimeout(() => confetti.remove(), 4000);
            }
        }
    </script>
</body>
</html>
