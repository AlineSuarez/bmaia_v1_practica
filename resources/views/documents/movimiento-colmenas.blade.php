<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Historial de Movimientos - Colmena #{{ $colmena->numero }}</title>
    <style>
        @page {
            margin: 2.5cm;
            size: A4;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
            line-height: 1.4;
            color: #333;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 20px;
            font-weight: bold;
            margin: 0 0 10px 0;
            color: #000;
        }

        .header h2 {
            font-size: 16px;
            margin: 0 0 15px 0;
            color: #666;
        }

        .colmena-info {
            background: #f5f5f5;
            padding: 15px;
            border: 1px solid #ddd;
            margin-bottom: 20px;
        }

        .colmena-info h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #000;
        }

        .info-line {
            margin-bottom: 5px;
        }

        .stats {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
        }

        .stats h3 {
            margin: 0 0 15px 0;
            font-size: 14px;
            color: #000;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }

        .stats-table {
            width: 100%;
            border-collapse: collapse;
        }

        .stats-table td {
            padding: 8px;
            text-align: center;
            border: 1px solid #ddd;
        }

        .stat-number {
            font-weight: bold;
            font-size: 16px;
        }

        .locations-summary {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
        }

        .locations-summary h3 {
            margin: 0 0 15px 0;
            font-size: 14px;
            color: #000;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }

        .location-item {
            background: #f9f9f9;
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
        }

        .location-name {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .location-stats {
            font-size: 11px;
            color: #666;
        }

        .movements-section {
            /* Evitar salto de página después del título */
            page-break-inside: avoid;
        }

        .movements-section h3 {
            font-size: 14px;
            color: #000;
            margin: 0 0 15px 0;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            /* Mantener el título con al menos un movimiento */
            page-break-after: avoid;
        }

        .movement-item {
            border: 1px solid #ddd;
            margin-bottom: 12px;
            padding: 12px;
            background: #fafafa;
            /* Evitar quiebre en medio de un movimiento */
            break-inside: avoid;
            page-break-inside: avoid;
            /* Evitar que el primer movimiento se separe del título */
        }

        .movement-item:first-child {
            page-break-before: avoid;
        }

        .movement-header {
            font-weight: bold;
            font-size: 13px;
            margin-bottom: 8px;
            padding-bottom: 6px;
            border-bottom: 1px solid #ccc;
        }

        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        .detail-table td {
            padding: 4px 10px 4px 0;
            vertical-align: top;
        }

        .detail-label {
            font-weight: bold;
            width: 120px;
            color: #555;
        }

        .empty-message {
            text-align: center;
            padding: 40px;
            border: 1px dashed #ccc;
            background: #f9f9f9;
            color: #666;
        }

        .footer {
            border-top: 1px solid #ddd;
            margin-top: 30px;
            padding-top: 15px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }

        /* Agrupaciones que deben mantenerse juntas */
        .stats,
        .locations-summary {
            break-inside: avoid;
            page-break-inside: avoid;
        }

        /* Forzar que el título y al menos el primer movimiento estén juntos */
        .movements-section h3+.movement-item {
            page-break-before: avoid;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>HISTORIAL DE MOVIMIENTOS DE COLMENA</h1>
        <h2>Sistema de Gestión Apícola</h2>
    </div>

    <div class="colmena-info">
        <h3>INFORMACIÓN DE LA COLMENA</h3>
        <div class="info-line"><strong>Número de Colmena:</strong> {{ $colmena->numero }}</div>
        <div class="info-line"><strong>Apiario Actual:</strong> {{ $apiario->nombre }}</div>
        <div class="info-line"><strong>Color de Etiqueta:</strong> {{ $colmena->color_etiqueta }}</div>
        @if($colmena->estado_inicial)
            <div class="info-line"><strong>Estado:</strong> {{ $colmena->estado_inicial }}</div>
        @endif
        @if($colmena->numero_marcos)
            <div class="info-line"><strong>Número de Marcos:</strong> {{ $colmena->numero_marcos }}</div>
        @endif
        @if($apiarioBase && $apiarioBase->id !== $apiario->id)
            <div class="info-line"><strong>Extraída de:</strong> {{ $apiarioBase->nombre }}</div>
        @endif
    </div>

    <div class="stats">
        <h3>RESUMEN ESTADÍSTICO</h3>
        <table class="stats-table">
            <tr>
                <td>
                    <div class="stat-number">{{ $movimientos->count() }}</div>
                    <div>Total de Movimientos</div>
                </td>
                <td>
                    <div class="stat-number">{{ $apiariosVisitados }}</div>
                    <div>Apiarios Visitados</div>
                </td>
                <td>
                    <div class="stat-number">{{ $tiempoEnActual }}</div>
                    <div>Tiempo en Ubicación Actual</div>
                </td>
            </tr>
        </table>
    </div>

    @if($ubicacionesConTiempo->isNotEmpty())
        <div class="locations-summary">
            <h3>RESUMEN DE UBICACIONES</h3>
            @foreach($ubicacionesConTiempo as $ubicacion)
                <div class="location-item">
                    <div class="location-name">{{ $ubicacion['nombre'] }}</div>
                    <div class="location-stats">
                        {{ $ubicacion['visitas'] }} {{ $ubicacion['visitas'] == 1 ? 'visita' : 'visitas' }} •
                        {{ $ubicacion['tiempo_total'] }} {{ $ubicacion['tiempo_total'] == 1 ? 'día' : 'días' }} •
                        Fecha: {{ $ubicacion['primera_visita']->format('d/m/Y') }}
                        @if($ubicacion['visitas'] > 1)
                            • Última: {{ $ubicacion['ultima_visita']->format('d/m/Y') }}
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="movements-section">
        @if($movimientos->isEmpty())
            <div class="empty-message">
                <h3>SIN MOVIMIENTOS REGISTRADOS</h3>
                <p>Esta colmena no presenta movimientos registrados en el sistema.</p>
            </div>
        @else
            <h3>DETALLE DE MOVIMIENTOS</h3>

            @foreach($movimientos as $index => $mov)
                @php
                    $siguienteMovimiento = $movimientos->get($index + 1);
                    $diasEnUbicacion = $siguienteMovimiento ?
                        abs((int) $mov->fecha_movimiento->diffInDays($siguienteMovimiento->fecha_movimiento)) :
                        abs((int) $mov->fecha_movimiento->diffInDays(now()));
                @endphp
                <div class="movement-item">
                    <div class="movement-header">
                        {{ \Carbon\Carbon::parse($mov->fecha_movimiento)->format('d/m/Y H:i') }} -
                        @if($index === 0)
                            UBICACIÓN ACTUAL
                        @else
                            MOVIMIENTO {{ $movimientos->count() - $index }}
                        @endif
                        ({{ $diasEnUbicacion }} {{ $diasEnUbicacion == 1 ? 'día' : 'días' }} en esta ubicación)
                    </div>

                    <table class="detail-table">
                        <tr>
                            <td class="detail-label">Ubicación:</td>
                            <td>{{ optional($mov->apiarioDestino)->nombre ?? 'No especificada' }}</td>
                        </tr>
                        @if($mov->apiarioOrigen && $index > 0)
                            <tr>
                                <td class="detail-label">Procedente de:</td>
                                <td>{{ $mov->apiarioOrigen->nombre }}</td>
                            </tr>
                        @endif
                        @if($index === 0 && $mov->apiarioDestino)
                            <tr>
                                <td class="detail-label">Detalles:</td>
                                <td>Ubicación actual desde el {{ $mov->fecha_movimiento->format('d \d\e F \d\e Y') }}</td>
                            </tr>
                        @endif
                        @if($mov->motivo_movimiento)
                            <tr>
                                <td class="detail-label">Motivo:</td>
                                <td>{{ $mov->motivo_movimiento }}</td>
                            </tr>
                        @endif
                        @if($mov->transportista)
                            <tr>
                                <td class="detail-label">Transportista:</td>
                                <td>{{ $mov->transportista }}</td>
                            </tr>
                        @endif
                        @if($mov->vehiculo)
                            <tr>
                                <td class="detail-label">Vehículo:</td>
                                <td>{{ $mov->vehiculo }}</td>
                            </tr>
                        @endif
                        @if($mov->observaciones)
                            <tr>
                                <td class="detail-label">Observaciones:</td>
                                <td>{{ $mov->observaciones }}</td>
                            </tr>
                        @endif
                    </table>
                </div>
            @endforeach
        @endif
    </div>

    <div class="footer">
        <p><strong>Documento generado el {{ $fechaGeneracion }}</strong></p>
        <p>Sistema de Gestión Apícola - Reporte de Historial de Movimientos</p>
    </div>
</body>

</html>