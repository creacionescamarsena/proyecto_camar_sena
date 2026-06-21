@extends('layouts.app_emp')
@section('title', 'Detalle del Pedido')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h4>Detalle del Pedido</h4>
    <p class="mb-0">Información completa del pedido</p>
  </div>
  <a href="{{ route('empleado.pedidos.index') }}" class="btn btn-outline-dark btn-sm">
    ← Volver
  </a>
</div>

<div class="card card-custom p-4">

  <!-- Encabezado -->
  <div class="d-flex justify-content-between flex-wrap">
 <div>
    <h5>Pedido {{ $pedido->codigo }}</h5>
    <p class="mb-1">Cliente: {{ $pedido->cliente->nombres }}</p>
    <p class="mb-0">Fecha entrega: {{ $pedido->fecha_entrega }}</p>
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

  <!-- Producto -->
  <div class="mb-3">
    <h6>Producto</h6>
    <p class="mb-1"><strong>{{ $pedido->producto }}</strong></p>
    <p class="mb-0 text-muted">{{ $pedido->descripcion }}</p>
  </div>

  <hr>

  <!-- Dirección -->
  <div class="mb-3">
    <h6>Dirección de envío</h6>
    <p class="mb-0">{{ $pedido->direccion }}</p>
  </div>

  <hr>

  <!-- Valores -->
  <div class="row mb-3">
    <div class="col-md-4">
      <h6>Valor producto</h6>
      <p>${{ number_format($pedido->valor_producto, 0, ',', '.') }}</p>
    </div>
    <div class="col-md-4">
      <h6>Costo envío</h6>
      <p>${{ number_format($pedido->costo_envio, 0, ',', '.') }}</p>
    </div>
    <div class="col-md-4">
      <h6>Total</h6>
      <p><strong>${{ number_format($pedido->valor_producto + $pedido->costo_envio, 0, ',', '.') }}</strong></p>
    </div>
  </div>

  <hr>

  <!-- Cambiar estado -->
  <div class="mb-3">
    <h6>Estado del pedido</h6>
    <form method="POST" action="{{ route('empleado.pedidos.update', $pedido->id ?? null) }}">
      @csrf
      @method('PATCH')
      <select name="estado" class="form-control" style="max-width:300px;">
        <option value="Pendiente"   {{ $pedido->estado === 'Pendiente'   ? 'selected' : '' }}>Pendiente</option>
        <option value="En proceso"  {{ $pedido->estado === 'En proceso'  ? 'selected' : '' }}>En proceso</option>
        <option value="Enviado"     {{ $pedido->estado === 'Enviado'     ? 'selected' : '' }}>Enviado</option>
        <option value="Entregado"   {{ $pedido->estado === 'Entregado'   ? 'selected' : '' }}>Entregado</option>
      </select>

      <div class="d-flex gap-2 flex-wrap mt-3">
        <button type="submit" class="btn btn-main">
          Guardar cambios
        </button>
      </div>
    </form>
  </div>

</div>

@endsection
