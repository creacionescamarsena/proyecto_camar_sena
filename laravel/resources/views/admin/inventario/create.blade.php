@extends('layouts.app_admin')
@section('title', 'Añadir Producto al Inventario')

@section('content')

<div class="d-flex justify-content-end mb-3">
  <a href="{{ route('admin.inventario.index') }}" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left me-1"></i> Volver
  </a>
</div>

<h4 class="mb-4">Añadir producto</h4>

<div class="card card-custom p-4">
  <h6 class="mb-4 text-muted">Información producto</h6>

  <form method="POST" action="{{ route('admin.inventario.store') }}">
    @csrf
    <div class="row g-3">

      <div class="col-12 col-md-6">
        <label class="form-label">Modelo chaqueta</label>
        <input type="text" name="modelo" class="form-control @error('modelo') is-invalid @enderror"
               placeholder="-" value="{{ old('modelo') }}">
        @error('modelo') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>

      <div class="col-12 col-md-6">
        <label class="form-label">Precio</label>
        <input type="number" name="precio" class="form-control @error('precio') is-invalid @enderror"
               placeholder="0" value="{{ old('precio') }}" min="0">
        @error('precio') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>

      <div class="col-12 col-md-6">
        <label class="form-label">Categoría</label>
        <input type="text" name="categoria" class="form-control @error('categoria') is-invalid @enderror"
               placeholder="-" value="{{ old('categoria') }}">
        @error('categoria') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>

      <div class="col-12 col-md-6">
        <label class="form-label">Talla</label>
        <input type="text" name="talla" class="form-control @error('talla') is-invalid @enderror"
               placeholder="-" value="{{ old('talla') }}">
        @error('talla') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>

      <div class="col-12 col-md-6">
        <label class="form-label">Catálogo</label>
        <input type="text" name="catalogo" class="form-control @error('catalogo') is-invalid @enderror"
               placeholder="-" value="{{ old('catalogo') }}">
        @error('catalogo') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>

      <div class="col-12 col-md-6">
        <label class="form-label">Stock</label>
        <input type="number" name="stock" class="form-control @error('stock') is-invalid @enderror"
               placeholder="0" value="{{ old('stock') }}" min="0">
        @error('stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>

      <div class="col-12 col-md-6">
        <label class="form-label">Código</label>
        <input type="number" name="codigo" class="form-control @error('codigo') is-invalid @enderror"
               placeholder="0" value="{{ old('codigo') }}">
        @error('codigo') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>

    </div>

    <div class="d-flex justify-content-center gap-3 mt-4">
      <a href="{{ route('admin.inventario.index') }}" class="btn btn-outline-secondary px-4">Cancelar</a>
      <button type="submit" class="btn btn-main px-4">Guardar</button>
    </div>
  </form>
</div>

@endsection
