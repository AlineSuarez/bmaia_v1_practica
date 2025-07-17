<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro de Calidad de Reina – Apiario: {{ $apiario->nombre ?? 'N/A' }}</title>
  <style>
    body { font-family: sans-serif; font-size: 12px; margin: 20px; }
    h1, h2 { text-align: center; color: #333; margin: 10px 0; }
    p.section-title { font-weight: bold; margin: 10px 0 5px; }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 15px;
      page-break-inside: auto;
    }
    th, td {
      border: 1px solid #000;
      padding: 8px;
      vertical-align: top;
    }
    th { background-color: #f2f2f2; font-weight: bold; text-align: center; }
    td { text-align: left; }
    tr { page-break-inside: avoid; }
    .footer { text-align: center; font-size: 9px; color: #777; margin-top: 20px; }
  </style>
</head>
<body>

  <h1>REGISTRO DE CALIDAD DE REINA</h1>
  <h2>DATOS GENERALES:</h2>

  <p class="section-title">DATOS DEL APICULTOR/A REPRESENTANTE LEGAL</p>
  <table>
    <tr>
      <td colspan="2"><b>NOMBRE O RAZÓN SOCIAL:</b></td>
      <td colspan="2">{{ $apicultor->name ?? 'N/A' }}</td>
    </tr>
    <tr>
      <td colspan="2"><b>APELLIDOS:</b></td>
      <td colspan="2">{{ $apicultor->last_name ?? 'N/A' }}</td>
    </tr>
    <tr>
      <td colspan="2"><b>CORREO ELECTRÓNICO:</b></td>
      <td colspan="2">{{ $apicultor->email ?? 'N/A' }}</td>
    </tr>
    <tr>
      <td><b>RUT:</b></td>
      <td>{{ $apicultor->rut ?? 'N/A' }}</td>
      <td><b>TELÉFONO:</b></td>
      <td>+56 {{ $apicultor->phone ?? 'N/A' }}</td>
    </tr>
    <tr>
      <td><b>REGIÓN:</b></td>
      <td>{{ $apiario->comuna->region->nombre ?? 'N/A' }}</td>
      <td><b>COMUNA:</b></td>
      <td>{{ $apiario->comuna->nombre ?? 'N/A' }}</td>
    </tr>
  </table>

  <p class="section-title">DATOS DEL APIARIO</p>
  <table>
    <tr>
      <td><b>NOMBRE:</b></td>
      <td>{{ $apiario->nombre ?? 'N/A' }}</td>
      <td><b>N° COLMENAS:</b></td>
      <td>{{ $apiario->numero_colmenas ?? 'N/A' }}</td>
    </tr>
    <tr>
      <td><b>INSTALACIÓN:</b></td>
      <td>
        {{ $apiario->fecha_instalacion
             ? \Carbon\Carbon::parse($apiario->fecha_instalacion)->format('d/m/Y')
             : 'N/A' }}
      </td>
      <td><b>TRASHUMANTE:</b></td>
      <td>{{ ($apiario->trashumante ?? false) ? 'Sí' : 'No' }}</td>
    </tr>
    <tr>
      <td><b>LAT:</b></td>
      <td>{{ $apiario->latitud ?? 'N/A' }}</td>
      <td><b>LON:</b></td>
      <td>{{ $apiario->longitud ?? 'N/A' }}</td>
    </tr>
  </table>

  <h2>DETALLES DE LA CALIDAD DE REINA</h2>
  @php
    // Preparamos reemplazos
    $reemplazos = $calidadReina->reemplazos_realizados;
    if (is_string($reemplazos)) {
      $decoded = json_decode($reemplazos, true);
      $reemplazos = (json_last_error()===JSON_ERROR_NONE && is_array($decoded)) ? $decoded : [];
    } elseif (!is_array($reemplazos)) {
      $reemplazos = [];
    }
  @endphp

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
        <td>{{ $calidadReina->postura_reina ?? 'N/A' }}</td>
        <td>{{ $calidadReina->estado_cria ?? 'N/A' }}</td>
        <td>{{ $calidadReina->postura_zanganos ?? 'N/A' }}</td>
        <td>{{ $calidadReina->origen_reina ?? 'N/A' }}</td>
        <td>{{ $calidadReina->raza ?? 'N/A' }}</td>
        <td>
          {{ $calidadReina->fecha_introduccion
               ? \Carbon\Carbon::parse($calidadReina->fecha_introduccion)->format('d/m/Y')
               : 'N/A' }}
        </td>
        <td>{{ ucfirst($calidadReina->estado_actual ?? 'N/A') }}</td>
      </tr>
    </tbody>
  </table>

  @if(!empty($reemplazos))
    <p class="section-title">REEMPLAZOS REALIZADOS</p>
    <table>
      <thead>
        <tr>
          <th>FECHA</th>
          <th>MOTIVO</th>
        </tr>
      </thead>
      <tbody>
        @foreach($reemplazos as $r)
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
