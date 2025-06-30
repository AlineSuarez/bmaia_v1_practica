<!doctype html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>QR Colmena #{{ $colmena->numero }}</title>
  <style>
    /* Márgenes de página */
    @page { margin: 0; }
    body {
      margin: 2cm;
      font-family: Arial, sans-serif;
      color: #333;
      text-align: center;
    }
    /* Encabezado */
    .header {
      background-color: #ffc107;
      padding: 0.5em 0;
      border-top-left-radius: 8px;
      border-top-right-radius: 8px;
      margin-bottom: 1.5em;
    }
    .header h1 {
      margin: 0;
      font-size: 1.5rem;
      color: #fff;
    }
    /* Contenedor del QR */
    .qr-container {
      padding: 1em;
      border: 2px solid #333;
      border-radius: 8px;
      display: inline-block;
      background-color: #fafafa;
    }
    .qr-container img {
      width: 250px;
      height: 250px;
      display: block;
      margin: 0 auto;
    }
    /* Texto descriptivo */
    .info {
      margin-top: 1em;
      font-size: 1rem;
    }
    .info strong {
      display: block;
      font-size: 1.1rem;
      margin-bottom: 0.3em;
    }
    /* Pie de página */
    .footer {
      position: fixed;
      bottom: 1cm;
      left: 0;
      width: 100%;
      font-size: 0.8rem;
      color: #777;
    }
  </style>
</head>
<body>
  <div class="header">
    <h1>QR Colmena #{{ $colmena->numero }}</h1>
  </div>

  <div class="qr-container">
    <img src="https://api.qrserver.com/v1/create-qr-code/?data={{ urlencode($url) }}&size=300x300"
         alt="QR Colmena #{{ $colmena->numero }}">
  </div>

    <div class="info">
        <p><strong>Apiario:</strong> {{ $apiario->nombre }}</p>
        <p><strong>Fecha registro:</strong> {{ $colmena->created_at->format('d/m/Y') }}</p>
        @if($colmena->numero_marcos)
            <p><strong>Marcos:</strong> {{ $colmena->numero_marcos }}</p>
        @endif
        @if($colmena->estado_inicial)
            <p><strong>Estado Inicial:</strong> {{ $colmena->estado_inicial }}</p>
        @endif
        @if($colmena->observaciones)
            <p><strong>Obs.:</strong> {{ Str::limit($colmena->observaciones, 60) }}</p>
        @endif
    </div>

  <div class="footer">
    Generado el {{ now()->format('d/m/Y H:i') }}
  </div>
</body>
</html>
