<?php
require_once '../../backend/includes/auth.php';
verificarPermiso(['Administrador', 'Secretaria']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Lubricentro Cusqueña - Gestión de Dominical</title>
  <link href="../css/bootstrap.css" rel="stylesheet" />
  <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
  <style>
    .table {
      width: 100%;
      table-layout: auto;
    }
    .table th,
    .table td {
      text-align: center;
      vertical-align: middle;
      overflow-wrap: break-word;
      max-width: 180px;
      padding: 10px;
    }
    .table td button {
      margin: 0 3px;
      white-space: nowrap;
    }
    .table-responsive {
      overflow-x: auto;
    }
    @media (max-width: 768px) {
      .table th,
      .table td {
        font-size: 13px;
        max-width: 140px;
      }
    }
    .form-label.fw-bold {
        margin-bottom: 0.5rem;
    }
  </style>
</head>
<body class="sb-nav-fixed">
  <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark fixed-top">
    <?php if ($_SESSION['rol'] === 'Administrador'): ?>
    <a class="navbar-brand ps-3" href="base.php">La Cusqueña</a>
    <?php endif; ?>
    <?php if ($_SESSION['rol'] === 'Secretaria'): ?>
    <a class="navbar-brand ps-3" href="base2.php">La Cusqueña</a>
    <?php endif; ?>
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
      <?php if ($_SESSION['rol'] === 'Administrador'): ?>
      <script>
        fetch('sidebear_Admin.php')
          .then(r => r.text())
          .then(html => document.getElementById('layoutSidenav_nav').innerHTML = html)
          .catch(e => console.error('Error cargando sidebar:', e));
      </script>
      <?php endif; ?>
      <?php if ($_SESSION['rol'] === 'Secretaria'): ?>
      <script>
        fetch('sidebear_secre.php')
          .then(r => r.text())
          .then(html => document.getElementById('layoutSidenav_nav').innerHTML = html)
          .catch(e => console.error('Error cargando sidebar:', e));
      </script>
      <?php endif; ?>
    </div>
    <div id="layoutSidenav_content">
      <main class="container-xl my-2 col-11 mx-auto">
        <div class="container-fluid px-4">
          <h1 class="mb-4 text-center">Gestión de Dominical</h1>
          <div class="row">
            <div class="col-12 d-flex justify-content-between align-items-center">
              <div class="d-flex">
                <input type="text" class="form-control me-2" id="buscarDominical" placeholder="Buscar por Nombre">
                <input type="date" class="form-control me-2" id="semanaInicioFiltro" placeholder="Semana Inicio">
                <input type="date" class="form-control me-2" id="semanaFinFiltro" placeholder="Semana Fin">
                <button class="btn btn-primary" id="btnBuscar">Buscar</button>
              </div>
              <?php if ($_SESSION['rol'] === 'Administrador'): ?>
              <div>
                <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAgregarDominical">Agregar Dominical</a>
              </div>
              <?php endif; ?>
            </div>
          </div>

          <!-- Modal Agregar Dominical -->
          <div class="modal fade" id="modalAgregarDominical">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h3 class="modal-title">Registro de Dominical</h3>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
              <form id="formAgregarDominical">
                <div class="mb-3">
                  <label for="nombre" class="form-label fw-bold">Nombre:</label>
                  <input type="text" class="form-control" id="nombre" name="nombre" required>
                </div>
                <div class="mb-3">
                  <label for="apellidos" class="form-label fw-bold">Apellidos:</label>
                  <input type="text" class="form-control" id="apellidos" name="apellidos" required>
                </div>
                <div class="mb-3">
                  <label for="fechaDomingo" class="form-label fw-bold">Fecha Domingo:</label>
                  <input type="date" class="form-control" id="fechaDomingo" name="fecha_domingo" required>
                </div>
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="semanaInicio" class="form-label fw-bold">Semana Inicio:</label>
                    <input type="date" class="form-control" id="semanaInicio" name="semana_inicio" required>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="semanaFin" class="form-label fw-bold">Semana Fin:</label>
                    <input type="date" class="form-control" id="semanaFin" name="semana_fin" required>
                  </div>
                </div>
                <div class="mb-3">
                  <label for="montoDominical" class="form-label fw-bold">Monto Dominical:</label>
                  <input type="number" step="0.01" class="form-control" id="montoDominical" name="monto_dominical" required>
                </div>
                <div class="mb-3">
                  <label for="estado" class="form-label fw-bold">Estado:</label>
                  <select class="form-select" id="estado" name="estado" required>
                    <option value="Pendiente">Pendiente</option>
                    <option value="Pagado">Pagado</option>
                    <option value="Exento">Exento</option>
                  </select>
                </div>
                <div class="mb-3">
                  <label for="diferencia" class="form-label fw-bold">Diferencia:</label>
                  <input type="number" step="0.01" class="form-control" id="diferencia" name="diferencia" value="0.00">
                </div>
                <div class="modal-footer d-flex justify-content-center">
                  <button type="submit" class="btn btn-primary">Agregar Dominical</button>
                </div>
              </form>
               </div>
              </div>
            </div>
          </div>

          <!-- Modal Editar Dominical -->
          <div class="modal fade" id="modalEditarDominical">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h3 class="modal-title">Editar Dominical</h3>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                  <form id="formEditarDominical">
                    <input type="hidden" id="editDominicalId" name="dominicalId">
                    <div class="mb-3">
                      <label for="edit_cotizacionId" class="form-label fw-bold">Cotización:</label>
                      <select class="form-select" id="edit_cotizacionId" name="cotizacionId" required>
                        <!-- Opciones cargadas dinámicamente con JavaScript/PHP -->
                      </select>
                    </div>
                    <div class="mb-3">
                      <label for="edit_nombre" class="form-label fw-bold">Nombre:</label>
                      <input type="text" class="form-control" id="edit_nombre" name="nombre" required>
                    </div>
                    <div class="mb-3">
                      <label for="edit_apellidos" class="form-label fw-bold">Apellidos:</label>
                      <input type="text" class="form-control" id="edit_apellidos" name="apellidos" required>
                    </div>
                    <div class="mb-3">
                      <label for="edit_fechaDomingo" class="form-label fw-bold">Fecha Domingo:</label>
                      <input type="date" class="form-control" id="edit_fechaDomingo" name="fechaDomingo" required>
                    </div>
                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <label for="edit_semanaInicio" class="form-label fw-bold">Semana Inicio:</label>
                        <input type="date" class="form-control" id="edit_semanaInicio" name="semanaInicio" required>
                      </div>
                      <div class="col-md-6 mb-3">
                        <label for="edit_semanaFin" class="form-label fw-bold">Semana Fin:</label>
                        <input type="date" class="form-control" id="edit_semanaFin" name="semanaFin" required>
                      </div>
                    </div>
                    <div class="mb-3">
                      <label for="edit_montoDominical" class="form-label fw-bold">Monto Dominical:</label>
                      <input type="number" step="0.01" class="form-control" id="edit_montoDominical" name="montoDominical" required>
                    </div>
                    <div class="mb-3">
                      <label for="edit_estado" class="form-label fw-bold">Estado:</label>
                      <select class="form-select" id="edit_estado" name="estado" required>
                        <option value="Pendiente">Pendiente</option>
                        <option value="Pagado">Pagado</option>
                        <option value="Exento">Exento</option>
                      </select>
                    </div>
                    <div class="mb-3">
                      <label for="edit_diferencia" class="form-label fw-bold">Diferencia:</label>
                      <input type="number" step="0.01" class="form-control" id="edit_diferencia" name="diferencia" value="0.00">
                    </div>
                    <div class="modal-footer d-flex justify-content-center">
                      <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>

          <!-- Modal Ver Pagos -->
          <div class="modal fade" id="modalVerPagos" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Historial de Pagos de: <span id="nombreDominical"></span></h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <div class="mb-4 p-3 border rounded">
                    <h6>Registrar Nuevo Pago</h6>
                    <form id="formAgregarPago">
                      <input type="hidden" id="pagoDominicalId" name="dominicalId">
                      <div class="row align-items-end">
                        <div class="col-md-4">
                          <label for="fechaPago" class="form-label">Fecha de Pago</label>
                          <input type="date" class="form-control" id="fechaPago" required>
                        </div>
                        <div class="col-md-4">
                          <label for="montoPago" class="form-label">Monto Pagado</label>
                          <input type="number" step="0.01" class="form-control" id="montoPago" required>
                        </div>
                        <div class="col-md-4">
                          <button type="submit" class="btn btn-success w-100">Registrar Pago</button>
                        </div>
                      </div>
                    </form>
                  </div>
                  <h6>Historial</h6>
                  <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                      <thead class="table-light">
                        <tr>
                          <th>Fecha de Pago</th>
                          <th>Monto Pagado</th>
                          <th>Acciones</th>
                        </tr>
                      </thead>
                      <tbody id="tablaPagosHistorial">
                        <!-- Los datos se cargarán dinámicamente con JavaScript -->
                      </tbody>
                    </table>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
              </div>
            </div>
          </div>

          <!-- Tabla de Dominical -->
          <div class="table-responsive my-4">
            <table class="table table-striped table-bordered">
              <thead class="table-dark">
                <tr>
                  <th scope="col">Nombre</th>
                  <th scope="col">Apellidos</th>
                  <th scope="col">Fecha Domingo</th>
                  <th scope="col">Semana Inicio</th>
                  <th scope="col">Semana Fin</th>
                  <th scope="col">Monto Dominical</th>
                  <th scope="col">Estado</th>
                  <th scope="col">Diferencia</th>
                  <th scope="col">Acciones</th>
                </tr>
              </thead>
              <tbody id="tablaDominical">
                <!-- Los datos se cargarán dinámicamente con JavaScript o PHP -->
              </tbody>
            </table>
          </div>
          <nav aria-label="Page navigation">
            <ul class="pagination justify-content-end">
              <li class="page-item disabled">
                <a class="page-link" href="#" aria-label="Previous">
                  <span aria-hidden="true">«</span>
                </a>
              </li>
              <li class="page-item active"><a class="page-link" href="#">1</a></li>
              <li class="page-item"><a class="page-link" href="#">2</a></li>
              <li class="page-item"><a class="page-link" href="#">3</a></li>
              <li class="page-item">
                <a class="page-link" href="#" aria-label="Next">
                  <span aria-hidden="true">»</span>
                </a>
              </li>
            </ul>
          </nav>
        </div>
      </main>
    </div>
  </div>
  <script src="../js/bootstrap.bundle.min.js"></script>
  <script src="../js/functions/gestionDominical.js"></script>
</body>
</html>