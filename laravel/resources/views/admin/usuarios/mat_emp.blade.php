<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Materiales - Creaciones Camar</title>
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
    <small>Empleado</small>
  </div>

  <a href="dashboard_emp.html" ><i class="bi bi-grid me-2"></i>Dashboard</a>
  <a href="ped_emp.html"><i class="bi bi-cart-fill me-2"></i>Pedidos</a>
  <a href="prod_emp.html" ><i class="bi bi-box me-2"></i>Productos</a>
  <a href="mat_emp.html" class="active"><i class="bi bi-stack me-2"></i>Materiales</a>
  <a href="env_emp.html"><i class="bi bi-truck me-2"></i>Envíos</a>

  <hr class="mt-auto">
  <button class="btn btn-light w-100" onclick="window.location.href='index.html'"><i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión</button>
</div>

<!-- Contenido -->
<div class="content">

  <div class="d-flex justify-content-between align-items-center mb-1">
    <div>
      <h4 class="mb-0">Materiales</h4>
      <p class="text-muted small">Gestiona los materiales para productos</p>
    </div>
    
  </div>

  <!-- Alerta stock bajo -->
  <div class="alert-stock mt-3 mb-3 d-flex align-items-center gap-3">
    <i class="bi bi-exclamation-triangle-fill fs-4" style="color:#856404"></i>
    <div>
      <strong>Alerta de stock bajo</strong><br>
      <span class="small">2 materiales necesitan reabastecimiento</span>
    </div>
  </div>

  <!-- Tabla materiales -->
  <div class="card card-custom p-3">
    <h6 class="mb-3">Lista de materiales</h6>
    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th>Material</th>
            <th>Tipo</th>
            <th>Stock</th>
            <th>Estado</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Forro térmico</td>
            <td>Forro</td>
            <td>10</td>
            <td><span class="badge bg-success">Disponible</span></td>
          </tr>
          <tr>
            <td>Forro térmico</td>
            <td>Forro</td>
            <td>10</td>
            <td><span class="badge bg-success">Disponible</span></td>
          </tr>
          <tr>
            <td>Forro térmico</td>
            <td>Forro</td>
            <td>10</td>
            <td><span class="badge bg-success">Disponible</span></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

</div>

</body>
</html>
