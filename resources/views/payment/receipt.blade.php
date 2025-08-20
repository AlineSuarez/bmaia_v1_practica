<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<style>
  body { font-family: sans-serif; font-size: 12px; color:#111; }
  h1 { text-align:center; font-size:16px; margin:0 0 6px 0; }
  .muted { color:#666; font-size:11px; text-align:center; margin-bottom:8px; }
  table { width:100%; border-collapse:collapse; margin-top:10px;}
  th,td { border:1px solid #ddd; padding:6px; }
  th { background:#f5f5f5; }
  .right { text-align:right; }
</style>
</head>
<body>
  <h1>Comprobante de Pago</h1>
  <div class="muted">Este comprobante respalda el pago con tarjeta (boleta/voucher). No reemplaza factura.</div>

  <table>
    <tr><th>Nº Transacción / Folio</th><td>{{ $payment->receipt_number ?? $payment->buy_order }}</td></tr>
    <tr><th>Fecha y hora</th><td>{{ optional($payment->receipt_issued_at)->format('d/m/Y H:i') }}</td></tr>
    <tr><th>Monto total</th><td>${{ number_format($payment->amount,0,',','.') }} CLP</td></tr>
    <tr><th>Medio de pago</th><td>{{ $payment->receipt_payment_method ?? 'Tarjeta' }}</td></tr>
    <tr><th>Emisor</th><td>{{ $empresa['razon'] }} — RUT {{ $empresa['rut'] }}</td></tr>
    <tr><th>Cliente</th><td>{{ $user->email }}</td></tr>
  </table>

  <h3>Detalle</h3>
  <table>
    <thead><tr><th>Descripción</th><th class="right">Cant.</th><th class="right">Precio</th><th class="right">Total</th></tr></thead>
    <tbody>
      @foreach(($payment->receipt_items ?? []) as $it)
        <tr>
          <td>{{ $it['desc'] }}</td>
          <td class="right">{{ $it['qty'] }}</td>
          <td class="right">${{ number_format($it['price'],0,',','.') }}</td>
          <td class="right">${{ number_format($it['qty']*$it['price'],0,',','.') }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>

  <p class="muted">Ante consultas: contacto@bmaia.cl</p>
</body>
</html>
