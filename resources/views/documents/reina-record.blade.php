<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Calidad de Reina</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h1, h2 { text-align: center; margin: 10px 0; }
        p.section-title { font-weight: bold; margin: 15px 0 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid black; padding: 6px; text-align: center; }
        th { background-color: #f2f2f2; }
        .footer { text-align: center; font-size: 9px; color: #777; margin-top: 20px; }
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
            <td colspan="2"><b>NÚMERO DE REGISTRO:</b></td>
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
            <td><b>N° COLMENAS:</b></td>
            <td>{{ $data['hive_count'] }}</td>
        </tr>
        <tr>
            <td><b>FECHA DE INSTALACIÓN:</b></td>
            <td>{{ $data['installation_date'] }}</td>
            <td><b>TRASHUMANTE:</b></td>
            <td>{{ $data['nomadic'] }}</td>
        </tr>
        <tr>
            <td><b>LATITUD:</b></td>
            <td>{{ $data['latitude'] }}</td>
            <td><b>LONGITUD:</b></td>
            <td>{{ $data['longitude'] }}</td>
        </tr>
    </table>

    <h2>REGISTRO DE CALIDAD DE REINA</h2>
    <table>
        <thead>
            <tr>
                <th>Postura</th>
                <th>Estado Cría</th>
                <th>Postura Zánganos</th>
                <th>Origen</th>
                <th>Raza</th>
                <th>Introducción</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $data['calidadReina']->postura_reina ?? 'N/A' }}</td>
                <td>{{ $data['calidadReina']->estado_cria ?? 'N/A' }}</td>
                <td>{{ $data['calidadReina']->postura_zanganos ?? 'N/A' }}</td>
                <td>{{ $data['calidadReina']->origen_reina ?? 'N/A' }}</td>
                <td>{{ $data['calidadReina']->raza ?? 'N/A' }}</td>
                <td>
                    {{ $data['calidadReina']->fecha_introduccion
                       ? \Carbon\Carbon::parse($data['calidadReina']->fecha_introduccion)->format('d/m/Y')
                       : 'N/A' }}
                </td>
                <td>{{ ucfirst($data['calidadReina']->estado_actual ?? 'N/A') }}</td>
            </tr>
        </tbody>
    </table>

    @if(! empty($data['reemplazos']))
        <p class="section-title">REEMPLAZOS REALIZADOS</p>
        <table>
            <thead>
                <tr>
                    <th>FECHA</th>
                    <th>MOTIVO</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['reemplazos'] as $r)
                    <tr>
                        <td>
                            {{ $r['fecha']
                               ? \Carbon\Carbon::parse($r['fecha'])->format('d/m/Y')
                               : 'N/A' }}
                        </td>
                        <td>{{ $r['motivo'] ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        Generado el: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
    </div>

</body>
</html>
