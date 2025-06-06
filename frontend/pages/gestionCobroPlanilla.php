<?php
require_once '../../backend/includes/auth.php';
verificarPermiso(['Administrador']);
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
      <main class="container-xl my-2 col-10 mx-auto">
        <div class="container-fluid px-4 ">
          <h1 class="mb-4 text-center">Gestión de Cobro de Planilla</h1>

          <div class="row">
            <div class="col-12 d-flex justify-content-between align-items-center">
              <div class="d-flex align-items-center">
              <label for="inicio" class="me-2">Inicio:</label>
              <input type="date" id="inicio" class="form-control me-2" placeholder="Inicio">
              <label for="fin" class="me-2">Fin:</label>
              <input type="date" id="fin" class="form-control me-2" placeholder="Fin">
              <input type="text" class="form-control me-2" placeholder="Buscar Socio">
              <a href="#" class="btn btn-primary">Buscar</a>
              </div>
              <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#miModal">Agregar</a>
            </div>
          </div>
          <!-- Modal Agregar -->
          <div class="modal fade " id="miModal" tabindex="-1" aria-labelledby="miModalLabel">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header ">
                  <h3 class="modal-title" id="miModalLabel">Agregar Cobro de Planilla</h3>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                  <form action="" method="">
                    <div class="mb-3">
                      <label for="socio" class="form-label fw-bold">Socio:</label>
                      <input type="text" class="form-control" id="socio" name="socio" required>
                    </div>
                    <div class="mb-3">
                      <label for="montoTotal" class="form-label fw-bold">Monto total:</label>
                      <input type="text" class="form-control" id="montoTotal" name="montoTotal" required>
                    </div>
                    <div class="mb-3">
                      <label for="totalPagado" class="form-label fw-bold">Total Pagado:</label>
                      <input type="number" class="form-control" id="totalPagado" name="totalPagado" required>
                    </div>
                    <div class="mb-3">
                      <label for="pagoPendiente" class="form-label fw-bold">Pago Pendiente:</label>
                      <input type="number" class="form-control" id="pagoPendiente" name="pagoPendiente" required>
                    </div>
                    <div class="mb-3">
                      <label for="fechaEmision" class="form-label fw-bold">Fecha Inicio:</label>
                      <input type="date" class="form-control" id="fechaEmision" name="fechaEmision" required>
                    </div>
                    <div class="mb-3">
                      <label for="fechaVencimiento" class="form-label fw-bold">Fecha Fin:</label>
                      <input type="date" class="form-control" id="fechaVencimiento" name="fechaVencimiento" required>
                    </div>
                    <div class="mb-3 d-flex align-items-center">
                      <label for="estado" class="form-label fw-bold me-3">Estado:</label>
                      <select id="estado" name="estado" class="form-select w-auto" required>
                        <option value="" disabled selected>Seleccione una opción</option>
                        <option value="Pendiente">Pendiente</option>
                        <option value="Completo">Completo</option>
                      </select>
                    </div> <br>

                    <div class="modal-footer d-flex justify-content-center">
                      <button type="submit" class="btn btn-primary">Agregar</button>
                    </div>
                  </form>
                </div>

              </div>
            </div>
          </div>
          <!-- Modal Editar -->
          <div class="modal fade" id="modalEditar">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h3 class="modal-title" id="miModalLabel">Actualización de Cobro de Planilla</h3>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                  <form action="" method="">
                    <input type="hidden" id="editarId">
                    <div class="mb-3">
                      <label for="socio" class="form-label fw-bold">Socio:</label>
                      <input type="text" class="form-control" id="editarSocio" name="socio" required>
                    </div>
                    <div class="mb-3">
                      <label for="montoTotal" class="form-label fw-bold">Monto total:</label>
                      <input type="text" class="form-control" id="editarMontoTotal" name="montoTotal" required>
                    </div>
                    <div class="mb-3">
                      <label for="totalPagado" class="form-label fw-bold">Total Pagado:</label>
                      <input type="number" class="form-control" id="editarTotalPagado" name="totalPagado" required>
                    </div>
                    <div class="mb-3">
                      <label for="pagoPendiente" class="form-label fw-bold">Pago Pendiente:</label>
                      <input type="number" class="form-control" id="editarPagoPendiente" name="pagoPendiente" required>
                    </div>
                    <div class="mb-3">
                      <label for="fechaEmision" class="form-label fw-bold">Fecha Inicio:</label>
                      <input type="date" class="form-control" id="editarFechaEmision" name="fechaEmision" required>
                    </div>
                    <div class="mb-3">
                      <label for="fechaVencimiento" class="form-label fw-bold">Fecha Fin:</label>
                      <input type="date" class="form-control" id="editarFechaVencimiento" name="fechaVencimiento" required>
                    </div>
                    <div class="mb-3 d-flex align-items-center">
                      <label for="estado" class="form-label fw-bold me-3">Estado:</label>
                      <select id="editarEstado" name="estado" class="form-select w-auto" required>
                        <option value="" disabled selected>Seleccione una opción</option>
                        <option value="Pendiente">Pendiente</option>
                        <option value="Completo">Completo</option>
                      </select>
                    </div> <br>

                    <div class="modal-footer d-flex justify-content-center">
                      <button type="submit" class="btn btn-success">Modificar</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>

          <div class="table-responsive my-5">
            <table class="table  table-bordered table-hover text-center">
              <thead>
                <tr class="table-dark">
                  <th>ID</th>
                  <th>Socio</th>
                  <th>Monto Total</th>
                  <th>Total Pagado</th>
                  <th>Pago Pendiente</th>
                  <th>Fecha Inicio</th>
                  <th>Fecha Fin</th>
                  <th>Estado</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody class="align-middle">
              </tbody>
            </table>
          </div>

          <!-- Paginación -->
          <nav aria-label="Page navigation example" class="d-flex justify-content-end ">
            <ul class="pagination">
              <li class="page-item">
                <a class="page-link" href="#" aria-label="Previous">
                  <span aria-hidden="true">&laquo;</span>
                </a>
              </li>
              <li class="page-item"><a class="page-link" href="#">1</a></li>
              <li class="page-item"><a class="page-link" href="#">2</a></li>
              <li class="page-item"><a class="page-link" href="#">3</a></li>
              <li class="page-item">
                <a class="page-link" href="#" aria-label="Next">
                  <span aria-hidden="true">&raquo;</span>
                </a>
              </li>
            </ul>
          </nav>
        </div>
    </div>
  </div>
    <!-- Toast Bootstrap Personalizado -->
    <div id="toastAgregar" class="toast align-items-center border-0 position-fixed bottom-0 end-0 mb-3 me-3 z-3"
    role="alert" aria-live="assertive" aria-atomic="true" style="min-width: 250px;">
    <div class="toast-header bg-success text-white d-flex justify-content-between w-100">
      <strong class="me-auto">Registro Exitoso</strong>
      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body bg-white text-dark" id="toastMessageAgregar">

    </div>
  </div>

  <div id="toastEditar" class="toast align-items-center border-0 position-fixed bottom-0 end-0 mb-3 me-3 z-3"
    role="alert" aria-live="assertive" aria-atomic="true" style="min-width: 250px;">
    <div class="toast-header bg-success text-white">
      <strong class="me-auto">Edicion Exitosa</strong>
      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body bg-white text-dark" id="toastMessageEditar">

    </div>
  </div>

  <div id="toastEliminar" class="toast align-items-center border-0 position-fixed bottom-0 end-0 mb-3 me-3 z-3"
    role="alert" aria-live="assertive" aria-atomic="true" style="min-width: 250px;">
    <div class="toast-header bg-danger text-white">
      <strong class="me-auto">Eliminación Exitosa</strong>
      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body bg-white text-dark" id="toastMessageEliminar">

    </div>
  </div>


  <div class="modal fade" id="modalEliminarConfirmacion" tabindex="-1" aria-labelledby="modalEliminarLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title" id="modalEliminarLabel">¿Confirmar Eliminación?</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          ¿Estás seguro de que deseas eliminar este usuario?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
          <button type="button" class="btn btn-danger" id="btnConfirmarEliminar">Sí, eliminar</button>
        </div>
      </div>
    </div>
  </div>

  <script src="../js/functions/gestionCobroPlanilla.js"></script>
  <script src="../js/bootstrap.bundle.min.js"></script>
</body>

</html>