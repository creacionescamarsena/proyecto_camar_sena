@extends('layouts.app_admin')
@section('title', 'Detalle producto')

@section('content')
<div class="d-flex justify-content-end mb-3">
  <a href="{{ route('admin.productos') }}" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left me-1"></i> Volver
  </a>
</div>

<div class="card card-custom p-4">
  <h4 class="mb-3">{{ $producto->modelo_chaqueta }}</h4>
  <p class="text-muted">Categoría: {{ $producto->categoria?->tipo_categoria ?? 'Sin categoría' }}</p>
  <p class="fw-semibold">Precio: ${{ number_format($producto->precio, 0, ',', '.') }}</p>

  @if($producto->imagen)
    <img src="{{ asset('storage/' . $producto->imagen) }}" alt="{{ $producto->modelo_chaqueta }}" class="img-fluid rounded mb-3" style="max-height:260px; object-fit:cover;">
  @endif

  <ul class="mb-0">
    @foreach($producto->materiales as $material)
      <li>{{ $material->material }}</li>
    @endforeach
  </ul>
</div>
@endsection
