@extends('layouts.app_admin')
@section('title', 'Reporte de envios')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
  <div>
    <h4 class="mb-1">Reporte de envios</h4>
    <p class="text-muted small mb-0">Seguimiento filtrado por pedido, cliente, transportadora y estado.</p>
  </div>
  <a href="{{ route('admin.envios') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Atras</a>
</div>

<form method="GET" class="card card-custom p-3 mb-4">
  <div class="row g-3 align-items-end">
    <div class="col-md-2">
      <label class="form-label small">Buscar</label>
      <input name="buscar" value="{{ request('buscar') }}" class="form-control" placeholder="Cliente, pedido o envio">
    </div>
    <div class="col-md-2">
      <label class="form-label small">Estado</label>
      <select name="estado_envio" class="form-select">
        <option value="">Todos</option>
        @foreach($estadosEnvio as $estado)
          <option value="{{ $estado }}" @selected(request('estado_envio') === $estado)>{{ $estado }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-2">
      <label class="form-label small">Transportadora</label>
      <input name="transportadora" value="{{ request('transportadora') }}" class="form-control" placeholder="Empresa">
    </div>
    <div class="col-md-2">
      <label class="form-label small">Desde</label>
      <input type="date" name="desde" value="{{ request('desde') }}" class="form-control">
    </div>
    <div class="col-md-2">
      <label class="form-label small">Hasta</label>
      <input type="date" name="hasta" value="{{ request('hasta') }}" class="form-control">
    </div>
    <div class="col-md-2 d-flex gap-2">
      <button class="btn btn-main"><i class="bi bi-funnel me-1"></i> Filtrar</button>
      <a href="{{ route('admin.reportes.envios') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg me-1"></i></a>
    </div>
  </div>
</form>

<div class="card card-custom p-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h6 class="mb-0">Envios encontrados</h6>
    <a href="{{ route('admin.reportes.envios.pdf', request()->query()) }}" class="btn btn-main btn-sm"><i class="bi bi-file-earmark-pdf me-1"></i> PDF</a>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead class="table-light">
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
            <td><span class="badge bg-info text-dark">{{ $envio->tipo_envio }}</span></td>
          </tr>
        @empty
          <tr><td colspan="6" class="text-center text-muted py-4">No hay datos para estos filtros.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  {{ $envios->links() }}
</div>
@endsection
