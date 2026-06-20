@extends('layouts.app_cliente')
@section('title', 'Mis Pedidos')

@section('content')

<div class="pedidos-container">
  <h2 class="section-title" style="margin-bottom:24px;">Mis pedidos</h2>

  @forelse($pedidos as $pedido)
    <div class="pedido-card">
      <div class="pedido-header">
        <div>
          <p class="pedido-id">Pedido {{ $pedido->codigo }}</p>
          <p class="pedido-fecha">{{ \Carbon\Carbon::parse($pedido->created_at)->isoFormat('D [de] MMMM, YYYY') }}</p>
        </div>
        @php
          $estadoClase = match($pedido->estado) {
            'En camino'  => 'enviado',
            'Entregado'  => 'entregado',
            'Pendiente'  => 'pendiente',
            'En proceso' => 'pendiente',
            default      => 'pendiente',
          };
        @endphp
        <span class="pedido-estado {{ $estadoClase }}">{{ $pedido->estado }}</span>
      </div>

      <div class="pedido-items">
        @foreach($pedido->items as $item)
          <div class="pedido-item-row">
            <span>{{ $item->producto->nombre }} — Talla {{ $item->talla }}</span>
            <span>x{{ $item->cantidad }} &nbsp; ${{ number_format($item->producto->precio * $item->cantidad, 0, ',', '.') }}</span>
          </div>
        @endforeach
      </div>

      <div class="pedido-footer">
        <span class="pedido-total-label">Total del pedido</span>
        <span class="pedido-total-val">${{ number_format($pedido->total, 0, ',', '.') }}</span>
      </div>
    </div>
  @empty
    <div class="text-center text-muted py-5">
      <i class="bi bi-bag-x fs-2 d-block mb-2"></i>
      Aún no tienes pedidos.
      <br>
      <a href="{{ route('cliente.catalogo') }}" class="btn btn-main mt-3">Ir al catálogo</a>
    </div>
  @endforelse
</div>

@endsection
