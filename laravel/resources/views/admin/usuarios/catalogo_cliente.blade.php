<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Catálogo – Creaciones Camar</title>
   <link href="bootstrap.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <link href="fonts.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
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

<!-- Hero Banner -->
<section class="hero-banner">
  <div class="hero-content">
    <p class="hero-tag">Nueva colección 2025</p>
    <h1 class="hero-title">Colección de<br><em>chaquetas</em></h1>
    <p class="hero-sub">Descubre nuestra selección premium de chaquetas para cada ocasión</p>
  </div>
  <div class="hero-decoration"></div>
</section>

<!-- Filtros y catálogo -->
<div class="catalog-container">
  <div class="search-filter-bar">
    <div class="search-wrap">
      <i class="bi bi-search search-icon"></i>
      <input type="text" id="searchInput" class="search-input" placeholder="Buscar chaquetas..." oninput="filtrar()">
    </div>
    <div class="filter-pills">
      <button class="pill active">Todas</button>
      <button class="pill">Premium</button>
      <button class="pill">Deportiva</button>
      <button class="pill">Casual</button>
      <button class="pill">Urbana</button>
    </div>
  </div>

  <div class="products-grid" id="productsGrid">

    <a href="detalle_prod_clie.html" class="product-card" data-categoria="Premium">
      <div class="product-img-wrap">
        <div class="product-img-placeholder"><i class="bi bi-image"></i></div>
        <span class="product-category-badge">Premium</span>
      </div>
      <div class="product-info">
        <div class="product-top">
          <div>
            <p class="product-name">Chaqueta Alpina</p>
            <p class="product-desc">Corte slim, forro interior</p>
          </div>
          <span class="product-status disponible">Disponible</span>
        </div>
        <div class="product-bottom">
          <span class="product-price">$120.000</span>
        </div>
      </div>
    </a>

    <a href="detalle_prod_clie.html" class="product-card" data-categoria="Deportiva">
      <div class="product-img-wrap">
        <div class="product-img-placeholder"><i class="bi bi-image"></i></div>
        <span class="product-category-badge">Deportiva</span>
      </div>
      <div class="product-info">
        <div class="product-top">
          <div>
            <p class="product-name">Chaqueta Sprint</p>
            <p class="product-desc">Tela técnica, transpirable</p>
          </div>
          <span class="product-status disponible">Disponible</span>
        </div>
        <div class="product-bottom">
          <span class="product-price">$95.000</span>
        </div>
      </div>
    </a>

    <a href="detalle_prod_clie.html" class="product-card" data-categoria="Casual">
      <div class="product-img-wrap">
        <div class="product-img-placeholder"><i class="bi bi-image"></i></div>
        <span class="product-category-badge">Casual</span>
      </div>
      <div class="product-info">
        <div class="product-top">
          <div>
            <p class="product-name">Chaqueta Urban</p>
            <p class="product-desc">Algodón premium, casual</p>
          </div>
          <span class="product-status agotado">Agotado</span>
        </div>
        <div class="product-bottom">
          <span class="product-price">$85.000</span>
        </div>
      </div>
    </a>

    <a href="detalle_prod_clie.html" class="product-card" data-categoria="Urbana">
      <div class="product-img-wrap">
        <div class="product-img-placeholder"><i class="bi bi-image"></i></div>
        <span class="product-category-badge">Urbana</span>
      </div>
      <div class="product-info">
        <div class="product-top">
          <div>
            <p class="product-name">Chaqueta Metro</p>
            <p class="product-desc">Diseño urbano, versátil</p>
          </div>
          <span class="product-status disponible">Disponible</span>
        </div>
        <div class="product-bottom">
          <span class="product-price">$110.000</span>
        </div>
      </div>
    </a>

    <a href="detalle_prod_clie.html" class="product-card" data-categoria="Premium">
      <div class="product-img-wrap">
        <div class="product-img-placeholder"><i class="bi bi-image"></i></div>
        <span class="product-category-badge">Premium</span>
      </div>
      <div class="product-info">
        <div class="product-top">
          <div>
            <p class="product-name">Chaqueta Vogue</p>
            <p class="product-desc">Alta costura, exclusiva</p>
          </div>
          <span class="product-status disponible">Disponible</span>
        </div>
        <div class="product-bottom">
          <span class="product-price">$180.000</span>
        </div>
      </div>
    </a>

    <a href="detalle_prod_clie.html" class="product-card" data-categoria="Casual">
      <div class="product-img-wrap">
        <div class="product-img-placeholder"><i class="bi bi-image"></i></div>
        <span class="product-category-badge">Casual</span>
      </div>
      <div class="product-info">
        <div class="product-top">
          <div>
            <p class="product-name">Chaqueta Brisa</p>
            <p class="product-desc">Ligera, para primavera</p>
          </div>
          <span class="product-status disponible">Disponible</span>
        </div>
        <div class="product-bottom">
          <span class="product-price">$75.000</span>
        </div>
      </div>
    </a>

  </div>
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


</body>
</html>
