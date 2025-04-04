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
