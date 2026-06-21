@extends('layouts.app_admin')
@section('title', 'Usuarios inactivos')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-1">
  <div>
    <h4 class="mb-0">Usuarios inactivos</h4>
    <p class="text-muted small mb-0">Reactiva usuarios archivados del sistema</p>
  </div>
  <a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left me-1"></i> Volver a activos
  </a>
</div>

<form method="GET" action="{{ route('admin.usuarios.inactivos') }}" class="card card-custom p-3 mt-3">
  <div class="row g-2 align-items-center">
    <div class="col-12 col-md">
      <div class="input-group">
        <span class="input-group-text"><i class="bi bi-search"></i></span>
        <input type="search" name="buscar" value="{{ request('buscar') }}" class="form-control" placeholder="Buscar por documento, nombre, email, telefono o rol">
      </div>
    </div>
    <div class="col-12 col-md-auto d-flex gap-2">
      <button type="submit" class="btn btn-main"><i class="bi bi-search me-1"></i> Buscar</button>
      @if(request('buscar'))
        <a href="{{ route('admin.usuarios.inactivos') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg me-1"></i> Limpiar</a>
      @endif
    </div>
  </div>
</form>

<div class="card card-custom p-3 mt-3">
  <h6 class="mb-3">Usuarios inactivos</h6>
  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead class="table-light">
        <tr>
          <th>Tipo documento</th>
          <th>Documento</th>
          <th>Nombre</th>
          <th>Email</th>
          <th>Telefono</th>
          <th>Rol</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        @forelse($usuarios as $usuario)
          @php
            $usuarioFull = trim(($usuario->nombres ?? '') . ' ' . ($usuario->apellidos ?? ''));
            $colores = ['Admin' => '#506d2f', 'Empleado' => '#7d5642', 'Cliente' => '#6c757d'];
          @endphp
          <tr>
            <td>{{ optional($usuario->tipo_documento)->tipo ?? '-' }}</td>
            <td>{{ $usuario->id_usuario }}</td>
            <td>{{ $usuarioFull }}</td>
            <td>{{ $usuario->email }}</td>
            <td>{{ $usuario->telefono ?? '-' }}</td>
            <td>
              <span class="badge" style="background:{{ $colores[$usuario->rol] ?? '#6c757d' }}">
                {{ $usuario->rol }}
              </span>
            </td>
            <td><span class="badge bg-secondary">{{ $usuario->estado }}</span></td>
            <td>
              <form method="POST" action="{{ route('admin.usuarios.reactivar', $usuario) }}" class="d-inline">
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
            <td colspan="8" class="text-center text-muted py-5">
              <i class="bi bi-person-check fs-2 d-block mb-2"></i>
              No hay usuarios inactivos.
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
