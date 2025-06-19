<?php
require_once '../../backend/includes/auth.php';
verificarPermiso(['Administrador', 'Secretaria']);
?>
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
    <a class="navbar-brand ps-3" href="base.php">La Cusqueña</a>
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
          <li><a class="dropdown-item" href="#!">Cerrar Sesion</a></li>
        </ul>
      </li>
    </ul>
    <!-- Fin Navbar -->
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
      <main>
        <div class="container-fluid px-4 ">
          <h1 class="mt-4 text-center mb-4">Gestión de Gastos</h1>

          <div class="row">
            <div class="col-12 d-flex justify-content-between align-items-center">
              <div class="d-flex align-items-center">
                <label for="inicio" class="me-2">Inicio:</label>
                <input type="date" id="inicio" class="form-control me-2">
                <label for="fin" class="me-2">Fin:</label>
                <input type="date" id="fin" class="form-control me-2">
                <input type="text" class="form-control me-2" placeholder="Buscar">
                <a href="#" class="btn btn-primary">Buscar</a>
              </div>
              <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAgregar">Agregar</a>
            </div>
          </div>

          <!-- Modal Agregar -->
          <div class="modal fade " id="modalAgregar">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header ">
                  <h3 class="modal-title w-100 text-center" id="miModalLabel">Registro de Gasto</h3>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                  <form action="" method="">
                    <div class="mb-3">
                      <label for="descripcion" class="form-label fw-bold">Descripcion:</label>
                      <input type="text" class="form-control" id="descripcion" name="descripcion" required>
                    </div>
                    <div class="mb-3">
                      <label for="tipo" class="form-label fw-bold">Tipo de Gastos:</label>
                      <select class="form-select" id="tipo" name="tipo" required>
                        <option value="">Seleccione una opción:</option>
                        <option value="operativo">Operativo</option>
                        <option value="administrativo">Administrativo</option>
                        <option value="mantenimiento">Mantenimiento</option>
                        <option value="otro">Otro</option>
                      </select>
                    </div>
                    <div class="mb-3">
                      <label for="monto" class="form-label fw-bold">Monto:</label>
                      <input type="number" class="form-control" id="monto" name="monto" required>
                    </div>
                    <div class="mb-3">
                      <label for="fecha" class="form-label fw-bold">Fecha:</label>
                      <input type="date" class="form-control" id="fecha" name="fecha" required>
                    </div>
                    <div class="mb-3">
                      <label for="detalle" class="form-label fw-bold">Detalle:</label>
                      <textarea class="form-control" id="detalle" name="detalle" rows="3" required></textarea>
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
                  <h3 class="modal-title w-100 text-center" id="miModalLabel">Actualización de Gasto</h3>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                  <form action="" method="">
                    <div class="mb-3">
                      <input type="hidden" id="editarId">
                      <label for="editardescripcion" class="form-label fw-bold">Descripcion:</label>
                      <input type="text" class="form-control" id="editardescripcion" name="descripcion" required>
                    </div>
                    <div class="mb-3">
                      <label for="editartipo" class="form-label fw-bold">Tipo de Gasto:</label>
                      <select class="form-select" id="editartipo" name="tipo" required>
                        <option value="">Seleccione una opción:</option>
                        <option value="operativo">Operativo</option>
                        <option value="administrativo">Administrativo</option>
                        <option value="mantenimiento">Mantenimiento</option>
                        <option value="otro">Otro</option>
                      </select>
                    </div>
                    <div class="mb-3">
                      <label for="editarmonto" class="form-label fw-bold">Monto:</label>
                      <input type="number" class="form-control" id="editarmonto" name="monto" required>
                    </div>
                    <div class="mb-3">
                      <label for="editarfecha" class="form-label fw-bold">Fecha:</label>
                      <input type="date" class="form-control" id="editarfecha" name="fecha" required>
                    </div>
                    <div class="mb-3">
                      <label for="editardetalle" class="form-label fw-bold">Detalle:</label>
                      <textarea class="form-control" id="editardetalle" name="detalle" rows="3" required></textarea>
                    </div>
                    <div class="modal-footer d-flex justify-content-center">
                      <button type="submit" class="btn btn-success">Modificar</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
          <!-- Fin Modal Editar -->

          <div class="table-responsive my-4">
            <table class="table table-bordered table-hover text-center">
              <thead>
                <tr class="table-dark">
                  <th>ID</th>
                  <th>Descripcion</th>
                  <th>Tipo de Gasto</th>
                  <th>Monto</th>
                  <th>Fecha</th>
                  <th>Detalle</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody class="align-middle">
                <tr>
                  <td>01</td>
                  <td>Cambio de bujias</td>
                  <td>Mantenimiento</td>
                  <td>S/. 70</td>
                  <td>12/05/2025</td>
                  <td>Limpieza y cambio de bujias.</td>
                  <td>
                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEditar" onclick="cargarDatosGasto(1, 'Cambio de aceite', 'mantenimiento', 150, '2025-05-10', 'Limpieza y cambio de aceite de motor.')">Editar</button>
                    <button class="btn btn-sm btn-danger">Eliminar</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <nav aria-label="Page navigation example" class="d-flex justify-content-end">
            <ul class="pagination">
              <li class="page-item">
                <a class="page-link" href="#" aria-label="Previous">
                  <span aria-hidden="true">«</span>
                </a>
              </li>
              <li class="page-item"><a class="page-link" href="#">1</a></li>
              <li class="page-item"><a class="page-link" href="#">2</a></li>
              <li class="page-item"><a class="page-link" href="#">3</a></li>
              <li class="page-item">
                <a class="page-link" href="#" aria-label="Next">
                  <span aria-hidden="true">»</span>
                </a>
              </li>
            </ul>
          </nav>
      </main>
    </div>
  </div>

  <!-- Toast Bootstrap Personalizado -->
  <div id="toastAgregar" class="toast align-items-center border-0 position-fixed bottom-0 end-0 mb-3 me-3 z-3"
    role="alert" aria-live="assertive" aria-atomic="true" style="min-width: 250px;">
    <div>
    <div id="toastHeaderAgregar" class="toast-header bg-success text-white d-flex justify-content-between w-100">
      <strong id="toastTitleAgregar" class="me-auto"></strong>
      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body bg-white text-dark" id="toastMessageAgregar"></div>
  </div>

  <div id="toastEditar" class="toast align-items-center border-0 position-fixed bottom-0 end-0 mb-3 me-3 z-3"
    role="alert" aria-live="assertive" aria-atomic="true" style="min-width: 250px;">
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
    <div class="toast-body bg-white text-dark" id="toastMessageEliminar"></div>
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

  <script src="../js/bootstrap.bundle.min.js"></script>
  <script>
    function cargarDatosGasto(id, descripcion, tipo, monto, fecha, detalle) {
      document.getElementById('editarId').value = id;
      document.getElementById('editardescripcion').value = descripcion;
      document.getElementById('editartipo').value = tipo;
      document.getElementById('editarmonto').value = monto;
      document.getElementById('editarfecha').value = fecha;
      document.getElementById('editardetalle').value = detalle;
    }
  </script>
</body>

</html>