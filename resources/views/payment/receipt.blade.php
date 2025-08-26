<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <style>
    body { font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #222; margin: 15px 25px; }
    h1 { text-align: center; font-size: 16px; margin-bottom: 3px; text-transform: uppercase; }
    .subtitle { text-align: center; color: #555; font-size: 10px; margin-bottom: 10px; }
    .section-title { background: #f0f0f0; padding: 4px 6px; font-weight: bold; border: 1px solid #ccc; margin: 10px 0 4px; font-size: 11px; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
    th, td { border: 1px solid #ccc; padding: 4px 6px; font-size: 10.5px; }
    th { background: #fafafa; font-weight: bold; text-align: left; }
    .right { text-align: right; }
    .center { text-align: center; }
    .footer { font-size: 10px; color: #666; text-align: center; margin-top: 12px; border-top: 1px solid #ccc; padding-top: 5px; }
  </style>
</head>
<body>
  <h1>Comprobante de Pago</h1>
  <div class="subtitle">
    Este comprobante acredita el pago efectuado.<br>
    No constituye factura ni reemplaza los documentos tributarios oficiales.
  </div>

  {{-- Emisor --}}
  <div class="section-title">Emisor</div>
  <table>
    <tr><th>Razón Social</th><td>{{ $empresa['razon'] ?? '—' }}</td></tr>
    <tr><th>RUT</th><td>{{ $empresa['rut'] ?? '—' }}</td></tr>
    <tr><th>Contacto</th><td>{{ $empresa['correo'] ?? 'contacto@bmaia.cl' }}</td></tr>
  </table>

  {{-- Cliente --}}
  <div class="section-title">Cliente</div>
  <table>
    <tr><th>Nombre</th><td>{{ $user->name ?? $user->email ?? '—' }}</td></tr>
    <tr><th>Correo</th><td>{{ $user->email ?? '—' }}</td></tr>
    <tr><th>RUT</th><td>{{ $user->rut ?? '—' }}</td></tr>
  </table>

  {{-- Transacción --}}
  <div class="section-title">Transacción</div>
  <table>
    <tr><th>Nº Transacción</th><td>{{ $payment->receipt_number ?? $payment->buy_order ?? '—' }}</td></tr>
    <tr><th>Fecha</th><td>{{ optional($payment->receipt_issued_at)->format('d/m/Y H:i') ?? '—' }}</td></tr>
    <tr><th>Medio de pago</th><td>{{ $payment->receipt_payment_method ?? 'Tarjeta' }}</td></tr>
  </table>

  {{-- Detalle --}}
  <div class="section-title">Detalle</div>
  <table>
    <thead>
      <tr>
        <th>Descripción</th>
        <th class="right">Cant.</th>
        <th class="right">Precio</th>
        <th class="right">Total</th>
      </tr>
    </thead>
    <tbody>
      @forelse(($payment->receipt_items ?? []) as $it)
      <tr>
        <td>{{ $it['desc'] ?? '—' }}</td>
        <td class="right">{{ $it['qty'] ?? 1 }}</td>
        <td class="right">${{ number_format($it['price'] ?? 0, 0, ',', '.') }}</td>
        <td class="right">${{ number_format(($it['qty'] ?? 1) * ($it['price'] ?? 0), 0, ',', '.') }}</td>
      </tr>
      @empty
      <tr><td colspan="4" class="center">Sin ítems</td></tr>
      @endforelse
    </tbody>
  </table>

  {{-- Totales --}}
  <div class="section-title">Totales</div>
  <table>
    <tr>
      <th>Neto</th>
      <th>IVA (19%)</th>
      <th>Total Pagado</th>
    </tr>
    <tr>
      <td class="right">
        ${{ number_format(($payment->amount ?? 0) / 1.19, 0, ',', '.') }} CLP
      </td>
      <td class="right">
        ${{ number_format(($payment->amount ?? 0) - (($payment->amount ?? 0) / 1.19), 0, ',', '.') }} CLP
      </td>
      <td class="right"><strong>
        ${{ number_format($payment->amount ?? 0, 0, ',', '.') }} CLP
      </strong></td>
    </tr>
  </table>


  <div class="footer">
    Ante consultas: {{ $empresa['correo'] ?? 'contacto@bmaia.cl' }}<br>
    Gracias por su preferencia.
  </div>
</body>
</html>
