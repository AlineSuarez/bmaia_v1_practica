<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Todas mis tareas</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 20px;
            color: #333;
        }
        h1 {
            text-align: center;
            font-size: 18px;
            margin-bottom: 30px;
        }
        .tarea-general {
            background-color: #f4f4f4;
            padding: 8px;
            margin-top: 20px;
            border-left: 4px solid #0c5460;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        th, td {
            border: 1px solid #aaa;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #ddd;
        }

        /* Prioridad: texto normal + círculo de color antes del texto
           Texto en negro; evitar que el círculo y el texto se apilen */
        .prioridad-urgente,
        .prioridad-alta,
        .prioridad-media,
        .prioridad-baja {
            font-weight: normal;
            color: #000000;
            white-space: nowrap;
            vertical-align: middle;
        }

        /* Indicador: usar marcado inline para mayor compatibilidad con PDF renderers */
        .prio {
            display: inline-block;
            white-space: nowrap;
            vertical-align: middle;
        }

        .prio-dot {
            display: inline-block;
            width: 0.9em;
            height: 0.9em;
            border-radius: 50%;
            margin-right: 0.45em;
            vertical-align: middle;
        }

        .prioridad-urgente .prio-dot { background-color: red; }
        .prioridad-alta .prio-dot { background-color: yellow; }
        .prioridad-media .prio-dot { background-color: green; }
        .prioridad-baja .prio-dot { background-color: lightblue; }
        .estado {
            font-style: italic;
        }
    </style>
</head>
<body>

    <h1>Listado de todas mis tareas</h1>

    @forelse ($subtareas->groupBy('tareaGeneral.nombre') as $nombreGeneral => $subtareasAgrupadas)
        <div class="tarea-general">{{ $nombreGeneral }}</div>
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Límite</th>
                    <th>Prioridad</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($subtareasAgrupadas as $tarea)
                    <tr>
                        <td>{{ $tarea->nombre }}</td>
                        <td>{{ \Carbon\Carbon::parse($tarea->fecha_inicio)->format('d/m/Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($tarea->fecha_limite)->format('d/m/Y') }}</td>
                        <td class="prioridad-{{ strtolower($tarea->prioridad ?? 'media') }}">
                            <span class="prio"><span class="prio-dot" aria-hidden="true"></span>{{ ucfirst($tarea->prioridad ?? 'Media') }}</span>
                        </td>
                        <td class="estado">
                            {{ ucfirst($tarea->estado ?? 'Pendiente') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @empty
        <p>No se encontraron subtareas para mostrar.</p>
    @endforelse

    <div class="footer">
      <p><strong>Generado el {{ now()->format('d/m/Y H:i') }}</strong> - Sistema de Gestión Apícola</p>
    </div>
</body>
</html>
