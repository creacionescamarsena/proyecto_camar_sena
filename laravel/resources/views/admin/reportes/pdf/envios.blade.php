<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <style>
    body { font-family: DejaVu Sans, sans-serif; color: #202124; font-size: 10px; }
    h1 { font-size: 20px; margin: 0 0 4px; }
    .muted { color: #6b7280; }
    .header { border-bottom: 2px solid #111827; padding-bottom: 10px; margin-bottom: 14px; }
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

  <table>
    <thead>
      <tr>
        <th>Envio</th>
        <th>Pedido</th>
        <th>Cliente</th>
        <th>Destino</th>
        <th>Transportadora</th>
        <th>Estado</th>
      </tr>
    </thead>
    <tbody>
      @forelse($envios as $envio)
        <tr>
          <td>ENV-{{ str_pad($envio->id_envio, 3, '0', STR_PAD_LEFT) }}</td>
          <td>PED-{{ str_pad($envio->facturacion_id_facturacion ?? 0, 3, '0', STR_PAD_LEFT) }}</td>
          <td>{{ trim(($envio->nombres ?? '') . ' ' . ($envio->apellidos ?? '')) ?: ($envio->correo ?? 'Cliente no registrado') }}</td>
          <td>{{ trim(($envio->direccion ?? '') . ' ' . ($envio->ciudad ?? '')) ?: 'Sin destino' }}</td>
          <td>{{ $envio->empresa_transportadora ?: 'Sin empresa' }}</td>
          <td>{{ $envio->tipo_envio }}</td>
        </tr>
      @empty
        <tr><td colspan="6">No hay datos para estos filtros.</td></tr>
      @endforelse
    </tbody>
  </table>
</body>
</html>
