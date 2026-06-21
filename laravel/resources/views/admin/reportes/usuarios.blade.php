@extends('layouts.app_admin')
@section('title', 'Reporte de usuarios')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
  <div>
    <h4 class="mb-1">Reporte de usuarios</h4>
    <p class="text-muted small mb-0">Filtra por rol, estado, documento o texto.</p>
  </div>
  <a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Atras</a>
</div>

<form method="GET" class="card card-custom p-3 mb-4">
  <div class="row g-3 align-items-end">
    <div class="col-md-4">
      <label class="form-label small">Buscar</label>
      <input name="buscar" value="{{ request('buscar') }}" class="form-control" placeholder="Nombre, correo o documento">
    </div>
    <div class="col-md-2">
      <label class="form-label small">Rol</label>
      <select name="rol" class="form-select">
        <option value="">Todos</option>
        @foreach($roles as $rol)
          <option value="{{ $rol }}" @selected(request('rol') === $rol)>{{ $rol }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-2">
      <label class="form-label small">Estado</label>
      <select name="estado" class="form-select">
        <option value="">Todos</option>
        <option value="Activo" @selected(request('estado') === 'Activo')>Activo</option>
        <option value="Inactivo" @selected(request('estado') === 'Inactivo')>Inactivo</option>
      </select>
    </div>
    <div class="col-md-2">
      <label class="form-label small">Documento</label>
      <select name="tipo_documento_id" class="form-select">
        <option value="">Todos</option>
        @foreach($tiposDocumento as $tipo)
          <option value="{{ $tipo->id_tipo }}" @selected(request('tipo_documento_id') == $tipo->id_tipo)>{{ $tipo->tipo }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-2 d-flex gap-2">
      <button class="btn btn-main"><i class="bi bi-funnel me-1"></i> Filtrar</button>
      <a href="{{ route('admin.reportes.usuarios') }}" class="btn btn-outline-secondary" title="Limpiar"><i class="bi bi-x-lg"></i></a>
    </div>
  </div>
</form>

<div class="card card-custom p-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h6 class="mb-0">Usuarios encontrados</h6>
    <a href="{{ route('admin.reportes.usuarios.pdf', request()->query()) }}" class="btn btn-main btn-sm">
      <i class="bi bi-file-earmark-pdf me-1"></i> PDF
    </a>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead class="table-light">
        <tr>
          <th>Documento</th>
          <th>Nombre</th>
          <th>Correo</th>
          <th>Telefono</th>
          <th>Rol</th>
          <th>Estado</th>
        </tr>
      </thead>
      <tbody>
        @forelse($usuarios as $usuario)
          <tr>
            <td>{{ $usuario->tipo_documento?->tipo ?? 'Doc' }} {{ $usuario->id_usuario }}</td>
            <td><strong>{{ trim($usuario->nombres . ' ' . $usuario->apellidos) }}</strong></td>
            <td>{{ $usuario->correo }}</td>
            <td>{{ $usuario->telefono ?? '-' }}</td>
            <td>{{ $usuario->rol }}</td>
            <td><span class="badge {{ $usuario->estado === 'Activo' ? 'bg-success' : 'bg-secondary' }}">{{ $usuario->estado }}</span></td>
          </tr>
        @empty
          <tr><td colspan="6" class="text-center text-muted py-4">No hay datos para estos filtros.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  {{ $usuarios->links() }}
</div>
@endsection
