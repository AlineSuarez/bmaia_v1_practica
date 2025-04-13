<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Detalle de Subtarea</title>
</head>
<body>
    <h2 style="text-align: center;">Detalle de Subtarea</h2>

    <table border="1" cellpadding="10" cellspacing="0" width="100%">
        <tr>
            <th>Tarea General</th>
            <td>{{ $tarea->tareaGeneral->nombre ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Nombre</th>
            <td>{{ $tarea->nombre }}</td>
        </tr>
        <tr>
            <th>Fecha de Inicio</th>
            <td>{{ \Carbon\Carbon::parse($tarea->fecha_inicio)->format('d-m-Y') }}</td>
        </tr>
        <tr>
            <th>Fecha de Fin</th>
            <td>{{ \Carbon\Carbon::parse($tarea->fecha_limite)->format('d-m-Y') }}</td>
        </tr>
        <tr>
            <th>Prioridad</th>
            <td>{{ ucfirst($tarea->prioridad) }}</td>
        </tr>
        <tr>
            <th>Estado</th>
            <td>{{ $tarea->estado }}</td>
        </tr>
    </table>
</body>
</html>
