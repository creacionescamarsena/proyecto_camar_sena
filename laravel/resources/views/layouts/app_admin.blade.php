<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Panel Admin') - Creaciones Camar</title>
  <link href="{{ asset('css/bootstrap.css') }}" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

<!-- Sidebar -->
<div class="sidebar p-3 d-flex flex-column">
  <div class="mb-3 text-center">
    <img src="{{ asset('img/logo.png') }}" alt="Logo" class="logo mx-auto">
    <p class="mb-0 fw-bold mt-1">Creaciones Camar</p>
    <small>{{ auth()->user()?->nombres }}</small>
    <br>
    <small>Administrador</small>
  </div>

  <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
    <i class="bi bi-grid me-2"></i>Dashboard
  </a>
  <a href="{{ route('admin.usuarios.index') }}" class="{{ request()->routeIs('admin.usuarios.*') ? 'active' : '' }}">
    <i class="bi bi-person me-2"></i>Usuarios
  </a>
  <a href="{{ route('admin.productos') }}" class="{{ request()->routeIs('admin.productos.*') ? 'active' : '' }}">
    <i class="bi bi-box me-2"></i>Productos
  </a>
  <a href="{{ route('admin.materiales.index') }}" class="{{ request()->routeIs('admin.materiales.*') ? 'active' : '' }}">
    <i class="bi bi-stack me-2"></i>Materiales
  </a>
  <a href="{{ route('admin.inventario.index') }}" class="{{ request()->routeIs('admin.inventario.*') ? 'active' : '' }}">
    <i class="bi bi-clipboard me-2"></i>Inventario
  </a>
  <a href="{{ route('admin.facturacion') }}" class="{{ request()->routeIs('admin.facturacion.*') ? 'active' : '' }}">
    <i class="bi bi-receipt me-2"></i>Facturación
  </a>
  <a href="{{ route('admin.envios') }}" class="{{ request()->routeIs('admin.envios.*') ? 'active' : '' }}">
    <i class="bi bi-truck me-2"></i>Envíos
  </a>
  <hr class="mt-auto">
  <form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit" class="btn btn-light w-100">
      <i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión
    </button>
  </form>
</div>

<!-- Contenido -->
<div class="content">
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
      {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  @yield('content')
</div>

@stack('scripts')

<script>
  (function(){
    function fadeOutAlert(alert){
      alert.style.transition = 'opacity 350ms ease';
      alert.style.opacity = '0';
      setTimeout(function(){ if (alert.parentNode) alert.remove(); }, 350);
    }

    document.querySelectorAll('.btn-close').forEach(function(btn){
      btn.addEventListener('click', function(){
        var alert = btn.closest('.alert');
        if (alert) fadeOutAlert(alert);
      });
    });

    document.querySelectorAll('.alert').forEach(function(alert){
      setTimeout(function(){ fadeOutAlert(alert); }, 3500);
    });
  })();
</script>

</body>
</html>
