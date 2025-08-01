<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Historial de Movimiento de Colmenas</title>
    <style>
        @page {
            margin: 15mm;
            size: letter;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
        }

        h1, h2 {
            margin-bottom: 0.4em;
        }

        h1 {
            font-size: 14px;
        }

        h2 {
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1em;
        }

        th, td {
            border: 1px solid #333;
            padding: 4px;
            text-align: left;
            font-size: 10px;
        }

        th {
            background: #eee;
        }

        .section-title {
            margin-top: 1.8em;
            font-size: 12px;
            font-weight: bold;
            border-bottom: 1px solid #ccc;
            padding-bottom: .3em;
        }

        .image-container {
            text-align: center;
            vertical-align: middle;
            height: 140px;
            border: 1px solid #333;
        }

        .no-image {
            color: #666;
            font-style: italic;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 120px;
            font-size: 9px;
        }

        .signature-section {
            margin-top: 20px;
            width: 100%;
            page-break-inside: avoid;
        }

        .signature-container {
            float: right;
            width: 280px;
            text-align: center;
            border: 1px solid #333;
            padding: 15px;
            margin-top: 10px;
        }

        .signature-box {
            border-bottom: 1px solid #333;
            padding-bottom: 8px;
            margin-bottom: 8px;
            height: 65px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .signature-label {
            font-size: 10px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .signature-info {
            font-size: 9px;
            line-height: 1.2;
        }

        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }

        .colmena-header {
            background: #f0f0f0;
            padding: 0.4em;
            margin-top: 1em;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <h1>Historial de Movimiento de Colmenas</h1>
    <p><strong>Apiario Temporal:</strong> {{ $apiario->nombre }}</p>
    <p><strong>Fecha de generación:</strong> {{ $fechaGeneracion }}</p>

    {{-- 1. DATOS DEL APICULTOR --}}
    <h2 class="section-title">1. Datos del Apicultor</h2>
    <table>
        <tr><th>Representante Legal</th><td>{{ $beekeeper['legal_representative'] }} {{ $beekeeper['last_name'] }}</td></tr>
        <tr><th>RUT</th><td>{{ $beekeeper['rut'] }}</td></tr>
        <tr><th>N° Registro</th><td>{{ $beekeeper['registration_number'] }}</td></tr>
        <tr><th>Email</th><td>{{ $beekeeper['email'] }}</td></tr>
        <tr><th>Teléfono</th><td>{{ $beekeeper['phone'] }}</td></tr>
        <tr><th>Dirección</th><td>{{ $beekeeper['address'] }}</td></tr>
        <tr><th>Región</th><td>{{ $beekeeper['region'] }}</td></tr>
        <tr><th>Comuna</th><td>{{ $beekeeper['commune'] }}</td></tr>
    </table>

    {{-- 2. DATOS DEL APIARIO --}}
    <h2 class="section-title">2. Datos del Apiario</h2>
    <table>
        <tr><th>Nombre Apiario</th><td>{{ $apiarioData['apiary_name'] }}</td></tr>
        <tr><th>Código</th><td>{{ $apiarioData['apiary_number'] }}</td></tr>
        <tr><th>Actividad</th><td>{{ $apiarioData['activity'] }}</td></tr>
        <tr><th>Fecha de Instalación</th><td>{{ $apiarioData['installation_date'] }}</td></tr>
        <tr><th>Latitud / Longitud</th><td>{{ $apiarioData['latitude'] }}, {{ $apiarioData['longitude'] }}</td></tr>
        <tr><th>UTM X / Y</th><td>{{ $apiarioData['utm_x'] }} / {{ $apiarioData['utm_y'] }}</td></tr>
        <tr><th>Huso UTM</th><td>{{ $apiarioData['utm_huso'] }}</td></tr>
        <tr><th>Trashumante</th><td>{{ $apiarioData['nomadic'] }}</td></tr>
        <tr><th>N° Colmenas</th><td>{{ $apiarioData['hive_count'] }}</td></tr>
        <tr>
            <th>Foto del Apiario</th>
            <td class="image-container">
                @if(!empty($apiarioData['foto_base64']))
                    <img src="{{ $apiarioData['foto_base64'] }}" alt="Foto del Apiario" style="max-height:120px; width:auto;">
                @else
                    <div class="no-image">Sin imagen disponible</div>
                @endif
            </td>
        </tr>
    </table>

    {{-- 3. RESUMEN DEL MOVIMIENTO --}}
    <h2 class="section-title">3. Resumen del Movimiento</h2>
    <table>
        <tr><th>Tipo</th><td>{{ ucfirst($tipo_movimiento ?? 'Traslado') }}</td></tr>
        <tr><th>Motivo</th><td>{{ $motivo_movimiento ?? '—' }}</td></tr>
        <tr><th>Fecha de Inicio</th><td>{{ \Carbon\Carbon::parse($fecha_inicio_mov)->format('d/m/Y') }}</td></tr>
        <tr><th>Fecha de Término</th><td>{{ \Carbon\Carbon::parse($fecha_termino_mov)->format('d/m/Y') }}</td></tr>
        <tr><th>Región de Destino</th><td>{{ $region_destino }}</td></tr>
        <tr><th>Comuna de Destino</th><td>{{ $comuna_destino }}</td></tr>
        <tr><th>Coordenadas</th><td>{{ $coordenadas_destino }}</td></tr>
    </table>

    {{-- 4. POLINIZACIÓN (si aplica) --}}
    @if($motivo_movimiento == 'Polinización')
    <h2 class="section-title">4. Datos de Polinización</h2>
    <table>
        <tr><th>Cultivo</th><td>{{ $cultivo }}</td></tr>
        <tr><th>Periodo de Floración</th><td>{{ $periodo_floracion }}</td></tr>
        <tr><th>Hectáreas</th><td>{{ $hectareas }}</td></tr>
    </table>
    @endif

    {{-- 5. TRANSPORTE --}}
    <h2 class="section-title">5. Transporte</h2>
    <table>
        <tr><th>Transportista</th><td>{{ $transportista }}</td></tr>
        <tr><th>Vehículo</th><td>{{ $vehiculo }}</td></tr>
    </table>

    {{-- 6. HISTORIAL DE MOVIMIENTOS CONSOLIDADO --}}
    <h2 class="section-title">6. Detalle Consolidado de Movimientos por Colmena</h2>

    @if($colmenas->isEmpty())
        <p>No hay colmenas históricas para este apiario.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Colmena</th>
                    <th>Fecha</th>
                    <th>Origen</th>
                    <th>Destino</th>
                    <th>Motivo</th>
                </tr>
            </thead>
            <tbody>
                @foreach($colmenas as $colmena)
                    @foreach($colmena->movimientos as $mov)
                        <tr>
                            <td>#{{ $colmena->numero }}</td>
                            <td>{{ $mov->fecha_movimiento->format('d/m/Y H:i') }}</td>
                            <td>{{ optional($mov->apiarioOrigen)->nombre ?? '—' }}</td>
                            <td>{{ optional($mov->apiarioDestino)->nombre ?? '—' }}</td>
                            <td>{{ $mov->motivo_movimiento ?? '—' }}</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- FIRMA --}}
    @if($beekeeper['firma_base64'])
    <div class="signature-section clearfix">
        <div class="signature-container">
            <div class="signature-label">FIRMA DEL REPRESENTANTE LEGAL</div>
            <div class="signature-box">
                <img src="{{ $beekeeper['firma_base64'] }}" alt="Firma" style="max-height:55px; max-width:230px;">
            </div>
            <div class="signature-info">
                <strong>{{ $beekeeper['legal_representative'] }} {{ $beekeeper['last_name'] }}</strong><br>
                RUT: {{ $beekeeper['rut'] }}<br>
                Representante Legal
            </div>
        </div>
    </div>
    @endif

    <div class="footer">
      <p><strong>Generado el {{ now()->format('d/m/Y H:i') }}</strong> - Sistema de Gestión Apícola</p>
    </div>
</body>
</html>
