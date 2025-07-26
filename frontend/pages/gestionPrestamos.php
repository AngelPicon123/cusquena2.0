<?php
require_once '../../backend/includes/auth.php';
verificarPermiso(['Administrador', 'Secretaria']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Lubricentro Cusqueña - Gestión de Préstamos</title>
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
            max-width: 200px;
            padding: 10px;
        }
        .table td button {
            margin: 0 5px;
            white-space: nowrap;
        }
        .table-responsive {
            overflow-x: auto;
        }
        @media (max-width: 576px) {
            .table th,
            .table td {
                font-size: 14px;
                max-width: 150px;
            }
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
            <main class="container-xl my-2 col-10 mx-auto">
                <div class="container-fluid px-4">
                    <h1 class="mb-4 text-center">Gestión de Préstamos</h1>
                    <div class="row">
                        <div class="col-12 d-flex justify-content-between align-items-center">
                            <div class="d-flex">
                                <input type="text" class="form-control me-2" id="buscarPrestamo" placeholder="Buscar Préstamo">
                                <button class="btn btn-primary" id="btnBuscar">Buscar</button>
                            </div>
                            <?php if ($_SESSION['rol'] === 'Administrador'): ?>
                            <div>
                                <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAgregar">Agregar</a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="modal fade" id="modalAgregar">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h3 class="modal-title" id="miModalLabel">Registro de Préstamo</h3>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="formAgregar">
                                        <div class="mb-3">
                                            <label for="nombre" class="form-label fw-bold">Nombre:</label>
                                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="tipo_persona" class="form-label fw-bold">Tipo de Persona:</label>
                                            <select class="form-select" id="tipo_persona" name="tipo_persona" required>
                                                <option value="natural">Natural</option>
                                                <option value="juridica">Jurídica</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="monto_deuda" class="form-label fw-bold">Monto Deuda:</label>
                                            <input type="number" step="0.01" class="form-control" id="monto_deuda" name="monto_deuda" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="saldo_pendiente" class="form-label fw-bold">Saldo Pendiente:</label>
                                            <input type="number" step="0.01" class="form-control" id="saldo_pendiente" name="saldo_pendiente" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="estado" class="form-label fw-bold">Estado:</label>
                                            <select class="form-select" id="estado" name="estado" required>
                                                <option value="activo">Activo</option>
                                                <option value="inactivo">Inactivo</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="fecha_inicio_deuda" class="form-label fw-bold">Fecha Inicio Deuda:</label>
                                            <input type="date" class="form-control" id="fecha_inicio_deuda" name="fecha_inicio_deuda" required>
                                        </div>
                                        <div class="modal-footer d-flex justify-content-center">
                                            <button type="submit" class="btn btn-primary">Agregar</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="modalEditarDeuda" tabindex="-1" aria-labelledby="modalEditarDeudaLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalEditarDeudaLabel">Editar Deuda</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="formEditarDeuda">
                                        <input type="hidden" id="editPrestamoId" name="id">
                                        <div class="mb-3">
                                            <label for="editNombre" class="form-label fw-bold">Nombre:</label>
                                            <input type="text" class="form-control" id="editNombre" name="nombre" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="editTipoPersona" class="form-label fw-bold">Tipo de Persona:</label>
                                            <select class="form-select" id="editTipoPersona" name="tipo_persona" required>
                                                <option value="natural">Natural</option>
                                                <option value="juridica">Jurídica</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="editMontoDeuda" class="form-label fw-bold">Monto Deuda:</label>
                                            <input type="number" step="0.01" class="form-control" id="editMontoDeuda" name="monto_deuda" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="editSaldoPendiente" class="form-label fw-bold">Saldo Pendiente:</label>
                                            <input type="number" step="0.01" class="form-control" id="editSaldoPendiente" name="saldo_pendiente" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="editEstado" class="form-label fw-bold">Estado:</label>
                                            <select class="form-select" id="editEstado" name="estado" required>
                                                <option value="activo">Activo</option>
                                                <option value="inactivo">Inactivo</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="editFechaInicioDeuda" class="form-label fw-bold">Fecha Inicio Deuda:</label>
                                            <input type="date" class="form-control" id="editFechaInicioDeuda" name="fecha_inicio_deuda" required>
                                        </div>
                                        <div class="modal-footer d-flex justify-content-center">
                                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="eliminarModal" tabindex="-1" aria-labelledby="eliminarModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title" id="eliminarModalLabel">¿Confirmar Eliminación?</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                </div>
                                <div class="modal-body">
                                    ¿Estás seguro de que deseas eliminar este préstamo? Esta acción no se puede deshacer.
                                    <input type="hidden" id="prestamoIdParaEliminarConfirmacion">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="button" class="btn btn-danger" id="confirmarEliminarPrestamo">Eliminar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="modalVerPagosPrestamo" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Historial de Pagos de: <span id="nombrePrestamo"></span></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-4 p-3 border rounded">
                                        <h6>Registrar Nuevo Pago</h6>
                                        <form id="formAgregarPagoPrestamo">
                                            <input type="hidden" id="pagoPrestamoId" name="prestamoId">
                                            <div class="row align-items-end">
                                                <div class="col-md-4">
                                                    <label for="fechaNuevoPago" class="form-label">Fecha de Pago</label>
                                                    <input type="date" class="form-control" id="fechaNuevoPago" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="montoNuevoPago" class="form-label">Monto Pagado</label>
                                                    <input type="number" step="0.01" class="form-control" id="montoNuevoPago" required>
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
                                            <tbody id="tablaPagosHistorialPrestamo">
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
                    <div class="modal fade" id="modalEliminarPagoPrestamoConfirmacion" tabindex="-1" aria-labelledby="modalEliminarPagoPrestamoConfirmacionLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title" id="modalEliminarPagoPrestamoConfirmacionLabel">¿Confirmar Eliminación de Pago?</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                </div>
                                <div class="modal-body">
                                    ¿Estás seguro de que deseas eliminar este **pago de préstamo**? Esta acción no se puede deshacer y el saldo pendiente del préstamo será recalculado.
                                    <input type="hidden" id="pagoIdParaEliminarPrestamoConfirmacion">
                                    <input type="hidden" id="prestamoIdParaPagoEliminarConfirmacion">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="button" class="btn btn-danger" id="btnConfirmarEliminarPagoPrestamo">Eliminar Pago</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive my-4">
                        <table class="table table-striped table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th scope="col">Nombre</th>
                                    <th scope="col">Tipo de Persona</th>
                                    <th scope="col">Monto Deuda</th>
                                    <th scope="col">Saldo Pendiente</th>
                                    <th scope="col">Estado</th>
                                    <th scope="col">Fecha Inicio Deuda</th>
                                    <th scope="col">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tablaDeudas">
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
                </div>
            </main>
        </div>
    </div>
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="toastSuccess" class="toast align-items-center border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body text-white bg-success" id="toastSuccessBody">
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>

        <div id="toastError" class="toast align-items-center border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body text-white bg-danger" id="toastErrorBody">
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <script src="../js/bootstrap.bundle.min.js"></script>
    <script src="../js/functions/gestionPrestamos.js"></script>
</body>
</html>