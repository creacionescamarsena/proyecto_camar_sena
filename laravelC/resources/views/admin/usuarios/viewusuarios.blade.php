@extends('layouts.app_admin')
@section('title', 'Usuarios')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h4 class="mb-0">Usuarios</h4>
    <p class="text-muted small mb-0">Gestiona los usuarios del sistema</p>
  </div>
  <a href="{{ route('admin.usuarios.create') }}" class="btn btn-main">
    <i class="bi bi-person-plus me-1"></i> Nuevo usuario
  </a>
</div>

<div class="card card-custom p-3">
  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead class="table-light">
        <tr>
          <th>#</th>
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
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $usuario->nombre_completo }}</td>
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
            <a href="{{ route('admin.usuarios.edit', $usuario) }}"
               class="btn btn-sm btn-outline-secondary me-1 btn-outline-fixed">
              <i class="bi bi-pencil"></i> Editar
            </a>
            @php
              $nombreUsuario = is_string($usuario->nombre_completo) ? $usuario->nombre_completo : (is_object($usuario->nombre_completo) ? ($usuario->nombre_completo->nombre ?? $usuario->nombre_completo->nombre_completo ?? '') : ($usuario->nombre_completo ?? ''));
            @endphp
            <button class="btn btn-sm btn-outline-danger"
                    onclick="mostrarModal({{ $usuario->id_usuario }}, {!! json_encode($nombreUsuario) !!})">
              <i class="bi bi-trash"></i> Eliminar
            </button>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7" class="text-center text-muted py-5">
            <i class="bi bi-person-x fs-2 d-block mb-2"></i>
            No hay usuarios registrados aún.
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

<script>
  function mostrarModal(id, nombre) {
    document.getElementById('nombreUsuario').textContent = nombre;
    document.getElementById('formEliminar').action = `/admin/usuarios/${id}`;
    document.getElementById('modalOverlay').classList.add('show');
  }
  function cerrarModal() {
    document.getElementById('modalOverlay').classList.remove('show');
  }
</script>
@endpush
