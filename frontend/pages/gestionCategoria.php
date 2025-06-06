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
      <main class="container-xl my-2 col-10 mx-auto">
        <div class="container-fluid px-4 ">
          <h1 class="mb-4 text-center">Gestión de Categorias</h1>

          <div class="row">
            <div class="col-12 d-flex justify-content-between align-items-center">
              <div class="d-flex">
                <input type="text" class="form-control me-2" placeholder="Buscar categoria">
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
                  <h3 class="modal-title" id="miModalLabel">Registro de Categoria</h3>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                  <form action="" method="">
                    <div class="mb-3">
                      <label for="descripcion" class="form-label fw-bold">Descripción:</label>
                      <input type="text" class="form-control" id="descripcion" name="descripcion" required>
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
                  <h3 class="modal-title" id="miModalLabel">Actualización de Categoria</h3>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                  <form action="" method="">
                    <input type="hidden" id="editarId">
                    <div class="mb-3">
                      <label for="descripcion" class="form-label fw-bold">Descripción:</label>
                      <input type="text" class="form-control" id="editarDescripcion" name="descripcion" required>
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
                    <div class="modal-footer d-flex justify-content-center">
                      <button type="submit" class="btn btn-success">Modificar</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
          <!-- Fin Modal Editar -->
          
          <!-- Tabla -->
          <div class="table-responsive my-4">
            <table class="table  table-bordered table-hover text-center">
              <thead>
                <tr class="table-dark">
                  <th>ID</th>
                  <th>Descripcion</th>
                  <th>Estado</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody class="align-middle">
                <tr>
                  <td>01</td>
                  <td>Aceite</td>
                  <td>
                    <span class="badge bg-success">Activo</span>
                  </td>
                  <td>
                    <a href="#" class="btn btn-success p-1" data-bs-toggle="modal" data-bs-target="#modalEditar">Editar</a>
                    <a href="#" class="btn btn-danger p-1">Eliminar</a>
                  </td>
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
  <script src="../js/bootstrap.bundle.min.js"></script>
  <script src="../js/functions/gestionCategoria.js"></script>
  
</body>

</html>