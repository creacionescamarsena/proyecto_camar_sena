<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detalle producto – Creaciones Camar</title>
   <link href="bootstrap.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <link href="fonts.css" rel="stylesheet">
  <link rel="stylesheet" href="style_cliente.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar-cliente">
  <div class="nav-brand">
  <img src="logo.png" alt="Logo" style="width:65px; height:65px; object-fit:contain;">
    <span class="brand-name">Creaciones Camar</span>
  </div>
  <div class="nav-links">
    <a href="catalogo_cliente.html" class="nav-link-item active">Catálogo</a>
    <a href="pedidos_cliente.html" class="nav-link-item">Mis pedidos</a>
  </div>
  <div class="nav-actions">
    <a href="carrito_cliente.html" class="nav-icon-btn" title="Carrito">
      <i class="bi bi-cart3"></i>
      <span class="cart-badge">2</span>
    </a>
    <button class="nav-icon-btn" onclick="document.getElementById('modalUsuario').classList.add('show')" title="Perfil">
      <i class="bi bi-person-circle"></i>
    </button>
    <a href="index.html" class="nav-icon-btn" title="Cerrar sesión">
      <i class="bi bi-box-arrow-right"></i>
    </a>
  </div>
</nav>

<!-- Detalle -->
<div class="detalle-container">
  <a href="catalogo_cliente.html" class="btn-back">
    <i class="bi bi-arrow-left"></i> Volver al catálogo
  </a>

  <div class="detalle-grid">
    <!-- Imagen -->
    <div class="detalle-img-wrap">
      <div class="detalle-img-placeholder"><i class="bi bi-image"></i></div>
    </div>

    <!-- Info -->
    <div class="detalle-info">
      <p class="detalle-cat">Premium</p>
      <h1 class="detalle-nombre">Chaqueta Alpina</h1>
      <p class="detalle-desc">
        Chaqueta de corte slim con forro interior en polar suave. Ideal para climas fríos sin sacrificar el estilo. 
        Confeccionada a mano con materiales de primera calidad seleccionados por nuestros artesanos.
      </p>

      <p class="detalle-precio">$120.000</p>

      <!-- Materiales -->
      <div>
        <p class="detalle-section-label">Materiales</p>
        <div class="materiales-wrap">
          <span class="material-tag">Algodón</span>
          <span class="material-tag">Poliéster</span>
          <span class="material-tag">Forro polar</span>
        </div>
      </div>

      <!-- Tallas -->
      <div>
        <p class="detalle-section-label">Selecciona tu talla</p>
        <div class="tallas-wrap">
          <button class="talla-btn" onclick="selTalla(this)">XS</button>
          <button class="talla-btn" onclick="selTalla(this)">S</button>
          <button class="talla-btn active" onclick="selTalla(this)">M</button>
          <button class="talla-btn" onclick="selTalla(this)">L</button>
          <button class="talla-btn" onclick="selTalla(this)">XL</button>
        </div>
      </div>

      <!-- Cantidad -->
      <div>
        <p class="detalle-section-label">Cantidad</p>
        <div class="cantidad-wrap">
          <button class="cantidad-btn" onclick="cambiarCant(-1)">−</button>
          <span class="cantidad-num" id="cantidad">1</span>
          <button class="cantidad-btn" onclick="cambiarCant(1)">+</button>
        </div>
      </div>

      <!-- Botón agregar -->
      <button class="btn-agregar" onclick="agregarCarrito()">
        <i class="bi bi-cart-plus"></i> Agregar al carrito
      </button>
    </div>
  </div>
</div>

<!-- Toast confirmación -->
<div id="toast" style="
  position:fixed; bottom:28px; right:28px;
  background:var(--verde); color:white;
  padding:14px 22px; border-radius:12px;
  font-size:0.9rem; font-weight:600;
  box-shadow:0 4px 20px rgba(80,109,47,0.3);
  opacity:0; transform:translateY(10px);
  transition:all 0.3s; pointer-events:none;
  display:flex; align-items:center; gap:10px;
">
  <i class="bi bi-check-circle-fill"></i> Producto agregado al carrito
</div>

<!-- Modal Usuario -->
<div class="modal-overlay" id="modalUsuario">
  <div class="modal-user">
    <button class="modal-close" onclick="document.getElementById('modalUsuario').classList.remove('show')">
      <i class="bi bi-x-lg"></i>
    </button>
    <div class="user-avatar-lg"><i class="bi bi-person-fill"></i></div>
    <h5 class="modal-user-title">Datos del usuario</h5>
    <div class="user-field-group">
      <label>Nombres</label>
      <div class="user-field">
        <input type="text" value="Carlos" disabled id="inputNombre">
        <button class="edit-btn" onclick="toggleEdit('inputNombre', this)"><i class="bi bi-pencil"></i></button>
      </div>
      <label>Apellidos</label>
      <div class="user-field">
        <input type="text" value="Martínez" disabled id="inputApellido">
        <button class="edit-btn" onclick="toggleEdit('inputApellido', this)"><i class="bi bi-pencil"></i></button>
      </div>
      <label>Dirección</label>
      <div class="user-field">
        <input type="text" value="Calle 10 # 5-30" disabled id="inputDireccion">
        <button class="edit-btn" onclick="toggleEdit('inputDireccion', this)"><i class="bi bi-pencil"></i></button>
      </div>
      <label>Teléfono</label>
      <div class="user-field">
        <input type="text" value="3001234567" disabled id="inputTelefono">
        <button class="edit-btn" onclick="toggleEdit('inputTelefono', this)"><i class="bi bi-pencil"></i></button>
      </div>
      <label>Correo</label>
      <div class="user-field">
        <input type="email" value="carlos@email.com" disabled id="inputCorreo">
        <button class="edit-btn" onclick="toggleEdit('inputCorreo', this)"><i class="bi bi-pencil"></i></button>
      </div>
      <label>Contraseña</label>
      <div class="user-field">
        <input type="password" value="mipassword" disabled id="inputPass">
        <button class="edit-btn" onclick="toggleEdit('inputPass', this)"><i class="bi bi-pencil"></i></button>
      </div>
    </div>
  </div>
</div>

<script>
  function selTalla(btn) {
    document.querySelectorAll('.talla-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
  }

  let cantidad = 1;
  function cambiarCant(delta) {
    cantidad = Math.max(1, cantidad + delta);
    document.getElementById('cantidad').textContent = cantidad;
  }

  function agregarCarrito() {
    const toast = document.getElementById('toast');
    toast.style.opacity = '1';
    toast.style.transform = 'translateY(0)';
    setTimeout(() => {
      toast.style.opacity = '0';
      toast.style.transform = 'translateY(10px)';
    }, 2500);
  }

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
</body>
</html>
