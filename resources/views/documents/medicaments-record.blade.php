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

<table>
    <thead>
        <tr>
            <th>FECHA</th>
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
            @php
                // Determinar motivo en minúsculas
                $motivo = strtolower($visit->motivo_tratamiento ?? $visit->motivo);

                // Por defecto:
                $med = $act = null;

                if ($motivo === 'varroa' && $visit->presenciaVarroa) {
                    $med = $visit->presenciaVarroa->producto_comercial;
                    $act = $visit->presenciaVarroa->ingrediente_activo;
                }
                elseif ($motivo === 'nosema' && $visit->presenciaNosemosis) {
                    $med = $visit->presenciaNosemosis->producto_comercial;
                    $act = $visit->presenciaNosemosis->ingrediente_activo;
                }
            @endphp
            <tr>
                <td>{{ \Carbon\Carbon::parse($visit->fecha_visita)->format('d/m/Y') }}</td>
                <td>{{ ucfirst($motivo) }}</td>
                <td>{{ $med  ?? '-' }}</td>
                <td>{{ $act  ?? '-' }}</td>
                <td>{{ $visit->periodo_resguardo ?? '-' }}</td>
                <td>{{ $visit->responsable        ?? '-' }}</td>
                <td>{{ $visit->observaciones      ?? '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center">No hay registros disponibles.</td>
            </tr>
        @endforelse
    </tbody>
</table>

    <div class="footer">
      <p><strong>Generado el {{ now()->format('d/m/Y H:i') }}</strong> - Sistema de Gestión Apícola</p>
    </div>
</body>
</html>
