@extends('layouts.app_cliente')
@section('title', $producto->nombre)

@section('content')

<div class="detalle-container">
  <a href="{{ route('cliente.catalogo') }}" class="btn-back">
    <i class="bi bi-arrow-left"></i> Volver al catálogo
  </a>

  <div class="detalle-grid">
    <!-- Imagen -->
    <div class="detalle-img-wrap">
      @if($producto->imagen)
        <img src="{{ asset('storage/' . $producto->imagen) }}" alt="{{ $producto->nombre }}"
             style="width:100%;height:100%;object-fit:cover;border-radius:12px;">
      @else
        <div class="detalle-img-placeholder"><i class="bi bi-image"></i></div>
      @endif
    </div>

    <!-- Info -->
    <div class="detalle-info">
      <p class="detalle-cat">{{ $producto->categoria }}</p>
      <h1 class="detalle-nombre">{{ $producto->nombre }}</h1>
      <p class="detalle-desc">{{ $producto->descripcion }}</p>
      <p class="detalle-precio">${{ number_format($producto->precio, 0, ',', '.') }}</p>

      <!-- Materiales -->
      @if($producto->materiales->count())
        <div>
          <p class="detalle-section-label">Materiales</p>
          <div class="materiales-wrap">
            @foreach($producto->materiales as $material)
              <span class="material-tag">{{ $material->nombre ?? $material->material ?? 'Material' }}</span>
            @endforeach
          </div>
        </div>
      @endif

      <!-- Tallas -->
      <div>
        <p class="detalle-section-label">Selecciona tu talla</p>
        <div class="tallas-wrap">
          @foreach($producto->tallas as $talla => $cantidad)
            <button type="button" class="talla-btn" onclick="selTalla(this)" data-talla="{{ $talla }}" data-stock="{{ $cantidad }}">
              {{ $talla }}
              @if($cantidad > 1)
                <small class="text-muted">({{ $cantidad }} disponibles)</small>
              @endif
            </button>
          @endforeach
        </div>
        <input type="hidden" id="tallaSeleccionada" value="">
      </div>

      <!-- Cantidad -->
      <div>
        <p class="detalle-section-label">Cantidad</p>
        <div class="cantidad-wrap">
          <button class="cantidad-btn" onclick="cambiarCant(-1)">−</button>
          <span class="cantidad-num" id="cantidad">1</span>
          <button class="cantidad-btn" onclick="cambiarCant(1)">+</button>
        </div>
      </div>

      <!-- Botón agregar al carrito -->
      <form method="POST" action="{{ route('cliente.carrito.agregar') }}" id="formCarrito">
        @csrf
        <input type="hidden" name="producto_id" value="{{ $producto->id }}">
        <input type="hidden" name="talla" id="inputTalla" value="">
        <input type="hidden" name="cantidad" id="inputCantidad" value="1">
        <button type="button" class="btn-agregar" onclick="agregarCarrito()">
          <i class="bi bi-cart-plus"></i> Agregar al carrito
        </button>
      </form>
    </div>
  </div>
</div>

<!-- Toast confirmación -->
<div id="toast" style="
  position:fixed; bottom:28px; right:28px;
  background:var(--verde); color:white;
  padding:14px 22px; border-radius:12px;
  font-size:0.9rem; font-weight:600;
  box-shadow:0 4px 20px rgba(80,109,47,0.3);
  opacity:0; transform:translateY(10px);
  transition:all 0.3s; pointer-events:none;
  display:flex; align-items:center; gap:10px;
">
  <i class="bi bi-check-circle-fill"></i> Producto agregado al carrito
</div>

@endsection

@push('scripts')
<script>
  function selTalla(btn) {
    document.querySelectorAll('.talla-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('inputTalla').value = btn.dataset.talla || btn.textContent.trim();
    const stock = parseInt(btn.dataset.stock || '0', 10);
    if (stock > 0) {
      cantidad = Math.min(cantidad, stock);
      document.getElementById('cantidad').textContent = cantidad;
      document.getElementById('inputCantidad').value = cantidad;
    }
  }

  let cantidad = 1;
  function cambiarCant(delta) {
    const tallaEl = document.getElementById('inputTalla').value;
    const active = document.querySelector('.talla-btn.active');
    const maxStock = active ? parseInt(active.dataset.stock || '0', 10) : 999;
    cantidad = Math.max(1, Math.min(maxStock, cantidad + delta));
    document.getElementById('cantidad').textContent = cantidad;
    document.getElementById('inputCantidad').value = cantidad;
  }

  function agregarCarrito() {
    const talla = document.getElementById('inputTalla').value;
    if (!talla) {
      alert('Por favor selecciona una talla.');
      return;
    }
    document.getElementById('formCarrito').submit();
    const toast = document.getElementById('toast');
    toast.style.opacity = '1';
    toast.style.transform = 'translateY(0)';
    setTimeout(() => {
      toast.style.opacity = '0';
      toast.style.transform = 'translateY(10px)';
    }, 2500);
  }
</script>
@endpush
