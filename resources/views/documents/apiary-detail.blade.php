<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalles Apiario</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h1, h2 { margin-bottom: 0.5em; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 1.5em; }
        th, td { border: 1px solid #333; padding: 6px; text-align: left; }
        th { background: #eee; }
    </style>
</head>
<body>

    <h1>Detalles del Apiario</h1>

    {{-- Datos del Apicultor --}}
    <h2>1. Datos del Apicultor</h2>
    <table>
        <tr>
            <th>Representante Legal</th>
            <td>{{ $data['legal_representative'] }} {{ $data['last_name'] }}</td>
        </tr>
        <tr>
            <th>RUT</th>
            <td>{{ $data['rut'] }}</td>
        </tr>
        <tr>
            <th>Nº Registro</th>
            <td>{{ $data['registration_number'] }}</td>
        </tr>
        <tr>
            <th>Email</th>
            <td>{{ $data['email'] }}</td>
        </tr>
        <tr>
            <th>Teléfono</th>
            <td>{{ $data['phone'] }}</td>
        </tr>
        <tr>
            <th>Dirección</th>
            <td>{{ $data['address'] }}</td>
        </tr>
        <tr>
            <th>Región</th>
            <td>{{ $data['region'] }}</td>
        </tr>
        <tr>
            <th>Comuna</th>
            <td>{{ $data['commune'] }}</td>
        </tr>
        <tr>
            <th>Firma</th>
            <td>
                @if(!empty($data['firma']))
                    <img src="{{ public_path('storage/firmas/' . $data['firma']) }}" alt="Firma" style="max-height:60px;">
                @else
                    (no disponible)
                @endif
            </td>
        </tr>
    </table>

    {{-- Datos del Apiario --}}
    <h2>2. Datos del Apiario</h2>
    
    <table>
        <tr>
            <th>Nombre Apiario</th>
            <td>{{ $data['apiary_name'] }}</td>
        </tr>
        <tr>
            <th>Código</th>
            <td>{{ $data['apiary_number'] }}</td>
        </tr>
        <tr>
            <th>Actividad / Objetivo</th>
            <td>{{ $data['activity'] }}</td>
        </tr>
        <tr>
            <th>Fecha de Instalación</th>
            <td>{{ $data['installation_date'] }}</td>
        </tr>
        <tr>
            <th>Latitud / Longitud</th>
            <td>{{ $data['latitude'] }}, {{ $data['longitude'] }}</td>
        </tr>
        <tr>
            <th>UTM X / Y</th>
            <td>{{ $data['utm_x'] }} / {{ $data['utm_y'] }}</td>
        </tr>
        <tr>
            <th>Huso UTM</th>
            <td>{{ $data['utm_huso'] }}</td>
        </tr>
        <tr>
            <th>Trashumante</th>
            <td>{{ $data['nomadic'] }}</td>
        </tr>
        <tr>
            <th>N° Colmenas</th>
            <td>{{ $data['hive_count'] }}</td>
        </tr>
        <tr>
            <th>Foto del Apiario</th>
            <td style="height:180px; text-align:center; vertical-align:middle; border:1px solid #333;">
                @php
                    // ruta física al fichero en storage/app/public
                    $rel = $data['foto'] ?? '';
                    $rutaFisica = $rel
                        ? storage_path("app/public/{$rel}")
                        : null;
                @endphp
        
                @if($rutaFisica && file_exists($rutaFisica))
                    <img
                        src="file://{{ $rutaFisica }}"
                        alt="Foto del Apiario"
                        style="max-height:160px; width:auto;"
                    >
                @else
                    {{-- queda el recuadro vacío si no hay imagen --}}
                @endif
            </td>
        </tr>
    </table>

</body>
</html>
