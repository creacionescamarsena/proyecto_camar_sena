@extends('layouts.app_admin')
@section('title', 'Productos inactivos')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-1">
  <div>
    <h4 class="mb-0">Productos inactivos</h4>
    <p class="text-muted small mb-0">Reactiva productos retirados del catalogo</p>
  </div>
  <a href="{{ route('admin.productos') }}" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left me-1"></i> Volver a activos
  </a>
</div>

<form method="GET" action="{{ route('admin.productos.inactivos') }}" class="card card-custom p-3 mt-3">
  <div class="row g-2 align-items-center">
    <div class="col-12 col-md">
      <div class="input-group">
        <span class="input-group-text"><i class="bi bi-search"></i></span>
        <input type="search" name="buscar" value="{{ request('buscar') }}" class="form-control" placeholder="Buscar por producto, categoria, ID o precio">
      </div>
    </div>
    <div class="col-12 col-md-auto d-flex gap-2">
      <button type="submit" class="btn btn-main"><i class="bi bi-search me-1"></i> Buscar</button>
      @if(request('buscar'))
        <a href="{{ route('admin.productos.inactivos') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg me-1"></i> Limpiar</a>
      @endif
    </div>
  </div>
</form>

<div class="row g-3 mt-2">
  @forelse($productos as $producto)
    @php
      $nombreProd = $producto->nombre ?? '';
      $categoriaProd = $producto->categoria ?? 'Sin categoria';
    @endphp
    <div class="col-12 col-md-6">
      <div class="card card-custom p-3 producto-card">
        <div class="d-flex justify-content-between align-items-start mb-2">
          <div>
            <h6 class="mb-0 fw-bold">{{ $nombreProd }}</h6>
            <small class="text-muted">{{ $categoriaProd }}</small>
          </div>
          <form method="POST" action="{{ route('admin.productos.reactivar', $producto->id) }}">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-sm btn-outline-success">
              <i class="bi bi-arrow-counterclockwise"></i> Reactivar
            </button>
          </form>
        </div>

        @if($producto->imagen)
          <img src="{{ asset('storage/' . $producto->imagen) }}" alt="{{ $nombreProd }}" class="img-fluid rounded mb-2" style="max-height:180px; object-fit:cover;">
        @endif

        <p class="text-muted small mb-1">{{ $producto->descripcion }}</p>
        <p class="fw-semibold mb-2">${{ number_format($producto->precio, 0, ',', '.') }}</p>

        <div class="d-flex justify-content-between align-items-center stock-bar pt-2">
          <span class="small text-muted">Stock total</span>
          <span class="badge bg-secondary">{{ $producto->stock_total }} unidades</span>
        </div>
      </div>
    </div>
  @empty
    <div class="col-12">
      <div class="card card-custom p-4 text-center text-muted">
        <i class="bi bi-box-seam fs-2 d-block mb-2"></i>
        No hay productos inactivos.
      </div>
    </div>
  @endforelse
</div>

@if($productos instanceof \Illuminate\Contracts\Pagination\Paginator)
  <div class="mt-3">
    {{ $productos->links() }}
  </div>
@endif

@endsection
