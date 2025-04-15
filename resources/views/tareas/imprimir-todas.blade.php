<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resumen de Subtareas</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        h2 { text-align: center; }
        @media print {
            button { display: none; }
        }
    </style>
</head>
<body>

    <button onclick="window.print()">🖨️ Imprimir</button>

    <h2>Resumen de Subtareas</h2>

    <table class="text-center">
        <thead>
            <tr>
                <th>Tarea General</th>
                <th>Nombre</th>
                <th style="width: 120px">Fecha Inicio</th>
                <th style="width: 120px">Fecha Fin</th>
                <th>Prioridad</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($subtareas as $task)
                <tr>
                    <td>{{ $task->tareaGeneral->nombre ?? 'N/A' }}</td>
                    <td>{{ $task->nombre }}</td>
                    <td>{{ \Carbon\Carbon::parse($task->fecha_inicio)->format('d-m-Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($task->fecha_limite)->format('d-m-Y') }}</td>
                    <td>{{ ucfirst($task->prioridad) }}</td>
                    <td>{{ $task->estado }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
