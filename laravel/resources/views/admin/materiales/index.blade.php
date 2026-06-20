@extends('layouts.app_admin')
@section('title', 'Materiales')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-1">
  <div>
    <h4 class="mb-0">Materiales</h4>
    <p class="text-muted small mb-0">Gestiona los materiales para productos</p>
  </div>
  <a href="{{ route('admin.materiales.create') }}" class="btn btn-main">
    <i class="bi bi-plus-circle me-1"></i> Nuevo material
  </a>
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
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        @forelse($materiales as $material)
          @php
            $nombreMaterial = is_string($material->nombre) ? $material->nombre : (is_object($material->nombre) ? ($material->nombre->nombre ?? $material->nombre->material ?? '') : ($material->nombre ?? ''));
          @endphp
          <tr>
            <td>{{ $nombreMaterial }}</td>
            <td>{{ $material->proveedor ?? '—' }}</td>
            <td>${{ number_format($material->precio, 0, ',', '.') }}</td>
            <td>{{ $material->cantidad }}</td>
            <td>
              <span class="badge {{ $material->cantidad > 10 ? 'bg-success' : 'bg-warning text-dark' }}">
                {{ $material->cantidad > 10 ? 'Disponible' : 'Bajo' }}
              </span>
            </td>
            <td>
              <a href="{{ route('admin.materiales.edit', $material->id) }}"
                 class="btn btn-sm btn-outline-secondary me-1">
                <i class="bi bi-pencil"></i> Editar
              </a>
              <button type="button" class="btn btn-sm btn-outline-danger"
                data-delete-url="{{ route('admin.materiales.destroy', $material->id) }}"
                data-delete-name="{{ $nombreMaterial }}"
                onclick="mostrarModal(this, {!! json_encode($nombreMaterial) !!})">
                <i class="bi bi-trash"></i> Eliminar
              </button>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center text-muted py-5">
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

@push('scripts')
<div id="modalOverlay" class="modal-overlay" onclick="cerrarModal()">
  <div class="modal-confirm" onclick="event.stopPropagation()">
    <p class="fw-semibold fs-5 mb-4">
      ¿Está seguro de eliminar <span id="nombreMaterial" class="text-danger"></span>?
    </p>
    <div class="d-flex justify-content-center gap-4">
      <button class="btn-icon-cancel" onclick="cerrarModal()" title="Cancelar">
        <i class="bi bi-x-lg"></i>
      </button>
      <form id="formEliminar" method="POST" action="">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn-icon-confirm" title="Confirmar">
          <i class="bi bi-check-lg"></i>
        </button>
      </form>
    </div>
  </div>
</div>

<script>
  function mostrarModal(buttonOrNombre, nombre) {
    var button = buttonOrNombre instanceof Element ? buttonOrNombre : null;
    var texto = typeof nombre === 'string' && nombre !== '' ? nombre : (button ? button.dataset.deleteName : (buttonOrNombre || ''));

    document.getElementById('nombreMaterial').textContent = texto;
    if (button && button.dataset.deleteUrl) {
      document.getElementById('formEliminar').action = button.dataset.deleteUrl;
    }
    document.getElementById('modalOverlay').classList.add('show');
  }

  function cerrarModal() {
    document.getElementById('modalOverlay').classList.remove('show');
  }

  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-delete-url]').forEach(function (button) {
      button.type = 'button';
      button.addEventListener('click', function (event) {
        event.preventDefault();
        mostrarModal(button, button.dataset.deleteName || '');
      });
    });
  });
</script>
@endpush
