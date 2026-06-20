@extends('layouts.app_admin')
@section('title', 'Dashboard')

@section('content')

<h4>Dashboard</h4>
<p>Vista general del sistema</p>

<!-- Tarjetas -->
<div class="row g-3 mb-4">
  <div class="col-12 col-md-3">
    <div class="card card-custom p-3">
      <h6>Ventas totales</h6>
      <p>{{ $ventasTotales ?? '$0' }}</p>
    </div>
  </div>
  <div class="col-12 col-md-3">
    <div class="card card-custom p-3">
      <h6>Envíos pendientes</h6>
      <p>{{ $enviosPendientes ?? 0 }}</p>
    </div>
  </div>
  <div class="col-12 col-md-3">
    <div class="card card-custom p-3">
      <h6>Total productos</h6>
      <p>{{ $totalProductos ?? 0 }}</p>
    </div>
  </div>
  <div class="col-12 col-md-3">
    <div class="card card-custom p-3">
      <h6>Stock bajo</h6>
      <p>{{ $stockBajo ?? 0 }} materiales</p>
    </div>
  </div>
</div>

<!-- Sección doble -->
<div class="row g-3">

  <!-- Envíos recientes -->
  <div class="col-12 col-lg-6">
    <div class="card card-custom p-3">
      <h6>Envíos recientes</h6>
      @forelse($enviosRecientes ?? [] as $envio)
        <div class="border p-2 mb-2">
          <strong>{{ $envio->cliente }}</strong><br>
          {{ $envio->producto }}<br>
          {{ $envio->estado }}
        </div>
      @empty
        <p class="text-muted small">No hay envíos recientes.</p>
      @endforelse
    </div>
  </div>

  <!-- Materiales stock bajo -->
  <div class="col-12 col-lg-6">
    <div class="card card-custom p-3">
      <h6>Materiales con stock bajo</h6>
      @forelse($materialesStockBajo ?? [] as $material)
        <div class="border p-2 mb-2 d-flex justify-content-between">
          <span>{{ $material->nombre }}</span>
          <span>
            {{ $material->cantidad }} unidades<br>
            <a href="{{ route('admin.materiales.edit', $material->id_materiales) }}" class="small text-success">Restablecer</a>
          </span>
        </div>
      @empty
        <p class="text-muted small">Sin materiales en stock bajo.</p>
      @endforelse
    </div>
  </div>

</div>

<!-- Tabla resumen ventas -->
<div class="card card-custom p-3 mt-4">
  <h6>Resumen de ventas</h6>
  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th>Producto</th>
          <th>Categoría</th>
          <th>Precio</th>
          <th>Stock</th>
          <th>Estado</th>
        </tr>
      </thead>
      <tbody>
        @forelse($resumenVentas ?? [] as $item)
          <tr>
            <td>{{ $item->nombre }}</td>
            <td>{{ $item->categoria }}</td>
            <td>${{ number_format($item->precio, 0, ',', '.') }}</td>
            <td>{{ $item->stock }}</td>
            <td>{{ $item->estado }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="text-center text-muted py-3">Sin datos de ventas.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@endsection
