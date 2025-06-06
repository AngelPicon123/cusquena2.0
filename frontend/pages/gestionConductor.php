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
          <li><a class="dropdown-item" href="#!">Cerrar Sesión</a></li>
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

<!-- Tabla -->

    <div id="layoutSidenav_content">
      <main class="container-xl my-2 col-12 mx-auto">
       <div class="container-fluid px-4 ">
          <h1 class="mb-4 text-center">Gestión de Conductores</h1>

          <div class="row">
            <div class="col-12 d-flex justify-content-between align-items-center">
              <div class="d-flex">
                <input type="text" id="buscadorConductor" class="form-control me-2" placeholder="Buscar Conductor">
                <button class="btn btn-primary" style="height: 37px;">Buscar</button>
              </div>
              <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#miModal">Agregar</a>
            </div>
          </div>
          <!-- MODAL REGISTRA CONDUCTOR -->
          <div class="modal fade " id="miModal" tabindex="-1" aria-labelledby="miModalLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header ">
                  <h3 class="modal-title" id="miModalLabel">Registro de Usuario</h3>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                  <form id="formAgregarConductor">
                    <div class="mb-3">
                      <label for="nombre" class="form-label fw-bold">Nombre:</label>
                      <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="mb-3">
                      <label for="apellido" class="form-label fw-bold">Apellido:</label>
                      <input type="text" class="form-control" id="apellido" name="apellido" required>
                    </div>
                    <div class="mb-3">
                      <label for="telefono" class="form-label fw-bold">Telefono:</label>
                      <input type="number" class="form-control" id="telefono" name="telefono" required>
                    </div>
                    <div class="mb-3">
                      <label for="dni" class="form-label fw-bold">DNI:</label>
                      <input type="number" class="form-control" id="dni" name="dni" required>
                    </div>
                    <div class="mb-3">
                      <label for="placa" class="form-label fw-bold">Placa:</label>
                      <input type="text" class="form-control" id="placa" name="placa" required>
                    </div>  

                    <div class="mb-3">
                      <label for="idTipoConductor" class="form-label fw-bold">Tipo de Conductor:</label>
                      <select class="form-select" id="idTipoConductor" name="idTipoConductor" required>
                        <option value="" disabled selected>Seleccionar tipo...</option>
                        
                      </select>
                    </div>
                     <div class="mb-3 d-flex align-items-center">
                      <label class="form-label me-3 fw-bold"></label>
                      <div class="form-check form-check-inline">
                        
                      </div>
                      <label class="form-label me-3 fw-bold"></label>
                      <div class="form-check form-check-inline">
                        
                      </div>
                    </div>
                    <div class="mb-3 d-flex align-items-center">
                      <label class="form-label me-3 fw-bold">Estado:</label>
                      <div class="form-check form-check-inline">
                        <input type="radio" class="form-check-input" name="estado" value="activo" id="estado_activo" required>
                        <label class="form-check-label" for="estado_activo">Activo</label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input type="radio" class="form-check-input" name="estado" value="inactivo" id="estado_inactivo">
                        <label class="form-check-label" for="estado_inactivo">Inactivo</label>
                      </div>
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

          <!--MODAL button EDITAR-->
          <!-- Modal editar -->
              <div class="modal fade" id="modalEditar" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                  <form id="formEditarConductor" method="POST">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title">Editar Conductor</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                      </div>
                      <div class="modal-body">

                        <input type="hidden" name="id_conductor" id="id_conductor">

                        <div class="mb-2">
                          <label>Nombre</label>
                          <input type="text" name="nombre" id="nombreEditar" class="form-control">
                        </div>

                        <div class="mb-2">
                          <label>Apellido</label>
                          <input type="text" name="apellido" id="apellidoEditar" class="form-control">
                        </div>

                        <div class="mb-2">
                          <label>Teléfono</label>
                          <input type="text" name="telefono" id="telefonoEditar" class="form-control">
                        </div>

                        <div class="mb-2">
                          <label>DNI</label>
                          <input type="text" name="dni" id="dniEditar" class="form-control">
                        </div>

                        <div class="mb-2">
                          <label>Placa</label>
                          <input type="text" name="placa" id="placaEditar" class="form-control">
                        </div>
                        <div class="mb-3">
                          <label for="idTipoConductor" class="form-label fw-bold">Tipo de Conductor:</label>
                          <select class="form-select" id="idTipoConductorEditar" name="idTipoConductor" required>
                            <option value="" disabled selected>Seleccionar tipo...</option>
                          </select>
                          
                          
                        <div class="mb-2">
                          <label>Estado</label>
                          <select name="estado" id="estadoEditar" class="form-select">
                            <option value="activo">Activo</option> <!-- Minúsculas -->
                            <option value="inactivo">Inactivo</option>
                        </select>
                        </div>

                        <div class="mb-2">
                          <label>Detalle</label>
                          <textarea name="detalle" id="detalleEditar" class="form-control"></textarea>
                        </div>

                      </div>
                      <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Actualizar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
            <!-- MODAL VER SOAT -->
            <div class="modal fade" id="modalVer" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                  <div class="modal-content">
                      <div class="modal-header bg-primary text-white">
                          <h5 class="modal-title">Registrar SOAT</h5>
                          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                      </div>
                      <div class="modal-body">
                          <form id="formVerSoat">
                              <!-- Datos del Conductor -->
                              <div class="mb-3">
                                <input type="hidden" id="idConductorHidden" name="idConductor">
                                  <label class="form-label fw-bold">Placa:</label>
                                  <input type="text" class="form-control" id="placaVer" readonly>
                              </div>
                              
                              <div class="mb-3">
                                  <label class="form-label fw-bold">Nombre:</label>
                                  <input type="text" class="form-control" id="nombreVer" readonly>
                              </div>
                              
                              <div class="mb-3">
                                  <label class="form-label fw-bold">Apellido:</label>
                                  <input type="text" class="form-control" id="apellidoVer" readonly>
                              </div>
                              
                              <div class="mb-3">
                                <label class="form-label fw-bold">telefono:</label>
                                <input type="text" class="form-control" id="telefonoVer" readonly>
                            </div>

                            <div class="mb-3">
                              <label class="form-label fw-bold">dni:</label>
                              <input type="text" class="form-control" id="dniVer" readonly>
                          </div>
                          
                          <div class="mb-3">
                                <label class="form-label fw-bold">Estado:</label>
                                <input type="text" class="form-control" id="estadoVer" readonly>
                             </div>
                              
                              <!-- Datos del SOAT -->
                                <!-- Campos editables del SOAT -->
                                <div class="mb-3">
                                  <label class="form-label fw-bold">Fecha de Emisión:</label>
                                  <input type="date" class="form-control" id="emisionVer" name="fechaMantenimiento" required>
                              </div>
                              
                              <div class="mb-3">
                                  <label class="form-label fw-bold">Fecha de Vencimiento:</label>
                                  <input type="date" class="form-control" id="vencimientoVer" name="fechaProxMantenimiento" required>
                              </div>
                              
                              <div class="mb-3">
                                <label class="form-label fw-bold">Número de SOAT:</label>
                                <input type="text" class="form-control" id="numsoatVer" name="nombre" required>
                               
                            </div>

                              <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Guardar SOAT</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            </div>
                            </form>
                            
                      </div>
                      
                      </div>
                  </div>
              </div>
            </div>

         <!-- MODAL VER COTIZACIÓN -->
<div class="modal fade" id="modalCotizacion">
  <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
          <div class="modal-header">
              <h3 class="modal-title">Detalles de Cotización</h3>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="modal-body">
              <form id="formCotizacion">
                  <!-- Campo oculto para el ID del conductor -->
                  <input type="hidden" id="id_conductor_cotizacion" name="id_conductor">
                  <input type="hidden" id="idTipoConductorHidden" name="idTipoConductorHidden">

                  <!-- Campos de visualización -->
                  <div class="mb-3">
                      <label for="nombreCotizacion" class="form-label fw-bold">Nombre:</label>
                      <input type="text" class="form-control" id="nombreCotizacion" name="nombreCotizacion" readonly>
                  </div>
                  <div class="mb-3">
                      <label for="apellidoCotizacion" class="form-label fw-bold">Apellido:</label>
                      <input type="text" class="form-control" id="apellidoCotizacion" name="apellidoCotizacion" readonly>
                  </div>
                  <div class="mb-3">
                      <label for="tipoCotizacion" class="form-label fw-bold">Tipo de conductor:</label>
                      <input type="text" class="form-control" id="tipoCotizacion" name="tipoCotizacion" readonly>
                  </div>
                  <div class="mb-3">
                      <label for="Cotizacion" class="form-label fw-bold">Cotización:</label>
                      <input type="text" class="form-control" id="Cotizacion" name="Cotizacion" readonly>
                  </div>
                  <div class="mb-3">
                      <label for="descripcion" class="form-label fw-bold">Descripción:</label>
                      <input type="text" class="form-control" id="descripcion" name="descripcion" readonly>
                  </div>
                  <div class="mb-3">
                      <label for="placaCotizacion" class="form-label fw-bold">Placa:</label>
                      <input type="text" class="form-control" id="placaCotizacion" name="placaCotizacion" readonly>
                  </div>
                  <div class="mb-3">
                      <label for="fechaCotizacion" class="form-label fw-bold">Fecha:</label>
                      <input type="date" class="form-control" id="fechaCotizacion" name="fechaCotizacion" required>
                  </div>
                  
                  

                  <div class="modal-footer d-flex justify-content-center">
                      <button type="submit" class="btn btn-success">Guardar Cotización</button>
                  </div>
              </form>
          </div>
      </div>
  </div>
</div>
                <!-- Paginación 
                    <div class="mb-3">
                      <a href="#" class="btn btn-primary p-1">Agregar Servicio</a>
                      <a href="#" class="btn btn-primary p-1">Agregar Producto</a>
                    </div>
                    <div class="mb-3">
                      <label for="estadoVer" class="form-label fw-bold">TOTAL:</label>
                      <input type="number" class="form-control form-control-sm" id="estadoVer" name="estadoVer" readonly
                        style="width: 100px;">
                    </div>
                  -->
          <div class="table-responsive my-5">
            <table class="table  table-bordered table-hover text-center">
              <thead>
                <tr class="table-dark">
                  <th>ID</th>
                  <th>Nombre</th>
                  <th>Apellido</th>
                  <th>Telefono</th>
                  <th>DNI</th>
                  <th>Placa</th>
                  <th>#SOAT</th>
                  <th>Tipo Conductor</th>
                  <th>estado</th>
                  <th>Detalles</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody class="align-middle">
                <tr>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>                                
                  <td></td>
                  <td></td>
                  <td>
                  
                    <form action="../../backend/api/controllers/registrarConductores.php" method="POST">
                      <button type="submit" class="btn btn-success" name="btnActualizar">Modificar</button>

                    </form>
              
                    <a href="#" class="btn btn-danger p-1">Eliminar</a>
                  </td>
                </tr>

              </tbody>
            </table>
          </div>

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
          <footer class="py-4 bg-light mt-auto">
            <div class="container-fluid px-4">
              <div class="d-flex align-items-center justify-content-between small">

              </div>
            </div>
          </footer>

        </div>
    </div>
  </div>
  <script src="../js/bootstrap.bundle.min.js"></script>
  <script src="../js/functions/gestionConductor.js"></script>
    
</body>

</html>