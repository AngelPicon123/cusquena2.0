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
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <style>
    th, td {
      vertical-align: middle !important;
      text-align: center;
      word-break: break-word;
    }
    main.container-xl {
      width: 100%;
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
    <button class="btn btn-link btn-sm me-4" id="sidebarToggle"><i class="fas fa-bars"></i></button>
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
          .then(html => document.getElementById('layoutSidenav_nav').innerHTML = html);
      </script>
      <?php endif; ?>
      <?php if ($_SESSION['rol'] === 'Secretaria'): ?>
      <script>
        fetch('sidebear_secre.php')
          .then(r => r.text())
          .then(html => document.getElementById('layoutSidenav_nav').innerHTML = html);
      </script>
      <?php endif; ?>
    </div>
    <div id="layoutSidenav_content">
      <main class="container-xl my-4">
        <div class="container-fluid px-4">
          <h1 class="mb-4 text-center fw-bold">Gestión de Productos</h1>

          <div class="row mb-3">
            <div class="col-md-6 d-flex">
              <input type="text" class="form-control me-2" id="buscarProducto" placeholder="Buscar producto">
              <button class="btn btn-primary" id="btnBuscar">Buscar</button>
            </div>
            <div class="col-md-6 text-end">
              <?php if ($_SESSION['rol'] === 'Administrador'): ?>
              <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAgregarProducto">Agregar</a>
              <?php endif; ?>
            </div>
          </div>

          <!-- Modal: Agregar Producto -->
          <div class="modal fade" id="modalAgregarProducto" tabindex="-1" aria-labelledby="modalAgregarProductoLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
              <form id="formAgregarProducto">
                <div class="modal-content">
                  <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="modalAgregarProductoLabel">Agregar Producto</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body row g-3">
                    <div class="col-md-6">
                      <label>Descripción</label>
                      <input type="text" class="form-control" name="descripcion" required>
                    </div>
                    <div class="col-md-3">
                      <label>P. Compra</label>
                      <input type="number" step="0.01" class="form-control" name="precio_compra" required>
                    </div>
                    <div class="col-md-3">
                      <label>P. Venta</label>
                      <input type="number" step="0.01" class="form-control" name="precio_venta" required>
                    </div>
                    <div class="col-md-3">
                      <label>Stock</label>
                      <input type="number" class="form-control" name="stock" required>
                    </div>
                    <div class="col-md-3">
                      <label>Categoría</label>
                      <select class="form-select" name="categoria" required>
                        <option value="">Seleccione</option>
                        <option value="1">Aceites</option>
                        <option value="2">Filtros</option>
                      </select>
                    </div>
                    <div class="col-md-3">
                      <label>Presentación</label>
                      <input type="text" class="form-control" name="presentacion">
                    </div>
                    <div class="col-md-3">
                      <label>Ingreso Producto</label>
                      <input type="text" class="form-control" name="ingreso_producto">
                    </div>
                    <div class="col-md-3">
                      <label>Fecha Ingreso</label>
                      <input type="date" class="form-control" name="fecha_ingreso">
                    </div>
                    <div class="col-md-3">
                      <label>Estado</label>
                      <select class="form-select" name="estado">
                        <option value="Activo">Activo</option>
                        <option value="Inactivo">Inactivo</option>
                      </select>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Guardar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                  </div>
                </div>
              </form>
            </div>
          </div>

          <!-- Modal: Editar Producto -->
          <div class="modal fade" id="modalEditarProducto" tabindex="-1" aria-labelledby="modalEditarProductoLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
              <form id="formEditarProducto">
                <div class="modal-content">
                  <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="modalEditarProductoLabel">Editar Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body row g-3">
                    <input type="hidden" name="idProducto" id="editar_idProducto">
                    <div class="col-md-6">
                      <label>Descripción</label>
                      <input type="text" class="form-control" name="descripcion" id="editar_descripcion" required>
                    </div>
                    <div class="col-md-3">
                      <label>P. Compra</label>
                      <input type="number" step="0.01" class="form-control" name="precio_compra" id="editar_precio_compra" required>
                    </div>
                    <div class="col-md-3">
                      <label>P. Venta</label>
                      <input type="number" step="0.01" class="form-control" name="precio_venta" id="editar_precio_venta" required>
                    </div>
                    <div class="col-md-3">
                      <label>Stock</label>
                      <input type="number" class="form-control" name="stock" id="editar_stock" required>
                    </div>
                    <div class="col-md-3">
                      <label>Categoría</label>
                      <select class="form-select" name="categoria" id="editar_categoria" required>
                        <option value="1">Aceites</option>
                        <option value="2">Filtros</option>
                      </select>
                    </div>
                    <div class="col-md-3">
                      <label>Presentación</label>
                      <input type="text" class="form-control" name="presentacion" id="editar_presentacion">
                    </div>
                    <div class="col-md-3">
                      <label>Ingreso Producto</label>
                      <input type="text" class="form-control" name="ingreso_producto" id="editar_ingreso_producto">
                    </div>
                    <div class="col-md-3">
                      <label>Fecha Ingreso</label>
                      <input type="date" class="form-control" name="fecha_ingreso" id="editar_fecha_ingreso">
                    </div>
                    <div class="col-md-3">
                      <label>Estado</label>
                      <select class="form-select" name="estado" id="editar_estado">
                        <option value="Activo">Activo</option>
                        <option value="Inactivo">Inactivo</option>
                      </select>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="submit" class="btn btn-warning">Actualizar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                  </div>
                </div>
              </form>
            </div>
          </div>

          <!-- Modal: Vender Producto -->
          <div class="modal fade" id="modalVenderProducto" tabindex="-1" aria-labelledby="modalVenderProductoLabel" aria-hidden="true">
            <div class="modal-dialog">
              <form id="formVenderProducto">
                <div class="modal-content">
                  <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalVenderProductoLabel">Vender Producto</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body">
                    <input type="hidden" name="idProducto" id="vender_idProducto">
                    <div class="mb-3">
                      <label>Descripción</label>
                      <input type="text" class="form-control" name="descripcion" id="vender_descripcion" readonly>
                    </div>
                    <div class="mb-3">
                      <label>Stock Disponible</label>
                      <input type="number" class="form-control" name="stock" id="vender_stock" readonly>
                    </div>
                    <div class="mb-3">
                      <label>Cantidad a Vender</label>
                      <input type="number" class="form-control" name="cantidad" id="vender_cantidad" required min="1">
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Vender</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                  </div>
                </div>
              </form>
            </div>
          </div>

          <div class="table-responsive">
            <table class="table table-bordered table-hover table-sm w-100 text-center align-middle" style="table-layout: auto;">
              <thead>
                <tr class="table-dark">
                  <th>ID</th>
                  <th>Descripción</th>
                  <th>P. Compra</th>
                  <th>P. Venta</th>
                  <th>Stock</th>
                  <th>Categoría</th>
                  <th>Presentación</th>
                  <th>Ingreso Producto</th>
                  <th>Fecha Ingreso</th>
                  <th>Estado</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody id="tablaProductos">
                <tr>
                  <td>1</td>
                  <td>Aceite 20W50</td>
                  <td>25.00</td>
                  <td>35.00</td>
                  <td>50</td>
                  <td>Aceites</td>
                  <td>1L</td>
                  <td>Lote 001</td>
                  <td>2025-06-13</td>
                  <td><span class="badge bg-success">Activo</span></td>
                  <td>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalEditarProducto" 
                            onclick="llenarModalEditar(1, 'Aceite 20W50', 25.00, 35.00, 50, '1', '1L', 'Lote 001', '2025-06-13', 'Activo')">
                      <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalVenderProducto" 
                            onclick="llenarModalVender(1, 'Aceite 20W50', 50)">
                      <i class="fas fa-dollar-sign"></i>
                    </button>
                  </td>
                </tr>
                <tr>
                  <td>2</td>
                  <td>Filtro de aceite</td>
                  <td>15.00</td>
                  <td>25.00</td>
                  <td>20</td>
                  <td>Filtros</td>
                  <td>Universal</td>
                  <td>Lote 002</td>
                  <td>2025-06-12</td>
                  <td><span class="badge bg-secondary">Inactivo</span></td>
                  <td>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalEditarProducto" 
                            onclick="llenarModalEditar(2, 'Filtro de aceite', 15.00, 25.00, 20, '2', 'Universal', 'Lote 002', '2025-06-12', 'Inactivo')">
                      <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalVenderProducto" 
                            onclick="llenarModalVender(2, 'Filtro de aceite', 20)">
                      <i class="fas fa-dollar-sign"></i>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="d-flex justify-content-end">
            <ul class="pagination">
              <li class="page-item"><a class="page-link" href="#">«</a></li>
              <li class="page-item"><a class="page-link" href="#">1</a></li>
              <li class="page-item"><a class="page-link" href="#">2</a></li>
              <li class="page-item"><a class="page-link" href="#">»</a></li>
            </ul>
          </div>
        </div>
      </main>
    </div>
  </div>

  <script>
    // Inicializar Flatpickr para todos los campos de fecha
    flatpickr("input[type=date]", {
      dateFormat: "Y-m-d",
      allowInput: true
    });

    // Función para llenar el modal de edición
    function llenarModalEditar(id, descripcion, precioCompra, precioVenta, stock, categoria, presentacion, ingreso, fecha, estado) {
      document.getElementById('editar_idProducto').value = id;
      document.getElementById('editar_descripcion').value = descripcion;
      document.getElementById('editar_precio_compra').value = precioCompra;
      document.getElementById('editar_precio_venta').value = precioVenta;
      document.getElementById('editar_stock').value = stock;
      document.getElementById('editar_categoria').value = categoria;
      document.getElementById('editar_presentacion').value = presentacion;
      document.getElementById('editar_ingreso_producto').value = ingreso;
      document.getElementById('editar_fecha_ingreso').value = fecha;
      document.getElementById('editar_estado').value = estado;
    }

    // Función para llenar el modal de venta
    function llenarModalVender(id, descripcion, stock) {
      document.getElementById('vender_idProducto').value = id;
      document.getElementById('vender_descripcion').value = descripcion;
      document.getElementById('vender_stock').value = stock;
      document.getElementById('vender_cantidad').value = 1;
      document.getElementById('vender_cantidad').max = stock; // Limitar cantidad máxima al stock disponible
    }

    // Manejar el submit de los formularios (placeholder para backend)
    document.getElementById('formAgregarProducto').addEventListener('submit', function(e) {
      e.preventDefault();
      console.log('Formulario de agregar producto enviado');
      // Aquí iría la lógica para enviar los datos al backend
    });

    document.getElementById('formEditarProducto').addEventListener('submit', function(e) {
      e.preventDefault();
      console.log('Formulario de editar producto enviado');
      // Aquí iría la lógica para enviar los datos al backend
    });

    document.getElementById('formVenderProducto').addEventListener('submit', function(e) {
      e.preventDefault();
      console.log('Formulario de vender producto enviado');
      // Aquí iría la lógica para enviar los datos al backend
    });
  </script>
  <script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>