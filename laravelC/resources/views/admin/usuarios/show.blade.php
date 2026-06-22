@extends('layouts.app_admin')
@section('title', 'Detalle del usuario')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h4 class="mb-0">Detalle del usuario</h4>
    <p class="text-muted small mb-0">Información registrada del usuario seleccionado</p>
  </div>
  <a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left me-1"></i> Volver
  </a>
</div>

<div class="card card-custom p-4">
  <div class="row g-3">
    <div class="col-12 col-md-6">
      <label class="form-label text-muted">Nombres</label>
      <div class="form-control bg-light">{{ $usuario->nombres ?? '—' }}</div>
    </div>

    <div class="col-12 col-md-6">
      <label class="form-label text-muted">Apellidos</label>
      <div class="form-control bg-light">{{ $usuario->apellidos ?? '—' }}</div>
    </div>

    <div class="col-12 col-md-6">
      <label class="form-label text-muted">Correo</label>
      <div class="form-control bg-light">{{ $usuario->email ?? '—' }}</div>
    </div>

    <div class="col-12 col-md-6">
      <label class="form-label text-muted">Teléfono</label>
      <div class="form-control bg-light">{{ $usuario->telefono ?? '—' }}</div>
    </div>

    <div class="col-12 col-md-6">
      <label class="form-label text-muted">Rol</label>
      <div class="form-control bg-light">{{ $usuario->rol ?? '—' }}</div>
    </div>

    <div class="col-12 col-md-6">
      <label class="form-label text-muted">Estado</label>
      <div class="form-control bg-light">{{ $usuario->estado ?? '—' }}</div>
    </div>
  </div>
</div>

@endsection
