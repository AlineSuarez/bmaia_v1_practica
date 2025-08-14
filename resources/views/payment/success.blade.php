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
@php
    // Helpers locales por si no los cargaste vía autoload
    if (!function_exists('mask_middle')) {
        function mask_middle(string $value, int $keepStart = 2, int $keepEnd = 2, string $mask = '*'): string {
            $value = (string) $value;
            $len = mb_strlen($value);
            if ($len <= $keepStart + $keepEnd) return str_repeat($mask, $len);
            return mb_substr($value, 0, $keepStart)
                . str_repeat($mask, $len - $keepStart - $keepEnd)
                . mb_substr($value, -$keepEnd);
        }
    }
    if (!function_exists('mask_email')) {
        function mask_email(string $email): string {
            if (!str_contains($email, '@')) return mask_middle($email);
            [$user, $domain] = explode('@', $email, 2);
            return mask_middle($user, 1, 1) . '@' . $domain;
        }
    }

    // Fallback si por accidente no envían $payment desde el controlador
    if (!isset($payment)) {
        $payment = \App\Models\Payment::where('user_id', \Illuminate\Support\Facades\Auth::id())
            ->where('status', 'paid')
            ->latest()
            ->with('datosFacturacion')
            ->first();
    }

    $tokenShort = $payment?->transaction_id
        ? substr($payment->transaction_id, 0, 4) . '…' . substr($payment->transaction_id, -6)
        : '—';

    $bs = $payment?->billing_snapshot ?? null;
    $df = $payment?->datosFacturacion ?? null;

    $razon       = $bs['razon_social']        ?? ($df->razon_social        ?? '—');
    $rut         = $bs['rut']                  ?? ($df->rut                  ?? '—');
    $giro        = $bs['giro']                 ?? ($df->giro                 ?? '—');
    $dir         = $bs['direccion_comercial']  ?? ($df->direccion_comercial  ?? '—');
    $ciudad      = $bs['ciudad']               ?? ($df->ciudad               ?? '—');
    $telefono    = $bs['telefono']             ?? ($df->telefono             ?? '—');
    $correoDte   = $bs['correo_envio_dte']     ?? ($df->correo_envio_dte     ?? '—');
    $envioDte    = isset($bs['autorizacion_envio_dte'])
                    ? ($bs['autorizacion_envio_dte'] ? 'Sí' : 'No')
                    : ($df?->autorizacion_envio_dte ? 'Sí' : 'No');
@endphp

<div class="payment-success-container py-5">
    <div class="container">
        <!-- Encabezado -->
        <div class="text-center mb-4">
            <div class="success-animation-wrapper mb-3">
                <div class="success-circle"><div class="success-icon"><i class="fas fa-check"></i></div></div>
                <div class="success-ring"></div>
                <div class="success-ring-2"></div>
                <div class="success-ring-3"></div>
            </div>
            <h1 class="success-title mb-1">¡Pago Exitoso!</h1>
            <p class="text-muted mb-0">Tu transacción se ha completado correctamente.</p>
        </div>

        <!-- Detalle compacto -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light"><strong><i class="fas fa-receipt me-2"></i>Detalle de la transacción</strong></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <small class="text-muted d-block">Fecha</small>
                        <strong>{{ optional($payment->created_at)->format('d/m/Y H:i') }}</strong>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted d-block">Plan</small>
                        <strong>{{ strtoupper($payment->plan ?? '—') }}</strong>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted d-block">Total (incl. IVA)</small>
                        <strong>${{ number_format((int)($payment->amount ?? 0), 0, ',', '.') }}</strong>
                    </div>

                    <div class="col-md-3">
                        <small class="text-muted d-block">Vence</small>
                        <strong>
                            {{ optional($payment->expires_at ?? ($payment->user->fecha_vencimiento ?? null))->format('d/m/Y') ?? '—' }}
                        </strong>
                        
                    </div>
                    <div class="col-md-3">
                        @php
                            // URLs para ver/descargar
                            $verUrl = null; $descargarUrl = null;
                            if(!empty($payment?->factura?->id)) {
                                $verUrl        = route('facturas.ver',        $payment->factura);
                                $descargarUrl  = route('facturas.descargar',  $payment->factura);
                            } elseif(!empty($facturaUrl)) {
                                $verUrl = $facturaUrl;
                                $descargarUrl = $facturaUrl; // con atributo download
                            }
                        @endphp

                        @if($verUrl)
                            <a href="{{ $verUrl }}" class="text-decoration-none" target="_blank"
                            data-bs-toggle="tooltip" title="Ver PDF en otra pestaña">
                            <i class="fas fa-file-pdf"></i>
                            </a>
                        @endif

                        @if($descargarUrl)
                            <a href="{{ $descargarUrl }}" class="text-decoration-none"
                            @if(empty($payment?->factura?->id)) download @endif
                            data-bs-toggle="tooltip" title="Descargar PDF">
                            <i class="fas fa-download"></i>
                            </a>
                        @else
                            <span class="badge bg-secondary">Sin PDF</span>
                        @endif
                    </div>                  
                    
                </div>
                <div class="d-flex align-items-center gap-2 mt-3">
                    <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Aprobado</span>
                    <small class="text-muted">Gracias por tu compra.</small>
                </div>
            </div>
        </div>

        <!-- Acordeón: datos de facturación enmascarados -->
        <div class="accordion mb-4" id="billingAccordion">
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingBill">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBill">
                        Ver datos de facturación usados (enmascarados)
                    </button>
                </h2>
                <div id="collapseBill" class="accordion-collapse collapse" data-bs-parent="#billingAccordion">
                    <div class="accordion-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><small class="text-muted">Razón social</small><br><strong>{{ $razon }}</strong></p>
                                <p class="mb-1"><small class="text-muted">RUT</small><br><span>{{ $rut !== '—' ? mask_middle($rut, 4, 2) : '—' }}</span></p>
                                <p class="mb-1"><small class="text-muted">Giro</small><br><span>{{ $giro }}</span></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><small class="text-muted">Dirección comercial</small><br><span>{{ $dir ? '• • •' : '—' }}</span></p>
                                <p class="mb-1"><small class="text-muted">Ciudad</small><br><span>{{ $ciudad }}</span></p>
                                <p class="mb-1"><small class="text-muted">Teléfono</small><br><span>{{ $telefono !== '—' ? mask_middle($telefono, 2, 2) : '—' }}</span></p>
                                <p class="mb-1"><small class="text-muted">Correo envío DTE</small><br><span>{{ $correoDte !== '—' ? mask_email($correoDte) : '—' }}</span></p>
                                <p class="mb-0"><small class="text-muted">Autorización envío DTE</small><br><span>{{ $envioDte }}</span></p>
                            </div>
                        </div>
                        <small class="text-muted d-block mt-3">
                            *Este es un snapshot de tus datos al momento del pago. Para modificarlos, ve a
                            <a href="{{ route('user.settings') }}#billing">Configuración &gt; Datos de facturación</a>.
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Qué sigue -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-light"><strong><i class="fas fa-list-check me-2"></i>¿Qué sigue ahora?</strong></div>
            <div class="card-body">
                <ul class="mb-0">
                    <li>Ya tienes acceso a todas las funciones premium.</li>
                    <li>Revisa tu configuración de cuenta actualizada.</li>
                    <li>Explora las nuevas herramientas disponibles.</li>
                </ul>
            </div>
        </div>

        <!-- Acciones -->
        <div class="d-flex gap-2 justify-content-center">
            <a href="{{ route('user.settings') }}" class="btn btn-primary btn-lg">
                <i class="fas fa-cog me-2"></i>Ir a Configuración
            </a>
            <a href="{{ route('home') }}" class="btn btn-outline-success btn-lg">
                <i class="fas fa-home me-2"></i>Volver al Inicio
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const successCircle = document.querySelector('.success-circle');
    if (successCircle) {
        successCircle.addEventListener('click', createConfettiBurst);
        setTimeout(createConfettiBurst, 1200);
    }
    function createConfettiBurst() {
        const container = document.querySelector('.payment-success-container');
        if (!container) return;
        for (let i = 0; i < 14; i++) {
            const c = document.createElement('div');
            c.className = 'confetti';
            c.style.left = Math.random() * 100 + '%';
            c.style.animationDelay = Math.random() * 1.2 + 's';
            c.style.animationDuration = (Math.random() * 1.2 + 1.6) + 's';
            container.appendChild(c);
            setTimeout(() => c.remove(), 2600);
        }
    }
});
</script>
</body>
</html>