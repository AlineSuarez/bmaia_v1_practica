<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Alimentación - Apiario {{ $data['apiario']['nombre'] ?? '' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        h2, h3 {
            text-align: center;
            margin: 10px 0;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .table th, .table td {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: left;
        }
        .table th {
            background-color: #eee;
        }
    </style>
</head>
<body>

    <h2>Registro de Alimentación - Apiario: {{ $data['apiario']['nombre'] ?? 'Desconocido' }}</h2>
    <p><strong>Apicultor:</strong> {{ $data['beekeeper']['nombre'] ?? 'No disponible' }}</p>

    @foreach ($data['visits'] as $visita)
        <hr>
        <h3>Colmena #{{ $visita->colmena->numero ?? 'N/D' }} - Fecha: {{ \Carbon\Carbon::parse($visita->fecha)->format('d/m/Y') }}</h3>

        <table class="table">
            <tbody>
                <tr>
                    <th>Tipo de Alimentación</th>
                    <td>{{ $visita->estadoNutricional->tipo_alimentacion ?? 'No especificado' }}</td>
                </tr>
                <tr>
                    <th>Fecha de Aplicación</th>
                    <td>{{ $visita->estadoNutricional->fecha_aplicacion ?? 'No especificado' }}</td>
                </tr>
                <tr>
                    <th>Insumo Utilizado</th>
                    <td>{{ $visita->estadoNutricional->insumo_utilizado ?? 'No especificado' }}</td>
                </tr>
                <tr>
                    <th>Dosificación</th>
                    <td>{{ $visita->estadoNutricional->dosificacion ?? 'No especificado' }}</td>
                </tr>
                <tr>
                    <th>Método Utilizado</th>
                    <td>{{ $visita->estadoNutricional->metodo_utilizado ?? 'No especificado' }}</td>
                </tr>
                <tr>
                    <th>Objetivo</th>
                    <td>{{ $visita->estadoNutricional->objetivo ?? 'No especificado' }}</td>
                </tr>
                <tr>
                    <th>Nº Colmenas Tratadas</th>
                    <td>{{ $visita->estadoNutricional->n_colmenas_tratadas ?? 'No especificado' }}</td>
                </tr>
            </tbody>
        </table>

        <p><strong>Observaciones:</strong><br>
            {{ $visita->estadoNutricional->observaciones ?? 'No se registraron observaciones.' }}
        </p>
    @endforeach

</body>
</html>
