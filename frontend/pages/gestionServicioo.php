<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once $_SERVER['DOCUMENT_ROOT'] . '/cusquena/backend/includes/auth.php';
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
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <style>
    th, td {
      vertical-align: middle !important;
      text-align: center;
    }
  </style>
</head>
<body class="sb-nav-fixed">
  <!-- Navbar superior -->
  <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark fixed-top">
    <a class="navbar-brand ps-3" href="base.php">La Cusqueña</a>
    <button class="btn btn-link btn-sm me-4" id="sidebarToggle"><i class="fas fa-bars"></i></button>
    <ul class="navbar-nav ms-auto me-3 me-lg-4">
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown">
          <i class="fas fa-user fa-fw"></i>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="../../backend/includes/logout.php">Cerrar Sesión</a></li>
        </ul>
      </li>
    </ul>
  </nav>

  <!-- Contenedor general con sidebar -->
  <div id="layoutSidenav">
    <div id="layoutSidenav_nav">
      <?php include 'sidebear_admin.php'; ?>
    </div>
    <div id="layoutSidenav_content">
      <main>
        <div class="container-fluid px-4">
          <h1 class="mt-4 text-center mb-4">Gestión de Servicios</h1>
          <div class="row mb-3">
            <div class="col-12 d-flex justify-content-between align-items-center">
              <div class="d-flex">
                <input type="text" id="buscarServicio" class="form-control me-2" placeholder="Buscar servicio">
                <button class="btn btn-primary" id="btnBuscar">Buscar</button>
              </div>
              <div class="d-flex gap-2">
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalVender">Vender</button>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAgregar">Agregar</button>
              </div>
            </div>
          </div>

          <div class="table-responsive my-4">
            <table class="table table-bordered table-hover text-center">
              <thead class="table-dark">
                <tr>
                  <th>ID</th>
                  <th>Nombre</th>
                  <th>Tipo de Servicio</th>
                  <th>Precio</th>
                  <th>Fecha</th>
                  <th>Estado</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody class="align-middle">
                <tr>
                  <td>01</td>
                  <td>Julio Sanchez</td>
                  <td>Lavado</td>
                  <td>S/. 100.00</td>
                  <td>04-04-2025</td>
                  <td><span class="badge bg-success">Activo</span></td>
                  <td>
                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEditar" 
                            onclick="cargarDatosServicio(1, 'Julio Sanchez', 'Lavado', 100.00, '2025-04-04', 'Activo')">
                      <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="eliminarServicio(1)">
                      <i class="fas fa-trash"></i>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <nav aria-label="Paginación" class="d-flex justify-content-end">
            <ul class="pagination">
              <li class="page-item"><a class="page-link" href="#">«</a></li>
              <li class="page-item"><a class="page-link" href="#">1</a></li>
              <li class="page-item"><a class="page-link" href="#">2</a></li>
              <li class="page-item"><a class="page-link" href="#">»</a></li>
            </ul>
          </nav>
        </div>
      </main>
    </div>
  </div>

  <!-- Modal Agregar -->
  <div class="modal fade" id="modalAgregar" tabindex="-1" aria-labelledby="modalAgregarLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h3 class="modal-title" id="modalAgregarLabel">Registro de Servicio</h3>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form id="formAgregar">
            <div class="mb-3">
              <label class="form-label fw-bold">Nombre:</label>
              <input type="text" class="form-control" name="nombre" required>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Tipo de Servicio:</label>
              <select class="form-select" name="tipoServicio" required>
                <option value="">Seleccione un tipo</option>
                <option value="Cambio de aceite">Cambio de aceite</option>
                <option value="Lavado">Lavado</option>
                <option value="Mantenimiento">Mantenimiento</option>
                <option value="Otros">Otros</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Precio:</label>
              <input type="number" step="0.01" class="form-control" name="precioUnitario" required>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Cantidad:</label>
              <input type="number" class="form-control" name="cantidad" required min="1">
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Fecha:</label>
              <input type="date" class="form-control" name="fechaRegistro" required>
            </div>
            <div class="mb-3 d-flex align-items-center">
              <label class="form-label fw-bold me-3">Estado:</label>
              <div class="form-check form-check-inline">
                <input type="radio" name="estado" class="form-check-input" value="Activo" checked required>
                <label class="form-check-label">Activo</label>
              </div>
              <div class="form-check form-check-inline">
                <input type="radio" name="estado" class="form-check-input" value="Inactivo">
                <label class="form-check-label">Inactivo</label>
              </div>
            </div>
            <div class="modal-footer justify-content-center">
              <button type="submit" class="btn btn-primary">Agregar Servicio</button>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Editar -->
  <div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="modalEditarLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-warning text-dark">
          <h3 class="modal-title" id="modalEditarLabel">Actualizar Servicio</h3>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form id="formEditar">
            <input type="hidden" name="id" id="editarId">
            <div class="mb-3">
              <label class="form-label fw-bold">Nombre:</label>
              <input type="text" class="form-control" name="nombre" id="editarNombre" required>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Tipo de Servicio:</label>
              <select class="form-select" name="tipoServicio" id="editarTipoServicio" required>
                <option value="">Seleccione un tipo</option>
                <option value="Cambio de aceite">Cambio de aceite</option>
                <option value="Lavado">Lavado</option>
                <option value="Mantenimiento">Mantenimiento</option>
                <option value="Otros">Otros</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Precio:</label>
              <input type="number" step="0.01" class="form-control" name="precioUnitario" id="editarPrecioUnitario" required>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Fecha:</label>
              <input type="date" class="form-control" name="fechaRegistro" id="editarFecha" required>
            </div>
            <div class="mb-3 d-flex align-items-center">
              <label class="form-label fw-bold me-3">Estado:</label>
              <div class="form-check form-check-inline">
                <input type="radio" name="estado" id="editarEstadoActivo" class="form-check-input" value="Activo" required>
                <label class="form-check-label">Activo</label>
              </div>
              <div class="form-check form-check-inline">
                <input type="radio" name="estado" id="editarEstadoInactivo" class="form-check-input" value="Inactivo">
                <label class="form-check-label">Inactivo</label>
              </div>
            </div>
            <div class="modal-footer justify-content-center">
              <button type="submit" class="btn btn-warning">Modificar</button>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Vender -->
  <div class="modal fade" id="modalVender" tabindex="-1" aria-labelledby="modalVenderLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-success text-white">
          <h3 class="modal-title" id="modalVenderLabel">Registrar Venta</h3>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form id="formVenta">
            <div class="mb-3">
              <label class="form-label fw-bold">Servicio:</label>
              <select class="form-select" id="venderServicio" name="idServicio" required>
                <option value="">Seleccione un servicio</option>
                <!-- Opciones cargadas dinámicamente -->
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Precio Unitario:</label>
              <input type="number" step="0.01" class="form-control" id="venderPrecioUnitario" name="precioUnitario" readonly>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Cantidad:</label>
              <input type="number" class="form-control" id="venderCantidad" name="cantidad" min="1" value="1" required>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Fecha:</label>
              <input type="date" class="form-control" id="venderFechaVenta" name="fechaVenta" required>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Total:</label>
              <input type="number" step="0.01" class="form-control" id="venderTotal" name="total" readonly>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-success" id="btnRegistrarVenta">Registrar Venta</button>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="../js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      // Inicializar Flatpickr para campos de fecha
      flatpickr("input[type=date]", {
        dateFormat: "Y-m-d",
        allowInput: true,
        defaultDate: new Date()
      });

      // Toggle sidebar
      const toggleBtn = document.getElementById('sidebarToggle');
      if (toggleBtn) {
        toggleBtn.addEventListener('click', function (e) {
          e.preventDefault();
          document.body.classList.toggle('sb-sidenav-toggled');
          localStorage.setItem('sb-sidebar-toggle', document.body.classList.contains('sb-sidenav-toggled'));
        });
      }
      if (localStorage.getItem('sb-sidebar-toggle') === 'true') {
        document.body.classList.add('sb-sidenav-toggled');
      }

      // Datos de ejemplo para el select de servicios (deberías cargarlos desde el backend)
      const servicios = [
        { id: 1, nombre: 'Lavado - Julio Sanchez', tipo: 'Lavado', precio: 100.00, estado: 'Activo' }
        // Agrega más servicios según tus necesidades
      ];

      // Cargar servicios en el select del modal de venta
      const selectServicio = document.getElementById('venderServicio');
      servicios.forEach(servicio => {
        if (servicio.estado === 'Activo') {
          const option = document.createElement('option');
          option.value = servicio.id;
          option.textContent = `${servicio.nombre} (${servicio.tipo})`;
          option.dataset.precio = servicio.precio;
          selectServicio.appendChild(option);
        }
      });

      // Actualizar precio y total al seleccionar un servicio
      selectServicio.addEventListener('change', function () {
        const selectedOption = this.options[this.selectedIndex];
        const precio = selectedOption.dataset.precio || 0;
        const cantidad = document.getElementById('venderCantidad').value || 1;
        document.getElementById('venderPrecioUnitario').value = parseFloat(precio).toFixed(2);
        document.getElementById('venderTotal').value = (precio * cantidad).toFixed(2);
      });

      // Actualizar total al cambiar la cantidad
      document.getElementById('venderCantidad').addEventListener('input', function () {
        const precio = parseFloat(document.getElementById('venderPrecioUnitario').value) || 0;
        const cantidad = this.value || 1;
        document.getElementById('venderTotal').value = (precio * cantidad).toFixed(2);
      });

      // Cargar datos en el modal de edición
      window.cargarDatosServicio = function (id, nombre, tipoServicio, precioUnitario, fecha, estado) {
        document.getElementById('editarId').value = id;
        document.getElementById('editarNombre').value = nombre;
        document.getElementById('editarTipoServicio').value = tipoServicio;
        document.getElementById('editarPrecioUnitario').value = precioUnitario.toFixed(2);
        document.getElementById('editarFecha').value = fecha;
        document.getElementById('editarEstadoActivo').checked = (estado === 'Activo');
        document.getElementById('editarEstadoInactivo').checked = (estado === 'Inactivo');
      };

      // Eliminar servicio (placeholder)
      window.eliminarServicio = function (id) {
        if (confirm('¿Estás seguro de que deseas eliminar este servicio?')) {
          console.log(`Eliminando servicio con ID: ${id}`);
          // Aquí iría la lógica para enviar la solicitud de eliminación al backend
        }
      };

      // Buscar servicio (placeholder)
      document.getElementById('btnBuscar').addEventListener('click', function () {
        const query = document.getElementById('buscarServicio').value;
        console.log(`Buscando servicio: ${query}`);
        // Aquí iría la lógica para filtrar los servicios
      });

      // Manejar formulario de agregar
      document.getElementById('formAgregar').addEventListener('submit', function (e) {
        e.preventDefault();
        console.log('Formulario de agregar servicio enviado:', new FormData(this));
        // Aquí iría la lógica para enviar los datos al backend
        this.reset();
        bootstrap.Modal.getInstance(document.getElementById('modalAgregar')).hide();
      });

      // Manejar formulario de editar
      document.getElementById('formEditar').addEventListener('submit', function (e) {
        e.preventDefault();
        console.log('Formulario de editar servicio enviado:', new FormData(this));
        // Aquí iría la lógica para enviar los datos al backend
        bootstrap.Modal.getInstance(document.getElementById('modalEditar')).hide();
      });

      // Manejar formulario de venta
      document.getElementById('formVenta').addEventListener('submit', function (e) {
        e.preventDefault();
        console.log('Formulario de venta enviado:', new FormData(this));
        // Aquí iría la lógica para enviar los datos al backend
        this.reset();
        bootstrap.Modal.getInstance(document.getElementById('modalVender')).hide();
      });
    });
  </script>
</body>
</html>