@extends('layouts.app_admin')
@section('title', 'Editar Usuario')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h4 class="mb-0">Editar usuario</h4>
    <p class="text-muted small mb-0">Modifica la información del usuario</p>
  </div>
  <a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left me-1"></i> Volver
  </a>
</div>

<div class="card card-custom p-4">
  <h6 class="mb-4 text-muted">Información</h6>

  <form method="POST" action="{{ route('admin.usuarios.update', $usuario) }}">
    @csrf
    @method('PUT')
    <div class="row g-3">

      <div class="col-12 col-md-6">
        <label class="form-label">ID de usuario <span class="text-danger">*</span></label>
        <input type="text" name="id_usuario"
               class="form-control @error('id_usuario') is-invalid @enderror"
               value="{{ old('id_usuario', $usuario->id_usuario) }}">
        @error('id_usuario')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>

      <div class="col-12 col-md-6">
        <label class="form-label">Tipo de documento <span class="text-danger">*</span></label>
        <select name="tipo_documento_id" class="form-control @error('tipo_documento_id') is-invalid @enderror">
          <option value="">Selecciona tipo de documento</option>
          @foreach($tiposDocumento as $tipoDocumento)
            <option value="{{ $tipoDocumento->id_tipo }}" {{ old('tipo_documento_id', $usuario->tipo_documento_id) == $tipoDocumento->id_tipo ? 'selected' : '' }}>{{ $tipoDocumento->tipo }}</option>
          @endforeach
        </select>
        @error('tipo_documento_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>

      <div class="col-12 col-md-6">
        <label class="form-label">Nombres <span class="text-danger">*</span></label>
        <input type="text" name="nombres"
               class="form-control @error('nombres') is-invalid @enderror"
               value="{{ old('nombres', $usuario->nombres) }}">
        @error('nombres')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>

      <div class="col-12 col-md-6">
        <label class="form-label">Apellidos <span class="text-danger">*</span></label>
        <input type="text" name="apellidos"
               class="form-control @error('apellidos') is-invalid @enderror"
               value="{{ old('apellidos', $usuario->apellidos) }}">
        @error('apellidos')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>

      

      <div class="col-12 col-md-6">
        <label class="form-label">Email <span class="text-danger">*</span></label>
        <input type="email" name="email"
               class="form-control @error('email') is-invalid @enderror"
               value="{{ old('email', $usuario->email) }}">
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>

      <div class="col-12 col-md-6">
        <label class="form-label">Teléfono <span class="text-danger">*</span></label>
        <input type="tel" name="telefono"
               class="form-control @error('telefono') is-invalid @enderror"
               value="{{ old('telefono', $usuario->telefono) }}" minlength="8" maxlength="16" required>
        @error('telefono')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>

      <div class="col-12 col-md-6">
        <label class="form-label">
          Nueva contraseña
          <small class="text-muted">(dejar en blanco para no cambiar)</small>
        </label>
        <input type="password" name="password"
               class="form-control @error('password') is-invalid @enderror"
               placeholder="Nueva contraseña">
        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>

      <div class="col-12 col-md-6">
        <label class="form-label">Rol <span class="text-danger">*</span></label>
        <select name="rol" class="form-control @error('rol') is-invalid @enderror">
          @foreach(['Admin','Empleado','Cliente'] as $rol)
            <option value="{{ $rol }}" {{ old('rol', $usuario->rol) === $rol ? 'selected' : '' }}>
              {{ $rol }}
            </option>
          @endforeach
        </select>
        @error('rol')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>

      <div class="col-12 col-md-6">
        <label class="form-label">Estado <span class="text-danger">*</span></label>
        <select name="estado" class="form-control @error('estado') is-invalid @enderror">
          @foreach(['Activo','Inactivo'] as $estado)
            <option value="{{ $estado }}" {{ old('estado', $usuario->estado) === $estado ? 'selected' : '' }}>
              {{ $estado }}
            </option>
          @endforeach
        </select>
        @error('estado')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>

    </div>

    <div class="d-flex justify-content-center gap-3 mt-4">
      <a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline-secondary px-4">Cancelar</a>
      <button type="submit" class="btn btn-main px-4">Actualizar</button>
    </div>
  </form>
</div>

@endsection
