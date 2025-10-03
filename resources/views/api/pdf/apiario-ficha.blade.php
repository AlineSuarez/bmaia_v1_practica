<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
    h1 { font-size: 18px; margin-bottom: 4px; }
    .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
    .card { border: 1px solid #ddd; padding: 8px; border-radius: 6px; }
    .muted { color:#555; }
    table { width:100%; border-collapse: collapse; margin-top:6px; }
    th, td { border:1px solid #ddd; padding:6px; text-align:left; }
  </style>
</head>
<body>
  <h1>Ficha de Apiario</h1>
  <div class="grid">
    <div class="card">
      <strong>Nombre:</strong> {{ $apiario->nombre }}<br>
      <strong>Tipo:</strong> {{ ucfirst($apiario->tipo) }}<br>
      <strong>Región/Comuna:</strong>
      {{ optional($apiario->region)->nombre }} / {{ optional($apiario->comuna)->nombre }}<br>
      <strong>Dirección:</strong> {{ $apiario->direccion ?? '—' }}<br>
      <strong>Coordenadas:</strong> {{ $apiario->latitud }}, {{ $apiario->longitud }}<br>
      <strong>Estado:</strong> {{ $apiario->activo ? 'Activo' : 'Archivado' }}<br>
    </div>
    <div class="card">
      <strong>Colmenas actuales:</strong> {{ $apiario->colmenas_count ?? $apiario->colmenas()->count() }}<br>
      <strong>Creado:</strong> {{ optional($apiario->created_at)->format('Y-m-d H:i') }}<br>
      <strong>Actualizado:</strong> {{ optional($apiario->updated_at)->format('Y-m-d H:i') }}<br>
      @if($apiario->latitud && $apiario->longitud)
        <div class="muted">Mapa: https://www.openstreetmap.org/?mlat={{ $apiario->latitud }}&mlon={{ $apiario->longitud }}#map=15/{{ $apiario->latitud }}/{{ $apiario->longitud }}</div>
      @endif
    </div>
  </div>

  @if(isset($movimientos) && count($movimientos))
    <h3>Últimos movimientos</h3>
    <table>
      <thead><tr><th>Tipo</th><th>Desde</th><th>Hacia</th><th>Inicio</th><th>Término</th><th>Motivo</th></tr></thead>
      <tbody>
        @foreach($movimientos as $m)
          <tr>
            <td>{{ $m->tipo_movimiento }}</td>
            <td>#{{ $m->apiario_origen_id }}</td>
            <td>#{{ $m->apiario_destino_id }}</td>
            <td>{{ optional($m->fecha_inicio_mov)->format('Y-m-d') }}</td>
            <td>{{ optional($m->fecha_termino_mov)->format('Y-m-d') }}</td>
            <td>{{ $m->motivo ?? '—' }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @endif

  <p class="muted">Documento generado automáticamente por el sistema.</p>
</body>
</html>
