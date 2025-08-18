<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>B-MaiA - Transacción Exitosa</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/components/home-user/payment/success.css') }}">
    <script src="https://unpkg.com/@lottiefiles/dotlottie-wc@0.6.2/dist/dotlottie-wc.js" type="module"></script>
</head>

<body>
    @php
        // Helpers locales por si no los cargaste vía autoload
        if (!function_exists('mask_middle')) {
            function mask_middle(string $value, int $keepStart = 2, int $keepEnd = 2, string $mask = '*'): string
            {
                $value = (string) $value;
                $len = mb_strlen($value);
                if ($len <= $keepStart + $keepEnd)
                    return str_repeat($mask, $len);
                return mb_substr($value, 0, $keepStart)
                    . str_repeat($mask, $len - $keepStart - $keepEnd)
                    . mb_substr($value, -$keepEnd);
            }
        }
        if (!function_exists('mask_email')) {
            function mask_email(string $email): string
            {
                if (!str_contains($email, '@'))
                    return mask_middle($email);
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

        $razon = $bs['razon_social'] ?? ($df->razon_social ?? '—');
        $rut = $bs['rut'] ?? ($df->rut ?? '—');
        $giro = $bs['giro'] ?? ($df->giro ?? '—');
        $dir = $bs['direccion_comercial'] ?? ($df->direccion_comercial ?? '—');
        $ciudad = $bs['ciudad'] ?? ($df->ciudad ?? '—');
        $telefono = $bs['telefono'] ?? ($df->telefono ?? '—');
        $correoDte = $bs['correo_envio_dte'] ?? ($df->correo_envio_dte ?? '—');
        $envioDte = isset($bs['autorizacion_envio_dte'])
            ? ($bs['autorizacion_envio_dte'] ? 'Sí' : 'No')
            : ($df?->autorizacion_envio_dte ? 'Sí' : 'No');
    @endphp

    <div class="payment-success-container">
        <!-- ===========================================
             SECCIÓN IZQUIERDA - ANIMACIÓN Y TÍTULO
             =========================================== -->
        <div class="left-section">
            <div class="success-animation-wrapper">
                <dotlottie-wc src="https://lottie.host/7f5b81f9-883d-4b2c-a295-eebfc5ea8690/QuFBgjcEi0.lottie"
                    class="success-lottie" speed="1" autoplay>
                </dotlottie-wc>
            </div>

            <div class="success-content">
                <h1 class="success-title">Transacción Exitosa</h1>
                <p class="success-subtitle">Su pago ha sido procesado correctamente y está disponible en su cuenta</p>
                <div class="success-badge">
                    <i class="fas fa-check-circle"></i>
                    <span>Pago Confirmado</span>
                </div>
            </div>
        </div>

        <!-- ===========================================
             SECCIÓN DERECHA - DETALLES DE LA TRANSACCIÓN
             =========================================== -->
        <div class="right-section">
            <div class="details-container">
                <!-- Encabezado de Detalles -->
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <h2 class="section-title">Detalles de la Transacción</h2>
                </div>

                <!-- Grid de Detalles de Pago -->
                <div class="details-grid">
                    <div class="detail-item">
                        <label class="detail-label">Fecha de Transacción</label>
                        <span class="detail-value">{{ optional($payment->created_at)->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="detail-item">
                        <label class="detail-label">Plan Contratado</label>
                        <span class="detail-value plan-badge">{{ strtoupper($payment->plan ?? '—') }}</span>
                    </div>
                    <div class="detail-item">
                        <label class="detail-label">Monto Total</label>
                        <span
                            class="detail-value amount">${{ number_format((int) ($payment->amount ?? 0), 0, ',', '.') }}</span>
                    </div>
                    <div class="detail-item">
                        <label class="detail-label">Fecha de Vencimiento</label>
                        <span class="detail-value">
                            {{ optional($payment->expires_at ?? ($payment->user->fecha_vencimiento ?? null))->format('d/m/Y') ?? '—' }}
                        </span>
                    </div>
                    <div class="detail-item" style="grid-column: 1 / -1;">
                        @php
                            // URLs para ver/descargar facturas
                            $verUrl = null;
                            $descargarUrl = null;
                            if (!empty($payment?->factura?->id)) {
                                $verUrl = route('facturas.ver', $payment->factura);
                                $descargarUrl = route('facturas.descargar', $payment->factura);
                            } elseif (!empty($facturaUrl)) {
                                $verUrl = $facturaUrl;
                                $descargarUrl = $facturaUrl;
                            }
                        @endphp

                        <label class="detail-label">Documentos</label>
                        <div class="invoice-actions">
                            @if($verUrl)
                                <a href="{{ $verUrl }}" class="action-link" target="_blank" title="Ver Factura">
                                    <i class="fas fa-eye"></i>
                                    <span>Ver Factura</span>
                                </a>
                            @endif

                            @if($descargarUrl)
                                <a href="{{ $descargarUrl }}" class="action-link download"
                                    @if(empty($payment?->factura?->id)) download @endif title="Descargar Factura">
                                    <i class="fas fa-download"></i>
                                    <span>Descargar PDF</span>
                                </a>
                            @else
                                <span class="no-document">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Sin documento disponible
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- ===========================================
                     INFORMACIÓN DE FACTURACIÓN (ACORDEÓN)
                     =========================================== -->
                <div class="billing-section">
                    <div class="accordion-trigger" onclick="toggleBillingAccordion()">
                        <div class="accordion-title">
                            <div class="accordion-icon">
                                <i class="fas fa-building"></i>
                            </div>
                            <span>Información de Facturación Utilizada</span>
                        </div>
                        <i class="fas fa-chevron-down accordion-arrow" id="billingArrow"></i>
                    </div>
                    <div class="accordion-content" id="billingAccordionContent">
                        <div class="billing-info-grid">
                            <div class="billing-column">
                                <div class="billing-field">
                                    <label class="field-label">Razón Social</label>
                                    <span class="field-value">{{ $razon }}</span>
                                </div>
                                <div class="billing-field">
                                    <label class="field-label">RUT</label>
                                    <span
                                        class="field-value masked">{{ $rut !== '—' ? mask_middle($rut, 4, 2) : '—' }}</span>
                                </div>
                                <div class="billing-field">
                                    <label class="field-label">Giro Comercial</label>
                                    <span class="field-value">{{ $giro }}</span>
                                </div>
                                <div class="billing-field">
                                    <label class="field-label">Ciudad</label>
                                    <span class="field-value">{{ $ciudad }}</span>
                                </div>
                            </div>
                            <div class="billing-column">
                                <div class="billing-field">
                                    <label class="field-label">Dirección Comercial</label>
                                    <span class="field-value masked">{{ $dir ? '• • •' : '—' }}</span>
                                </div>
                                <div class="billing-field">
                                    <label class="field-label">Teléfono</label>
                                    <span
                                        class="field-value masked">{{ $telefono !== '—' ? mask_middle($telefono, 2, 2) : '—' }}</span>
                                </div>
                                <div class="billing-field">
                                    <label class="field-label">Correo Electrónico DTE</label>
                                    <span
                                        class="field-value masked">{{ $correoDte !== '—' ? mask_email($correoDte) : '—' }}</span>
                                </div>
                                <div class="billing-field">
                                    <label class="field-label">Autorización Envío DTE</label>
                                    <span
                                        class="field-value authorization {{ strtolower($envioDte) === 'sí' ? 'authorized' : 'not-authorized' }}">
                                        <i
                                            class="fas {{ strtolower($envioDte) === 'sí' ? 'fa-check' : 'fa-times' }}"></i>
                                        {{ $envioDte }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="billing-disclaimer">
                            <p>
                                <strong>Nota:</strong> Esta información corresponde a los datos registrados al momento
                                del pago.
                                Para actualizar sus datos de facturación, visite
                                <a href="{{ route('user.settings') }}#billing" class="settings-link">Configuración de
                                    Cuenta</a>.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- ===========================================
                     PRÓXIMOS PASOS
                     =========================================== -->
                <div class="next-steps-section">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="fas fa-list-check"></i>
                        </div>
                        <h2 class="section-title">Próximos Pasos</h2>
                    </div>

                    <ul class="steps-list">
                        <li class="step-item">
                            <div class="step-icon">
                                <i class="fas fa-unlock"></i>
                            </div>
                            <span>Acceso completo a todas las funcionalidades premium habilitado</span>
                        </li>
                        <li class="step-item">
                            <div class="step-icon">
                                <i class="fas fa-user-cog"></i>
                            </div>
                            <span>Revise y configure sus preferencias de cuenta</span>
                        </li>
                        <li class="step-item">
                            <div class="step-icon">
                                <i class="fas fa-rocket"></i>
                            </div>
                            <span>Explore las nuevas herramientas y características disponibles</span>
                        </li>
                    </ul>
                </div>

                <!-- ===========================================
                     BOTONES DE ACCIÓN FINALES
                     =========================================== -->
                <div class="action-buttons">
                    <a href="{{ route('user.settings') }}" class="btn btn-primary">
                        <i class="fas fa-cog"></i>
                        <span>Configuración de Cuenta</span>
                    </a>
                    <a href="{{ route('home') }}" class="btn btn-secondary">
                        <i class="fas fa-home"></i>
                        <span>Ir al Dashboard</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleBillingAccordion() {
            const content = document.getElementById('billingAccordionContent');
            const arrow = document.getElementById('billingArrow');

            if (content.classList.contains('active')) {
                content.classList.remove('active');
                arrow.style.transform = 'rotate(0deg)';
            } else {
                content.classList.add('active');
                arrow.style.transform = 'rotate(180deg)';
            }
        }
    </script>
</body>

</html>