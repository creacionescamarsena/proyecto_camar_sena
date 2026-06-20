@extends('layouts.app_emp')
@section('title', 'Productos')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-1">
  <div>
    <h4 class="mb-0">Productos</h4>
    <p class="text-muted small mb-0">Gestiona el catálogo de productos</p>
  </div>
</div>

<div class="row g-3 mt-2">
  @forelse($productos as $producto)
    <div class="col-12 col-md-6">
      <div class="card card-custom p-3 producto-card">
        <div class="d-flex justify-content-between align-items-start mb-2">
          <div>
            <h6 class="mb-0 fw-bold">{{ $producto->nombre }}</h6>
            <small class="text-muted">{{ $producto->categoria }}</small>
          </div>
          <div class="d-flex gap-2">
          </div>
        </div>

        <p class="text-muted small mb-1">{{ $producto->descripcion }}</p>
        <p class="fw-semibold mb-2">${{ number_format($producto->precio, 0, ',', '.') }}</p>

        <p class="small mb-1"><strong>Tallas disponibles</strong></p>
        <div class="d-flex gap-2 mb-3 flex-wrap">
          @foreach($producto->tallas ?? [] as $talla)
            <span class="talla-badge">{{ $talla }}</span>
          @endforeach
        </div>

        <p class="small mb-1"><strong>Materiales</strong></p>
        <p class="small text-muted mb-2">
          @forelse($producto->materiales ?? [] as $mat)
            {{ $mat->material ?? $mat->nombre ?? '—' }}{{ !$loop->last ? ', ' : '' }}
          @empty
            <span class="text-muted">Sin materiales especificados</span>
          @endforelse
        </p>

        <div class="d-flex justify-content-between align-items-center stock-bar pt-2">
          <span class="small text-muted">Stock total</span>
          <span class="badge {{ $producto->stock_total > 10 ? 'bg-success' : 'bg-warning text-dark' }}">
            {{ $producto->stock_total }} unidades
          </span>
        </div>
      </div>
    </div>
  @empty
    <div class="col-12">
      <div class="card card-custom p-4 text-center text-muted">
        <i class="bi bi-box fs-2 d-block mb-2"></i>
        No hay productos registrados aún.
      </div>
    </div>
  @endforelse
</div>

@endsection


