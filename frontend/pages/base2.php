<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="" />
  <meta name="author" content="" />
  <title>Lubricentro Cusqueña</title>
  <link href="../css/bootstrap.css" rel="stylesheet" />
  <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body>
  <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
    <!-- Navbar Brand-->
    <a class="navbar-brand ps-3" href="base2.php">La Cusqueña</a>
    <!--Fin Navbar Brand-->
    <!-- Sidebar Toggle-->
    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i
        class="fas fa-bars"></i></button>
    <!-- Fin Sidebar Toggle-->
    <!-- Navbar-->
    <ul class="navbar-nav ms-auto me-3 me-lg-4 text-end">
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown"
          aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
          <li><a class="dropdown-item" href="../../index.html">Cerrar Sesion</a></li>
        </ul>
      </li>
    </ul>
    <!-- Fin Navbar -->
  </nav>
  <div id="layoutSidenav">
    <div id="layoutSidenav_nav">
      <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
          <div class="nav">
            <div class="sb-sidenav-menu-heading">Gestión</div>
            <a class="nav-link collapsed" href="gestionProducto.php" data-bs-toggle="collapse" data-bs-target="#collapseLayoutsProductos"
              aria-expanded="false" aria-controls="collapseLayouts">
              <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
              Gestionar Productos
              <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
            </a>
            <div class="collapse" id="collapseLayoutsProductos" aria-labelledby="headingOne"
              data-bs-parent="#sidenavAccordion">
              <nav class="sb-sidenav-menu-nested nav">
                <a class="nav-link" href="gestionProducto.php">Gestión de Productos</a>
                <a class="nav-link" href="gestionHistorialIngreso.php">Ingresos de Productos</a>
              </nav>
            </div>
            <a class="nav-link" href="gestionServicio.php">
              <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
              Gestionar Servicios
            </a>
            <a class="nav-link collapsed" href="gestionConductor.html" data-bs-toggle="collapse"
              data-bs-target="#collapseLayoutsConductores" aria-expanded="false" aria-controls="collapseLayouts">
              <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
              Gestionar Conductores
              <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
            </a>
            <div class="collapse" id="collapseLayoutsConductores" aria-labelledby="headingOne"
              data-bs-parent="#sidenavAccordion">
              <nav class="sb-sidenav-menu-nested nav">
                <a class="nav-link" href="gestionSoat.php">Lista de SOAT</a>
              </nav>
            </div>
            <a class="nav-link" href="gestionAlquiler.php">
              <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
              Gestionar Alquileres
            </a>
            <div class="sb-sidenav-menu-heading">Balances</div>
            <a class="nav-link" href="balanceServicio.php">
              <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
              Balance de Servicios
            </a>
            <a class="nav-link" href="balanceProducto.php">
              <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
              Balance de Productos
            </a>
            <a class="nav-link" href="balacenGastos.php">
              <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
              Balance de Gastos
            </a>
          </div>
        </div>
        <div class="sb-sidenav-footer">
          <div class="small">Sesion Inciada como:</div>
          Secretaria
        </div>
      </nav>
    </div>
    <div id="layoutSidenav_content">
      <main class="container-xl my-2 col-10 mx-auto">
        <div class="container-fluid px-4 ">
          <h1 class="mb-4 text-center">Titulo de la Sección</h1>
        </div>
      </main>

    </div>
  </div>
  <script src="../js/bootstrap.bundle.min.js"></script>
</body>

</html>