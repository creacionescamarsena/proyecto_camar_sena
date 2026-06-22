@extends('layouts.app_emp')
@section('title', 'Materiales')

@section('content')

  <div class="d-flex justify-content-between align-items-center mb-1">
  <div>
    <h4 class="mb-0">Materiales</h4>
    <p class="text-muted small mb-0">Gestiona los materiales para productos</p>
  </div>
</div>

@if($materialesStockBajo > 0)
  <div class="alert-stock mt-3 mb-3 d-flex align-items-center gap-3">
    <i class="bi bi-exclamation-triangle-fill fs-4" style="color:#856404"></i>
    <div>
      <strong>Alerta de stock bajo</strong><br>
      <span class="small">{{ $materialesStockBajo }} materiales necesitan reabastecimiento</span>
    </div>
  </div>
@endif

<div class="card card-custom p-3">
  <h6 class="mb-3">Lista de materiales</h6>
  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead class="table-light">
        <tr>
          <th>Material</th>
          <th>Proveedor</th>
          <th>Precio</th>
          <th>Stock</th>
          <th>Estado</th>
        </tr>
      </thead>
      <tbody>
        @forelse($materiales as $material)
          <tr>
            <td>{{ $material->nombre }}</td>
            <td>{{ $material->proveedor ?? '—' }}</td>
            <td>${{ number_format($material->precio, 0, ',', '.') }}</td>
            <td>{{ $material->cantidad }}</td>
            <td>
              <span class="badge {{ $material->cantidad > 10 ? 'bg-success' : 'bg-warning text-dark' }}">
                {{ $material->cantidad > 10 ? 'Disponible' : 'Bajo' }}
              </span>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="text-center text-muted py-5">
              <i class="bi bi-stack fs-2 d-block mb-2"></i>
              No hay materiales registrados aún.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@endsection


