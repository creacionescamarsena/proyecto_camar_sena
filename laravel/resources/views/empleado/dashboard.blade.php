@extends('layouts.app_emp')
@section('title', 'Dashboard')

@section('content')

<h4>Dashboard</h4>
<p>Vista general del sistema</p>

<!-- Tarjetas -->
<div class="row g-3 mb-4">
  <div class="col-12 col-md-3">
    <div class="card card-custom p-3">
      <h6>Envíos pendientes</h6>
      <p>{{ $enviosPendientes ?? 0 }}</p>
    </div>
  </div>
  <div class="col-12 col-md-3">
    <div class="card card-custom p-3">
      <h6>En tránsito</h6>
      <p>{{ $enTransito ?? 0 }}</p>
    </div>
  </div>
  <div class="col-12 col-md-3">
    <div class="card card-custom p-3">
      <h6>Productos disponibles</h6>
      <p>{{ $productosDisponibles ?? 0 }}</p>
    </div>
  </div>
  <div class="col-12 col-md-3">
    <div class="card card-custom p-3">
      <h6>Entregados hoy</h6>
      <p>{{ $entregadosHoy ?? 0 }}</p>
    </div>
  </div>
</div>

<div class="card card-custom p-3 mb-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Pedidos prioritarios</h5>
  </div>

  @forelse($pedidosActivos ?? [] as $pedido)
    <div class="border rounded p-3 mb-2">
      <div class="d-flex justify-content-between">
        <div>
          <strong>Pedido {{ $pedido->codigo }}</strong><br>
          <span>{{ $pedido->cliente }}</span>
        </div>
        <div class="text-end">
          <span class="badge bg-warning text-dark">{{ $pedido->estado }}</span>
        </div>
      </div>
      <div class="d-flex justify-content-between flex-wrap mt-2 small text-muted">
        <span>{{ $pedido->descripcion }}</span>
        <span>${{ number_format($pedido->valor, 0, ',', '.') }}</span>
      </div>
    </div>
  @empty
    <p class="text-muted small mb-0">No hay pedidos activos.</p>
  @endforelse
</div>
<!-- Acciones rápidas -->
<div class="row mt-2 g-3">
  <div class="col-12 col-md-6">
    <div class="card card-custom p-4 h-100">
      <h6>Procesar pedidos</h6>
      <p>Actualiza el estado de los pedidos pendientes</p>
      <a href="{{ route('empleado.pedidos.index') }}" class="btn btn-main mt-auto">
        Ir a pedidos
      </a>
    </div>
  </div>
  <div class="col-12 col-md-6">
    <div class="card card-custom p-4 h-100">
      <h6>Gestionar Envíos</h6>
      <p>Actualiza información de tracking y entrega</p>
      <a href="{{ route('empleado.envios') }}" class="btn btn-main mt-auto">
        Ir a envíos
      </a>
    </div>
  </div>
</div>

@endsection
