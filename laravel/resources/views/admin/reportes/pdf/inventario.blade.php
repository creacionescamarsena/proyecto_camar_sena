<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <style>
    body { font-family: DejaVu Sans, sans-serif; color: #202124; font-size: 10px; }
    h1 { font-size: 20px; margin: 0 0 4px; }
    .muted { color: #6b7280; }
    .header { border-bottom: 2px solid #111827; padding-bottom: 10px; margin-bottom: 14px; }
    .summary { margin-bottom: 12px; }
    .chip { display: inline-block; border: 1px solid #d1d5db; padding: 4px 8px; margin: 0 6px 6px 0; border-radius: 3px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #d1d5db; padding: 6px; vertical-align: top; }
    th { background: #f3f4f6; font-weight: bold; }
  </style>
</head>
<body>
  <div class="header">
    <h1>{{ $titulo }}</h1>
    <div class="muted">Creaciones Camar - Generado el {{ now()->format('d/m/Y H:i') }}</div>
  </div>

  <div class="summary">
    <span class="chip">Productos: {{ $resumen['total'] }}</span>
    <span class="chip">Disponibles: {{ $resumen['disponibles'] }}</span>
    <span class="chip">Stock bajo: {{ $resumen['bajos'] }}</span>
    <span class="chip">Agotados: {{ $resumen['agotados'] }}</span>
    <span class="chip">Stock total: {{ $resumen['stock_total'] }}</span>
  </div>

  @if(count($filtros))
    <p class="muted">Filtros: {{ collect($filtros)->map(fn($v, $k) => $k . ': ' . $v)->implode(' | ') }}</p>
  @endif

  <table>
    <thead>
      <tr>
        <th>Modelo</th>
        <th>Categoria</th>
        <th>Precio</th>
        <th>Stock</th>
        <th>Tallas</th>
        <th>Estado</th>
      </tr>
    </thead>
    <tbody>
      @forelse($productos as $producto)
        <tr>
          <td>{{ $producto->modelo }}</td>
          <td>{{ $producto->categoria }}</td>
          <td>${{ number_format($producto->precio, 0, ',', '.') }}</td>
          <td>{{ $producto->stock_total }}</td>
          <td>{{ $producto->tallas ?: '-' }}</td>
          <td>{{ $producto->estado_stock }}</td>
        </tr>
      @empty
        <tr><td colspan="6">No hay datos para estos filtros.</td></tr>
      @endforelse
    </tbody>
  </table>
</body>
</html>
