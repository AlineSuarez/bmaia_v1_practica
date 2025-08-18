<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>QR Colmena #{{ $colmena->numero }}</title>
  <style>
    @page {
      margin: 1.5cm;
      size: A4 portrait;
    }

    body {
      font-family: Arial, sans-serif;
      font-size: 11px;
      margin: 0;
      padding: 0;
      line-height: 1.3;
      color: #333;
      height: 100vh;
      overflow: hidden;
    }

    .container {
      height: 100%;
      display: flex;
      flex-direction: column;
    }

    .header {
      text-align: center;
      border-bottom: 2px solid #000;
      padding-bottom: 15px;
      margin-bottom: 20px;
      flex-shrink: 0;
    }

    .header h1 {
      font-size: 18px;
      font-weight: bold;
      margin: 0 0 8px 0;
      color: #000;
    }

    .header h2 {
      font-size: 14px;
      margin: 0;
      color: #666;
    }

    .content {
      flex: 1;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 15px;
      overflow: hidden;
    }

    .left-column {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    .right-column {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    .qr-section {
      text-align: center;
      flex-shrink: 0;
    }

    .qr-container {
      display: inline-block;
      padding: 10px;
      border: 2px solid #000;
      background: #fafafa;
      margin-bottom: 15px;
      border-radius: 5px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .qr-container img {
      width: 160px;
      height: 160px;
      display: block;
    }

    .colmena-info {
      background: #f5f5f5;
      padding: 12px;
      border: 1px solid #ddd;
      flex: 1;
      overflow: hidden;
    }

    .colmena-info h3 {
      margin: 0 0 8px 0;
      font-size: 13px;
      color: #000;
    }

    .info-line {
      margin-bottom: 4px;
      font-size: 10px;
    }

    .apiario-info {
      border: 1px solid #ddd;
      padding: 12px;
      flex: 1;
      overflow: hidden;
    }

    .apiario-info h3 {
      margin: 0 0 10px 0;
      font-size: 13px;
      color: #000;
      border-bottom: 1px solid #ddd;
      padding-bottom: 4px;
    }

    .info-grid {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }

    .info-card {
      background: #f9f9f9;
      border: 1px solid #ddd;
      padding: 8px;
      border-radius: 3px;
    }

    .info-card .label {
      font-weight: bold;
      color: #555;
      margin-bottom: 3px;
      font-size: 10px;
    }

    .info-card .value {
      color: #000;
      font-size: 11px;
    }

    .link-section {
      border: 1px solid #ddd;
      padding: 12px;
      flex-shrink: 0;
    }

    .link-section h3 {
      margin: 0 0 10px 0;
      font-size: 13px;
      color: #000;
      border-bottom: 1px solid #ddd;
      padding-bottom: 4px;
    }

    .link-item {
      background: #f9f9f9;
      border: 1px solid #ddd;
      padding: 8px;
      border-radius: 3px;
      word-break: break-all;
    }

    .link-item .label {
      font-weight: bold;
      margin-bottom: 5px;
      color: #555;
      font-size: 10px;
    }

    .link-item a {
      color: #000;
      text-decoration: none;
      font-size: 9px;
      line-height: 1.2;
    }

    .link-item a:hover {
      text-decoration: underline;
    }

    .footer {
      border-top: 1px solid #ddd;
      margin-top: 15px;
      padding-top: 10px;
      text-align: center;
      font-size: 9px;
      color: #666;
      flex-shrink: 0;
    }

    /* Evitar saltos de página innecesarios */
    .qr-section,
    .colmena-info,
    .apiario-info,
    .link-section {
      break-inside: avoid;
      page-break-inside: avoid;
    }

    /* Ajustes para impresión */
    @media print {
      .info-card:hover {
        transform: none;
        box-shadow: none;
      }

      .link-item a:hover {
        text-decoration: none;
      }
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="header">
      <h1>QR COLMENA #{{ $colmena->numero }}</h1>
      <h2>Sistema de Gestión Apícola</h2>
    </div>

    <div class="content">
      <div class="left-column">
        <div class="qr-section">
          <div class="qr-container">
            <img src="https://api.qrserver.com/v1/create-qr-code/?data={{ urlencode($url) }}&size=300x300"
              alt="QR Colmena #{{ $colmena->numero }}">
          </div>
        </div>

        <div class="colmena-info">
          <h3>INFORMACIÓN DE LA COLMENA</h3>
          <div class="info-line"><strong>Número:</strong> {{ $colmena->numero }}</div>
          <div class="info-line"><strong>Nombre:</strong> {{ $colmena->nombre ?? ('#' . $colmena->numero) }}</div>
          <div class="info-line"><strong>Registro:</strong> {{ $colmena->created_at->format('d/m/Y') }}</div>
          @if($colmena->numero_marcos)
        <div class="info-line"><strong>Marcos:</strong> {{ $colmena->numero_marcos }}</div>
      @endif
          @if($colmena->estado_inicial)
        <div class="info-line"><strong>Estado:</strong> {{ $colmena->estado_inicial }}</div>
      @endif
          @if($colmena->observaciones)
        <div class="info-line"><strong>Obs.:</strong> {{ Str::limit($colmena->observaciones, 50) }}</div>
      @endif
        </div>
      </div>

      <div class="right-column">
        <div class="apiario-info">
          <h3>INFORMACIÓN DEL APIARIO</h3>
          <div class="info-grid">
            <div class="info-card">
              <div class="label">Nombre del Apiario:</div>
              <div class="value">{{ $apiario->nombre }}</div>
            </div>

            <div class="info-card">
              <div class="label">Tipo de Apiario:</div>
              <div class="value">
                @if($apiario->tipo_apiario === 'fijo')
          Fijo
        @elseif($apiario->tipo_apiario === 'trashumante' && !$apiario->es_temporal)
          Base
        @elseif($apiario->tipo_apiario === 'trashumante' && $apiario->es_temporal)
          Temporal
        @else
          {{ ucfirst($apiario->tipo_apiario) }}
        @endif
              </div>
            </div>

            @if($apiario->ubicacion)
        <div class="info-card">
          <div class="label">Ubicación:</div>
          <div class="value">{{ $apiario->ubicacion }}</div>
        </div>
      @endif

            @if($apiario->user && $apiario->user->name)
        <div class="info-card">
          <div class="label">Propietario:</div>
          <div class="value">{{ $apiario->user->name }}</div>
        </div>
      @endif
          </div>
        </div>

        <div class="link-section">
          <h3>ENLACE PÚBLICO</h3>
          <div class="link-item">
            <div class="label">URL de Acceso:</div>
            <a href="{{ $url }}" target="_blank">{{ $url }}</a>
          </div>
        </div>
      </div>
    </div>

    <div class="footer">
      <p><strong>Generado el {{ now()->format('d/m/Y H:i') }}</strong> - Sistema de Gestión Apícola</p>
    </div>
  </div>
</body>

</html>