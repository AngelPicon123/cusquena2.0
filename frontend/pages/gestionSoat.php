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
      <main class="container-xl my-2 col-12 mx-auto">
        <div class="container-fluid px-4 ">
          <h1 class="mb-4 text-center">Gestión de SOAT</h1>

          <div class="row">
            <div class="col-12 d-flex justify-content-between align-items-center">
              <div class="d-flex">
                <input type="text" class="form-control me-2" placeholder="Buscar Conductor" enabled>
                <a href="#" class="btn btn-primary" style="height: 37px;">Buscar</a>
              </div>
            </div>
          </div>
          <!--MODAL EDITAR-->
          <div class="modal fade" id="modalEditar">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h3 class="modal-title" id="miModalLabel">Actualización de SOAT</h3>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                  <form action="" method="">
                    <div class="mb-3">
                      <label for="dni" class="form-label fw-bold">DNI:</label>
                      <input type="number" class="form-control" id="dni" name="dni" required>
                    </div>
                    <div class="mb-3">
                      <label for="nombre" class="form-label fw-bold">Nombres:</label>
                      <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="mb-3">
                      <label for="apellido" class="form-label fw-bold">Apellidos:</label>
                      <input type="text" class="form-control" id="apellido" name="apellido" required>
                    </div>
                    <div class="mb-3">
                      <label for="telefono" class="form-label fw-bold">Telefono:</label>
                      <input type="number" class="form-control" id="telefono" name="telefono" required>
                    </div>
                    <div class="mb-3">
                      <label for="placa" class="form-label fw-bold">Placa:</label>
                      <input type="text" class="form-control" id="placa" name="placa" required>
                    </div>
                    <div class="mb-3">
                      <label for="placa" class="form-label fw-bold">Emisión:</label>
                      <input type="date" class="form-control" id="emisionsoat" name="emisionsoat" required>
                    </div>
                    <div class="mb-3">
                      <label for="placa" class="form-label fw-bold">Vencimiento:</label>
                      <input type="date" class="form-control" id="vencimientosoat" name="vencimientosoat" required>
                    </div>

                    <div class="mb-3">
                      <label for="placa" class="form-label fw-bold">N° SOAT:</label>
                      <input type="text" class="form-control" id="numsoat" name="numsoat" required>
                    </div>
                    <div class="mb-3 d-flex align-items-center">
                      <label for="estado" class="form-label fw-bold me-3">Estado:</label>
                      <select id="estado" name="estado" class="form-select w-auto" required>
                        <option value="" disabled selected>Seleccione una opción</option>
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
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
                  <th>DNI</th>
                  <th>Nombres</th>
                  <th>Apellidos</th>
                  <th>Telefono</th>
                  <th>Placa</th>
                  <th>Emision</th>
                  <th>Vencimiento</th>
                  <th>N°SOAT</th>
                  <th>Estado</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody class="align-middle">
                <tr>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td>
                    <a href="#" class="btn btn-success p-1" data-bs-toggle="modal"
                      data-bs-target="#modalEditar">Editar</a>
                    <a href="#" class="btn btn-danger p-1">Eliminar</a>
                  </td>
                </tr>

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
        <footer class="py-4 bg-light mt-auto">
          <div class="container-fluid px-4">
            <div class="d-flex align-items-center justify-content-between small">

            </div>
          </div>
        </footer>

      </div>
  </div>
</div>

<!-- Toast para ediciones o errores -->
<div id="toastEditar" class="toast align-items-center border-0 position-fixed bottom-0 end-0 mb-3 me-3 z-3"
  role="alert" aria-live="assertive" aria-atomic="true" style="min-width: 250px;">
  <div id="toastHeaderEditar" class="toast-header bg-success text-white">
    <strong id="toastTitleEditar" class="me-auto"></strong>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
  </div>
  <div class="toast-body bg-white text-dark" id="toastMessageEditar"></div>
</div>

<!-- Toast de eliminación -->
<div id="toastEliminar" class="toast align-items-center border-0 position-fixed bottom-0 end-0 mb-3 me-3 z-3"
  role="alert" aria-live="assertive" aria-atomic="true" style="min-width: 250px;">
  <div class="toast-header bg-danger text-white">
    <strong class="me-auto">Eliminación Exitosa</strong>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
  </div>
  <div class="toast-body bg-white text-dark" id="toastMessageEliminar"></div>
</div>

<!-- Modal de confirmación -->
<div class="modal fade" id="modalEliminarConfirmacion" tabindex="-1" aria-labelledby="modalEliminarLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="modalEliminarLabel">¿Confirmar Eliminación?</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        ¿Estás seguro de que deseas eliminar este Soat?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
        <button type="button" class="btn btn-danger" id="btnConfirmarEliminar">Sí, eliminar</button>
      </div>
    </div>
  </div>
</div>

  <script src="../js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../js/functions/gestionSoat.js"></script>
  <script>const ROL_USUARIO = "<?php echo $_SESSION['rol']; ?>";</script>
</body>

</html>