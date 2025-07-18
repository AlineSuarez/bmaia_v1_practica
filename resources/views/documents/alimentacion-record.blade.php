<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Alimentación</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h1, h2 { text-align: center; }
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

    <h2>REGISTRO DE ALIMENTACIÓN</h2>

    <p><strong>Apiario:</strong> {{ $data['apiary_name'] }} ({{ $data['apiary_number'] }})</p>

    <table>
        <thead>
            <tr>
                <th>FECHA APLICACIÓN</th>
                <th>TIPO DE ALIMENTACIÓN</th>
                <th>OBJETIVO</th>
                <th>INSUMO UTILIZADO</th>
                <th>DOSIFICACIÓN</th>
                <th>MÉTODO UTILIZADO</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data['visits'] as $visit)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($visit->estadoNutricional->fecha_aplicacion)->format('d/m/Y') }}</td>
                    <td>{{ $visit->estadoNutricional->tipo_alimentacion }}</td>
                    <td>{{ ucfirst($visit->estadoNutricional->objetivo) }}</td>
                    <td>{{ $visit->estadoNutricional->insumo_utilizado }}</td>
                    <td>{{ $visit->estadoNutricional->dosifiacion }}</td>
                    <td>{{ $visit->estadoNutricional->metodo_utilizado }}</td>
                </tr>
            @empty
                <tr><td colspan="6">No hay registros disponibles.</td></tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>