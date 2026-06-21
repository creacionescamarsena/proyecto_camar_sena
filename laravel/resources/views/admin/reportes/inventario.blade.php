@extends('layouts.app_admin')
@section('title', 'Reporte de inventario')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
  <div>
    <h4 class="mb-1">Reporte de inventario</h4>
    <p class="text-muted small mb-0">Filtra productos y descarga la misma tabla en PDF.</p>
  </div>
  <a href="{{ route('admin.productos') }}" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left me-1"></i> Atras
  </a>
</div>

<form method="GET" class="card card-custom p-3 mb-4">
  <div class="row g-3 align-items-end">
    <div class="col-md-2">
      <label class="form-label small">Buscar</label>
      <input type="text" name="buscar" value="{{ request('buscar') }}" class="form-control" placeholder="Modelo">
    </div>
    <div class="col-md-2">
      <label class="form-label small">Categoria</label>
      <select name="categoria_id" class="form-select">
        <option value="">Todas</option>
        @foreach($categorias as $categoria)
          <option value="{{ $categoria->id_categoria }}" @selected(request('categoria_id') == $categoria->id_categoria)>{{ $categoria->tipo_categoria }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-2">
      <label class="form-label small">Estado producto</label>
      <select name="estado" class="form-select">
        <option value="">Todos</option>
        <option value="Activo" @selected(request('estado') === 'Activo')>Activo</option>
        <option value="Inactivo" @selected(request('estado') === 'Inactivo')>Inactivo</option>
      </select>
    </div>
    <div class="col-md-2">
      <label class="form-label small">Estado stock</label>
      <select name="stock_estado" class="form-select">
        <option value="">Todos</option>
        <option value="Disponible" @selected(request('stock_estado') === 'Disponible')>Disponible</option>
        <option value="Bajo" @selected(request('stock_estado') === 'Bajo')>Bajo</option>
        <option value="Agotado" @selected(request('stock_estado') === 'Agotado')>Agotado</option>
      </select>
    </div>
    <div class="col-md-1">
      <label class="form-label small">Min</label>
      <input type="number" name="stock_min" value="{{ request('stock_min') }}" class="form-control" min="0">
    </div>
    <div class="col-md-1">
      <label class="form-label small">Max</label>
      <input type="number" name="stock_max" value="{{ request('stock_max') }}" class="form-control" min="0">
    </div>
    <div class="col-md-2 d-flex gap-2">
      <button class="btn btn-main" title="Filtrar"><i class="bi bi-funnel"></i>Filtrar</button>
      <a href="{{ route('admin.reportes.inventario') }}" class="btn btn-outline-secondary" title="Limpiar"><i class="bi bi-x-lg"></i></a>
    </div>
  </div>
</form>

<div class="row g-3 mb-4">
  <div class="col-md-3"><div class="card card-custom p-3"><span class="small text-muted">Productos</span><strong class="fs-4">{{ $resumen['total'] }}</strong></div></div>
  <div class="col-md-3"><div class="card card-custom p-3"><span class="small text-muted">Disponibles</span><strong class="fs-4">{{ $resumen['disponibles'] }}</strong></div></div>
  <div class="col-md-3"><div class="card card-custom p-3"><span class="small text-muted">Stock bajo</span><strong class="fs-4">{{ $resumen['bajos'] }}</strong></div></div>
  <div class="col-md-3"><div class="card card-custom p-3"><span class="small text-muted">Stock total</span><strong class="fs-4">{{ $resumen['stock_total'] }}</strong></div></div>
</div>

<div class="card card-custom p-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h6 class="mb-0">Productos filtrados</h6>
    <a href="{{ route('admin.reportes.inventario.pdf', request()->query()) }}" class="btn btn-main btn-sm">
      <i class="bi bi-file-earmark-pdf me-1"></i> PDF
    </a>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead class="table-light">
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
            <td><strong>{{ $producto->modelo }}</strong></td>
            <td>{{ $producto->categoria }}</td>
            <td>${{ number_format($producto->precio, 0, ',', '.') }}</td>
            <td>{{ $producto->stock_total }}</td>
            <td class="small">{{ $producto->tallas ?: '-' }}</td>
            <td><span class="badge {{ $producto->estado_stock === 'Disponible' ? 'bg-success' : ($producto->estado_stock === 'Bajo' ? 'bg-warning text-dark' : 'bg-danger') }}">{{ $producto->estado_stock }}</span></td>
          </tr>
        @empty
          <tr><td colspan="6" class="text-center text-muted py-4">No hay datos para estos filtros.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
{{ $productos->links() }}
@endsection
