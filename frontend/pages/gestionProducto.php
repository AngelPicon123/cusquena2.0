<?php
session_start(); 
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
        <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'Administrador'): ?>
        <a class="navbar-brand ps-3" href="base.php">La Cusqueña</a>
        <?php endif; ?>
        <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'Secretaria'): ?>
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
            <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'Administrador'): ?>
            <script>
                fetch('sidebear_Admin.php') 
                    .then(r => r.text())
                    .then(html => document.getElementById('layoutSidenav_nav').innerHTML = html);
            </script>
            <?php endif; ?>
            <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'Secretaria'): ?>
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
                            <input type="text" class="form-control me-2" id="buscarProducto" placeholder="Buscar producto por descripción o categoría">
                            <button class="btn btn-primary" id="btnBuscar">Buscar</button>
                        </div>
                        <div class="col-md-6 text-end">
                            <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'Administrador'): ?>
                            <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAgregarProducto">Agregar</a>
                            <?php endif; ?>
                        </div>
                    </div>

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
                                            <label class="form-label">Descripción</label>
                                            <input type="text" class="form-control" name="descripcion" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">P. Compra</label>
                                            <input type="number" step="0.01" class="form-control" name="precio_compra" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">P. Venta</label>
                                            <input type="number" step="0.01" class="form-control" name="precio_venta" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Inicial</label>
                                            <input type="number" class="form-control" name="inicial" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Ingreso</label>
                                            <input type="number" class="form-control" name="ingreso" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Queda</label>
                                            <input type="number" class="form-control" name="queda" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Venta</label>
                                            <input type="number" class="form-control" name="venta" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Monto</label>
                                            <input type="number" step="0.01" class="form-control" name="monto" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Categoría</label>
                                            <select class="form-select" name="categoria" id="agregar_categoria" required>
                                                <option value="">Seleccione</option>
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
                                            <label class="form-label">Descripción</label>
                                            <input type="text" class="form-control" name="descripcion" id="editar_descripcion" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">P. Compra</label>
                                            <input type="number" step="0.01" class="form-control" name="precio_compra" id="editar_precio_compra" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">P. Venta</label>
                                            <input type="number" step="0.01" class="form-control" name="precio_venta" id="editar_precio_venta" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Inicial</label>
                                            <input type="number" class="form-control" name="inicial" id="editar_inicial" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Ingreso</label>
                                            <input type="number" class="form-control" name="ingreso" id="editar_ingreso" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Queda</label>
                                            <input type="number" class="form-control" name="queda" id="editar_queda" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Venta</label>
                                            <input type="number" class="form-control" name="venta" id="editar_venta" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Monto</label>
                                            <input type="number" step="0.01" class="form-control" name="monto" id="editar_monto" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Categoría</label>
                                            <select class="form-select" name="categoria" id="editar_categoria" required>
                                                <option value="">Seleccione</option>
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
                                            <label class="form-label">Descripción</label>
                                            <input type="text" class="form-control" name="descripcion" id="vender_descripcion" readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Queda Disponible</label>
                                            <input type="number" class="form-control" name="queda" id="vender_queda" readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Cantidad a Vender</label>
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
                                    <th>Inicial</th>
                                    <th>Ingreso</th>
                                    <th>Total</th>
                                    <th>Queda</th>
                                    <th>Venta</th>
                                    <th>Monto</th>
                                    <th>Categoría</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tablaProductos">
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

    <script src="../js/bootstrap.bundle.min.js"></script>
    <script src="../js/functions/gestionProducto.js"></script>
    <script>
        function llenarModalEditar(id, descripcion, precioCompra, precioVenta, inicial, ingreso, queda, venta, monto, categoria) {
            document.getElementById('editar_idProducto').value = id;
            document.getElementById('editar_descripcion').value = descripcion;
            document.getElementById('editar_precio_compra').value = precioCompra;
            document.getElementById('editar_precio_venta').value = precioVenta;
            document.getElementById('editar_inicial').value = inicial;
            document.getElementById('editar_ingreso').value = ingreso;
            document.getElementById('editar_queda').value = queda;
            document.getElementById('editar_venta').value = venta;
            document.getElementById('editar_monto').value = monto;
            document.getElementById('editar_categoria').value = categoria;
        }

        function llenarModalVender(id, descripcion, queda) {
            document.getElementById('vender_idProducto').value = id;
            document.getElementById('vender_descripcion').value = descripcion;
            document.getElementById('vender_queda').value = queda;
            document.getElementById('vender_cantidad').value = 1;
            document.getElementById('vender_cantidad').max = queda;
        }

        document.addEventListener('DOMContentLoaded', () => {
            listarProductos();
        });
    </script>