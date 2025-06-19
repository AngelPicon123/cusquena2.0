<?php
require_once '../../backend/includes/auth.php';
verificarPermiso(['Administrador', 'Secretaria']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Lubricentro Cusqueña - Gestión de Deudas</title>
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
      max-width: 180px; /* Ajustado para más columnas */
      padding: 10px;
    }
    .table td button {
      margin: 0 3px; /* Espacio entre botones */
      white-space: nowrap;
    }
    .table-responsive {
      overflow-x: auto;
    }
    @media (max-width: 768px) { /* Ajuste para tablets y móviles */
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
          <h1 class="mb-4 text-center">Gestión de Deudas</h1>
          <div class="row">
            <div class="col-12 d-flex justify-content-between align-items-center">
              <div class="d-flex">
                <input type="text" class="form-control me-2" id="buscarDeuda" placeholder="Buscar por Nombre">
                <button class="btn btn-primary" id="btnBuscar">Buscar</button>
              </div>
              <?php if ($_SESSION['rol'] === 'Administrador'): ?>
              <div>
                <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAgregarDeuda">Agregar Deuda</a>
              </div>
              <?php endif; ?>
            </div>
          </div>

          <div class="modal fade" id="modalAgregarDeuda">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h3 class="modal-title">Registro de Deuda</h3>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                  <form id="formAgregarDeuda">
                    <div class="mb-3">
                      <label for="nombre" class="form-label fw-bold">Nombre:</label>
                      <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label for="tipoDeuda" class="form-label fw-bold">Tipo de Deuda:</label>
                        <select class="form-select" id="tipoDeuda" name="tipoDeuda" required>
                            <option value="" disabled selected>Seleccione un tipo</option>
                            <option value="credito_cliente">Crédito a Cliente</option>
                            <option value="prestamo_personal">Préstamo Personal</option>
                            <option value="adelanto_sueldo">Adelanto de Sueldo</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="tipoPersona" class="form-label fw-bold">Tipo de Persona:</label>
                        <select class="form-select" id="tipoPersona" name="tipoPersona" required>
                            <option value="" disabled selected>Seleccione un tipo</option>
                            <option value="cliente">Cliente</option>
                            <option value="empleado">Empleado</option>
                            <option value="proveedor">Proveedor</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="montoDeuda" class="form-label fw-bold">Monto Deuda:</label>
                            <input type="number" step="0.01" class="form-control" id="montoDeuda" name="montoDeuda" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="saldoPendiente" class="form-label fw-bold">Saldo Pendiente:</label>
                            <input type="number" step="0.01" class="form-control" id="saldoPendiente" name="saldoPendiente" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="fechaInicioDeuda" class="form-label fw-bold">Fecha Inicio:</label>
                            <input type="date" class="form-control" id="fechaInicioDeuda" name="fechaInicioDeuda" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tasaInteres" class="form-label fw-bold">Tasa Interés (%):</label>
                            <input type="number" step="0.01" class="form-control" id="tasaInteres" name="tasaInteres" value="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="estado" class="form-label fw-bold">Estado:</label>
                        <select class="form-select" id="estado" name="estado" required>
                            <option value="pendiente">Pendiente</option>
                            <option value="pagada">Pagada</option>
                            <option value="en_atraso">En Atraso</option>
                        </select>
                    </div>
                    <div class="modal-footer d-flex justify-content-center">
                      <button type="submit" class="btn btn-primary">Agregar Deuda</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
          <div class="modal fade" id="modalEditarDeuda">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h3 class="modal-title">Editar Deuda</h3>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                <form id="formEditarDeuda">
                    <input type="hidden" id="editDeudaId" name="deudaId"> <div class="mb-3">
                      <label for="edit_nombre" class="form-label fw-bold">Nombre:</label>
                      <input type="text" class="form-control" id="edit_nombre" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_tipoDeuda" class="form-label fw-bold">Tipo de Deuda:</label>
                        <select class="form-select" id="edit_tipoDeuda" name="tipoDeuda" required>
                            <option value="credito_cliente">Crédito a Cliente</option>
                            <option value="prestamo_personal">Préstamo Personal</option>
                            <option value="adelanto_sueldo">Adelanto de Sueldo</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_tipoPersona" class="form-label fw-bold">Tipo de Persona:</label>
                        <select class="form-select" id="edit_tipoPersona" name="tipoPersona" required>
                            <option value="cliente">Cliente</option>
                            <option value="empleado">Empleado</option>
                            <option value="proveedor">Proveedor</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_montoDeuda" class="form-label fw-bold">Monto Deuda:</label>
                            <input type="number" step="0.01" class="form-control" id="edit_montoDeuda" name="montoDeuda" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_saldoPendiente" class="form-label fw-bold">Saldo Pendiente:</label>
                            <input type="number" step="0.01" class="form-control" id="edit_saldoPendiente" name="saldoPendiente" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_fechaInicioDeuda" class="form-label fw-bold">Fecha Inicio:</label>
                            <input type="date" class="form-control" id="edit_fechaInicioDeuda" name="fechaInicioDeuda" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_tasaInteres" class="form-label fw-bold">Tasa Interés (%):</label>
                            <input type="number" step="0.01" class="form-control" id="edit_tasaInteres" name="tasaInteres">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_estado" class="form-label fw-bold">Estado:</label>
                        <select class="form-select" id="edit_estado" name="estado" required>
                            <option value="pendiente">Pendiente</option>
                            <option value="pagada">Pagada</option>
                            <option value="en_atraso">En Atraso</option>
                        </select>
                    </div>
                    <div class="modal-footer d-flex justify-content-center">
                      <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
          <div class="modal fade" id="modalVerPagos" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Historial de Pagos de: <span id="nombreDeudor"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-4 p-3 border rounded">
                            <h6>Registrar Nuevo Pago</h6>
                            <form id="formAgregarPago">
                                <input type="hidden" id="pagoDeudaId" name="deudaId">
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
                                    <tr>
                                        <td>05-06-2025</td>
                                        <td>S/. 50.00</td>
                                        <td><button class="btn btn-xs btn-danger">Eliminar</button></td>
                                    </tr>
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
        <div class="table-responsive my-4">
            <table class="table table-striped table-bordered">
              <thead class="table-dark">
                <tr>
                  <th scope="col">Nombre</th>
                  <th scope="col">Tipo de Deuda</th>
                  <th scope="col">Tipo de Persona</th>
                  <th scope="col">Monto Deuda</th>
                  <th scope="col">Saldo Pendiente</th>
                  <th scope="col">Fecha Inicio</th>
                  <th scope="col">Tasa Int.</th>
                  <th scope="col">Estado</th>
                  <th scope="col">Acciones</th>
                </tr>
              </thead>
              <tbody id="tablaDeudas">
                <tr>
                  <td>Juan Pérez C.</td>
                  <td>Crédito a Cliente</td>
                  <td>Cliente</td>
                  <td>S/. 500.00</td>
                  <td>S/. 150.00</td>
                  <td>01-05-2025</td>
                  <td>5%</td>
                  <td><span class="badge bg-warning text-dark">Pendiente</span></td>
                  <td>
                    <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#modalVerPagos" title="Ver Pagos"><i class="fas fa-eye"></i></button>
                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEditarDeuda" title="Editar"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-sm btn-danger" title="Eliminar"><i class="fas fa-trash"></i></button>
                  </td>
                </tr>
                 <tr>
                  <td>Ana García L.</td>
                  <td>Adelanto Sueldo</td>
                  <td>Empleado</td>
                  <td>S/. 300.00</td>
                  <td>S/. 0.00</td>
                  <td>15-04-2025</td>
                  <td>0%</td>
                  <td><span class="badge bg-success">Pagada</span></td>
                  <td>
                    <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#modalVerPagos" title="Ver Pagos"><i class="fas fa-eye"></i></button>
                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEditarDeuda" title="Editar"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-sm btn-danger" title="Eliminar"><i class="fas fa-trash"></i></button>
                  </td>
                </tr>
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
  <script src="../js/functions/gestionDeudas.js"></script> 
  <script>
  </script>
</body>
</html>