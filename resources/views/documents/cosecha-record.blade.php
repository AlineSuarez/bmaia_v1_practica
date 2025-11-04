<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Cosecha de Miel</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            margin: 20px;
        }
        h1, h2, h3 {
            text-align: center;
            margin-bottom: 8px;
        }
        h1 { font-size: 16px; }
        h2 { font-size: 13px; margin-top: 25px; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            table-layout: fixed;
            word-wrap: break-word;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px 4px;
            text-align: center;
            vertical-align: middle;
            font-size: 10px;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        td {
            white-space: normal !important;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .footer {
            text-align: right;
            font-size: 10px;
            margin-top: 25px;
        }
    </style>
</head>
<body>

    <h1>REGISTRO DE COSECHA DE MIEL</h1>

    <!-- DATOS DEL APICULTOR/A -->
    <h2>DATOS DEL APICULTOR/A</h2>
    <table>
        <tr>
            <td><strong>Nombre/Razón Social:</strong></td>
            <td>{{ $data['legal_representative'] }}</td>
            <td><strong>Apellidos:</strong></td>
            <td>{{ $data['last_name'] ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td><strong>Registro N°:</strong></td>
            <td>{{ $data['registration_number'] ?? 'N/A' }}</td>
            <td><strong>Email:</strong></td>
            <td>{{ $data['email'] ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td><strong>RUT:</strong></td>
            <td>{{ $data['rut'] ?? 'N/A' }}</td>
            <td><strong>Teléfono:</strong></td>
            <td>+56 {{ $data['phone'] ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td><strong>Dirección:</strong></td>
            <td>{{ $data['address'] ?? 'N/A' }}</td>
            <td><strong>Región / Comuna:</strong></td>
            <td>{{ $data['region'] ?? '' }} / {{ $data['commune'] ?? '' }}</td>
        </tr>
    </table>

    <!-- DATOS DEL APIARIO -->
    <h2>DATOS DEL APIARIO</h2>
    <table>
        <tr>
            <td><strong>Apiario:</strong></td>
            <td>{{ $data['apiary_name'] }}</td>
            <td><strong>N° Colmenas:</strong></td>
            <td>{{ $data['hive_count'] }}</td>
        </tr>
        <tr>
            <td><strong>Instalación:</strong></td>
            <td>{{ $data['installation_date'] }}</td>
            <td><strong>Trashumante:</strong></td>
            <td>{{ $data['nomadic'] == 'trashumante' ? 'Sí' : 'No' }}</td>
        </tr>
        <tr>
            <td><strong>Latitud / Longitud:</strong></td>
            <td colspan="3">{{ $data['latitude'] ?? 'N/A' }}, {{ $data['longitude'] ?? 'N/A' }}</td>
        </tr>
    </table>

    <!-- DETALLES DE LA COSECHA -->
    <h2>DETALLES DE LA COSECHA</h2>
    <table>
        <thead>
            <tr>
                <th>ID Lote</th>
                <th>Fecha Cosecha</th>
                <th>Fecha Extracción</th>
                <th>Lugar Extracción</th>
                <th>% Madurez</th>
                <th>% Humedad</th>
                <th>N° Alzadas</th>
                <th>N° Marcos</th>
                <th>Temperatura (°C)</th>
                <th>Responsable</th>
                <th>Notas</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data['cosechas'] as $cosecha)
                <tr>
                    <td>{{ $cosecha->id_lote_cosecha ?? '-' }}</td>
                    <td>{{ $cosecha->fecha_cosecha ? \Carbon\Carbon::parse($cosecha->fecha_cosecha)->format('d/m/Y') : '-' }}</td>
                    <td>{{ $cosecha->fecha_extraccion ? \Carbon\Carbon::parse($cosecha->fecha_extraccion)->format('d/m/Y') : '-' }}</td>
                    <td>{{ $cosecha->lugar_extraccion ?? '-' }}</td>
                    <td>{{ $cosecha->madurez_miel ?? '-' }}</td>
                    <td>{{ $cosecha->humedad_miel ?? '-' }}</td>
                    <td>{{ $cosecha->num_alzadas ?? '-' }}</td>
                    <td>{{ $cosecha->marcos_miel ?? '-' }}</td>
                    <td>{{ $cosecha->temperatura_ambiente ?? '-' }}</td>
                    <td>{{ $cosecha->responsable_cosecha ?? '-' }}</td>
                    <td>{{ $cosecha->notas ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="11">No hay registros disponibles.</td></tr>
            @endforelse
        </tbody>
    </table>

    <p class="footer">
        <strong>Generado el {{ now()->format('d/m/Y H:i') }}</strong> - Sistema de Gestión Apícola
    </p>
</body>
</html>
