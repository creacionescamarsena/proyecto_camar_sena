@extends('layouts.app_admin')
@section('title', 'Productos')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-1">
  <div>
    <h4 class="mb-0">Productos</h4>
    <p class="text-muted small mb-0">Gestiona el catálogo de productos</p>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('admin.productos.inactivos') }}" class="btn btn-outline-secondary">
      <i class="bi bi-archive me-1"></i> Ver inactivos
    </a>
    <a href="{{ route('admin.reportes.inventario') }}" class="btn btn-outline-secondary">
      <i class="bi bi-bar-chart-line me-1"></i> Reporte stock
    </a>
    <a href="{{ route('admin.productos.create') }}" class="btn btn-main">
      <i class="bi bi-plus-circle me-1"></i> Nuevo producto
    </a>
  </div>
</div>

<form method="GET" action="{{ route('admin.productos') }}" class="card card-custom p-3 mt-3">
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
        <a href="{{ route('admin.productos') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg me-1"></i> Limpiar</a>
      @endif
    </div>
  </div>
</form>

<div class="row g-3 mt-2">
  @forelse($productos as $producto)
    @php
      $nombreProd = is_string($producto->nombre) ? $producto->nombre : (is_object($producto->nombre) ? ($producto->nombre->nombre ?? $producto->nombre->material ?? $producto->nombre->modelo_chaqueta ?? '') : ($producto->nombre ?? ''));
      $categoriaProd = is_string($producto->categoria) ? $producto->categoria : (is_object($producto->categoria) ? ($producto->categoria->tipo_categoria ?? $producto->categoria ?? '') : ($producto->categoria ?? ''));
    @endphp
    <div class="col-12 col-md-6">
      <div class="card card-custom p-3 producto-card">
        <div class="d-flex justify-content-between align-items-start mb-2">
          <div>
            <h6 class="mb-0 fw-bold">{{ $nombreProd }}</h6>
            <small class="text-muted">{{ $categoriaProd }}</small>
          </div>
          <div class="d-flex gap-2">
            <a href="{{ route('admin.productos.edit', $producto->id) }}" class="btn btn-sm btn-outline-secondary btn-outline-fixed">Editar</a>
            <button type="button" class="btn btn-sm btn-outline-danger"
              data-delete-url="{{ route('admin.productos.destroy', $producto->id) }}"
              data-delete-name="{{ $nombreProd }}"
              onclick="mostrarModal(this, {!! json_encode($nombreProd) !!})">
              <i class="bi bi-trash"></i>
            </button>
          </div>
        </div>

        @if($producto->imagen)
          <img src="{{ asset('storage/' . $producto->imagen) }}" alt="{{ $nombreProd }}" class="img-fluid rounded mb-2" style="max-height:180px; object-fit:cover;">
        @endif

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
          @foreach($producto->materiales ?? [] as $mat)
            ({{ $mat->nombre ?? $mat->material ?? 'Sin nombre' }}){{ !$loop->last ? ', ' : '' }}
          @endforeach
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

@if($productos instanceof \Illuminate\Contracts\Pagination\Paginator)
  <div class="mt-3">
    {{ $productos->links() }}
  </div>
@endif

@endsection

@push('scripts')
<div id="modalOverlay" class="modal-overlay" onclick="cerrarModal()">
  <div class="modal-confirm" onclick="event.stopPropagation()">
    <p class="fw-semibold fs-5 mb-4">¿Está seguro de eliminar <span id="nombreProducto" class="text-danger"></span>?</p>
    <div class="d-flex justify-content-center gap-4">
      <button class="btn-icon-cancel" onclick="cerrarModal()" title="Cancelar"><i class="bi bi-x-lg"></i></button>
      <form id="formEliminar" method="POST" action="">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn-icon-confirm" title="Confirmar"><i class="bi bi-check-lg"></i></button>
      </form>
    </div>
  </div>
</div>

<script>
  function mostrarModal(buttonOrNombre, nombre) {
    var button = buttonOrNombre instanceof Element ? buttonOrNombre : null;
    var texto = typeof nombre === 'string' && nombre !== '' ? nombre : (button ? button.dataset.deleteName : (buttonOrNombre || ''));

    document.getElementById('nombreProducto').textContent = texto;
    if (button && button.dataset.deleteUrl) {
      document.getElementById('formEliminar').action = button.dataset.deleteUrl;
    }
    document.getElementById('modalOverlay').classList.add('show');
  }

  function cerrarModal() {
    document.getElementById('modalOverlay').classList.remove('show');
  }

  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-delete-url]').forEach(function (button) {
      button.type = 'button';
      button.addEventListener('click', function (event) {
        event.preventDefault();
        mostrarModal(button, button.dataset.deleteName || '');
      });
    });
  });
</script>
@endpush
