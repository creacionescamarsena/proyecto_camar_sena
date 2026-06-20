<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Actualizar Material - Creaciones Camar</title>
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
  <a href="mat_admin.html" class="active"><i class="bi bi-stack me-2"></i>Materiales</a>
  <a href="inv_admin.html"><i class="bi bi-clipboard me-2"></i>Inventario</a>
  <a href="fac_admin.html"><i class="bi bi-receipt me-2"></i>Facturación</a>
  <a href="env_admin.html"><i class="bi bi-truck me-2"></i>Envíos</a>

  <hr class="mt-auto">
  <button class="btn btn-light w-100" onclick="window.location.href='index.html'"><i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión</button>
</div>

<!-- Contenido -->
<div class="content">

  <div class="d-flex justify-content-end mb-3">
    <a href="mat_admin.html" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left me-1"></i> Volver
    </a>
  </div>

  <h4 class="mb-4">Actualizar material</h4>

  <div class="card card-custom p-4">
    <h6 class="mb-4 text-muted">Información</h6>

    <form>
      <div class="row g-3">
        <div class="col-12 col-md-6">
          <label class="form-label">Material</label>
          <input type="text" class="form-control" value="Forro térmico">
        </div>
        <div class="col-12 col-md-6">
          <label class="form-label">Proveedor material</label>
          <input type="text" class="form-control" value="Textiles S.A.">
        </div>
        <div class="col-12 col-md-6">
          <label class="form-label">Precio</label>
          <input type="number" class="form-control" value="15000">
        </div>
        <div class="col-12 col-md-6">
          <label class="form-label">Cantidad</label>
          <input type="number" class="form-control" value="10">
        </div>
      </div>

      <div class="d-flex justify-content-center gap-3 mt-4">
        <a href="mat_admin.html" class="btn btn-outline-secondary px-4">Cancelar</a>
        <button type="submit" class="btn btn-main px-4">Actualizar</button>
      </div>
    </form>
  </div>

</div>

<style>
  .sidebar a.active { background-color: #3e5525; border-radius: 6px; }
</style>

</body>
</html>
