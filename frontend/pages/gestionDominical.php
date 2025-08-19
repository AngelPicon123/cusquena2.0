<?php
require_once '../../backend/includes/auth.php';
verificarPermiso(['Administrador', 'Secretaria']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Lubricentro Cusqueña - Gestión de Dominical</title>
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
                    <h1 class="mb-4 text-center">Gestión de Dominical</h1>


                    <div class="row mb-4">
                        <div class="col-12 d-flex flex-wrap align-items-center justify-content-between gap-3">
                            
                            <div class="d-flex flex-wrap align-items-center gap-3">
                                
                                <div class="d-flex align-items-center gap-2">
                                    <label for="buscarDominical" class="form-label mb-0 text-nowrap d-none d-sm-block">Buscar por Nombre:</label>
                                    <input type="text" class="form-control form-control-sm" id="buscarDominical" placeholder="Nombre">
                                </div>
                                
                                <div class="d-flex align-items-center gap-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <label for="semanaInicioFiltro" class="form-label mb-0 text-nowrap d-none d-sm-block">Semana Inicio:</label>
                                        <input type="date" class="form-control form-control-sm" id="semanaInicioFiltro">
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <label for="semanaFinFiltro" class="form-label mb-0 text-nowrap d-none d-sm-block">Semana Fin:</label>
                                        <input type="date" class="form-control form-control-sm" id="semanaFinFiltro">
                                    </div>
                                </div>

                                <div class="d-flex align-items-center gap-2">
                                    <label for="filterEstadoDominical" class="form-label mb-0 text-nowrap d-none d-sm-block">Estado:</label>
                                    <select class="form-select form-select-sm" id="filterEstadoDominical">
                                        <option value="">Todos</option>
                                        <option value="Pendiente">Pendiente</option>
                                        <option value="Pagado">Pagado</option>
                                        <option value="Exento">Exento</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <button class="btn btn-primary btn-sm" id="btnBuscar">Buscar</button>
                                </div>
                            </div>
                            
                            <?php if ($_SESSION['rol'] === 'Administrador'): ?>
                                <div class="ms-auto">
                                    <a href="#" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAgregarDominical">Agregar Dominical</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="modal fade" id="modalAgregarDominical">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h3 class="modal-title">Registro de Dominical</h3>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="formAgregarDominical">
                                        <div class="mb-3">
                                            <label for="nombre" class="form-label fw-bold">Nombre:</label>
                                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="apellidos" class="form-label fw-bold">Apellidos:</label>
                                            <input type="text" class="form-control" id="apellidos" name="apellidos" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="fechaDomingo" class="form-label fw-bold">Fecha Domingo:</label>
                                            <input type="date" class="form-control" id="fechaDomingo" name="fecha_domingo" required>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="semanaInicio" class="form-label fw-bold">Semana Inicio:</label>
                                                <input type="date" class="form-control" id="semanaInicio" name="semana_inicio" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="semanaFin" class="form-label fw-bold">Semana Fin:</label>
                                                <input type="date" class="form-control" id="semanaFin" name="semana_fin" required>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="montoDominical" class="form-label fw-bold">Monto Dominical:</label>
                                            <input type="number" step="0.01" class="form-control" id="montoDominical" name="monto_dominical" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="estado" class="form-label fw-bold">Estado:</label>
                                            <select class="form-select" id="estado" name="estado" required>
                                                <option value="Pendiente">Pendiente</option>
                                                <option value="Pagado">Pagado</option>
                                                <option value="Exento">Exento</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="diferencia" class="form-label fw-bold">Diferencia:</label>
                                            <input type="number" step="0.01" class="form-control" id="diferencia" name="diferencia" value="0.00">
                                        </div>
                                        <div class="modal-footer d-flex justify-content-center">
                                            <button type="submit" class="btn btn-primary">Agregar Dominical</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="modalEditarDominical">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h3 class="modal-title">Editar Dominical</h3>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="formEditarDominical">
                                        <input type="hidden" id="editDominicalId" name="id"> <div class="mb-3">
                                            <label for="edit_nombre" class="form-label fw-bold">Nombre:</label>
                                            <input type="text" class="form-control" id="edit_nombre" name="nombre" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="edit_apellidos" class="form-label fw-bold">Apellidos:</label>
                                            <input type="text" class="form-control" id="edit_apellidos" name="apellidos" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="edit_fechaDomingo" class="form-label fw-bold">Fecha Domingo:</label>
                                            <input type="date" class="form-control" id="edit_fechaDomingo" name="fecha_domingo" required> </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="edit_semanaInicio" class="form-label fw-bold">Semana Inicio:</label>
                                                <input type="date" class="form-control" id="edit_semanaInicio" name="semana_inicio" required> </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="edit_semanaFin" class="form-label fw-bold">Semana Fin:</label>
                                                <input type="date" class="form-control" id="edit_semanaFin" name="semana_fin" required> </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="edit_montoDominical" class="form-label fw-bold">Monto Dominical:</label>
                                            <input type="number" step="0.01" class="form-control" id="edit_montoDominical" name="monto_dominical" required> </div>
                                        <div class="mb-3">
                                            <label for="edit_estado" class="form-label fw-bold">Estado:</label>
                                            <select class="form-select" id="edit_estado" name="estado" required>
                                                <option value="Pendiente">Pendiente</option>
                                                <option value="Pagado">Pagado</option>
                                                <option value="Exento">Exento</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="edit_diferencia" class="form-label fw-bold">Diferencia:</label>
                                            <input type="number" step="0.01" class="form-control" id="edit_diferencia" name="diferencia" value="0.00">
                                        </div>
                                        <div class="modal-footer d-flex justify-content-center">
                                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                                
                    <div class="modal fade" id="modalEliminarDominicalConfirmacion" tabindex="-1" aria-labelledby="modalEliminarDominicalConfirmacionLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title" id="modalEliminarDominicalConfirmacionLabel">¿Confirmar Eliminación?</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                </div>
                                <div class="modal-body">
                                    ¿Estás seguro de que deseas eliminar este coordinador? Esta acción no se puede deshacer.
                                    <input type="hidden" id="dominicalIdParaConfirmarEliminar">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="button" class="btn btn-danger" id="btnConfirmarEliminarDominical">Eliminar</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="modalEditarPago" tabindex="-1" aria-labelledby="modalEditarPagoLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalEditarPagoLabel">Editar Pago de Dominical</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form id="formEditarPago">
                                    <div class="modal-body">
                                        <input type="hidden" id="editPagoId" name="id">
                                        <input type="hidden" id="editPagoDominicalId" name="dominical_id">
                                        <div class="mb-3">
                                            <label for="editFechaPago" class="form-label">Fecha de Pago</label>
                                            <input type="date" class="form-control" id="editFechaPago" name="fecha_pago" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="editMontoPago" class="form-label">Monto Pagado</label>
                                            <input type="number" step="0.01" class="form-control" id="editMontoPago" name="monto_pagado" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="modalVerPagos" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Historial de Pagos de: <span id="nombreDominical"></span></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-4 p-3 border rounded">
                                        <h6>Registrar Nuevo Pago</h6>
                                        <form id="formAgregarPago">
                                            <input type="hidden" id="pagoDominicalId" name="dominicalId">
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
    
                    <div class="modal fade" id="modalEliminarPagoConfirmacion" tabindex="-1" aria-labelledby="modalEliminarPagoConfirmacionLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title" id="modalEliminarPagoConfirmacionLabel">¿Confirmar Eliminación de Pago?</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                </div>
                                <div class="modal-body">
                                    ¿Estás seguro de que deseas eliminar este **pago de dominical**? Esta acción no se puede deshacer y el monto del dominical será recalculado.
                                    <input type="hidden" id="pagoIdParaEliminarConfirmacion">
                                    <input type="hidden" id="dominicalIdParaPagoEliminarConfirmacion">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="button" class="btn btn-danger" id="btnConfirmarEliminarPago">Eliminar Pago</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive my-4">
                        <table class="table table-striped table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th scope="col">Nombre</th>
                                    <th scope="col">Apellidos</th>
                                    <th scope="col">Fecha Domingo</th>
                                    <th scope="col">Semana Inicio</th>
                                    <th scope="col">Semana Fin</th>
                                    <th scope="col">Monto Dominical</th>
                                    <th scope="col">Estado</th>
                                    <th scope="col">Diferencia</th>
                                    <th scope="col">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tablaDominical">
                                </tbody>
                        </table>
                    </div>

                    <div class="card-footer text-end mb-4">
                        <strong>Total del Monto Dominical:</strong>
                        <span id="totalGeneralMontoDisplay" class="fw-bold">S/. 0.00</span>
                    </div>

                    <div class="card-footer text-end mb-4">
                        <strong>Total de Diferencia:</strong>
                        <span id="totalDiferenciaDisplay" class="fw-bold">S/. 0.00</span>
                    </div>
                    
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-end">
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
    <script src="../js/functions/gestionDominical.js"></script>
</body>
</html>