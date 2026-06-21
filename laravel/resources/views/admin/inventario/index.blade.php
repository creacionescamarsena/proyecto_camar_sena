@extends('layouts.app_admin')
@section('title', 'Inventario')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-1">
  <div>
    <h4 class="mb-0">Inventario</h4>
    <p class="text-muted small mb-0">Gestiona el stock de productos por tallas</p>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('admin.reportes.inventario') }}" class="btn btn-outline-secondary">
      <i class="bi bi-bar-chart-line me-1"></i> Ver reportes
    </a>
  </div>
</div>

<form method="GET" action="{{ route('admin.inventario.index') }}" class="card card-custom p-3 mt-3">
  <div class="row g-2 align-items-center">
    <div class="col-12 col-md">
      <div class="input-group">
        <span class="input-group-text"><i class="bi bi-search"></i></span>
        <input type="search" name="buscar" value="{{ request('buscar') }}" class="form-control" placeholder="Buscar por producto, categoria o ID">
      </div>
    </div>
    <div class="col-12 col-md-auto d-flex gap-2">
      <button type="submit" class="btn btn-main"><i class="bi bi-search me-1"></i> Buscar</button>
      @if(request('buscar'))
        <a href="{{ route('admin.inventario.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg me-1"></i> Limpiar</a>
      @endif
    </div>
  </div>
</form>

<!-- Tarjetas -->
<div class="row g-3 mb-4 mt-1">
  <div class="col-12 col-md-4">
    <div class="card card-custom p-3">
      <h6>Ventas totales</h6>
      <p>{{ $ventasTotales ?? '$0' }}</p>
    </div>
  </div>
  <div class="col-12 col-md-4">
    <div class="card card-custom p-3">
      <h6>Envíos pendientes</h6>
      <p>{{ $enviosPendientes ?? 0 }}</p>
    </div>
  </div>
  <div class="col-12 col-md-4">
    <div class="card card-custom p-3">
      <h6>Total productos</h6>
      <p>{{ $totalProductos ?? 0 }}</p>
    </div>
  </div>
</div>

<!-- Tabla inventario -->
<div class="card card-custom p-3">
  <h6 class="mb-3">Lista de inventario</h6>
  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead class="table-light">
        <tr>
          <th>Producto</th>
          @foreach($tallasList as $tallaId => $tallaNombre)
            <th>Talla {{ $tallaNombre }}</th>
          @endforeach
          <th>Total</th>
          <th>Estado</th>
        </tr>
      </thead>
      <tbody>
        @forelse($inventario as $item)
          <tr>
            <td>{{ $item->nombre }}</td>
            @foreach($tallasList as $tallaId => $tallaNombre)
              <td>{{ $item->stock_por_talla[$tallaNombre] ?? 0 }}</td>
            @endforeach
            <td>{{ $item->stock_total }}</td>
            <td>
              @php
                $clase = $item->stock_total >= 10 ? 'bg-success' : 'bg-warning text-dark';
              @endphp
              <span class="badge {{ $clase }}">{{ $item->estado_stock }}</span>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" class="text-center text-muted py-5">
              <i class="bi bi-clipboard fs-2 d-block mb-2"></i>
              No hay registros en el inventario.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@if($inventario instanceof \Illuminate\Contracts\Pagination\Paginator)
  <div class="mt-3">
    {{ $inventario->links() }}
  </div>
@endif

@endsection
