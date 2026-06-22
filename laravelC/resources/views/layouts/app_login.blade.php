<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Usuarios') – Creaciones Camar</title>
  <link href="{{ asset('css/bootstrap.css') }}" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
  @stack('styles')
</head>
<body>

<!-- Navbar simple -->
<!--<nav class="navbar navbar-light px-4 py-3" style="background:#fff; border-bottom:1px solid #e5e7eb; display:flex; justify-content:space-between; align-items:center;">
  <div class="d-flex align-items-center gap-2">
    <img src="{{ asset('img/logo.png') }}" alt="Logo" style="width:42px;height:42px;object-fit:contain;">
    <span style="font-weight:700; font-size:1.05rem; color:#2d3748;">Creaciones Camar</span>
  </div>
  <span style="font-size:0.85rem; color:#6b7280;">Panel de Administrador</span>
</nav>
-->

<!-- Contenido -->
<div class="content" style="max-width:1100px; margin:0 auto; padding:32px 24px;">

  {{-- Alertas de sesión --}}
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
      <i class="bi bi-check-circle-fill"></i>
      {{ session('success') }}
      <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
  @endif

  @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
      <i class="bi bi-exclamation-triangle-fill"></i>
      {{ session('error') }}
      <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
  @endif

  @yield('content')
</div>

@stack('scripts')
</body>
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
</html>
</body>
</html>