<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura Nº {{ $numero }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
        .header { text-align: center; margin-bottom: 10px; }
        .header h1 { margin: 0; font-size: 18px; }
        .header small { color: #666; }
        .grid { width: 100%; display: table; }
        .col { display: table-cell; vertical-align: top; width: 50%; }
        .box { border: 1px solid #ddd; padding: 8px; margin-bottom: 10px; border-radius: 4px; }
        h3 { margin: 0 0 6px; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        th, td { border: 1px solid #ddd; padding: 6px; }
        th { background: #f4f6f8; text-align: left; }
        .right { text-align: right; }
        .muted { color: #666; }
        .totals td { border: 0; }
        .totals .label { text-align: right; padding-right: 8px; }
        .totals .value { text-align: right; width: 140px; }
        .footer { margin-top: 18px; font-size: 10px; text-align: center; color: #666; }
    </style>
</head>
<body>

<div class="header">
    <h1>Factura</h1>
    <small>Nº {{ $numero }} • Emisión: {{ now()->format('d/m/Y') }}</small>
</div>

<div class="grid">
    <div class="col">
        <div class="box">
            <h3>Emisor</h3>
            <div><strong>B‑MaiA</strong></div>
            <div>RUT: 78.023.662-0</div>
            <div>Contacto: soporte@bmaia.cl</div>
        </div>
    </div>
    <div class="col">
        <div class="box">
            <h3>Receptor</h3>
            @php $s = $snapshot ?? []; @endphp
            <div><strong>{{ $s['razon_social'] ?? ($user->razon_social ?? $user->name) }}</strong></div>
            <div>RUT: {{ $s['rut'] ?? ($user->rut ?? '—') }}</div>
            <div>Giro: {{ $s['giro'] ?? '—' }}</div>
            <div>Dirección: {{ $s['direccion_comercial'] ?? ($user->direccion ?? '—') }}</div>
            <div>Ciudad: {{ $s['ciudad'] ?? '—' }}</div>
            <div>Región: {{ $s['region'] ?? ($s['region_nombre'] ?? '—') }}</div>
            <div>Correo DTE: {{ $s['correo_envio_dte'] ?? ($s['correo'] ?? $user->email) }}</div>
        </div>
    </div>
</div>

<div class="box">
    <h3>Detalle</h3>
    <table>
        <thead>
            <tr>
                <th>Descripción</th>
                <th class="right">Cantidad</th>
                <th class="right">P.Unitario</th>
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
</div>

<table class="totals" style="margin-left:auto; margin-top:6px;">
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
    Documento generado por B‑MaiA. Este PDF no reemplaza la DTE autorizada por el SII.
</div>

</body>
</html>
