<!doctype html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <style>
    body { font-family: sans-serif; font-size: 12px; }
    h1, h2 { margin: .5em 0; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 1em; }
    th, td { border: 1px solid #ccc; padding: .4em; }
    .colmena-header { background: #f0f0f0; padding: .4em; margin-top: 1em; }
  </style>
</head>
<body>

  <h1>Historial de Movimientos</h1>
  <p><strong>Apiario:</strong> {{ $apiario->nombre }}</p>
  <p><strong>Generado:</strong> {{ $fechaGeneracion }}</p>

  @forelse($colmenas as $colmena)
    <div class="colmena-header">
      <h2>Colmena #{{ $colmena->numero }}</h2>
    </div>
    @php
      $movs = $colmena->movimientos;
    @endphp

    @if($movs->isEmpty())
      <p>No hay movimientos registrados.</p>
    @else
      <table>
        <thead>
          <tr>
            <th>Fecha</th>
            <th>Origen</th>
            <th>Destino</th>
            <th>Motivo</th>
          </tr>
        </thead>
        <tbody>
          @foreach($movs as $mov)
            <tr>
              <td>{{ $mov->fecha_movimiento->format('d/m/Y H:i') }}</td>
              <td>{{ optional($mov->apiarioOrigen)->nombre ?? '—' }}</td>
              <td>{{ optional($mov->apiarioDestino)->nombre ?? '—' }}</td>
              <td>{{ $mov->motivo_movimiento ?? '—' }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @endif
  @empty
    <p>No hay colmenas históricas para este apiario.</p>
  @endforelse

</body>
</html>
