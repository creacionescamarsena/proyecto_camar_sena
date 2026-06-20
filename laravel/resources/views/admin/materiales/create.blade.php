@extends('layouts.app_admin')
@section('title', 'Añadir Material')

@section('content')

<div class="d-flex justify-content-end mb-3">
  <a href="{{ route('admin.materiales.index') }}" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left me-1"></i> Volver
  </a>
</div>

<h4 class="mb-4">Añadir material</h4>

<div class="card card-custom p-4">
  <h6 class="mb-4 text-muted">Información material</h6>

  <form method="POST" action="{{ route('admin.materiales.store') }}">
    @csrf
    <div class="row g-3">

      <div class="col-12 col-md-6">
        <label class="form-label">ID de material</label>
        <input type="number" name="id_materiales" class="form-control @error('id_materiales') is-invalid @enderror"
               placeholder="ID de material (opcional)" value="{{ old('id_materiales') }}">
        @error('id_materiales') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>

      <div class="col-12 col-md-6">
        <label class="form-label">Material</label>
        <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
               placeholder="Nombre del material" value="{{ old('nombre') }}">
        @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>

      

      <div class="col-12 col-md-6">
        <label class="form-label">Proveedor material</label>
        <input type="text" name="proveedor" class="form-control @error('proveedor') is-invalid @enderror"
               placeholder="Nombre del proveedor" value="{{ old('proveedor') }}">
        @error('proveedor') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>

      <div class="col-12 col-md-6">
        <label class="form-label">Precio</label>
        <input type="number" name="precio" class="form-control @error('precio') is-invalid @enderror"
               placeholder="0" value="{{ old('precio') }}" min="0">
        @error('precio') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>

      <div class="col-12 col-md-6">
        <label class="form-label">Cantidad</label>
        <input type="number" name="cantidad" class="form-control @error('cantidad') is-invalid @enderror"
               placeholder="0" value="{{ old('cantidad') }}" min="0">
        @error('cantidad') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>

    </div>

    <div class="d-flex justify-content-center gap-3 mt-4">
      <a href="{{ route('admin.materiales.index') }}" class="btn btn-outline-secondary px-4">Cancelar</a>
      <button type="submit" class="btn btn-main px-4">Guardar</button>
    </div>
  </form>
</div>

@endsection
