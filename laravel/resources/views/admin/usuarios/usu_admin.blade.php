@extends('layouts.app_admin')
@section('title', 'Usuarios')
 
@section('content')
 
<div class="d-flex justify-content-between align-items-center mb-1">
  <div>
    <h4 class="mb-0">Usuarios</h4>
    <p class="text-muted small mb-0">Gestiona los usuarios del sistema</p>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('admin.usuarios.inactivos') }}" class="btn btn-outline-secondary">
      <i class="bi bi-archive me-1"></i> Ver inactivos
    </a>
    <a href="{{ route('admin.reportes.usuarios') }}" class="btn btn-outline-secondary">
      <i class="bi bi-bar-chart-line me-1"></i> Reporte usuarios
    </a>
    <a href="{{ route('admin.usuarios.create') }}" class="btn btn-main">
      <i class="bi bi-person-plus me-1"></i> Nuevo usuario
    </a>
  </div>
</div>

<form method="GET" action="{{ route('admin.usuarios.index') }}" class="card card-custom p-3 mt-3">
  <div class="row g-2 align-items-center">
    <div class="col-12 col-md">
      <div class="input-group">
        <span class="input-group-text"><i class="bi bi-search"></i></span>
        <input type="search" name="buscar" value="{{ request('buscar') }}" class="form-control" placeholder="Buscar por documento, nombre, email, telefono o rol">
      </div>
    </div>
    <div class="col-12 col-md-auto d-flex gap-2">
      <button type="submit" class="btn btn-main">
        <i class="bi bi-search me-1"></i> Buscar
      </button>
      @if(request('buscar'))
        <a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline-secondary">
          <i class="bi bi-x-lg me-1"></i> Limpiar
        </a>
      @endif
    </div>
  </div>
</form>
 
<div class="card card-custom p-3 mt-3">
  <h6 class="mb-3">Resumen de usuarios</h6>
  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead class="table-light">
        <tr>
          <th>Tipo documento</th>
          <th>Documento</th>
          <th>Nombre</th>
          <th>Email</th>
          <th>Teléfono</th>
          <th>Rol</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        @forelse($usuarios as $usuario)
          @php
            $usuarioFull = trim((is_string($usuario->nombres) ? $usuario->nombres : ($usuario->nombres ?? '')) . ' ' . (is_string($usuario->apellidos) ? $usuario->apellidos : ($usuario->apellidos ?? '')));
          @endphp
          <tr>
             <td>
              {{ optional($usuario->tipo_documento)->tipo ?? '—' }}
            </td>
            <td>{{ $usuario->id_usuario }}</td>
            <td>{{ $usuarioFull }}</td>
            <td>{{ $usuario->email }}</td>
            <td>{{ $usuario->telefono ?? '—' }}</td>
            <td>
              @php
                $colores = ['Admin' => '#506d2f', 'Empleado' => '#7d5642', 'Cliente' => '#6c757d'];
              @endphp
              <span class="badge" style="background:{{ $colores[$usuario->rol] ?? '#6c757d' }}">
                {{ $usuario->rol }}
              </span>
            </td>
           
            <td>
              <span class="badge {{ $usuario->estado === 'Activo' ? 'bg-success' : 'bg-secondary' }}">
                {{ $usuario->estado }}
              </span>
            </td>
            <td>
              <a href="{{ route('admin.usuarios.edit', $usuario) }}" class="btn btn-sm btn-outline-secondary btn-outline-fixed me-1">
                <i class="bi bi-pencil"></i> Editar
              </a>
              <button type="button" class="btn btn-sm btn-outline-danger"
                data-delete-url="{{ route('admin.usuarios.destroy', $usuario) }}"
                data-delete-name="{{ $usuarioFull }}"
                onclick="mostrarModal(this, {!! json_encode($usuarioFull) !!})">
                <i class="bi bi-trash"></i> Eliminar
              </button>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="text-center text-muted py-5">
              <i class="bi bi-person-x fs-2 d-block mb-2"></i>
              No hay usuarios activos para mostrar.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@if($usuarios instanceof \Illuminate\Contracts\Pagination\Paginator)
  <div class="mt-3">
    {{ $usuarios->links() }}
  </div>
@endif
 
@endsection
 
@push('scripts')
{{-- Modal confirmación eliminar --}}
<div id="modalOverlay" class="modal-overlay" onclick="cerrarModal()">
  <div class="modal-confirm" onclick="event.stopPropagation()">
    <p class="fw-semibold fs-5 mb-4">
      ¿Está seguro de eliminar a <span id="nombreUsuario" class="text-danger"></span>?
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
 
<style>
  .modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.45);
    z-index: 9999;
    justify-content: center;
    align-items: center;
  }
  .modal-overlay.show { display: flex; }
  .modal-confirm {
    background: white;
    border-radius: 16px;
    padding: 40px 50px;
    text-align: center;
    box-shadow: 0 8px 30px rgba(0,0,0,0.2);
    min-width: 340px;
  }
  .btn-icon-cancel {
    width: 56px; height: 56px;
    border-radius: 50%;
    border: 2px solid #dc3545;
    background: white;
    color: #dc3545;
    font-size: 1.4rem;
    cursor: pointer;
    transition: all .2s;
  }
  .btn-icon-cancel:hover { background: #dc3545; color: white; }
  .btn-icon-confirm {
    width: 56px; height: 56px;
    border-radius: 50%;
    border: 2px solid #506d2f;
    background: white;
    color: #506d2f;
    font-size: 1.4rem;
    cursor: pointer;
    transition: all .2s;
  }
  .btn-icon-confirm:hover { background: #506d2f; color: white; }
</style>
 
<script>
  function mostrarModal(buttonOrNombre, nombre) {
    var button = buttonOrNombre instanceof Element ? buttonOrNombre : null;
    var texto = typeof nombre === 'string' && nombre !== '' ? nombre : (button ? button.dataset.deleteName : (buttonOrNombre || ''));

    document.getElementById('nombreUsuario').textContent = texto;
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
