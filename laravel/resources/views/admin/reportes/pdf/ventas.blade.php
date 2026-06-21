<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <style>
    body { font-family: DejaVu Sans, sans-serif; color: #202124; font-size: 10px; }
    h1 { font-size: 20px; margin: 0 0 4px; }
    .muted { color: #6b7280; }
    .header { border-bottom: 2px solid #111827; padding-bottom: 10px; margin-bottom: 14px; }
    .total { margin-bottom: 12px; font-size: 13px; font-weight: bold; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #d1d5db; padding: 6px; vertical-align: top; }
    th { background: #f3f4f6; font-weight: bold; }
  </style>
</head>
<body>
  <div class="header">
    <h1>{{ $titulo }}</h1>
    <div class="muted">Creaciones Camar - Generado el {{ now()->format('d/m/Y H:i') }}</div>
    @if(count($filtros))
      <div class="muted">Filtros: {{ collect($filtros)->map(fn($v, $k) => $k . ': ' . $v)->implode(' | ') }}</div>
    @endif
  </div>

  <div class="total">Total filtrado: ${{ number_format($totalVentas, 0, ',', '.') }}</div>

  <table>
    <thead>
      <tr>
        <th>Factura</th>
        <th>Fecha</th>
        <th>Cliente</th>
        <th>Estado envio</th>
        <th>Total</th>
      </tr>
    </thead>
    <tbody>
      @forelse($ventas as $venta)
        <tr>
          <td>PED-{{ str_pad($venta->id_facturacion, 3, '0', STR_PAD_LEFT) }}</td>
          <td>{{ $venta->fecha ? \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') : '-' }}</td>
          <td>{{ trim(($venta->nombres ?? '') . ' ' . ($venta->apellidos ?? '')) ?: ($venta->correo ?? 'Cliente no registrado') }}</td>
          <td>{{ $venta->tipo_envio ?? 'Sin envio' }}</td>
          <td>${{ number_format((float) $venta->total, 0, ',', '.') }}</td>
        </tr>
      @empty
        <tr><td colspan="5">No hay datos para estos filtros.</td></tr>
      @endforelse
    </tbody>
  </table>
</body>
</html>
