@extends('layouts.app_cliente')
@section('title', 'Catálogo')

@section('content')

<!-- Hero Banner -->
<section class="hero-banner">
  <div class="hero-content">
    <p class="hero-tag">Nueva colección 2025</p>
    <h1 class="hero-title">Colección de<br><em>chaquetas</em></h1>
    <p class="hero-sub">Descubre nuestra selección premium de chaquetas para cada ocasión</p>
  </div>
  <div class="hero-decoration"></div>
</section>

<!-- Filtros y catálogo -->
<div class="catalog-container">
  <div class="search-filter-bar">
    <div class="search-wrap">
      <i class="bi bi-search search-icon"></i>
      <input type="text" id="searchInput" class="search-input" placeholder="Buscar chaquetas..." oninput="filtrar()">
    </div>
    <div class="filter-pills">
      <button class="pill active" onclick="filtrarCategoria(this, '')">Todas</button>
      @foreach($categorias as $cat)
        <button class="pill" onclick="filtrarCategoria(this, '{{ $cat }}')">{{ $cat }}</button>
      @endforeach
    </div>
  </div>

  <div class="products-grid" id="productsGrid">
    @forelse($productos as $producto)
      @php
        $prodNombre = is_string($producto->nombre) ? $producto->nombre : (is_object($producto->nombre) ? ($producto->nombre->nombre ?? $producto->nombre->material ?? $producto->nombre->modelo_chaqueta ?? '') : ($producto->nombre ?? ''));
        $prodCategoria = is_string($producto->categoria) ? $producto->categoria : (is_object($producto->categoria) ? ($producto->categoria->tipo_categoria ?? $producto->categoria ?? '') : ($producto->categoria ?? ''));
      @endphp
      <a href="{{ route('cliente.detalle_producto', ['id' => $producto->id]) }}" class="product-card"
         data-categoria="{{ $prodCategoria }}" data-nombre="{{ strtolower($prodNombre) }}">
        <div class="product-img-wrap">
          @if($producto->imagen)
            <img src="{{ asset('storage/' . $producto->imagen) }}" alt="{{ $producto->nombre }}"
                 style="width:100%;height:100%;object-fit:cover;">
          @else
            <div class="product-img-placeholder"><i class="bi bi-image"></i></div>
          @endif
          <span class="product-category-badge">{{ $producto->categoria }}</span>
        </div>
        <div class="product-info">
          <div class="product-top">
            <div>
              <p class="product-name">{{ $prodNombre }}</p>
              <p class="product-desc">{{ $producto->descripcion_corta }}</p>
            </div>
            <span class="product-status {{ $producto->stock_total > 0 ? 'disponible' : 'agotado' }}">
              {{ $producto->stock_total > 0 ? 'Disponible' : 'Agotado' }}
            </span>
          </div>
          <div class="product-bottom">
            <span class="product-price">${{ number_format($producto->precio, 0, ',', '.') }}</span>
          </div>
        </div>
      </a>
    @empty
      <div class="col-12 text-center text-muted py-5">
        <i class="bi bi-box fs-2 d-block mb-2"></i>
        No hay productos disponibles.
      </div>
    @endforelse
  </div>

  @if($productos instanceof \Illuminate\Contracts\Pagination\Paginator)
    <div class="mt-4">
      {{ $productos->links() }}
    </div>
  @endif
</div>

@endsection

@push('scripts')
<script>
  function filtrar() {
    const texto = document.getElementById('searchInput').value.toLowerCase();
    document.querySelectorAll('.product-card').forEach(card => {
      const nombre = card.dataset.nombre || '';
      card.style.display = nombre.includes(texto) ? '' : 'none';
    });
  }

  function filtrarCategoria(btn, cat) {
    document.querySelectorAll('.pill').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('.product-card').forEach(card => {
      card.style.display = (!cat || card.dataset.categoria === cat) ? '' : 'none';
    });
  }
</script>
@endpush
