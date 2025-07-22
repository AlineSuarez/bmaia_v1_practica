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
    .section-title { margin-top: 2em; font-size: 14px; border-bottom: 1px solid #ccc; padding-bottom: .3em; }
    .info-table td { border: none; padding: .3em .5em; }
  </style>
</head>
<body>

  <h1>Historial de Movimiento de Colmenas</h1>
  <p><strong>Apiario Temporal:</strong> {{ $apiario->nombre }}</p>
  <p><strong>Fecha de generación:</strong> {{ $fechaGeneracion }}</p>

  {{-- RESUMEN DEL TRASLADO --}}
  <h2 class="section-title">Resumen del Movimiento</h2>
  <table class="info-table">
    <tr><td><strong>Tipo:</strong></td><td>{{ ucfirst($tipo_movimiento ?? 'Traslado') }}</td></tr>
    <tr><td><strong>Motivo:</strong></td><td>{{ $motivo_movimiento ?? '—' }}</td></tr>
    <tr><td><strong>Fecha Inicio:</strong></td><td>{{ \Carbon\Carbon::parse($fecha_inicio_mov)->format('d/m/Y') }}</td></tr>
    <tr><td><strong>Fecha Término:</strong></td><td>{{ \Carbon\Carbon::parse($fecha_termino_mov)->format('d/m/Y') }}</td></tr>
  </table>

  {{-- APICULTOR --}}
  <h2 class="section-title">Datos del Apicultor</h2>
  <table class="info-table">
    <tr><td><strong>Nombre:</strong></td><td>{{ $apicultor_nombre }}</td></tr>
    <tr><td><strong>RUT:</strong></td><td>{{ $apicultor_rut }}</td></tr>
    <tr><td><strong>Registro Nacional:</strong></td><td>{{ $registro_nacional }}</td></tr>
  </table>

  {{-- DESTINO --}}
  <h2 class="section-title">Ubicación Destino</h2>
  <table class="info-table">
    <tr><td><strong>Región:</strong></td><td>{{ $region_destino }}</td></tr>
    <tr><td><strong>Comuna:</strong></td><td>{{ $comuna_destino }}</td></tr>
    <tr><td><strong>Coordenadas:</strong></td><td>{{ $coordenadas_destino }}</td></tr>
  </table>

  {{-- POLINIZACIÓN (solo si aplica) --}}
  @if($motivo_movimiento == 'Polinización')
    <h2 class="section-title">Datos de Polinización</h2>
    <table class="info-table">
      <tr><td><strong>Cultivo:</strong></td><td>{{ $cultivo }}</td></tr>
      <tr><td><strong>Floración:</strong></td><td>{{ $periodo_floracion }}</td></tr>
      <tr><td><strong>Hectáreas:</strong></td><td>{{ $hectareas }}</td></tr>
    </table>
  @endif

  {{-- TRANSPORTE --}}
  <h2 class="section-title">Transporte</h2>
  <table class="info-table">
    <tr><td><strong>Transportista:</strong></td><td>{{ $transportista }}</td></tr>
    <tr><td><strong>Vehículo:</strong></td><td>{{ $vehiculo }}</td></tr>
  </table>

  {{-- HISTORIAL POR COLMENA --}}
  <h2 class="section-title">Detalle por Colmena</h2>
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
