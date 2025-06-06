<?php
require_once '../../backend/includes/auth.php';
verificarPermiso(['Administrador', 'Secretaria']);
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
      <main class="container-xl my-2 col-10 mx-auto">
        <div class="container-fluid px-4 ">
          <h1 class="mb-4 text-center">Gestión de Servicios</h1>
          <div class="row">
            <div class="col-12 d-flex justify-content-between align-items-center">
              <div class="d-flex">
                <input type="text" class="form-control me-2" placeholder="Buscar servicio">
                <a href="#" class="btn btn-primary">Buscar</a>
              </div>
              <div class="d-flex gap-2">
                <a href="#" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalVender">Vender</a>
                <?php if ($_SESSION['rol'] === 'Administrador'): ?>
                <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAgregar">Agregar</a>
                <?php endif; ?>
              </div>
            </div>
          </div>
        
          <div class="modal fade " id="modalAgregar">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header ">
                  <h3 class="modal-title" id="miModalLabel">Registro de Servicio</h3>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                  <form action="" method="">
                    <div class="mb-3">
                      <label for="descripcion" class="form-label fw-bold">Descripción:</label>
                      <input type="text" class="form-control" id="descripcion" name="descripcion" required>
                    </div>
                    <div class="mb-3">
                      <label for="precioUnitario" class="form-label fw-bold">Precio:</label>
                      <input type="number" class="form-control" id="precioUnitario" name="precioUnitario" required>
                    </div>
                    <div class="mb-3 d-flex align-items-center">
                      <label class="form-label me-3 fw-bold">Estado:</label>
                      <div class="form-check form-check-inline">
                        <input type="radio" class="form-check-input" name="estado" value="activo" id="activo" required>
                        <label class="form-check-label" for="activo">Activo</label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input type="radio" class="form-check-input" name="estado" value="inactivo" id="inactivo">
                        <label class="form-check-label" for="inactivo">Inactivo</label>
                      </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-center">
                      <button type="submit" class="btn btn-primary">Agregar</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
          <!-- Fin Modal Agregar -->
          <!-- Modal Editar -->
          <div class="modal fade " id="modalEditar">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header ">
                  <h3 class="modal-title" id="miModalLabel">Actualización de Servicio</h3>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                  <form action="" method="">
                    <input type="hidden" id="editarId">
                    <div class="mb-3">
                      <label for="descripcion" class="form-label fw-bold">Descripción:</label>
                      <input type="text" class="form-control" id="editarDescripcion" name="descripcion" required>
                    </div>
                    <div class="mb-3">
                      <label for="precioUnitario" class="form-label fw-bold">Precio:</label>
                      <input type="number" class="form-control" id="editarPrecioUnitario" name="precioUnitario" required>
                    </div>
                    <div class="mb-3 d-flex align-items-center">
                      <label class="form-label me-3 fw-bold">Estado:</label>
                      <div class="form-check form-check-inline">
                        <input type="radio" class="form-check-input" name="editarEstado" value="activo" id="editarEstadoActivo" required>
                        <label class="form-check-label" for="activo">Activo</label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input type="radio" class="form-check-input" name="editarEstado" value="inactivo" id="editarEstadoInactivo">
                        <label class="form-check-label" for="inactivo">Inactivo</label>
                      </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-center">
                  <button type="submit" class="btn btn-success">Modificar</button>
                </div>
                </form>
              </div>
            </div>
          </div>
          <!-- Fin Modal Editar -->
          <!-- Modal Vender -->
          <div class="modal fade" id="modalVender" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title" id="miModalLabel">Registro de Venta</h3>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="">
                            <div class="mb-3">
                                <label for="venderDescripcion" class="form-label fw-bold">Descripción:</label>
                                <select class="form-select" id="venderDescripcion" name="descripcion" required>
                                    <option value="">Seleccione un servicio</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="venderPrecioUnitario" class="form-label fw-bold">Precio:</label>
                                <input type="number" class="form-control" id="venderPrecioUnitario" name="precioUnitario" disabled>
                            </div>
                            <div class="mb-3">
                                <label for="venderfechaVenta" class="form-label fw-bold">Fecha:</label>
                                <input type="date" class="form-control" id="venderfechaVenta" name="fechaVenta" required>
                            </div>
                            <div id="nuevosServicios"></div>
                            <div class="modal-footer d-flex justify-content-start">
                                <input type="text" class="form-control" id="venderTotal" placeholder="Total" disabled>
                                <button type="button" class="btn btn-primary" id="btnRegistrarVenta">Registrar Venta</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
          </div>
          <!-- Fin Modal Vender -->

          <!-- Tabla -->
          <div class="table-responsive my-4">
            <table class="table  table-bordered table-hover text-center">
              <thead>
                <tr class="table-dark">
                  <th>ID</th>
                  <th>Descripción</th>
                  <th>Precio</th>
                  <th>Estado</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody class="align-middle">
                <tr>
                </tr>
              </tbody>
            </table>
          </div>
          <!-- Fin Tabla -->

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
          <!-- Fin Paginación -->

        </div>
      </main>

    </div>
  </div>

    <!-- Toast Bootstrap Personalizado -->
  <div id="toastAgregar" class="toast align-items-center border-0 position-fixed bottom-0 end-0 mb-3 me-3 z-3"
    role="alert" aria-live="assertive" aria-atomic="true" style="min-width: 250px;">
    <div id="toastHeaderAgregar" class="toast-header bg-success text-white d-flex justify-content-between w-100">
      <strong id="toastTitleAgregar" class="me-auto"></strong>
      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body bg-white text-dark" id="toastMessageAgregar"></div>
  </div>

  <div id="toastEditar" class="toast align-items-center border-0 position-fixed bottom-0 end-0 mb-3 me-3 z-3"
    role="alert" aria-live="assertive" aria-atomic="true" style="min-width: 250px; ">
    <div id="toastHeaderEditar" class="toast-header bg-success text-white">
      <strong id="toastTitleEditar" class="me-auto"></strong>
      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body bg-white text-dark" id="toastMessageEditar"></div>
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
          ¿Estás seguro de que deseas eliminar este Servicio?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
          <button type="button" class="btn btn-danger" id="btnConfirmarEliminar">Sí, eliminar</button>
        </div>
      </div>
    </div>
  </div>

  <div id="toastContainer" class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;"></div>

  <script src="../js/bootstrap.bundle.min.js"></script>
  <script src="../js/functions/gestionServicios.js"></script>
  <script>
    const ROL_USUARIO = "<?php echo $_SESSION['rol']; ?>";
  </script>

</body>
</html>
