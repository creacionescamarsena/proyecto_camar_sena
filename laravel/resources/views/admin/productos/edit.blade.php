@extends('layouts.app_admin')
@section('title', 'Editar producto')

@php
  use Illuminate\Support\Facades\Schema;
  use Illuminate\Support\Facades\DB;
@endphp

@section('content')
<div class="d-flex justify-content-end mb-3">
  <a href="{{ route('admin.productos') }}" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left me-1"></i> Volver
  </a>
</div>

<h4 class="mb-4">Editar producto</h4>
<div class="card card-custom p-4">
  <h6 class="mb-4 text-muted">Información del producto</h6>

  <form method="POST" action="{{ route('admin.productos.update', ['producto' => $producto->getKey()]) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="row g-3">
      <div class="col-12 col-md-6">
        <label class="form-label">ID de chaqueta</label>
        <input type="number" name="id_chaqueta" class="form-control @error('id_chaqueta') is-invalid @enderror" value="{{ old('id_chaqueta', $producto->id_chaqueta) }}">
        @error('id_chaqueta')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="col-12 col-md-6">
        <label class="form-label">Nombre</label>
        <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $producto->modelo_chaqueta) }}">
        @error('nombre')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
      
      <div class="col-12 col-md-6">
        <label class="form-label">Categoría</label>
        <select name="categoria_id" class="form-select @error('categoria_id') is-invalid @enderror mb-2">
          <option value="">Seleccionar categoría existente</option>
          @foreach($categorias as $id => $nombre)
            <option value="{{ $id }}" {{ old('categoria_id', $producto->categoria?->id_categoria) == $id ? 'selected' : '' }}>{{ $nombre }}</option>
          @endforeach
        </select>
        @error('categoria_id')
          <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
        <input type="text" name="categoria_nueva" class="form-control @error('categoria_nueva') is-invalid @enderror mt-2" value="{{ old('categoria_nueva') }}" placeholder="O escribe una nueva categoría">
        @error('categoria_nueva')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
      <div class="col-12 col-md-6">
        <label class="form-label">Precio (COP)</label>
        <div class="input-group">
          <span class="input-group-text">COP</span>
          <input type="number" step="0.1" min="100" name="precio" class="form-control @error('precio') is-invalid @enderror" value="{{ old('precio', $producto->precio) }}">
        </div>
        @error('precio')
          <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
      </div>
      <div class="col-12">
        <label class="form-label">Cantidades por talla</label>
        <div class="row g-2">
          @php
            // Obtener cantidades actuales del stock
            $stockActual = collect([]);
            if (Schema::hasTable('stock')) {
              $stockActual = DB::table('stock')
                ->where('chaqueta_id_chaqueta', $producto->id_chaqueta)
                ->pluck('cantidad', 'talla_id_talla');
            }
          @endphp
          @foreach($tallas as $talla)
            <div class="col-12 col-md-4">
              <div class="input-group">
                <span class="input-group-text">{{ $talla->talla }}</span>
                <input type="number" min="0" name="cantidades[{{ $talla->id_talla }}]" class="form-control @error('cantidades.' . $talla->id_talla) is-invalid @enderror" value="{{ old('cantidades.' . $talla->id_talla, $stockActual[$talla->id_talla] ?? 0) }}" placeholder="0">
              </div>
              @error('cantidades.' . $talla->id_talla)
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          @endforeach
        </div>
        @error('cantidades')
          <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
      </div>
      <div class="col-12 col-md-6">
        <label class="form-label">Imagen</label>
        <input type="file" name="imagen" class="form-control @error('imagen') is-invalid @enderror" accept="image/*">
        @error('imagen')
          <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
      </div>
      <div class="col-12">
        <label class="form-label">Materiales</label>
        <select name="materiales[]" class="form-select @error('materiales') is-invalid @enderror" multiple>
          @foreach($materiales as $material)
            <option value="{{ $material->id_materiales }}" {{ in_array($material->id_materiales, old('materiales', $producto->materiales->pluck('id_materiales')->toArray())) ? 'selected' : '' }}>
              {{ $material->material }}
            </option>
          @endforeach
        </select>
        @error('materiales')
          <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
        @error('materiales.*')
          <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
      </div>
    </div>

    <div class="d-flex justify-content-center gap-3 mt-4">
      <a href="{{ route('admin.productos') }}" class="btn btn-outline-secondary px-4">Cancelar</a>
      <button type="submit" class="btn btn-main px-4">Actualizar</button>
    </div>
  </form>
</div>
@endsection
