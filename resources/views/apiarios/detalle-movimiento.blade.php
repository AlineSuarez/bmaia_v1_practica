{{-- filepath: resources/views/apiarios/detalle-movimiento.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Movimiento - {{ $apiario->nombre }}</title>
</head>

<body>
    <style>
        .detalle-movimiento-container {
            font-family: Arial, sans-serif;
            line-height: 1.4;
            color: #333;
            max-width: 100%;
            font-size: 14px;
        }

        .detalle-header {
            text-align: center;
            border-bottom: 2px solid #666;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }

        .detalle-header h1 {
            font-size: 18px;
            font-weight: bold;
            margin: 0 0 5px 0;
            color: #000;
        }

        .detalle-header h2 {
            font-size: 14px;
            margin: 0;
            color: #666;
        }

        .info-section {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 15px;
        }

        .info-section h3 {
            margin: 0 0 8px 0;
            font-size: 14px;
            color: #000;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .info-compact {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .info-item {
            display: flex;
            gap: 8px;
            min-width: 200px;
            font-size: 13px;
        }

        .info-label {
            font-weight: bold;
            color: #555;
            white-space: nowrap;
        }

        .info-value {
            color: #333;
        }

        .alert-custom {
            background: #f5f5f5;
            border: 1px solid #ccc;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 15px;
            text-align: center;
            font-size: 13px;
        }

        .alert-custom .alert-icon {
            color: #666;
            font-size: 18px;
            margin-bottom: 5px;
        }

        .colmenas-section {
            margin-top: 15px;
        }

        .colmenas-section h3 {
            font-size: 14px;
            color: #000;
            margin: 0 0 10px 0;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .origen-group {
            background: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .origen-header {
            font-weight: bold;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 6px;
            color: #333;
            font-size: 13px;
        }

        .origen-count {
            background: #666;
            color: white;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: normal;
        }

        /* LAYOUT HORIZONTAL FORZADO PARA COLMENAS */
        .colmenas-grid {
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            gap: 25px;
            align-items: center;
            justify-content: flex-start;
            width: 100%;
            overflow-x: auto;
            min-height: 80px;
        }

        .colmena-card {
            width: 65px;
            height: 65px;
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border: 2px solid #666;
            border-radius: 6px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: all 0.2s ease;
            position: relative;
            flex-shrink: 0;
            margin: 0;
            float: none;
            padding: 0;
            box-sizing: border-box;
        }

        .colmena-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            border-color: #333;
        }

        .colmena-card::before {
            content: '';
            position: absolute;
            top: 3px;
            right: 3px;
            width: 6px;
            height: 6px;
            background: #999;
            border-radius: 50%;
        }

        .colmena-numero {
            font-size: 12px;
            font-weight: bold;
            color: #333;
            margin: 0;
            line-height: 1;
            padding: 0;
            margin-top: 1.2rem;
        }

        .colmena-label {
            font-size: 8px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            line-height: 1;
            margin: 2px 0 0 0;
            padding: 0;
        }

        .empty-state {
            text-align: center;
            padding: 20px;
            border: 2px dashed #ccc;
            border-radius: 6px;
            background: #f8f9fa;
            color: #666;
            font-size: 13px;
        }

        .empty-state i {
            font-size: 24px;
            color: #bbb;
            margin-bottom: 8px;
        }

        .empty-state h4 {
            color: #555;
            margin-bottom: 5px;
            font-size: 14px;
        }

        .empty-state p {
            color: #777;
            margin: 0;
            font-size: 12px;
        }

        /* Evitar que se apilen verticalmente */
        .colmenas-grid>* {
            display: inline-block;
            vertical-align: top;
        }

        /* Clearfix para asegurar layout horizontal */
        .colmenas-grid::after {
            content: "";
            display: table;
            clear: both;
        }

        @media (max-width: 768px) {
            .info-compact {
                flex-direction: column;
                gap: 10px;
            }

            .info-item {
                min-width: auto;
                font-size: 12px;
            }

            .colmenas-grid {
                gap: 18px;
                overflow-x: scroll;
                padding-bottom: 5px;
            }

            .colmena-card {
                width: 55px;
                height: 55px;
            }

            .colmena-numero {
                font-size: 11px;
            }

            .colmena-label {
                font-size: 7px;
            }
        }

        @media print {
            .detalle-movimiento-container {
                font-size: 12px;
            }

            .colmenas-grid {
                gap: 20px;
                page-break-inside: avoid;
            }

            .colmena-card {
                break-inside: avoid;
                page-break-inside: avoid;
            }

            .origen-group {
                page-break-inside: avoid;
            }
        }
    </style>

    <div class="detalle-movimiento-container">
        {{-- Header del detalle --}}
        <div class="detalle-header">
            <h1>
                <i class="fas fa-route"></i>
                DETALLE DE MOVIMIENTO DE APIARIO
            </h1>
            <h2>{{ $apiario->nombre }}</h2>
        </div>

        {{-- Información del movimiento --}}
        @if($mov)
            <div class="info-section">
                <h3>
                    <i class="fas fa-info-circle"></i>
                    Información del Movimiento
                </h3>
                <div class="info-compact">
                    <div class="info-item">
                        <span class="info-label">Fecha:</span>
                        <span
                            class="info-value">{{ \Carbon\Carbon::parse($mov->fecha_movimiento)->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Tipo:</span>
                        <span class="info-value">{{ ucfirst($mov->tipo_movimiento) }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Motivo:</span>
                        <span class="info-value">{{ $mov->motivo_movimiento }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Transportista:</span>
                        <span class="info-value">{{ $mov->transportista ?: 'No especificado' }}</span>
                    </div>
                </div>
            </div>

            <div class="info-section">
                <h3>
                    <i class="fas fa-map-marker-alt"></i>
                    Ubicación de Destino
                </h3>
                <div class="info-compact">
                    <div class="info-item">
                        <span class="info-label">Región:</span>
                        <span
                            class="info-value">{{ optional($apiario->comuna->region)->nombre ?: 'No especificada' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Comuna:</span>
                        <span class="info-value">{{ optional($apiario->comuna)->nombre ?: 'No especificada' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Coordenadas:</span>
                        <span class="info-value">{{ $apiario->latitud ?: 'N/A' }}, {{ $apiario->longitud ?: 'N/A' }}</span>
                    </div>
                </div>
            </div>
        @else
            <div class="alert-custom">
                <div class="alert-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3>{{ $apiario->nombre }}</h3>
                <p><strong>No hay movimientos registrados para este apiario.</strong></p>
            </div>
        @endif

        {{-- Sección de colmenas trasladadas --}}
        <div class="colmenas-section">
            <h3>
                <i class="fas fa-cubes"></i>
                Colmenas Trasladadas
            </h3>

            @forelse($colmenasPorOrigen as $origenNombre => $colmenas)
                <div class="origen-group">
                    <div class="origen-header">
                        <i class="fas fa-home"></i>
                        {{ $origenNombre }}
                        <span class="origen-count">{{ $colmenas->count() }}
                            {{ $colmenas->count() == 1 ? 'colmena' : 'colmenas' }}</span>
                    </div>

                    <div class="colmenas-grid">
                        @foreach($colmenas as $colmena)
                            <div class="colmena-card">
                                <div class="colmena-numero">#{{ $colmena->numero }}</div>
                                <div class="colmena-label">Colmena</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h4>Sin colmenas registradas</h4>
                    <p>No hay colmenas registradas para este movimiento.</p>
                </div>
            @endforelse
        </div>
    </div>
</body>

</html>