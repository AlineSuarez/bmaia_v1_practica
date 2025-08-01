<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle PCC Colmena</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; margin: 20px; }
        h1 { font-size: 18px; margin-bottom: 0; }
        h2 { font-size: 14px; margin-top: 25px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        .footer { margin-top: 30px; font-size: 11px; text-align: center; color: #777; }
    </style>
</head>
<body>

    <h1>Detalle de PCC - Colmena N° {{ $colmena->numero }}</h1>
    <p><strong>Apiario:</strong> {{ $apiario->nombre }}</p>
    <p><strong>Generado:</strong> {{ $fechaGeneracion }}</p>

    @if($reina)
        <h2>1. Calidad de Reina</h2>
        <p><strong>Tipo:</strong> {{ $reina->tipo_reina }}</p>
        <p><strong>Observaciones:</strong> {{ $reina->observaciones }}</p>
    @endif

    @if($pcc2)
        <h2>2. Estado Nutricional</h2>
        <p><strong>Estado:</strong> {{ $pcc2->estado }}</p>
        <p><strong>Observaciones:</strong> {{ $pcc2->observaciones }}</p>
    @endif

    @if($pcc3)
        <h2>3. Presencia de Varroa</h2>
        <p><strong>Nivel:</strong> {{ $pcc3->nivel_infestacion }}</p>
        <p><strong>Tratamiento:</strong> {{ $pcc3->tratamiento_aplicado }}</p>
    @endif

    @if($pcc4)
        <h2>4. Presencia de Nosema</h2>
        <p><strong>Nivel:</strong> {{ $pcc4->nivel_infestacion }}</p>
        <p><strong>Tratamiento:</strong> {{ $pcc4->tratamiento_aplicado }}</p>
    @endif

    @if($pcc5)
        <h2>5. Índice de Cosecha</h2>
        <p><strong>Kilos Obtenidos:</strong> {{ $pcc5->kilos_cosechados ?? '-' }}</p>
        <p><strong>Observaciones:</strong> {{ $pcc5->observaciones }}</p>
    @endif

    @if($pcc6)
        <h2>6. Preparación para Invernada</h2>
        <p><strong>Técnica:</strong> {{ $pcc6->tecnica_utilizada }}</p>
        <p><strong>Observaciones:</strong> {{ $pcc6->observaciones }}</p>
    @endif

    <div class="footer">
        <p>Sistema de Gestión Apícola – {{ $fechaGeneracion }}</p>
    </div>
</body>
</html>
