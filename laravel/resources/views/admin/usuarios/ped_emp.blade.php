<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pedidos - Creaciones Camar</title>
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
  <a href="ped_emp.html" class="active"><i class="bi bi-cart-fill me-2"></i>Pedidos</a>
  <a href="prod_emp.html" ><i class="bi bi-box me-2"></i>Productos</a>
  <a href="mat_emp.html" ><i class="bi bi-stack me-2"></i>Materiales</a>
  <a href="env_emp.html"><i class="bi bi-truck me-2"></i>Envíos</a>

  <hr class="mt-auto">
  <button class="btn btn-light w-100" onclick="window.location.href='index.html'"><i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión</button>
</div>

<!-- Contenido -->
<div class="content">

  <h4>Pedidos</h4>
  <p>Actualiza el estado de los pedidos</p>

  <!-- Pedido 1 -->
  <div class="card card-custom p-3 mb-3">

    <div class="d-flex justify-content-between">
      <div>
        <strong>Pedido #001</strong><br>
        Juan Pérez - 10/04/2026
      </div>
      <div>
        <span class="badge bg-warning text-dark">Pendiente</span>
      </div>
    </div>

    <hr>

    <div class="d-flex justify-content-between flex-wrap">
      <div>
        <strong>Nombre del producto</strong><br>
        Descripción del producto
      </div>

      <div>
        <strong>$120.000</strong><br>
        Valor total envío
      </div>
    </div>

    <hr>

    <div>
      <strong>Dirección envío:</strong><br>
      Calle 123, Bogotá, Colombia
    </div>

    <div class="mt-3 d-flex gap-2 flex-wrap">
      <a href="detalle_envio.html" class="btn btn-outline-dark btn-sm">
         Ver detalles
      </a>
    </div>

  </div>

  <!-- Pedido 2 -->
  <div class="card card-custom p-3 mb-3">

    <div class="d-flex justify-content-between">
      <div>
        <strong>Pedido #002</strong><br>
        María López - 11/04/2026
      </div>
      <div>
        <span class="badge bg-primary">En proceso</span>
      </div>
    </div>

    <hr>

    <div class="d-flex justify-content-between flex-wrap">
      <div>
        <strong>Nombre del producto</strong><br>
        Descripción del producto
      </div>

      <div>
        <strong>$95.000</strong><br>
        Valor total envío
      </div>
    </div>

    <hr>

    <div>
      <strong>Dirección envío:</strong><br>
      Carrera 50, Medellín, Colombia
    </div>

    <div class="mt-3 d-flex gap-2 flex-wrap">
      <a href="detalle_envio.html" class="btn btn-outline-dark btn-sm">
         Ver detalles
      </a>

      <button class="btn btn-main btn-sm">
        Marcar como enviado
      </button>
    </div>

  </div>

</div>



</body>
</html>
