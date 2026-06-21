@extends('layouts.app_admin')
@section('title', 'Materiales inactivos')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-1">
  <div>
    <h4 class="mb-0">Materiales inactivos</h4>
    <p class="text-muted small mb-0">Reactiva materiales archivados</p>
  </div>
  <a href="{{ route('admin.materiales.index') }}" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left me-1"></i> Volver a activos
  </a>
</div>

<form method="GET" action="{{ route('admin.materiales.inactivos') }}" class="card card-custom p-3 mt-3">
  <div class="row g-2 align-items-center">
    <div class="col-12 col-md">
      <div class="input-group">
        <span class="input-group-text"><i class="bi bi-search"></i></span>
        <input type="search" name="buscar" value="{{ request('buscar') }}" class="form-control" placeholder="Buscar por material, proveedor, precio o stock">
      </div>
    </div>
    <div class="col-12 col-md-auto d-flex gap-2">
      <button type="submit" class="btn btn-main"><i class="bi bi-search me-1"></i> Buscar</button>
      @if(request('buscar'))
        <a href="{{ route('admin.materiales.inactivos') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg me-1"></i> Limpiar</a>
      @endif
    </div>
  </div>
</form>

<div class="card card-custom p-3 mt-3">
  <h6 class="mb-3">Materiales inactivos</h6>
  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead class="table-light">
        <tr>
          <th>Material</th>
          <th>Proveedor</th>
          <th>Precio</th>
          <th>Stock</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        @forelse($materiales as $material)
          <tr>
            <td>{{ $material->nombre }}</td>
            <td>{{ $material->proveedor ?? '-' }}</td>
            <td>${{ number_format($material->precio, 0, ',', '.') }}</td>
            <td>{{ $material->cantidad }}</td>
            <td><span class="badge bg-secondary">Inactivo</span></td>
            <td>
              <form method="POST" action="{{ route('admin.materiales.reactivar', $material->id) }}" class="d-inline">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-sm btn-outline-success">
                  <i class="bi bi-arrow-counterclockwise"></i> Reactivar
                </button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center text-muted py-5">
              <i class="bi bi-stack fs-2 d-block mb-2"></i>
              No hay materiales inactivos.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@if($materiales instanceof \Illuminate\Contracts\Pagination\Paginator)
  <div class="mt-3">
    {{ $materiales->links() }}
  </div>
@endif

@endsection
