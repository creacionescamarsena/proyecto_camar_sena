@extends('layouts.app_cliente')
@section('title', 'Carrito')

@section('content')

<div class="carrito-container">
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif
  <!-- Lista de items -->
  <div>
    <h2 class="section-title">Carrito de compras</h2>

    <div class="carrito-items" id="carritoItems">
      @forelse($items as $item)
        <div class="carrito-item" id="item{{ $item->id }}">
          <div class="carrito-item-img">
            @php
              $imagen = trim((string) ($item->producto->imagen ?? ''));
              $imagenSrc = '';
              if ($imagen !== '') {
                $imagenSrc = preg_match('/^https?:\/\//i', $imagen) || str_starts_with($imagen, '/')
                  ? $imagen
                  : asset(str_starts_with($imagen, 'storage/') ? $imagen : 'storage/' . $imagen);
              }
            @endphp
            @if($imagenSrc)
              <img src="{{ $imagenSrc }}" alt="{{ $item->producto->nombre }}" loading="lazy" decoding="async">
            @else
              <i class="bi bi-image"></i>
            @endif
          </div>
          <div class="carrito-item-info">
            <p class="carrito-item-nombre">{{ $item->producto->nombre }}</p>
            <p class="carrito-item-talla">Talla: {{ $item->talla }}</p>
            <div class="carrito-item-controls">
              <form method="POST" action="{{ route('cliente.carrito.actualizar', $item->key) }}" style="display:inline;">
                @csrf @method('PATCH')
                <input type="hidden" name="accion" value="restar">
                <button type="submit" class="ctrl-btn">-</button>
              </form>
              <span class="ctrl-num">{{ $item->cantidad }}</span>
              <form method="POST" action="{{ route('cliente.carrito.actualizar', $item->key) }}" style="display:inline;">
                @csrf @method('PATCH')
                <input type="hidden" name="accion" value="sumar">
                <button type="submit" class="ctrl-btn">+</button>
              </form>
            </div>
          </div>
          <span class="carrito-item-precio">
            ${{ number_format($item->producto->precio * $item->cantidad, 0, ',', '.') }}
          </span>
          <form method="POST" action="{{ route('cliente.carrito.eliminar', $item->key) }}">
            @csrf @method('DELETE')
            <button type="submit" class="btn-eliminar" title="Eliminar">
              <i class="bi bi-trash3"></i>
            </button>
          </form>
        </div>
      @empty
        <div class="text-center text-muted py-5">
          <i class="bi bi-cart-x fs-2 d-block mb-2"></i>
          Tu carrito está vacío.
          <br>
          <a href="{{ route('cliente.catalogo') }}" class="btn btn-main mt-3">Ver catálogo</a>
        </div>
      @endforelse
    </div>

    @if($items->count())
      <form method="POST" action="{{ route('cliente.carrito.vaciar') }}">
        @csrf @method('DELETE')
        <button type="submit" class="btn-vaciar">
          <i class="bi bi-trash3 me-2"></i>Vaciar carrito
        </button>
      </form>
    @endif
  </div>

  <!-- Resumen -->
  @if($items->count())
    <div>
      <div class="resumen-card">
        <p class="resumen-title">Resumen del pedido</p>
        <div class="resumen-row">
          <span>Subtotal</span>
          <span>${{ number_format($subtotal, 0, ',', '.') }}</span>
        </div>
        <div class="resumen-row">
          <span>Envío</span>
          <span>${{ number_format($costoEnvio, 0, ',', '.') }}</span>
        </div>
        <div class="resumen-total">
          <span>Total</span>
          <span class="resumen-total-val">${{ number_format($subtotal + $costoEnvio, 0, ',', '.') }}</span>
        </div>
        <div class="resumen-actions">
          <a href="{{ route('cliente.checkout') }}" class="btn-pagar">
            Proceder con el pago
          </a>
          <a href="{{ route('cliente.catalogo') }}" class="btn-seguir">
            Continuar comprando
          </a>
        </div>
      </div>
    </div>
  @endif
</div>

@endsection
