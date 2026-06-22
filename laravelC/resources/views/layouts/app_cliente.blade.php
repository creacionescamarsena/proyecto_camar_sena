<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Tienda') - Creaciones Camar</title>
  <link href="{{ asset('css/bootstrap.css') }}" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <link href="{{ asset('css/fonts.css') }}" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/style_cliente.css') }}">
</head>
<body> 

<!-- Navbar -->
<nav class="navbar-cliente">
  <div class="nav-brand">
    <img src="{{ asset('img/logo.png') }}" alt="Logo" style="width:65px; height:65px; object-fit:contain;">
    <span class="brand-name">Creaciones Camar</span>
  </div>
  <div class="nav-links">
    <a href="{{ route('cliente.catalogo') }}"
       class="nav-link-item {{ request()->routeIs('cliente.catalogo') || request()->routeIs('cliente.producto.*') ? 'active' : '' }}">
      Catálogo
    </a>
    <a href="{{ route('cliente.pedidos') }}"
       class="nav-link-item {{ request()->routeIs('cliente.pedidos') ? 'active' : '' }}">
      Mis pedidos
    </a>
  </div>
  <div class="nav-actions">
    <a href="{{ route('cliente.carrito') }}" class="nav-icon-btn {{ request()->routeIs('cliente.carrito') ? 'active' : '' }}" title="Carrito">
      <i class="bi bi-cart3"></i>
      @if(isset($cartCount) && $cartCount > 0)
        <span class="cart-badge">{{ $cartCount }}</span>
      @endif
    </a>
    <button class="nav-icon-btn" onclick="document.getElementById('modalUsuario').classList.add('show')" title="Perfil">
      <i class="bi bi-person-circle"></i>
    </button>
    <form method="POST" action="{{ route('logout') }}" style="display:inline;">
      @csrf
      <button type="submit" class="nav-icon-btn" title="Cerrar sesión" style="background:none;border:none;">
        <i class="bi bi-box-arrow-right"></i>
      </button>
    </form>
  </div>
</nav>

@yield('content')

<!-- Modal Usuario -->
<div class="modal-overlay" id="modalUsuario">
  <div class="modal-user">
    <button class="modal-close" onclick="document.getElementById('modalUsuario').classList.remove('show')">
      <i class="bi bi-x-lg"></i>
    </button>
    <div class="user-avatar-lg"><i class="bi bi-person-fill"></i></div>
    <h5 class="modal-user-title">Datos del usuario</h5>
    <form method="POST" action="{{ route('cliente.perfil.update') }}">
      @csrf
      @method('PUT')
      <div class="user-field-group">
        <label>Nombres</label>
        <div class="user-field">
          <input type="text" name="nombres" value="{{ auth()->user()->nombres ?? '' }}" disabled id="inputNombre">
          <button type="button" class="edit-btn" onclick="toggleEdit('inputNombre', this)"><i class="bi bi-pencil"></i></button>
        </div>
        <label>Apellidos</label>
        <div class="user-field">
          <input type="text" name="apellidos" value="{{ auth()->user()->apellidos ?? '' }}" disabled id="inputApellido">
          <button type="button" class="edit-btn" onclick="toggleEdit('inputApellido', this)"><i class="bi bi-pencil"></i></button>
        </div>
        <label>Dirección</label>
        <div class="user-field">
          <input type="text" name="direccion" value="{{ auth()->user()->direccion ?? '' }}" disabled id="inputDireccion">
          <button type="button" class="edit-btn" onclick="toggleEdit('inputDireccion', this)"><i class="bi bi-pencil"></i></button>
        </div>
        <label>Teléfono</label>
        <div class="user-field">
          <input type="text" name="telefono" value="{{ auth()->user()->telefono ?? '' }}" disabled id="inputTelefono">
          <button type="button" class="edit-btn" onclick="toggleEdit('inputTelefono', this)"><i class="bi bi-pencil"></i></button>
        </div>
        <label>Correo</label>
        <div class="user-field">
          <input type="email" name="email" value="{{ auth()->user()->email ?? '' }}" disabled id="inputCorreo">
          <button type="button" class="edit-btn" onclick="toggleEdit('inputCorreo', this)"><i class="bi bi-pencil"></i></button>
        </div>
        <label>Contraseña</label>
        <div class="user-field">
          <input type="password" name="password" placeholder="Nueva contraseña" disabled id="inputPass">
          <button type="button" class="edit-btn" onclick="toggleEdit('inputPass', this)"><i class="bi bi-pencil"></i></button>
        </div>
      </div>
      <div class="mt-3 text-center">
        <button type="submit" class="btn btn-main px-4">Guardar cambios</button>
      </div>
    </form>
  </div>
</div>

@stack('scripts')

<script>
  function toggleEdit(id, btn) {
    const input = document.getElementById(id);
    if (input.disabled) {
      input.disabled = false;
      input.focus();
      btn.innerHTML = '<i class="bi bi-check-lg"></i>';
      btn.style.color = '#506d2f';
    } else {
      input.disabled = true;
      btn.innerHTML = '<i class="bi bi-pencil"></i>';
      btn.style.color = '';
    }
  }
  document.getElementById('modalUsuario').addEventListener('click', function(e) {
    if (e.target === this) this.classList.remove('show');
  });
</script>

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
