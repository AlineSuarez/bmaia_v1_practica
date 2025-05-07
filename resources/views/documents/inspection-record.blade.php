<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Inspección de Apiario</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap');
        body { font-family: 'Roboto', sans-serif; font-size: 10px; line-height: 1.4; }
        h1 { text-align: center; }
        h2 { text-align: center; margin-top: 30px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th { background-color: #f2f2f2; }
        th, td { padding: 5px; border: 1px solid #000; text-align: center; font-size: 10px; }
        .section-title { font-weight: bold; margin-top: 20px; text-align: left; }
        .text-left { text-align: left; }
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

    <h2>REGISTRO DE INSPECCIÓN APIARIO</h2>

    <p class="section-title">REGISTRO DE INSPECCIONES</p>
    <table>
        <table>
            <thead>
                <tr>
                    <th rowspan="2">FECHA</th>
                    <th colspan="5">N° COLMENAS</th>
                    <th rowspan="2">NOMBRE REVISOR APIARIO</th>
                    <th rowspan="2">SOSPECHA DE ENFERMEDAD</th>
                    <th rowspan="2">OBSERVACIONES</th>
                </tr>
                <tr>
                    <th>TOTALES</th>
                    <th>ACTIVAS</th>
                    <th>ENFERMAS</th>
                    <th>MUERTAS</th>
                    <th>INSPECCIONADAS</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data['visits']->where('tipo_visita', 'Inspección de Visita') as $visit)
                    <tr>
                        <td>{{ $visit->fecha_visita ?? '' }}</td>
                        <td>{{ $visit->num_colmenas_totales ?? '' }}</td>
                        <td>{{ $visit->num_colmenas_activas ?? '' }}</td>
                        <td>{{ $visit->num_colmenas_enfermas ?? '' }}</td>
                        <td>{{ $visit->num_colmenas_muertas ?? '' }}</td>
                        <td>{{ $visit->num_colmenas_inspeccionadas ?? '' }}</td>
                        <td>{{ $visit->nombre_revisor_apiario ?? '' }}</td>
                        <td>{{ $visit->sospecha_enfermedad ?? '' }}</td>
                        <td class="text-left">{{ $visit->observaciones ?? '' }}</td>
                    </tr>
                    
                @empty
                    <tr><td colspan="9">No hay registros de inspección.</td></tr>
                @endforelse
            </tbody>
        </table>
    </table>
</body>
</html>