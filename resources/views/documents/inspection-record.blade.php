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

    <h2>REGISTRO DE INSPECCIÓN APIARIO</h2>

    <p class="section-title">DATOS DEL APIARIO</p>
    <table>
        <tr>
            <td style="width: 30%; font-weight: bold;" class="text-left">NOMBRE DEL APIARIO:</td>
            <td style="width: 70%;" class="text-left">{{ $data['apiary_name'] ?? '' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;" class="text-left">NÚMERO DEL APIARIO:</td>
            <td class="text-left">{{ $data['apiary_number'] ?? '' }}</td>
        </tr>
    </table>

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