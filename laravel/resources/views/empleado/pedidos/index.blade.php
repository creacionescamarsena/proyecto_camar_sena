@extends('layouts.app_emp')
@section('title', 'Pedidos')

@section('content')

<h4>Pedidos</h4>
<p>Actualiza el estado de los pedidos</p>

@forelse($pedidos as $pedido)
  <div class="card card-custom p-3 mb-3">

    <div class="d-flex justify-content-between">
      <div>
        <strong>Pedido {{ $pedido->codigo }}</strong><br>
        {{ $pedido->cliente }} - {{ $pedido->fecha }}
      </div>
      <div>
        @php
          $badgeClase = match($pedido->estado) {
            'Pendiente'  => 'bg-warning text-dark',
            'En proceso' => 'bg-primary',
            'Enviado'    => 'bg-info text-dark',
            'Entregado'  => 'bg-success',
            default      => 'bg-secondary',
          };
        @endphp
        <span class="badge {{ $badgeClase }}">{{ $pedido->estado }}</span>
      </div>
    </div>

    <hr>

    <div class="d-flex justify-content-between flex-wrap">
      <div>
        <strong>{{ $pedido->producto }}</strong><br>
        {{ $pedido->descripcion }}
      </div>
      <div>
        <strong>${{ number_format($pedido->valor, 0, ',', '.') }}</strong><br>
        Valor total envío
      </div>
    </div>

    <hr>

    <div>
      <strong>Dirección envío:</strong><br>
      {{ $pedido->direccion }}
    </div>

    <div class="mt-3 d-flex gap-2 flex-wrap">
      <a href="{{ route('empleado.pedidos.show', $pedido->id ?? $pedido->codigo ?? null) }}" class="btn btn-outline-dark btn-sm">
        Ver detalles
      </a>
      @if($pedido->estado === 'En proceso')
        <form method="POST" action="{{ route('empleado.pedidos.marcarEnviado', $pedido->id ?? $pedido->codigo ?? null) }}">
          @csrf
          @method('PATCH')
          <button type="submit" class="btn btn-main btn-sm">
            Marcar como enviado
          </button>
        </form>
      @endif
    </div>

  </div>
@empty
  <div class="card card-custom p-4 text-center text-muted">
    <i class="bi bi-cart-x fs-2 d-block mb-2"></i>
    No hay pedidos registrados.
  </div>
@endforelse

@endsection
