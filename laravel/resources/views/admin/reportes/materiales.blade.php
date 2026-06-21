@extends('layouts.app_admin')
@section('title', 'Reporte de materiales')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
  <div>
    <h4 class="mb-1">Reporte de materiales</h4>
    <p class="text-muted small mb-0">Control de materiales por proveedor, cantidad y estado.</p>
  </div>
  <a href="{{ route('admin.materiales.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Atras</a>
</div>

<form method="GET" class="card card-custom p-3 mb-4">
  <div class="row g-3 align-items-end">
    <div class="col-md-3">
      <label class="form-label small">Buscar</label>
      <input name="buscar" value="{{ request('buscar') }}" class="form-control" placeholder="Material">
    </div>
    <div class="col-md-3">
      <label class="form-label small">Proveedor</label>
      <select name="proveedor" class="form-select">
        <option value="">Todos</option>
        @foreach($proveedores as $proveedor)
          <option value="{{ $proveedor }}" @selected(request('proveedor') === $proveedor)>{{ $proveedor }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-2">
      <label class="form-label small">Estado</label>
      <select name="estado" class="form-select">
        <option value="">Todos</option>
        <option value="Activo" @selected(request('estado') === 'Activo')>Activo</option>
        <option value="Inactivo" @selected(request('estado') === 'Inactivo')>Inactivo</option>
      </select>
    </div>
    <div class="col-md-1">
      <label class="form-label small">Min</label>
      <input type="number" name="cantidad_min" value="{{ request('cantidad_min') }}" class="form-control" min="0">
    </div>
    <div class="col-md-1">
      <label class="form-label small">Max</label>
      <input type="number" name="cantidad_max" value="{{ request('cantidad_max') }}" class="form-control" min="0">
    </div>
    <div class="col-md-2 d-flex gap-2">
      <button class="btn btn-main"><i class="bi bi-funnel me-1"></i> Filtrar</button>
      <a href="{{ route('admin.reportes.materiales') }}" class="btn btn-outline-secondary" title="Limpiar"><i class="bi bi-x-lg"></i></a>
    </div>
  </div>
</form>

<div class="card card-custom p-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h6 class="mb-0">Materiales encontrados</h6>
    <a href="{{ route('admin.reportes.materiales.pdf', request()->query()) }}" class="btn btn-main btn-sm"><i class="bi bi-file-earmark-pdf me-1"></i> PDF</a>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead class="table-light">
        <tr>
          <th>Material</th>
          <th>Proveedor</th>
          <th>Precio</th>
          <th>Cantidad</th>
          <th>Estado</th>
        </tr>
      </thead>
      <tbody>
        @forelse($materiales as $material)
          <tr>
            <td><strong>{{ $material->material }}</strong></td>
            <td>{{ $material->proveedor ?? 'Sin proveedor' }}</td>
            <td>${{ number_format((float) ($material->precio ?? 0), 0, ',', '.') }}</td>
            <td>{{ (int) ($material->cantidad ?? 0) }}</td>
            <td><span class="badge {{ ($material->estado ?? 'Activo') === 'Activo' ? 'bg-success' : 'bg-secondary' }}">{{ $material->estado ?? 'Activo' }}</span></td>
          </tr>
        @empty
          <tr><td colspan="5" class="text-center text-muted py-4">No hay datos para estos filtros.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  {{ $materiales->links() }}
</div>
@endsection
