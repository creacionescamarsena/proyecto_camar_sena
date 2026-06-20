<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Productos - Creaciones Camar</title>
   <link href="bootstrap.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- Sidebar -->
<div class="sidebar p-3 d-flex flex-column">
  <div class="mb-3 text-center">
    <img src="logo.png" alt="Logo" style="width:70px; height:70px; object-fit:contain;">
    <p class="mb-0 fw-bold mt-1">Creaciones Camar</p>
    <small>Administrador</small>
  </div>

  <a href="dashboard_admin.html"><i class="bi bi-grid me-2"></i>Dashboard</a>
  <a href="usu_admin.html"><i class="bi bi-person me-2"></i>Usuarios</a>
  <a href="prod_admin.html" class="active"><i class="bi bi-box me-2"></i>Productos</a>
  <a href="mat_admin.html"><i class="bi bi-stack me-2"></i>Materiales</a>
  <a href="inv_admin.html"><i class="bi bi-clipboard me-2"></i>Inventario</a>
  <a href="fac_admin.html"><i class="bi bi-receipt me-2"></i>Facturación</a>
  <a href="env_admin.html"><i class="bi bi-truck me-2"></i>Envíos</a>

  <hr class="mt-auto">
  <button class="btn btn-light w-100" onclick="window.location.href='index.html'"><i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión</button>
</div>

<!-- Contenido -->
<div class="content">

  <div class="d-flex justify-content-between align-items-center mb-1">
    <div>
      <h4 class="mb-0">Productos</h4>
      <p class="text-muted small">Gestiona el catálogo de productos</p>
    </div>
    
  </div>

  <!-- Grid de productos -->
  <div class="row g-3 mt-2">

    <!-- Producto 1 -->
    <div class="col-12 col-md-6">
      <div class="card card-custom p-3 producto-card">
        <div class="d-flex justify-content-between align-items-start mb-2">
          <div>
            <h6 class="mb-0 fw-bold">Chaqueta casual</h6>
            <small class="text-muted">casual</small>
          </div>
          <div class="d-flex gap-2">
            
            </button>
          </div>
        </div>

        <p class="text-muted small mb-1">Descripción del producto</p>
        <p class="fw-semibold mb-2">$120.000</p>

        <p class="small mb-1"><strong>Tallas disponibles</strong></p>
        <div class="d-flex gap-2 mb-3 flex-wrap">
          <span class="talla-badge">S</span>
          <span class="talla-badge">M</span>
          <span class="talla-badge">L</span>
          <span class="talla-badge">XL</span>
          <span class="talla-badge">XXL</span>
        </div>

        <p class="small mb-1"><strong>Materiales</strong></p>
        <p class="small text-muted mb-2">(forro térmico), (algodón orgánico)</p>

        <div class="d-flex justify-content-between align-items-center stock-bar pt-2">
          <span class="small text-muted">Stock total</span>
          <span class="badge bg-success">48 unidades</span>
        </div>
      </div>
    </div>

    <!-- Producto 2 -->
    <div class="col-12 col-md-6">
      <div class="card card-custom p-3 producto-card">
        <div class="d-flex justify-content-between align-items-start mb-2">
          <div>
            <h6 class="mb-0 fw-bold">Chaqueta casual</h6>
            <small class="text-muted">casual</small>
          </div>
          <div class="d-flex gap-2">
            
            </button>
          </div>
        </div>

        <p class="text-muted small mb-1">Descripción del producto</p>
        <p class="fw-semibold mb-2">$120.000</p>

        <p class="small mb-1"><strong>Tallas disponibles</strong></p>
        <div class="d-flex gap-2 mb-3 flex-wrap">
          <span class="talla-badge">S</span>
          <span class="talla-badge">M</span>
          <span class="talla-badge">L</span>
          <span class="talla-badge">XL</span>
          <span class="talla-badge">XXL</span>
        </div>

        <p class="small mb-1"><strong>Materiales</strong></p>
        <p class="small text-muted mb-2">(forro térmico), (algodón orgánico)</p>

        <div class="d-flex justify-content-between align-items-center stock-bar pt-2">
          <span class="small text-muted">Stock total</span>
          <span class="badge bg-success">32 unidades</span>
        </div>
      </div>
    </div>

    <!-- Producto 3 -->
    <div class="col-12 col-md-6">
      <div class="card card-custom p-3 producto-card">
        <div class="d-flex justify-content-between align-items-start mb-2">
          <div>
            <h6 class="mb-0 fw-bold">Chaqueta casual</h6>
            <small class="text-muted">casual</small>
          </div>
          <div class="d-flex gap-2">
            
            </button>
          </div>
        </div>

        <p class="text-muted small mb-1">Descripción del producto</p>
        <p class="fw-semibold mb-2">$95.000</p>

        <p class="small mb-1"><strong>Tallas disponibles</strong></p>
        <div class="d-flex gap-2 mb-3 flex-wrap">
          <span class="talla-badge">S</span>
          <span class="talla-badge">M</span>
          <span class="talla-badge">L</span>
          <span class="talla-badge">XL</span>
          <span class="talla-badge">XXL</span>
        </div>

        <p class="small mb-1"><strong>Materiales</strong></p>
        <p class="small text-muted mb-2">(forro térmico), (algodón orgánico)</p>

        <div class="d-flex justify-content-between align-items-center stock-bar pt-2">
          <span class="small text-muted">Stock total</span>
          <span class="badge bg-warning text-dark">8 unidades</span>
        </div>
      </div>
    </div>

    <!-- Producto 4 -->
    <div class="col-12 col-md-6">
      <div class="card card-custom p-3 producto-card">
        <div class="d-flex justify-content-between align-items-start mb-2">
          <div>
            <h6 class="mb-0 fw-bold">Chaqueta casual</h6>
            <small class="text-muted">casual</small>
          </div>
          <div class="d-flex gap-2">
            
            </button>
          </div>
        </div>

        <p class="text-muted small mb-1">Descripción del producto</p>
        <p class="fw-semibold mb-2">$110.000</p>

        <p class="small mb-1"><strong>Tallas disponibles</strong></p>
        <div class="d-flex gap-2 mb-3 flex-wrap">
          <span class="talla-badge">S</span>
          <span class="talla-badge">M</span>
          <span class="talla-badge">L</span>
          <span class="talla-badge">XL</span>
          <span class="talla-badge">XXL</span>
        </div>

        <p class="small mb-1"><strong>Materiales</strong></p>
        <p class="small text-muted mb-2">(forro térmico), (algodón orgánico)</p>

        <div class="d-flex justify-content-between align-items-center stock-bar pt-2">
          <span class="small text-muted">Stock total</span>
          <span class="badge bg-success">21 unidades</span>
        </div>
      </div>
    </div>

  </div>
</div>

<!-- Modal confirmación eliminar -->
<div id="modalOverlay" class="modal-overlay" onclick="cerrarModal()">
  <div class="modal-confirm" onclick="event.stopPropagation()">
    <p class="fw-semibold fs-5 mb-4">¿Está seguro de eliminar <span id="nombreProducto"></span>?</p>
    <div class="d-flex justify-content-center gap-4">
      <button class="btn-icon-cancel" onclick="cerrarModal()" title="Cancelar">
        <i class="bi bi-x-lg"></i>
      </button>
      <button class="btn-icon-confirm" onclick="eliminarProducto()" title="Confirmar">
        <i class="bi bi-check-lg"></i>
      </button>
    </div>
  </div>
</div>



<script>
  function mostrarModal(nombre) {
    document.getElementById('nombreProducto').textContent = nombre;
    document.getElementById('modalOverlay').classList.add('show');
  }
  function cerrarModal() {
    document.getElementById('modalOverlay').classList.remove('show');
  }
  function eliminarProducto() {
    alert('Producto eliminado correctamente.');
    cerrarModal();
  }
</script>

</body>
</html>
