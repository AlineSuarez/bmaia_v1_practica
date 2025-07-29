<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Detalles Apiario</title>
    <style>
        @page {
            margin: 15mm;
            size: letter;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
        }

        h1,
        h2 {
            margin-bottom: 0.4em;
        }

        h1 {
            font-size: 14px;
        }

        h2 {
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1em;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 4px;
            text-align: left;
            font-size: 10px;
        }

        th {
            background: #eee;
        }

        .image-container {
            text-align: center;
            vertical-align: middle;
            height: 140px;
            border: 1px solid #333;
        }

        .no-image {
            color: #666;
            font-style: italic;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 120px;
            font-size: 9px;
        }

        /* Estilos para la sección de firma */
        .signature-section {
            margin-top: 20px;
            width: 100%;
            page-break-inside: avoid;
        }

        .signature-container {
            float: right;
            width: 280px;
            text-align: center;
            border: 1px solid #333;
            padding: 15px;
            margin-top: 10px;
        }

        .signature-box {
            border-bottom: 1px solid #333;
            padding-bottom: 8px;
            margin-bottom: 8px;
            height: 65px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .signature-label {
            font-size: 10px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .signature-info {
            font-size: 9px;
            line-height: 1.2;
        }

        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
    </style>
</head>

<body>

    <h1>Detalles del Apiario</h1>

    {{-- Datos del Apicultor --}}
    <h2>1. Datos del Apicultor</h2>
    <table>
        <tr>
            <th>Representante Legal</th>
            <td>{{ $data['legal_representative'] }} {{ $data['last_name'] }}</td>
        </tr>
        <tr>
            <th>RUT</th>
            <td>{{ $data['rut'] }}</td>
        </tr>
        <tr>
            <th>Nº Registro</th>
            <td>{{ $data['registration_number'] }}</td>
        </tr>
        <tr>
            <th>Email</th>
            <td>{{ $data['email'] }}</td>
        </tr>
        <tr>
            <th>Teléfono</th>
            <td>{{ $data['phone'] }}</td>
        </tr>
        <tr>
            <th>Dirección</th>
            <td>{{ $data['address'] }}</td>
        </tr>
        <tr>
            <th>Región</th>
            <td>{{ $data['region'] }}</td>
        </tr>
        <tr>
            <th>Comuna</th>
            <td>{{ $data['commune'] }}</td>
        </tr>
    </table>

    {{-- Datos del Apiario --}}
    <h2>2. Datos del Apiario</h2>

    <table>
        <tr>
            <th>Nombre Apiario</th>
            <td>{{ $data['apiary_name'] }}</td>
        </tr>
        <tr>
            <th>Código</th>
            <td>{{ $data['apiary_number'] }}</td>
        </tr>
        <tr>
            <th>Actividad / Objetivo</th>
            <td>{{ $data['activity'] }}</td>
        </tr>
        <tr>
            <th>Fecha de Instalación</th>
            <td>{{ $data['installation_date'] }}</td>
        </tr>
        <tr>
            <th>Latitud / Longitud</th>
            <td>{{ $data['latitude'] }}, {{ $data['longitude'] }}</td>
        </tr>
        <tr>
            <th>UTM X / Y</th>
            <td>{{ $data['utm_x'] }} / {{ $data['utm_y'] }}</td>
        </tr>
        <tr>
            <th>Huso UTM</th>
            <td>{{ $data['utm_huso'] }}</td>
        </tr>
        <tr>
            <th>Trashumante</th>
            <td>{{ $data['nomadic'] }}</td>
        </tr>
        <tr>
            <th>N° Colmenas</th>
            <td>{{ $data['hive_count'] }}</td>
        </tr>
        <tr>
            <th>Foto del Apiario</th>
            <td class="image-container">
                @if(!empty($data['foto_base64']))
                    {{-- Si ya tienes la imagen en base64 --}}
                    <img src="{{ $data['foto_base64'] }}" alt="Foto del Apiario" style="max-height:120px; width:auto;">
                @elseif(!empty($data['foto']))
                    {{-- Convertir imagen a base64 SOLO SI ES FORMATO COMPATIBLE --}}
                    @php
                        $fotoPath = storage_path('app/public/' . $data['foto']);
                        $fotoBase64 = null;
                        if (file_exists($fotoPath)) {
                            try {
                                $mimeType = mime_content_type($fotoPath);
                                // Solo procesar formatos compatibles con dompdf
                                $allowedMimes = ['image/jpeg', 'image/jpg', 'image/gif'];
                                if (in_array($mimeType, $allowedMimes)) {
                                    $imageData = file_get_contents($fotoPath);
                                    $fotoBase64 = 'data:' . $mimeType . ';base64,' . base64_encode($imageData);
                                }
                            } catch (\Exception $e) {
                                // Si hay error, continuar sin imagen
                            }
                        }
                    @endphp
                    @if($fotoBase64)
                        <img src="{{ $fotoBase64 }}" alt="Foto del Apiario" style="max-height:120px; width:auto;">
                    @else
                        <div class="no-image">
                            @if(file_exists(storage_path('app/public/' . $data['foto'])))
                                Formato de imagen no compatible
                            @else
                                Sin imagen disponible
                            @endif
                        </div>
                    @endif
                @else
                    <div class="no-image">Sin imagen disponible</div>
                @endif
            </td>
        </tr>
    </table>

    {{-- Sección de Firma --}}
    <div class="signature-section clearfix">
        <div class="signature-container">
            <div class="signature-label">FIRMA DEL REPRESENTANTE LEGAL</div>
            <div class="signature-box">
                @if(!empty($data['firma_base64']))
                    <img src="{{ $data['firma_base64'] }}" alt="Firma" style="max-height:55px; max-width:230px;">
                @elseif(!empty($data['firma']))
                    @php
                        $firmaPath = storage_path('app/public/firmas/' . $data['firma']);
                        $firmaBase64 = null;
                        if (file_exists($firmaPath)) {
                            try {
                                $mimeType = mime_content_type($firmaPath);
                                // Solo procesar formatos compatibles con dompdf
                                $allowedMimes = ['image/jpeg', 'image/jpg', 'image/gif'];
                                if (in_array($mimeType, $allowedMimes)) {
                                    $imageData = file_get_contents($firmaPath);
                                    $firmaBase64 = 'data:' . $mimeType . ';base64,' . base64_encode($imageData);
                                }
                            } catch (\Exception $e) {
                                // Si hay error, continuar sin imagen
                            }
                        }
                    @endphp
                    @if($firmaBase64)
                        <img src="{{ $firmaBase64 }}" alt="Firma" style="max-height:55px; max-width:230px;">
                    @endif
                @endif
            </div>
            <div class="signature-info">
                <strong>{{ $data['legal_representative'] }} {{ $data['last_name'] }}</strong><br>
                RUT: {{ $data['rut'] }}<br>
                Representante Legal
            </div>
        </div>
    </div>

</body>

</html>