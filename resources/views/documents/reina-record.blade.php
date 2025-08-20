<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro de Calidad de Reina – {{ $apiario->nombre }}</title>
  <style>
    body { font-family: sans-serif; font-size:12px; margin:20px; }
    h1, h2 { text-align:center; color:#333; margin:10px 0; }
    table { width:100%; border-collapse:collapse; margin:15px 0; }
    th,td { border:1px solid #000; padding:8px; vertical-align:top; }
    th { background:#f2f2f2; text-align:center; }
    td { text-align:left; }
    tr { page-break-inside:avoid; }
    .footer { text-align:center; font-size:9px; color:#777; margin-top:20px; }
  </style>
</head>
<body>

  <h1>REGISTRO DE CALIDAD DE REINA</h1>

  <h2>DATOS DEL APICULTOR/A</h2>
  <table>
    <tr>
      <td><strong>Nombre/Razón Social:</strong></td>
      <td>{{ $apicultor->name }}</td>
      <td><strong>Apellidos:</strong></td>
      <td>{{ $apicultor->last_name }}</td>
    </tr>
    <tr>
      <td><strong>Registro N°:</strong></td>
      <td>{{ $apicultor->registration_number ?? 'N/A' }}</td>
      <td><strong>Email:</strong></td>
      <td>{{ $apicultor->email }}</td>
    </tr>
    <tr>
      <td><strong>RUT:</strong></td>
      <td>{{ $apicultor->rut }}</td>
      <td><strong>Teléfono:</strong></td>
      <td>+56 {{ $apicultor->phone }}</td>
    </tr>
    <tr>
      <td><strong>Dirección:</strong></td>
      <td>{{ $apicultor->address }}</td>
      <td><strong>Región / Comuna:</strong></td>
      <td>{{ $apiario->comuna->region->nombre }} / {{ $apiario->comuna->nombre }}</td>
    </tr>
  </table>

  <h2>DATOS DEL APIARIO</h2>
  <table>
    <tr>
      <td><strong>Apiario:</strong></td>
      <td>{{ $apiario->nombre }}</td>
      <td><strong>N° Colmenas:</strong></td>
      <td>{{ $apiario->numero_colmenas }}</td>
    </tr>
    <tr>
      <td><strong>Instalación:</strong></td>
      <td>
        {{ $apiario->fecha_instalacion
           ? \Carbon\Carbon::parse($apiario->fecha_instalacion)->format('d/m/Y')
           : 'N/A' }}
      </td>
      <td><strong>Trashumante:</strong></td>
      <td>{{ $apiario->trashumante ? 'Sí' : 'No' }}</td>
    </tr>
    <tr>
      <td><strong>Latitud / Longitud:</strong></td>
      <td colspan="3">{{ $apiario->latitud }}, {{ $apiario->longitud }}</td>
    </tr>
  </table>

  <h2>DETALLES DE LA CALIDAD DE REINA</h2>
  <table>
    <thead>
      <tr>
        <th>Postura Reina</th>
        <th>Estado Cría</th>
        <th>Postura Zánganos</th>
        <th>Origen</th>
        <th>Raza</th>
        <th>Línea Genética</th>
        <th>Fecha Introducción</th>
        <th>Estado Actual</th>
        <th>Últ. Reemplazo</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>{{ $calidadReina?->postura_reina ?? 'No registrada' }}</td>
        <td>{{ $calidadReina?->estado_cria ?? 'No registrada' }}</td>
        <td>{{ $calidadReina?->postura_zanganos ?? 'No registrada' }}</td>
        <td>{{ ucfirst($calidadReina?->origen_reina ?? 'No registrada') }}</td>
        <td>{{ $calidadReina?->raza ?? 'No registrada' }}</td>
        <td>{{ $calidadReina?->linea_genetica ?? 'No registrada' }}</td>
        <td>
            {{ $calidadReina?->fecha_introduccion
                ? \Carbon\Carbon::parse($calidadReina->fecha_introduccion)->format('d/m/Y')
                : 'No registrada' }}
        </td>
        <td>{{ ucfirst($calidadReina?->estado_actual ?? 'No registrada') }}</td>
        <td>
          @if($ultimoReemplazo && !empty($ultimoReemplazo['fecha']))
            {{ \Carbon\Carbon::parse($ultimoReemplazo['fecha'])->format('d/m/Y') }}
          @else
            -
          @endif
        </td>
      </tr>
    </tbody>
  </table>

  @if(count($reemplazos))
    <h2>REEMPLAZOS REALIZADOS</h2>
    <table>
      <thead>
        <tr><th>Fecha</th><th>Motivo</th></tr>
      </thead>
      <tbody>
        @foreach($reemplazos as $r)
          <tr>
            <td>
              {{ !empty($r['fecha'])
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
      <p><strong>Generado el {{ now()->format('d/m/Y H:i') }}</strong> - Sistema de Gestión Apícola</p>
    </div>
</body>
</html>
