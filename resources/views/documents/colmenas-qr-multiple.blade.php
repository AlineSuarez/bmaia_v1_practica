<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Códigos QR - {{ $apiario->nombre }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        
        .header h1 {
            margin: 0 0 10px 0;
            color: #333;
            font-size: 20px;
        }
        
        .qr-grid {
            display: table;
            width: 100%;
            table-layout: fixed;
        }
        
        .qr-row {
            display: table-row;
        }
        
        .qr-cell {
            display: table-cell;
            width: 33.33%;
            padding: 15px;
            text-align: center;
            vertical-align: top;
            border: 1px solid #ddd;
        }
        
        .qr-item {
            background: #f9f9f9;
            border: 2px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin: 5px;
            min-height: 180px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        
        .qr-code {
            margin-bottom: 15px;
        }
        
        .qr-code img {
            width: 120px;
            height: 120px;
            border: 1px solid #ccc;
        }
        
        .colmena-info {
            text-align: center;
        }
        
        .colmena-number {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin: 0;
        }
        
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $apiario->nombre }}</h1>
    </div>

    <div class="qr-grid">
        @foreach($qrData->chunk(3) as $rowIndex => $row)
            @if($rowIndex > 0 && $rowIndex % 6 == 0)
                <div class="page-break"></div>
            @endif
            
            <div class="qr-row">
                @foreach($row as $item)
                    <div class="qr-cell">
                        <div class="qr-item">
                            <div class="qr-code">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?data={{ urlencode($item['url']) }}&size=120x120&format=png" 
                                     alt="QR Code para Colmena #{{ $item['colmena']->numero }}">
                            </div>
                            
                            <div class="colmena-info">
                                <div class="colmena-number">Colmena #{{ $item['colmena']->numero }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
                
                {{-- Completar la fila si no tiene 3 elementos --}}
                @for($i = count($row); $i < 3; $i++)
                    <div class="qr-cell"></div>
                @endfor
            </div>
        @endforeach
    </div>
</body>
</html>