<?php
require_once '../../backend/includes/auth.php';
verificarPermiso(['Administrador', 'Secretaria']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Lubricentro Cusqueña - Gestión de Coordinadores</title>
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
          <h1 class="mb-4 text-center">Gestión de Coordinadores</h1>
          <div class="row">
            <div class="col-12 d-flex justify-content-between align-items-center">
              <div class="d-flex">
                <input type="text" class="form-control me-2" id="buscarCoordinador" placeholder="Buscar por Nombre">
                <input type="date" class="form-control me-2" id="fechaFiltro" placeholder="Filtrar por Fecha">
                <input type="text" class="form-control me-2" id="paraderoFiltro" placeholder="Filtrar por Paradero">
                <button class="btn btn-primary" id="btnBuscar">Buscar</button>
              </div>
              <?php if ($_SESSION['rol'] === 'Administrador'): ?>
              <div>
                <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAgregarCoordinador">Agregar Coordinador</a>
              </div>
              <?php endif; ?>
            </div>
          </div>

          <!-- Modal Agregar Coordinador -->
          <div class="modal fade" id="modalAgregarCoordinador">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h3 class="modal-title">Registro de Coordinador</h3>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                  <form id="formAgregarCoordinador">
                    <div class="mb-3">
                      <label for="nombre" class="form-label fw-bold">Nombre:</label>
                      <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="mb-3">
                      <label for="apellidos" class="form-label fw-bold">Apellidos:</label>
                      <input type="text" class="form-control" id="apellidos" name="apellidos" required>
                    </div>
                    <div class="mb-3">
                      <label for="paradero" class="form-label fw-bold">Paradero:</label>
                      <input type="text" class="form-control" id="paradero" name="paradero" required>
                    </div>
                    <div class="mb-3">
                      <label for="montoDiario" class="form-label fw-bold">Monto Diario:</label>
                      <input type="number" step="0.01" class="form-control" id="montoDiario" name="monto_diario" required>
                    </div>
                    <div class="mb-3">
                      <label for="fecha" class="form-label fw-bold">Fecha:</label>
                      <input type="date" class="form-control" id="fecha" name="fecha" required>
                    </div>
                    <div class="mb-3">
                      <label for="estado" class="form-label fw-bold">Estado:</label>
                      <select class="form-select" id="estado" name="estado" required>
                        <option value="Pendiente">Pendiente</option>
                        <option value="Pagado">Pagado</option>
                      </select>
                    </div>
                    <div class="mb-3">
                      <label for="contacto" class="form-label fw-bold">Contacto:</label>
                      <input type="text" class="form-control" id="contacto" name="contacto" required>
                    </div>
                    <div class="modal-footer d-flex justify-content-center">
                      <button type="submit" class="btn btn-primary">Agregar Coordinador</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>

          <!-- Modal Editar Coordinador -->
          <div class="modal fade" id="modalEditarCoordinador">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h3 class="modal-title">Editar Coordinador</h3>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                  <form id="formEditarCoordinador">
                    <input type="hidden" id="editCoordinadorId" name="coordinadorId">
                    <div class="mb-3">
                      <label for="edit_nombre" class="form-label fw-bold">Nombre:</label>
                      <input type="text" class="form-control" id="edit_nombre" name="nombre" required>
                    </div>
                    <div class="mb-3">
                      <label for="edit_apellidos" class="form-label fw-bold">Apellidos:</label>
                      <input type="text" class="form-control" id="edit_apellidos" name="apellidos" required>
                    </div>
                    <div class="mb-3">
                      <label for="edit_paradero" class="form-label fw-bold">Paradero:</label>
                      <select class="form-select" id="edit_paradero" name="paradero" required>
                        <option value="">Seleccione un paradero</option>
                        <option value="San Pedro">San Pedro</option>
                      </select>
                    </div>
                    <div class="mb-3">
                      <label for="edit_montoDiario" class="form-label fw-bold">Monto Diario:</label>
                      <input type="number" step="0.01" class="form-control" id="edit_montoDiario" name="montoDiario" required>
                    </div>
                    <div class="mb-3">
                      <label for="edit_fecha" class="form-label fw-bold">Fecha:</label>
                      <input type="date" class="form-control" id="edit_fecha" name="fecha" required>
                    </div>
                    <div class="mb-3">
                      <label for="edit_estado" class="form-label fw-bold">Estado:</label>
                      <select class="form-select" id="edit_estado" name="estado" required>
                        <option value="Pendiente">Pendiente</option>
                        <option value="Pagado">Pagado</option>
                      </select>
                    </div>
                    <div class="mb-3">
                      <label for="edit_contacto" class="form-label fw-bold">Contacto:</label>
                      <input type="text" class="form-control" id="edit_contacto" name="contacto">
                    </div>
                    <div class="modal-footer d-flex justify-content-center">
                      <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>

        <!-- Modal de Confirmación de Eliminación -->
      <div class="modal fade" id="modalEliminarConfirmacion" tabindex="-1" aria-labelledby="modalEliminarLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header bg-danger text-white">
              <h5 class="modal-title" id="modalEliminarLabel">¿Confirmar Eliminación?</h5>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
              ¿Estás seguro de que deseas eliminar este coordinador? Esta acción no se puede deshacer.
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="button" class="btn btn-danger" id="btnEliminarConfirmado">Eliminar</button>
            </div>
          </div>
        </div>
      </div>
          <!-- Tabla de Coordinadores -->
          <div class="table-responsive my-4">
            <table class="table table-striped table-bordered">
              <thead class="table-dark">
                <tr>
                  <th scope="col">Nombre</th>
                  <th scope="col">Apellidos</th>
                  <th scope="col">Paradero</th>
                  <th scope="col">Monto Diario</th>
                  <th scope="col">Fecha</th>
                  <th scope="col">Estado</th>
                  <th scope="col">Contacto</th>
                  <th scope="col">Acciones</th>
                </tr>
              </thead>
              <tbody id="tablaCoordinadores">
        
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
  <script src="../js/functions/gestionCoordinadores.js"></script>
</body>
</html>