<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Tareas del Apiario</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #000; }
        h2, h3 { text-align: center; margin-bottom: 6px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: center; vertical-align: middle; }
        th { background-color: #f3f3f3; font-weight: bold; }
        .section-title { background-color: #eaeaea; text-align: center; font-weight: bold; padding: 5px; margin-top: 12px; border: 1px solid #000; }
        .info-table td { text-align: left; }
        .footer { text-align: right; margin-top: 15px; font-size: 11px; }
        .header { margin-bottom: 10px; }
        .small { font-size: 11px; }
    </style>
</head>
<body>

    <h2>REGISTRO DE TAREAS DEL APIARIO</h2>

    {{-- ===========================
        DATOS DEL APICULTOR/A
    ============================ --}}
    <div class="section-title">DATOS DEL APICULTOR/A</div>
    <table class="info-table">
        <tr>
            <th>Nombre / Razón Social:</th>
            <td>{{ $user->name ?? 'N/A' }}</td>
            <th>Email:</th>
            <td>{{ $user->email ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Teléfono:</th>
            <td>{{ $user->telefono ?? '+56' }}</td>
            <th>Dirección:</th>
            <td>{{ $user->direccion ?? 'No registrada' }}</td>
        </tr>
    </table>

    {{-- ===========================
        DATOS DEL APIARIO
    ============================ --}}
    <div class="section-title">DATOS DEL APIARIO</div>
    <table class="info-table">
        <tr>
            <th>Apiario:</th>
            <td>{{ $apiario->nombre ?? 'N/A' }}</td>
            <th>N° de Colmenas:</th>
            <td>{{ $apiario->colmenas->count() ?? 0 }}</td>
        </tr>
        <tr>
            <th>Ubicación:</th>
            <td colspan="3">{{ $apiario->nombre_region ?? '' }} / {{ $apiario->nombre_comuna ?? '' }}</td>
        </tr>
        <tr>
            <th>Latitud / Longitud:</th>
            <td colspan="3">{{ $apiario->latitud ?? '-' }}, {{ $apiario->longitud ?? '-' }}</td>
        </tr>
        <tr>
            <th>Tipo:</th>
            <td>{{ ucfirst($apiario->tipo_apiario ?? '-') }}</td>
            <th>Trashumante:</th>
            <td>{{ $apiario->es_temporal ? 'Sí' : 'No' }}</td>
        </tr>
    </table>

    {{-- ===========================
        DETALLES DE LAS TAREAS
    ============================ --}}
    <div class="section-title">DETALLES DE LAS TAREAS REGISTRADAS</div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Categoría</th>
                <th>Tarea Específica</th>
                <th>Acción Realizada</th>
                <th>Observaciones</th>
                <th>Fecha Inicio</th>
                <th>Fecha Término</th>
                <th>Próximo Seguimiento</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($tareas as $index => $tarea)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $tarea->categoria_tarea }}</td>
                    <td>{{ $tarea->tarea_especifica ?? '-' }}</td>
                    <td>{{ $tarea->accion_realizada ?? '-' }}</td>
                    <td>{{ $tarea->observaciones ?? '-' }}</td>
                    <td>{{ $tarea->fecha_inicio ? \Carbon\Carbon::parse($tarea->fecha_inicio)->format('d/m/Y') : '-' }}</td>
                    <td>{{ $tarea->fecha_termino ? \Carbon\Carbon::parse($tarea->fecha_termino)->format('d/m/Y') : '-' }}</td>
                    <td>{{ $tarea->proximo_seguimiento ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">No se han registrado tareas en este apiario.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Generado el {{ now()->format('d/m/Y H:i') }} - Sistema de Gestión Apícola
    </div>

</body>
</html>
