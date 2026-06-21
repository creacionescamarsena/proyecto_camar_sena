@extends('layouts.app_admin')
@section('title', 'Facturación')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-1">
  <div>
    <h4 class="mb-0">Facturación</h4>
    <p class="text-muted small mb-0">Gestiona las facturas y pagos de los clientes</p>
  </div>
  <a href="{{ route('admin.reportes.ventas') }}" class="btn btn-outline-secondary">
    <i class="bi bi-bar-chart-line me-1"></i> Reporte ventas
  </a>
</div>

<!-- Tarjetas -->
<div class="row g-3 mb-4 mt-1">
  <div class="col-12 col-md-4">
    <div class="card card-custom p-3">
      <h6>Total facturado</h6>
      <p>{{ $totalFacturado ?? '$0' }}</p>
    </div>
  </div>
  <div class="col-12 col-md-4">
    <div class="card card-custom p-3">
      <h6>Pagado</h6>
      <p>{{ $totalPagado ?? '$0' }}</p>
    </div>
  </div>
  <div class="col-12 col-md-4">
    <div class="card card-custom p-3">
      <h6>Pendiente</h6>
      <p>{{ $totalPendiente ?? '$0' }}</p>
    </div>
  </div>
</div>

<!-- Tabla facturas -->
<div class="card card-custom p-3">
  <h6 class="mb-3">Lista de facturas</h6>
  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead class="table-light">
        <tr>
          <th>Nº factura</th>
          <th>Cliente</th>
          <th>Fecha Emisión</th>
          <th>Vencimiento</th>
          <th>Monto</th>
          <th>Estado</th>
        </tr>
      </thead>
      <tbody>
        @forelse($facturas as $factura)
          <tr>
            <td>{{ $factura->numero }}</td>
            <td>{{ $factura->cliente }}</td>
            <td>{{ $factura->fecha_emision }}</td>
            <td>{{ $factura->fecha_vencimiento }}</td>
            <td>${{ number_format($factura->monto, 0, ',', '.') }}</td>
            <td>
              @php
                $badgeClase = match($factura->estado) {
                  'Pagado'   => 'bg-success',
                  'Pendiente'=> 'bg-warning text-dark',
                  default    => 'bg-secondary',
                };
              @endphp
              <span class="badge {{ $badgeClase }}">{{ $factura->estado }}</span>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center text-muted py-5">
              <i class="bi bi-receipt fs-2 d-block mb-2"></i>
              No hay facturas registradas aún.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@if($facturas instanceof \Illuminate\Contracts\Pagination\Paginator)
  <div class="mt-3">
    {{ $facturas->links() }}
  </div>
@endif

@endsection
