<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Panel Empleado') - Creaciones Camar</title>
  <link href="{{ asset('css/bootstrap.css') }}" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

<!-- Sidebar -->
<div class="sidebar p-3 d-flex flex-column">
  <div class="mb-3 text-center">
    <img src="{{ asset('img/logo.png') }}" alt="Logo" class="logo mx-auto">
    <p class="mb-0 fw-bold mt-2">Creaciones Camar</p>
    <small class="text-muted">Empleado</small>
  </div>

  <a href="{{ route('empleado.dashboard') }}" class="{{ request()->routeIs('empleado.dashboard') ? 'active' : '' }}">
    <i class="bi bi-grid me-2"></i>Dashboard
  </a>
  <a href="{{ route('empleado.pedidos.index') }}" class="{{ request()->routeIs('empleado.pedidos.*') ? 'active' : '' }}">
    <i class="bi bi-cart-fill me-2"></i>Pedidos
  </a>
  <a href="{{ route('empleado.productos') }}" class="{{ request()->routeIs('empleado.productos.*') ? 'active' : '' }}">
    <i class="bi bi-box me-2"></i>Productos
  </a>
  <a href="{{ route('empleado.materiales') }}" class="{{ request()->routeIs('empleado.materiales.*') ? 'active' : '' }}">
    <i class="bi bi-stack me-2"></i>Materiales
  </a>
  <a href="{{ route('empleado.envios') }}" class="{{ request()->routeIs('empleado.envios.*') ? 'active' : '' }}">
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
