<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detalle-envío</title>
 <link href="bootstrap.css" rel="stylesheet">
  <<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
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

  <a href="dashboard_emp.html" class="active"><i class="bi bi-grid me-2"></i>Dashboard</a>
  <a href="ped_emp.html"><i class="bi bi-cart-fill me-2"></i>Pedidos</a>
  <a href="prod_emp.html"><i class="bi bi-box me-2"></i>Productos</a>
  <a href="mat_emp.html"><i class="bi bi-stack me-2"></i>Materiales</a>
  <a href="env_emp.html"><i class="bi bi-truck me-2"></i>Envíos</a>

  <hr class="mt-auto">
  <button class="btn btn-light w-100" onclick="window.location.href='index.html'"><i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión</button>
</div>
<div class="content">

  <!-- Título -->
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h4>Detalle del Pedido</h4>
      <p class="mb-0">Información completa del pedido</p>
    </div>

    <a href="ped_emp.html" class="btn btn-outline-dark btn-sm">
      ← Volver
    </a>
  </div>

  <!-- Card principal -->
  <div class="card card-custom p-4">

    <!-- Encabezado -->
    <div class="d-flex justify-content-between flex-wrap">
      <div>
        <h5>Pedido #001</h5>
        <p class="mb-1">Cliente: Juan Pérez</p>
        <p class="mb-0">Fecha entrega: 10/04/2026</p>
      </div>

      <div>
        <span class="badge bg-warning text-dark">Pendiente</span>
      </div>
    </div>

    <hr>

    <!-- Producto -->
    <div class="mb-3">
      <h6>Producto</h6>
      <p class="mb-1"><strong>Chaqueta deportiva</strong></p>
      <p class="mb-0 text-muted">Chaqueta impermeable color negro, talla M</p>
    </div>

    <hr>

    <!-- Dirección -->
    <div class="mb-3">
      <h6>Dirección de envío</h6>
      <p class="mb-0">
        Calle 123 #45-67 <br>
        Bogotá, Colombia
      </p>
    </div>

    <hr>

    <!-- Información de pago -->
    <div class="row mb-3">
      <div class="col-md-4">
        <h6>Valor producto</h6>
        <p>$100.000</p>
      </div>

      <div class="col-md-4">
        <h6>Costo envío</h6>
        <p>$20.000</p>
      </div>

      <div class="col-md-4">
        <h6>Total</h6>
        <p><strong>$120.000</strong></p>
      </div>
    </div>

    <hr>

    <!-- Estado -->
    <div class="mb-3">
      <h6>Estado del pedido</h6>

      <select class="form-control">
        <option>Pendiente</option>
        <option>En proceso</option>
        <option>Enviado</option>
        <option>Entregado</option>
      </select>
    </div>

    <!-- Botones -->
    <div class="d-flex gap-2 flex-wrap">
      <button class="btn btn-main">
        Guardar cambios
      </button>

      <button class="btn btn-outline-danger">
        Cancelar pedido
      </button>
    </div>

  </div>

</div>

</body>
</html>