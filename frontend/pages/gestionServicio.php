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
                                    <th>Precio Unitario</th>
                                    <th>Cantidad</th>
                                    <th>Fecha Registro</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tablaServicios" class="align-middle">
                                </tbody>
                        </table>
                    </div>

                    <nav aria-label="Paginación" class="d-flex justify-content-end">
                        <ul class="pagination" id="paginacionServicios">
                            </ul>
                    </nav>
                </div>
            </main>
        </div>
    </div>

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
                            <label class="form-label fw-bold">Precio Unitario:</label>
                            <input type="number" step="0.01" class="form-control" name="precioUnitario" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Cantidad:</label>
                            <input type="number" class="form-control" name="cantidad" required min="1">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Fecha de Registro:</label>
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
                            <label class="form-label fw-bold">Precio Unitario:</label>
                            <input type="number" step="0.01" class="form-control" name="precioUnitario" id="editarPrecioUnitario" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Cantidad:</label>
                            <input type="number" class="form-control" name="cantidad" id="editarCantidad" required min="1">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Fecha de Registro:</label>
                            <input type="date" class="form-control" name="fechaRegistro" id="editarFechaRegistro" required>
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

    <script src="../js/bootstrap.bundle.min.js"></script>
    <script src="../js/functions/gestionServicio.js"></script>
</body>
</html>