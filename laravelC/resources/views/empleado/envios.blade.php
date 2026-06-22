@extends('layouts.app_emp')
@section('title', 'Envíos')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-1">
  <div>
    <h4 class="mb-0">Envíos</h4>
    <p class="text-muted small mb-0">Gestiona los envíos de los clientes</p>
  </div>
</div>

<!-- Tarjetas -->
<div class="row g-3 mb-4 mt-1">
  <div class="col-12 col-md-4">
    <div class="card card-custom p-3">
      <h6>Total Envíos</h6>
      <p>{{ $totalEnvios ?? 0 }}</p>
    </div>
  </div>
  <div class="col-12 col-md-4">
    <div class="card card-custom p-3">
      <h6>En tránsito</h6>
      <p>{{ $enTransito ?? 0 }}</p>
    </div>
  </div>
  <div class="col-12 col-md-4">
    <div class="card card-custom p-3">
      <h6>Entregados</h6>
      <p>{{ $entregados ?? 0 }}</p>
    </div>
  </div>
</div>

<!-- Tabla envíos -->
<div class="card card-custom p-3">
  <h6 class="mb-3">Lista de envíos</h6>
  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead class="table-light">
        <tr>
          <th>ID envío</th>
          <th>Cliente</th>
          <th>Producto</th>
          <th>Destino</th>
          <th>Fecha</th>
          <th>Estado</th>
        </tr>
      </thead>
      <tbody>
        @forelse($envios as $envio)
          <tr>
            <td>{{ $envio->codigo }}</td>
            <td>{{ $envio->cliente }}</td>
            <td>{{ $envio->producto }}</td>
            <td>{{ $envio->destino }}</td>
            <td>{{ $envio->fecha }}</td>
            <td>
              @php
                $badgeClase = match($envio->estado) {
                  'Entregado'   => 'bg-success',
                  'En tránsito' => 'bg-warning text-dark',
                  'Pendiente'   => 'bg-secondary',
                  default       => 'bg-secondary',
                };
              @endphp
              <span class="badge {{ $badgeClase }}">{{ $envio->estado }}</span>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center text-muted py-5">
              <i class="bi bi-truck fs-2 d-block mb-2"></i>
              No hay envíos registrados.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@endsection
