<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inventario</title>
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
  <a href="prod_admin.html"><i class="bi bi-box me-2"></i>Productos</a>
  <a href="mat_admin.html" ><i class="bi bi-stack me-2"></i>Materiales</a>
  <a href="inv_admin.html" class="active"><i class="bi bi-clipboard me-2"></i>Inventario</a>
  <a href="fac_admin.html"><i class="bi bi-receipt me-2"></i>Facturación</a>
  <a href="env_admin.html"><i class="bi bi-truck me-2"></i>Envíos</a>

  <hr class="mt-auto">
  <button class="btn btn-light w-100" onclick="window.location.href='index.html'"><i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión</button>
</div>

<!-- Contenido -->
<div class="content">

  <div class="d-flex justify-content-between align-items-center mb-1">
    <div>
      <h4 class="mb-0">Inventario</h4>
      <p class="text-muted small">Gestiona el stock de productos por tallas</p>
    </div>
    <a href="add_inv_admin.html" class="btn btn-main">
      <i class="bi bi-plus-circle me-1"></i> nuevo producto
    </a>
  </div>

  <!-- Tarjetas -->
  <div class="row g-3 mb-4">

    <div class="col-12 col-md-4">
      <div class="card card-custom p-3">
        <h6>Ventas totales</h6>
        <p>$12.000.000</p>
      </div>
    </div>

    <div class="col-12 col-md-4">
      <div class="card card-custom p-3">
        <h6>Envíos pendientes</h6>
        <p>3</p>
      </div>
    </div>

    <div class="col-12 col-md-4">
      <div class="card card-custom p-3">
        <h6>Total productos</h6>
        <p>246</p>
      </div>
    </div>

  </div>

  <!-- Tabla inventario-->
  <div class="card card-custom p-3">
    <h6 class="mb-3">Lista de inventario</h6>
    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th>Producto</th>
            <th>Talla S</th>
            <th>Talla M</th>
            <th>Talla L</th>
            <th>Total</th>
            <th>stock</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Chaqueta deportiva</td>
            <td>12</td>
            <td>8</td>
            <td>5</td>
            <td>25</td>
            <td><span class="badge bg-warning text-dark">bajo</span></td>
          </tr>
          <tr>
            <td>Chaqueta deportiva</td>
            <td>12</td>
            <td>8</td>
            <td>5</td>
            <td>25</td>
            <td><span class="badge bg-warning text-dark">bajo</span></td>
          </tr>
          <tr>
            <td>Chaqueta deportiva</td>
            <td>12</td>
            <td>8</td>
            <td>5</td>
            <td>25</td>
            <td><span class="badge bg-warning text-dark">bajo</span></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

</div>

</body>
</html>