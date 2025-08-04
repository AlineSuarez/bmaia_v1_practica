<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle PCC Colmena #{{ $colmena->numero }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h1, h2, h3 { text-align: center; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 6px; vertical-align: top; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>

<h1>Detalle de Evaluación PCC</h1>
<h3>Apiario: {{ $apiario->nombre }} | Colmena #{{ $colmena->numero }}</h3>
@if($lastFecha)
<p style="text-align: center;"><strong>Última evaluación:</strong> {{ \Carbon\Carbon::parse($lastFecha)->format('d/m/Y') }}</p>
@endif

{{-- Repetir por cada PCC --}}
@foreach([
    'Desarrollo de Cámara de Cría' => $pcc1,
    'Estado de la Reina' => $pcc2,
    'Estado Nutricional' => $pcc3,
    'Control de Varroa' => $pcc4,
    'Control de Nosema' => $pcc5,
    'Índice de Cosecha' => $pcc6,
    'Preparación para la Invernada' => $pcc7
] as $titulo => $pcc)
    <h3>{{ $titulo }}</h3>
    @if($pcc)
        <table>
            @foreach($pcc->toArray() as $campo => $valor)
                @continue(in_array($campo, ['id', 'created_at', 'updated_at', 'colmena_id', 'visita_id']))
                <tr>
                    <th>{{ ucfirst(str_replace('_', ' ', $campo)) }}</th>
                    <td>
                        @if ($valor instanceof \Carbon\Carbon)
                            {{ $valor->format('d/m/Y') }}
                        @elseif (is_bool($valor))
                            {{ $valor ? 'Sí' : 'No' }}
                        @elseif (is_null($valor))
                            <span style="color: #888;">Sin datos</span>
                        @else
                            {{ $valor }}
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>
    @else
        <p><em>No hay datos registrados.</em></p>
    @endif
@endforeach

</body>
</html>
