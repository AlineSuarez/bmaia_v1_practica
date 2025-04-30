<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Uso de Medicamentos</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h1 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid black; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>DATOS GENERALES:</h1>

    <p class="section-title">DATOS DEL APICULTOR/A REPRESENTANTE LEGAL</p>
    <table>
        <tr>
            <td colspan="2"><b>NOMBRE O RAZÓN SOCIAL:</b></td>
            <td colspan="2">{{ $data['legal_representative'] }}</td>
        </tr>
        <tr>
            <td colspan="2"><b>APELLIDOS:</b></td>
            <td colspan="2">{{ $data['last_name'] }}</td>
        </tr>
        <tr>
            <td colspan="2"><b>NÚMERO DE REGISTRO DEL APICULTOR/A:</b></td>
            <td colspan="2">{{ $data['registration_number'] }}</td>
        </tr>
        <tr>
            <td colspan="2"><b>CORREO ELECTRÓNICO:</b></td>
            <td colspan="2">{{ $data['email'] }}</td>
        </tr>
        <tr>
            <td><b>RUT:</b></td>
            <td>{{ $data['rut'] }}</td>
            <td><b>TELÉFONO:</b></td>
            <td>+56 {{ $data['phone'] }}</td>
        </tr>
        <tr>
            <td><b>DIRECCIÓN:</b></td>
            <td>{{ $data['address'] }}</td>
            <td><b>REGIÓN:</b></td>
            <td>{{ $data['region'] }}</td>
        </tr>
        <tr>
            <td colspan="2"><b>COMUNA:</b></td>
            <td colspan="2">{{ $data['commune'] }}</td>
        </tr>
    </table>

    <p class="section-title">DATOS DEL APIARIO</p>
    <table>
        <tr>
            <td><b>NOMBRE DEL APIARIO:</b></td>
            <td>{{ $data['apiary_name'] }}</td>
        </tr>
        <tr>
            <td><b>NÚMERO DEL APIARIO:</b></td>
            <td>{{ $data['apiary_number'] }}</td>
        </tr>
        <tr>
            <td><b>ACTIVIDAD DEL APIARIO:</b></td>
            <td>{{ $data['activity'] }}</td>
        </tr>
        <tr>
            <td><b>FECHA DE INSTALACIÓN:</b></td>
            <td>{{ $data['installation_date'] }}</td>
        </tr>
        <tr>
            <td>X:</td>
            <td>{{ $data['utm_x'] }}</td>
        </tr>
        <tr>
            <td>Y:</td>
            <td>{{ $data['utm_y'] }}</td>
        </tr>
        <tr>
            <td>HUSO:</td>
            <td>{{ $data['utm_huso'] }}</td>
        </tr>
        <tr>
            <td>LATITUD:</td>
            <td>{{ $data['latitude'] }}</td>
        </tr>
        <tr>
            <td>LONGITUD:</td>
            <td>{{ $data['longitude'] }}</td>
        </tr>
        <tr>
            <td>TRASHUMANTE:</td>
            <td>{{ $data['nomadic'] }}</td>
        </tr>
        <tr>
            <td>NÚMERO DE COLMENAS:</td>
            <td>{{ $data['hive_count'] }}</td>
        </tr>
    </table>

<h1>REGISTRO DE USO DE MEDICAMENTOS</h1>

<p><strong>Apiario:</strong> {{ $data['apiary_name'] }} ({{ $data['apiary_number'] }})</p>

<table>
    <thead>
        <tr>
            <th>FECHA</th>
            <th>N° COLMENAS TRATADAS</th>
            <th>MOTIVO</th>
            <th>MEDICAMENTO</th>
            <th>PRINCIPIO ACTIVO</th>
            <th>PERÍODO DE RESGUARDO</th>
            <th>RESPONSABLE</th>
            <th>OBSERVACIONES</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data['visits'] as $visit)
            <tr>
                <td>{{ $visit->fecha_visita ?? 'N/A' }}</td>
                <td>{{ $visit->num_colmenas_tratadas ?? 'N/A' }}</td>
                <td>{{ $visit->motivo_tratamiento ?? 'N/A' }}</td>
                <td>{{ $visit->nombre_comercial_medicamento ?? 'N/A' }}</td>
                <td>{{ $visit->principio_activo_medicamento ?? 'N/A' }}</td>
                <td>{{ $visit->periodo_resguardo ?? 'N/A' }}</td>
                <td>{{ $visit->responsable ?? 'N/A' }}</td>
                <td>{{ $visit->observaciones ?? 'N/A' }}</td>
            </tr>
        @empty
            <tr><td colspan="8">No hay registros disponibles.</td></tr>
        @endforelse
    </tbody>
</table>

</body>
</html>
