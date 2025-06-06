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
    <!-- Incluir Flatpickr CSS y JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
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
                    <h1 class="mb-4 text-center">Historial de Ingresos</h1>
                    <div class="row">
                        <div class="col-12 d-flex justify-content-between align-items-center">
                            <div class="d-flex">
                                <input type="text" class="form-control me-2" id="buscarIngreso" placeholder="Buscar Ingreso">
                                <button class="btn btn-primary" id="btnBuscar">Buscar</button>
                            </div>
                            <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#miModal">Agregar</a>
                        </div>
                    </div>
                    <!-- MODAL AGREGAR -->
                    <div class="modal fade" id="miModal" tabindex="-1" aria-labelledby="miModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h3 class="modal-title" id="miModalLabel">Registro de Ingresos</h3>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="formAgregar">
                                        <div class="mb-3">
                                            <label for="fechaIngreso" class="form-label fw-bold">Fecha de ingreso:</label>
                                            <input type="text" class="form-control" id="fechaIngreso" name="fechaIngreso" placeholder="dd/mm/aaaa" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="stock" class="form-label fw-bold">Cantidad:</label>
                                            <input type="number" class="form-control" id="stock" name="stock" min="1" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="precioCompra" class="form-label fw-bold">Precio de compra:</label>
                                            <input type="number" step="0.01" class="form-control" id="precioCompra" name="precioCompra" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="idProducto" class="form-label fw-bold">Producto:</label>
                                            <select class="form-control" id="idProducto" name="idProducto" required>
                                                <option value="">--SELECCIONAR--</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="detalle" class="form-label fw-bold">Detalles:</label>
                                            <textarea class="form-control" id="detalle" name="detalle" rows="4"></textarea>
                                        </div>
                                        <div class="modal-footer d-flex justify-content-center">
                                            <button type="submit" class="btn btn-primary">Agregar</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- MODAL EDITAR -->
                    <div class="modal fade" id="modalEditar">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h3 class="modal-title">Actualización de Ingresos</h3>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="formEditar">
                                        <div class="mb-3">
                                            <label for="editarFechaIngreso" class="form-label fw-bold">Fecha de ingreso:</label>
                                            <input type="date" class="form-control" id="editarFechaIngreso" name="fechaIngreso" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="editarstock" class="form-label fw-bold">Cantidad:</label>
                                            <input type="number" class="form-control" id="editarstock" name="stock" min="1" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="editarPrecioCompra" class="form-label fw-bold">Precio de compra:</label>
                                            <input type="number" step="0.01" class="form-control" id="editarPrecioCompra" name="precioCompra" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="editarIdProducto" class="form-label fw-bold">Producto:</label>
                                            <select class="form-control" id="editarIdProducto" name="idProducto" required>
                                                <option value="">--SELECCIONAR--</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="editarDetalle" class="form-label fw-bold">Detalles:</label>
                                            <textarea class="form-control" id="editarDetalle" name="detalle" rows="4"></textarea>
                                        </div>
                                        <div class="modal-footer d-flex justify-content-center">
                                            <button type="submit" class="btn btn-success">Modificar</button>
                                        </div>
                                        <input type="hidden" id="editarIdIngresoProducto" name="idIngresoProducto">
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive my-5">
                        <table class="table table-bordered table-hover text-center">
                            <thead>
                                <tr class="table-dark">
                                    <th>ID</th>
                                    <th>Fecha de Ingreso</th>
                                    <th>Cantidad</th>
                                    <th>Precio de compra</th>
                                    <th>Producto</th>
                                    <th>Detalles</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="align-middle" id="tablaIngresos"></tbody>
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
                    <footer class="py-4 bg-light mt-auto">
                        <div class="container-fluid px-4">
                            <div class="d-flex align-items-center justify-content-between small"></div>
                        </div>
                    </footer>
                </div>
            </main>
        </div>
    </div>
    <script src="../js/bootstrap.bundle.min.js"></script>
    <script src="../js/functions/gestionHistorialIngreso.js"></script>
</body>
</html>