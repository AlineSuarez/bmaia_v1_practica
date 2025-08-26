<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura Nº {{ $numero }}</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #222; margin: 20px; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h1 { margin: 0; font-size: 18px; text-transform: uppercase; }
        .header small { color: #555; font-size: 11px; }

        .section-title { background: #f0f0f0; padding: 5px 8px; font-weight: bold; border: 1px solid #ccc; margin: 10px 0 6px; font-size: 11px; }
        .grid { width: 100%; display: table; }
        .col { display: table-cell; vertical-align: top; width: 50%; padding: 0 6px; }

        .box { border: 1px solid #ccc; padding: 6px 8px; border-radius: 3px; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 4px; }
        th, td { border: 1px solid #ddd; padding: 5px; font-size: 11px; }
        th { background: #f9f9f9; text-align: left; }
        tr:nth-child(even) { background: #fcfcfc; }

        .right { text-align: right; }
        .totals { margin-left: auto; margin-top: 8px; width: 50%; border-top: 2px solid #444; }
        .totals td { border: none; font-size: 11px; }
        .totals .label { text-align: right; padding-right: 8px; }
        .totals .value { text-align: right; width: 120px; }
        .totals strong { font-size: 12px; }

        .footer { margin-top: 15px; font-size: 10px; text-align: center; color: #666; border-top: 1px solid #ccc; padding-top: 6px; }
    </style>
</head>
<body>

<div class="header">
    <h1>Factura Electrónica</h1>
    <small>Nº {{ $numero }} &nbsp; | &nbsp; Emisión: {{ now()->format('d/m/Y') }}</small>
</div>

<div class="grid">
    <div class="col">
        <div class="section-title">Emisor</div>
        <div class="box">
            <strong>B-MaiA</strong><br>
            RUT: 78.023.662-0<br>
            Contacto: soporte@bmaia.cl
        </div>
    </div>
    <div class="col">
        <div class="section-title">Receptor</div>
        <div class="box">
            @php $s = $snapshot ?? []; @endphp
            <strong>{{ $s['razon_social'] ?? ($user->razon_social ?? $user->name) }}</strong><br>
            RUT: {{ $s['rut'] ?? ($user->rut ?? '—') }}<br>
            Giro: {{ $s['giro'] ?? '—' }}<br>
            Dirección: {{ $s['direccion_comercial'] ?? ($user->direccion ?? '—') }}<br>
            Ciudad: {{ $s['ciudad'] ?? '—' }}<br>
            Región: {{ $s['region'] ?? ($s['region_nombre'] ?? '—') }}<br>
            Correo DTE: {{ $s['correo_envio_dte'] ?? ($s['correo'] ?? $user->email) }}
        </div>
    </div>
</div>

<div class="section-title">Detalle</div>
<table>
    <thead>
        <tr>
            <th>Descripción</th>
            <th class="right">Cantidad</th>
            <th class="right">P. Unitario</th>
            <th class="right">Subtotal</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Plan {{ strtoupper($payment->plan ?? '—') }} — Suscripción anual</td>
            <td class="right">1</td>
            <td class="right">${{ number_format((int)$montoNeto, 0, ',', '.') }}</td>
            <td class="right">${{ number_format((int)$montoNeto, 0, ',', '.') }}</td>
        </tr>
    </tbody>
</table>

<table class="totals">
    <tr>
        <td class="label">Neto</td>
        <td class="value">${{ number_format((int)$montoNeto, 0, ',', '.') }}</td>
    </tr>
    <tr>
        <td class="label">IVA (19%)</td>
        <td class="value">${{ number_format((int)$montoIva, 0, ',', '.') }}</td>
    </tr>
    <tr>
        <td class="label"><strong>Total</strong></td>
        <td class="value"><strong>${{ number_format((int)$montoTotal, 0, ',', '.') }}</strong></td>
    </tr>
</table>

<div class="footer">
    Documento generado por B-MaiA.<br>
    Este PDF corresponde a una representación y no reemplaza la DTE oficial autorizada por el SII.
</div>

</body>
</html>
