<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Dashboard B-MaiA</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #000000;
            line-height: 1.5;
            padding: 20px;
        }

        .main-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .section-title {
            font-size: 11px;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: 1px solid #000000;
        }

        .info-table th {
            background-color: #ffffff;
            border: 1px solid #000000;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
        }

        .info-table td {
            border: 1px solid #000000;
            padding: 8px;
            font-size: 9px;
        }

        .info-table td.label {
            font-weight: bold;
            width: 40%;
            background-color: #f5f5f5;
        }

        .info-table td.value {
            width: 60%;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: 1px solid #000000;
        }

        .data-table th {
            background-color: #f5f5f5;
            border: 1px solid #000000;
            padding: 8px;
            text-align: center;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
        }

        .data-table td {
            border: 1px solid #000000;
            padding: 8px;
            font-size: 9px;
            text-align: center;
        }

        .subsection {
            margin-bottom: 15px;
        }

        .subsection-label {
            font-size: 10px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .footer-text {
            position: fixed;
            bottom: 10px;
            left: 20px;
            right: 20px;
            text-align: center;
            font-size: 8px;
            color: #666666;
        }

        .page-break {
            page-break-after: always;
        }

        .two-column-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .two-column-table td {
            width: 50%;
            vertical-align: top;
        }

        .indicator-box {
            border: 1px solid #000000;
            padding: 10px;
            margin: 5px;
            background-color: #ffffff;
        }

        .indicator-label {
            font-size: 8px;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .indicator-value {
            font-size: 16px;
            font-weight: bold;
            color: #000000;
        }

        .subtitle-text {
            font-size: 9px;
            margin-bottom: 8px;
        }
    </style>
</head>
<body>
    <!-- Título Principal -->
    <div class="main-title">
        REPORTE DE INDICADORES DEL SISTEMA B-MAIA
    </div>

    <!-- SECCIÓN 1: PRODUCTIVIDAD GLOBAL -->
    <div class="section-title">INDICADORES DE PRODUCTIVIDAD GLOBAL</div>

    <table class="info-table">
        <tr>
            <td class="label">PROMEDIO COLMENAS/APIARIO:</td>
            <td class="value">{{ number_format($promedioColmenasPorApiario, 1) }}</td>
        </tr>
        <tr>
            <td class="label">TASA DE SUPERVIVENCIA:</td>
            <td class="value">{{ number_format($tasaSupervivencia, 1) }}%</td>
        </tr>
        <tr>
            <td class="label">PROMEDIO DE TRATAMIENTOS (POR COLMENA/AÑO):</td>
            <td class="value">{{ number_format($promedioTratamientos, 1) }}</td>
        </tr>
        <tr>
            <td class="label">PRODUCCIÓN ANUAL PROMEDIO (KG/COLMENA/AÑO):</td>
            <td class="value">{{ number_format($promedioProduccion, 1) }} kg</td>
        </tr>
        <tr>
            <td class="label">TASA DE MORTALIDAD:</td>
            <td class="value">{{ number_format($tasaMortalidad, 1) }}%</td>
        </tr>
        <tr>
            <td class="label">ALIMENTO SUMINISTRADO (KG/COLMENA/AÑO):</td>
            <td class="value">{{ number_format($alimentoSuministrado, 1) }} kg</td>
        </tr>
        <tr>
            <td class="label">PROMEDIO DE VISITAS POR APIARIO:</td>
            <td class="value">{{ number_format($promedioVisitasPorApiario, 1) }}</td>
        </tr>
    </table>

    <!-- Actividades de Apiarios -->
    @if($actividadesApiarios->count() > 0)
    <div class="subsection-label">DISTRIBUCIÓN DE ACTIVIDADES EN APIARIOS</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>OBJETIVO DE PRODUCCIÓN</th>
                <th>TOTAL DE APIARIOS</th>
            </tr>
        </thead>
        <tbody>
            @foreach($actividadesApiarios as $actividad)
            <tr>
                <td>{{ $actividad->objetivo_produccion }}</td>
                <td>{{ $actividad->total }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="page-break"></div>

    <!-- SECCIÓN 2: PRODUCTIVIDAD POR APIARIO -->
    <div class="section-title">PRODUCTIVIDAD POR APIARIO</div>

    <table class="info-table">
        <tr>
            <td class="label">TOTAL DE APIARIOS ACTIVOS:</td>
            <td class="value">{{ number_format($totalApiariosActivos) }}</td>
        </tr>
        <tr>
            <td class="label">REGIONES CON APIARIOS:</td>
            <td class="value">{{ number_format($regionesConApiarios) }}</td>
        </tr>
        <tr>
            <td class="label">PORCENTAJE DE APIARIOS GEOLOCALIZADOS:</td>
            <td class="value">{{ number_format($porcentajeApiariosGeolocalizados, 1) }}%</td>
        </tr>
    </table>

    <!-- Colmenas por Región -->
    @if($colmenasPorRegion->count() > 0)
    <div class="subsection-label">DISTRIBUCIÓN DE COLMENAS POR REGIÓN</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>REGIÓN</th>
                <th>TOTAL COLMENAS</th>
            </tr>
        </thead>
        <tbody>
            @foreach($colmenasPorRegion as $region)
            <tr>
                <td>{{ $region->region }}</td>
                <td>{{ number_format($region->total_colmenas) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- Tipos de Alimento -->
    @if($tiposAlimento->count() > 0)
    <div class="subsection-label">TIPOS DE ALIMENTO UTILIZADO</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>TIPO DE ALIMENTACIÓN</th>
                <th>TOTAL KG</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tiposAlimento as $alimento)
            <tr>
                <td>{{ $alimento->tipo_alimentacion }}</td>
                <td>{{ number_format($alimento->total_kg, 2) }} kg</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- Apicultores por Región -->
    @if($apicultoresPorRegion->count() > 0)
    <div class="subsection-label">DISTRIBUCIÓN DE APICULTORES POR REGIÓN</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>REGIÓN</th>
                <th>TOTAL APICULTORES</th>
            </tr>
        </thead>
        <tbody>
            @foreach($apicultoresPorRegion as $apicultor)
            <tr>
                <td>{{ $apicultor->region }}</td>
                <td>{{ $apicultor->total_apicultores }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="page-break"></div>

    <!-- SECCIÓN 3: MOVIMIENTO DE COLMENAS -->
    <div class="section-title">MOVIMIENTO DE COLMENAS</div>

    <table class="info-table">
        <tr>
            <td class="label">MOVIMIENTO TOTAL DE COLMENAS:</td>
            <td class="value">{{ number_format($totalMovimientos) }}</td>
        </tr>
        <tr>
            <td class="label">TOTAL DE TRASHUMANCIA:</td>
            <td class="value">{{ number_format($totalTrashumancia) }}</td>
        </tr>
        <tr>
            <td class="label">TOTAL DE RETORNOS:</td>
            <td class="value">{{ number_format($totalRetornos) }}</td>
        </tr>
        <tr>
            <td class="label">DURACIÓN TOTAL DE POLINIZACIÓN (DÍAS):</td>
            <td class="value">{{ number_format($duracionPromedioPolinizacion, 0) }} días</td>
        </tr>
    </table>

    <!-- Movimientos por Motivo -->
    @if($movimientosPorMotivo->count() > 0)
    <div class="subsection-label">MOVIMIENTOS POR MOTIVO</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>MOTIVO DE MOVIMIENTO</th>
                <th>TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach($movimientosPorMotivo as $movimiento)
            <tr>
                <td>{{ $movimiento->motivo_movimiento }}</td>
                <td>{{ $movimiento->total }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- Footer -->
    <div class="footer-text">
        Generado el {{ date('d/m/Y H:i') }} - Sistema de Gestión Apícola B-MaiA
    </div>
</body>
</html>
