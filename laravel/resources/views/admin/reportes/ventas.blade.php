@extends('layouts.app_admin')
@section('title', 'Reporte de ventas')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
  <div>
    <h4 class="mb-1">Reporte de ventas</h4>
    <p class="text-muted small mb-0">Facturas filtradas por fecha, cliente y estado de envio.</p>
  </div>
  <a href="{{ route('admin.reportes.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Atras</a>
</div>

<form method="GET" class="card card-custom p-3 mb-4">
  <div class="row g-3 align-items-end">
    <div class="col-md-3">
      <label class="form-label small">Buscar</label>
      <input name="buscar" value="{{ request('buscar') }}" class="form-control" placeholder="Cliente o factura">
    </div>
    <div class="col-md-2">
      <label class="form-label small">Desde</label>
      <input type="date" name="desde" value="{{ request('desde') }}" class="form-control">
    </div>
    <div class="col-md-2">
      <label class="form-label small">Hasta</label>
      <input type="date" name="hasta" value="{{ request('hasta') }}" class="form-control">
    </div>
    <div class="col-md-3">
      <label class="form-label small">Estado envio</label>
      <select name="estado_envio" class="form-select">
        <option value="">Todos</option>
        @foreach($estadosEnvio as $estado)
          <option value="{{ $estado }}" @selected(request('estado_envio') === $estado)>{{ $estado }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-2 d-flex gap-2">
      <button class="btn btn-primary"><i class="bi bi-funnel me-1"></i> Filtrar</button>
      <a href="{{ route('admin.reportes.ventas') }}" class="btn btn-outline-secondary" title="Limpiar"><i class="bi bi-x-lg"></i></a>
    </div>
  </div>
</form>

<div class="card card-custom p-3 mb-4">
  <span class="text-muted small">Total filtrado</span>
  <strong class="fs-3">${{ number_format($totalVentas, 0, ',', '.') }}</strong>
</div>

<div class="card card-custom p-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h6 class="mb-0">Ventas encontradas</h6>
    <a href="{{ route('admin.reportes.ventas.pdf', request()->query()) }}" class="btn btn-danger btn-sm"><i class="bi bi-file-earmark-pdf me-1"></i> PDF</a>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead class="table-light">
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
            <td><strong>${{ number_format((float) $venta->total, 0, ',', '.') }}</strong></td>
          </tr>
        @empty
          <tr><td colspan="5" class="text-center text-muted py-4">No hay datos para estos filtros.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  {{ $ventas->links() }}
</div>
@endsection
