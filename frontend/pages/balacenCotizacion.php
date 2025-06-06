<?php
require_once '../../backend/includes/auth.php';
verificarPermiso(['Administrador']); // Solo administradores pueden acceder
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Lubricentro Cusqueña</title>
  <link href="../css/bootstrap.css" rel="stylesheet" />
  <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
 </head>


<body class="sb-nav-fixed">
  <!-- Navbar Superior (fijo) -->
  <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark fixed-top">
    <a class="navbar-brand ps-3" href="base.php">La Cusqueña</a>
    <button class="btn btn-link btn-sm me-4" id="sidebarToggle">
      <i class="fas fa-bars"></i>
    </button>
    <ul class="navbar-nav ms-auto me-3">
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown">
          <i class="fas fa-user fa-fw"></i>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="../../index.html">Cerrar Sesión</a></li>
        </ul>
      </li>
    </ul>
  </nav>
  <div id="layoutSidenav">
    <div id="layoutSidenav_nav">
      <script>
        fetch('sidebear_Admin.php')
          .then(r => r.text())
          .then(html => document.getElementById('layoutSidenav_nav').innerHTML = html)
          .catch(e => console.error('Error cargando sidebar:', e));
      </script>
    </div>

    <div id="layoutSidenav_content">
      <main class="container-xl my-2 col-12 mx-auto">
        <div class="container-fluid px-4 ">
          <h1 class="mt-4 text-center mb-4 ">Balances de cotizaciones</h1>
          <div class="row">
            <div class="col-12 d-flex justify-content-between align-items-center flex-wrap">
                <div class="d-flex align-items-center flex-wrap">
                  <input type="date" id="fechaInicio" class="form-control me-2 mb-2" style="width: 130px">
                  <input type="date" id="fechaFin" class="form-control me-2 mb-2" style="width: 130px">
                  <button id="btnBuscar" type="button" class="btn btn-primary mb-2">Buscar</button>
                  <button id="btnReset" type="button" class="btn btn-secondary mb-2">Resetear</button>
                
              </div>
              <div class="d-flex align-items-center flex-wrap gap-2">
    
                <button onclick="window.print()" class="btn btn-primary">Imprimir</button>
                <button class="btn btn-primary">Exportar PDF</button>
              </div>
            

        </div>
          <div class="table-responsive my-4">
            <table class="table  table-bordered table-hover text-center">
              <thead>
                <tr class="table-dark">
                  <th>ID</th>
                  <th>Nombre</th>
                  <th>Apellido</th>
                  <th>Tipo conductor</th>
                  <th>cotizacion</th>             
                  <th>Placa</th>
                  <th>Fecha</th>
                </tr>
              </thead>
              <tbody id="tablaCotizaciones" class="align-middle">
                <tr>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="total-container d-flex align-items-center mt-3">
            <label for="total" class="form-label fw-bold me-2 ">TOTAL:</label>
            <input type="text" id="totalGeneral" class="form-control form-control-sm me-2 mb-2" style="width: 120px; font-size: 0.8rem;" readonly>
          </div>
  
          <nav aria-label="Page navigation example" class="d-flex justify-content-end">
            <ul class="pagination" id="pagination">
              <li class="page-item">
                <a class="page-link" href="#" aria-label="Previous" id="prev-page">
                  <span aria-hidden="true">&laquo;</span>
                </a>
              </li>
              <!-- Los números de página se generarán dinámicamente -->
              <li class="page-item">
                <a class="page-link" href="#" aria-label="Next" id="next-page">
                  <span aria-hidden="true">&raquo;</span>
                </a>
              </li>
            </ul>
          </nav>
        </main>
  
      </div>
    </div>
    
    <script src="../js/bootstrap.bundle.min.js"></script>
    <script src="../js/functions/balanceCotizacion.js"></script>
    <script src="../js/functions/paginacion.js"></script>
</body>
</html>