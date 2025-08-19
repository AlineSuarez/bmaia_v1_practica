@extends('layouts.app')

@section('title', 'Factura ' . ($factura->numero_mostrar ?? 'Detalle'))

@section('content')
    <link href="{{ asset('./css/components/home-user/settings.css') }}" rel="stylesheet">
    <link href="{{ asset('./css/components/home-user/facturacion/facturacion.css') }}" rel="stylesheet">

    <div class="container">
        {{-- Header con navegación --}}
        <div class="invoice-detail-header">
            <div class="header-actions">
                <a href="{{ route('user.settings') }}#billing" class="btn-back" aria-label="Volver al listado">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z" />
                    </svg>
                    Volver
                </a>

                <div class="header-buttons">
                    @if($pdfUrl || $factura->pdf_path)
                        <a href="{{ route('facturas.ver', $factura) }}" class="btn-primary" target="_blank" rel="noopener">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z" />
                            </svg>
                            Abrir PDF
                        </a>
                    @endif
                    @if($xmlUrl)
                        <a href="{{ $xmlUrl }}" class="btn-secondary" target="_blank" rel="noopener">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M5,3H7V5H5V10A2,2 0 0,1 3,8V6A2,2 0 0,1 5,4V3M19,3V4A2,2 0 0,1 21,6V8A2,2 0 0,1 19,10V5H17V3H19M5,21H7V19H5V14A2,2 0 0,1 3,16V18A2,2 0 0,1 5,20V21M19,21V20A2,2 0 0,1 21,18V16A2,2 0 0,1 19,14V19H17V21H19Z" />
                            </svg>
                            XML
                        </a>
                    @endif
                </div>
            </div>

            <div class="invoice-title">
                <h1>Factura {{ $factura->numero_mostrar }}</h1>
                <div class="invoice-status {{ strtolower($factura->estado) }}">
                    {{ ucfirst($factura->estado) }}
                </div>
            </div>
        </div>

        {{-- Información principal --}}
        <div class="invoice-main-info">
            <div class="info-grid">
                <div class="info-card">
                    <div class="card-header">
                        <div class="card-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z" />
                            </svg>
                        </div>
                        <h3>Información de Factura</h3>
                    </div>
                    <div class="card-content">
                        <div class="info-items">
                            <div class="info-item">
                                <span class="label">Emisión:</span>
                                <span class="value">{{ $factura->fecha_emision?->format('d/m/Y H:i') ?? '—' }}</span>
                            </div>
                            <div class="info-item">
                                <span class="label">Vencimiento:</span>
                                <span class="value">{{ $factura->fecha_vencimiento?->format('d/m/Y') ?? '—' }}</span>
                            </div>
                            <div class="info-item">
                                <span class="label">Plan:</span>
                                <span class="value plan-name">{{ strtoupper($factura->plan ?? '—') }}</span>
                            </div>
                            @if($vencePlan)
                                <div class="info-item">
                                    <span class="label">Vence plan:</span>
                                    <span class="value">{{ $vencePlan->format('d/m/Y') }}</span>
                                </div>
                            @endif
                            @if($factura->sii_track_id)
                                <div class="info-item">
                                    <span class="label">Track ID:</span>
                                    <span class="value monospace">{{ $factura->sii_track_id }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="info-card totals-card">
                    <div class="card-header">
                        <div class="card-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M7,15H9C9,16.08 10.37,17 12,17C13.63,17 15,16.08 15,15C15,13.9 13.96,13.5 11.76,12.97C9.64,12.44 7,11.78 7,9C7,7.21 8.47,5.69 10.5,5.18V3H13.5V5.18C15.53,5.69 17,7.21 17,9H15C15,7.92 13.63,7 12,7C10.37,7 9,7.92 9,9C9,10.1 10.04,10.5 12.24,11.03C14.36,11.56 17,12.22 17,15C17,16.79 15.53,18.31 13.5,18.82V21H10.5V18.82C8.47,18.31 7,16.79 7,15Z" />
                            </svg>
                        </div>
                        <h3>Totales</h3>
                    </div>
                    <div class="card-content">
                        <div class="totals-breakdown">
                            <div class="total-item">
                                <span class="label">Neto:</span>
                                <span class="amount">${{ number_format((int) $factura->monto_neto, 0, ',', '.') }}</span>
                            </div>
                            <div class="total-item">
                                <span class="label">IVA ({{ $factura->porcentaje_iva ?? 19 }}%):</span>
                                <span class="amount">${{ number_format((int) $factura->monto_iva, 0, ',', '.') }}</span>
                            </div>
                            <div class="total-item total-final">
                                <span class="label">Total:</span>
                                <span class="amount">${{ number_format((int) $factura->monto_total, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Datos del receptor --}}
        <div class="invoice-receptor">
            <div class="receptor-header">
                <div class="section-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z" />
                    </svg>
                </div>
                <h2>Datos del Receptor</h2>
            </div>

            <div class="receptor-details">
                <div class="detail-group">
                    <div class="group-header">
                        <div class="group-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M22,3H2C0.91,3.04 0.04,3.91 0,5V19C0.04,20.09 0.91,20.96 2,21H22C23.09,20.96 23.96,20.09 24,19V5C23.96,3.91 23.09,3.04 22,3M22,19H2V5H22V19M14,17V15.75C14,14.09 10.66,13.25 9,13.25C7.34,13.25 4,14.09 4,15.75V17H14M9,7A2.5,2.5 0 0,0 6.5,9.5A2.5,2.5 0 0,0 9,12A2.5,2.5 0 0,0 11.5,9.5A2.5,2.5 0 0,0 9,7M14,7V8H20V7H14M14,9V10H20V9H14M14,11V12H18V11H14" />
                            </svg>
                        </div>
                        <h4>Identificación</h4>
                    </div>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <span class="label">RUT:</span>
                            <span class="value">{{ $snap['rut'] ?? '—' }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="label">Razón Social:</span>
                            <span class="value">{{ $snap['razon_social'] ?? '—' }}</span>
                        </div>
                        <div class="detail-item full-width">
                            <span class="label">Giro:</span>
                            <span class="value">{{ $snap['giro'] ?? '—' }}</span>
                        </div>
                    </div>
                </div>

                <div class="detail-group">
                    <div class="group-header">
                        <div class="group-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M12,11.5A2.5,2.5 0 0,1 9.5,9A2.5,2.5 0 0,1 12,6.5A2.5,2.5 0 0,1 14.5,9A2.5,2.5 0 0,1 12,11.5M12,2A7,7 0 0,0 5,9C5,14.25 12,22 12,22C12,22 19,14.25 19,9A7,7 0 0,0 12,2Z" />
                            </svg>
                        </div>
                        <h4>Ubicación</h4>
                    </div>
                    <div class="detail-grid">
                        <div class="detail-item full-width">
                            <span class="label">Dirección:</span>
                            <span class="value">{{ $snap['direccion_comercial'] ?? '—' }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="label">Ciudad:</span>
                            <span class="value">{{ $snap['ciudad'] ?? '—' }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="label">Región:</span>
                            <span class="value">{{ $snap['region'] ?? ($snap['region_nombre'] ?? '—') }}</span>
                        </div>
                    </div>
                </div>

                <div class="detail-group">
                    <div class="group-header">
                        <div class="group-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M20,15.5C18.8,15.5 17.5,15.3 16.4,14.9C16.3,14.9 16.2,14.9 16.1,14.9C15.8,14.9 15.6,15 15.4,15.2L13.2,17.4C10.4,15.9 8,13.6 6.6,10.8L8.8,8.6C9.1,8.3 9.2,7.9 9,7.6C8.7,6.5 8.5,5.2 8.5,4C8.5,3.5 8,3 7.5,3H4C3.5,3 3,3.5 3,4C3,13.4 10.6,21 20,21C20.5,21 21,20.5 21,20V16.5C21,16 20.5,15.5 20,15.5M5,5H6.5C6.6,5.9 6.8,6.8 7,7.6L5.8,8.8C5.4,7.6 5.1,6.3 5,5M19,19C17.7,18.9 16.4,18.6 15.2,18.2L16.4,17C17.2,17.2 18.1,17.4 19,17.4V19Z" />
                            </svg>
                        </div>
                        <h4>Contacto</h4>
                    </div>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <span class="label">Teléfono:</span>
                            <span class="value">{{ $snap['telefono'] ?? '—' }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="label">Correo:</span>
                            <span class="value">{{ $snap['correo'] ?? ($snap['correo_envio_dte'] ?? '—') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection