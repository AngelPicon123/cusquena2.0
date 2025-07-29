<?php
require_once '../../backend/includes/auth.php';
verificarPermiso(['Administrador', 'Secretaria']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Lubricentro Cusqueña - Gestión de Balance</title>
  <link href="../css/bootstrap.css" rel="stylesheet" />
  <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
  <style>
    .table {
      width: 100%;
      table-layout: auto;
      border-collapse: collapse;
    }
    .table th,
    .table td {
      text-align: center;
      vertical-align: middle;
      overflow-wrap: break-word;
      max-width: 200px;
      padding: 10px;
      border: 1px solid #dee2e6;
    }
    .table th {
      background-color: #343a40;
      color: white;
    }
    .table td button {
      margin: 0 5px;
      white-space: nowrap;
    }
    .table-responsive {
      overflow-x: auto;
    }
    @media print {
      .no-exportar {
        display: none; /* Oculta la columna Acciones al imprimir */
      }
      .table th,
      .table td {
        border: 1px solid black;
        padding: 8px;
        text-align: center;
      }
      .table th {
        background-color: #343a40;
        color: white;
      }
    }
    @media (max-width: 576px) {
      .table th,
      .table td {
        font-size: 14px;
        max-width: 150px;
      }
    }
    .total-container {
      margin-bottom: 10px;
      font-weight: bold;
      text-align: right;
      font-size: 16px;
    }
    .btn-custom {
      background-color: #007bff; /* Color azul de Bootstrap btn-primary */
      border: 1px solid #007bff;
      color: #fff; /* Texto blanco */
      padding: 6px 12px;
      font-size: 14px;
      border-radius: 4px;
      text-decoration: none;
    }
    .btn-custom:hover {
      background-color: #0056b3; /* Azul más oscuro para hover, similar a btn-primary */
      border-color: #0056b3;
      color: #fff;
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
          <h1 class="mb-4 text-center">Gestión Balance de Lubricentro</h1>
          <div class="row">
            <div class="col-12 d-flex justify-content-between align-items-center">
              <div class="d-flex">
                <input type="text" class="form-control me-2" id="buscarNombre" placeholder="Nombre">
                <select class="form-control me-2" id="buscarMes" style="width: 120px;">
                  <option value="">Mes</option>
                  <option value="Enero">Enero</option>
                  <option value="Febrero">Febrero</option>
                  <option value="Marzo">Marzo</option>
                  <option value="Abril">Abril</option>
                  <option value="Mayo">Mayo</option>
                  <option value="Junio">Junio</option>
                  <option value="Julio">Julio</option>
                  <option value="Agosto">Agosto</option>
                  <option value="Septiembre">Septiembre</option>
                  <option value="Octubre">Octubre</option>
                  <option value="Noviembre">Noviembre</option>
                  <option value="Diciembre">Diciembre</option>
                </select>
                <input type="number" class="form-control me-2" id="buscarAnio" placeholder="Año" style="width: 100px;">
                <button class="btn btn-primary" id="btnBuscar">Buscar</button>
              </div>
              <div>
                <button class="btn-custom me-2" id="btnImprimir">Imprimir</button>
                <button class="btn-custom me-2" id="btnExportarPDF">Exportar PDF</button>
                <?php if ($_SESSION['rol'] === 'Administrador'): ?>
                <a href="#" class="btn-custom" data-bs-toggle="modal" data-bs-target="#modalAgregarBalance">Agregar Balance</a>
                <?php endif; ?>
              </div>
            </div>
          </div>

          <div class="modal fade" id="modalAgregarBalance">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h3 class="modal-title">Registro de Balance</h3>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                  <form id="formAgregarBalance">
                    <div class="mb-3">
                      <label for="nombre" class="form-label fw-bold">Nombre / Descripción:</label>
                      <input type="text" class="form-control" id="nombre" name="nombre" required placeholder="Ej: Balance Mensual, Cierre de Caja">
                    </div>
                    <div class="mb-3">
                      <label class="form-label fw-bold">Tipo de Balance:</label>
                      <select class="form-select" name="tipoBalance" required>
                        <option value="">Seleccione un tipo</option>
                        <option value="Servicios">Servicios</option>
                        <option value="Productos">Productos</option>
                        <option value="Deudas">Deudas</option>
                        <option value="Gastos">Gastos</option>
                      </select>
                    </div>
                    <div class="mb-3">
                      <label class="form-label fw-bold">Mes:</label>
                      <select class="form-select" name="mes" required>
                        <option value="">Mes</option>
                        <option value="Enero">Enero</option>
                        <option value="Febrero">Febrero</option>
                        <option value="Marzo">Marzo</option>
                        <option value="Abril">Abril</option>
                        <option value="Mayo">Mayo</option>
                        <option value="Junio">Junio</option>
                        <option value="Julio">Julio</option>
                        <option value="Agosto">Agosto</option>
                        <option value="Septiembre">Septiembre</option>
                        <option value="Octubre">Octubre</option>
                        <option value="Noviembre">Noviembre</option>
                        <option value="Diciembre">Diciembre</option>
                      </select>
                    </div>
                    <div class="mb-3">
                      <label class="form-label fw-bold">Año:</label>
                      <input type="number" class="form-control" name="anio" min="2000" max="2100" required>
                    </div>
                    <div class="mb-3">
                      <label for="monto" class="form-label fw-bold">Monto:</label>
                      <input type="number" step="0.01" class="form-control" id="monto" name="monto" required>
                    </div>
                    <div class="modal-footer d-flex justify-content-center">
                      <button type="submit" class="btn btn-primary">Agregar Balance</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
          <div class="modal fade" id="modalEditarBalance">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h3 class="modal-title">Editar Balance</h3>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                  <form id="formEditarBalance">
                    <input type="hidden" id="editBalanceId" name="balanceId">
                    <div class="mb-3">
                      <label for="edit_nombre" class="form-label fw-bold">Nombre / Descripción:</label>
                      <input type="text" class="form-control" id="edit_nombre" name="nombre" required>
                    </div>
                    <div class="mb-3">
                      <label class="form-label fw-bold">Tipo de Balance:</label>
                      <select class="form-select" id="editarTipoBalance" name="tipoBalance" required>
                        <option value="">Seleccione un tipo</option>
                        <option value="Servicios">Servicios</option>
                        <option value="Productos">Productos</option>
                        <option value="Deudas">Deudas</option>
                        <option value="Gastos">Gastos</option>
                      </select>
                    </div>
                    <div class="mb-3">
                      <label class="form-label fw-bold">Mes:</label>
                      <select class="form-select" name="mes" required>
                        <option value="">Seleccione un mes</option>
                        <option value="Enero">Enero</option>
                        <option value="Febrero">Febrero</option>
                        <option value="Marzo">Marzo</option>
                        <option value="Abril">Abril</option>
                        <option value="Mayo">Mayo</option>
                        <option value="Junio">Junio</option>
                        <option value="Julio">Julio</option>
                        <option value="Agosto">Agosto</option>
                        <option value="Septiembre">Septiembre</option>
                        <option value="Octubre">Octubre</option>
                        <option value="Noviembre">Noviembre</option>
                        <option value="Diciembre">Diciembre</option>
                      </select>
                    </div>
                    <div class="mb-3">
                      <label class="form-label fw-bold">Año:</label>
                      <input type="number" class="form-control" name="anio" min="2000" max="2100" required>
                    </div>
                    <div class="mb-3">
                      <label for="edit_monto" class="form-label fw-bold">Monto:</label>
                      <input type="number" step="0.01" class="form-control" id="edit_monto" name="monto" required>
                    </div>
                    <div class="modal-footer d-flex justify-content-center">
                      <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
          <div id="contenedorBalanceImprimir">
            <div class="table-responsive my-4">
              <table class="table table-bordered table-hover text-center">
                <thead class="table-dark">
                  <tr>
                    <th>Nombre / Descripcion</th>
                    <th>Tipo de Balance</th>
                    <th>Mes</th>
                    <th>Año</th>
                    <th>Monto</th>
                    <th class="no-exportar">Acciones</th>
                  </tr>
                </thead>
                <tbody id="tablaBalance" class="align-middle">
                </tbody>
              </table>
            </div>
            <div class="total-container" id="totalGeneral">Total General: S/. 0.00</div>
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
  <script src="../js/bootstrap.bundle.min.js"></script>
  <script src="../js/functions/balanceLubricentro.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const btnImprimir = document.getElementById('btnImprimir');
      const btnExportarPDF = document.getElementById('btnExportarPDF');
      const { jsPDF } = window.jspdf;

      // Impresión
      btnImprimir.addEventListener('click', () => {
        const table = document.getElementById('tablaBalance');
        const total = document.getElementById('totalGeneral').textContent;
        let tableHTML = '<table style="border-collapse: collapse; width: 100%;"><thead><tr>';

        // Construir cabecera con los nombres de las columnas, excluyendo "Acciones"
        tableHTML += `
          <th style="background-color: #343a40; color: white; font-weight: bold; border: 1px solid #000; padding: 8px; text-align: center;">Nombre / Descripción</th>
          <th style="background-color: #343a40; color: white; font-weight: bold; border: 1px solid #000; padding: 8px; text-align: center;">Tipo de Balance</th>
          <th style="background-color: #343a40; color: white; font-weight: bold; border: 1px solid #000; padding: 8px; text-align: center;">Mes</th>
          <th style="background-color: #343a40; color: white; font-weight: bold; border: 1px solid #000; padding: 8px; text-align: center;">Año</th>
          <th style="background-color: #343a40; color: white; font-weight: bold; border: 1px solid #000; padding: 8px; text-align: center;">Monto</th>
        `;
        tableHTML += '</tr></thead><tbody>';

        // Construir cuerpo sin "Acciones"
        table.querySelectorAll('tr').forEach(row => {
          tableHTML += '<tr>';
          const cells = row.querySelectorAll('td');
          cells.forEach((cell, index) => {
            if (index < 5) {
              tableHTML += `<td style="border: 1px solid #000; padding: 8px; text-align: center;">${cell.textContent}</td>`;
            }
          });
          tableHTML += '</tr>';
        });
        tableHTML += '</tbody></table>';

        const ventana = window.open('', '', 'height=700,width=900');
        ventana.document.write('<html><head><title>Imprimir Balance</title>');
        ventana.document.write(`
          <style>
            .total-container {
              margin-top: 10px;
              font-weight: bold;
              text-align: right;
              font-size: 16px;
            }
            h1 {
              text-align: center;
              font-size: 24px;
              font-weight: bold;
              margin-bottom: 20px;
            }
          </style>
        `);
        ventana.document.write('</head><body>');
        ventana.document.write('<h1>Gestión de Balance - Lubricentro Cusqueña</h1>');
        ventana.document.write(tableHTML);
        ventana.document.write(`<div class="total-container">${total}</div>`);
        ventana.document.write('</body></html>');
        ventana.document.close();
        ventana.print();
      });

      // Exportación a PDF
      btnExportarPDF.addEventListener('click', () => {
        const doc = new jsPDF();
        const table = document.getElementById('tablaBalance');
        const total = document.getElementById('totalGeneral').textContent;

        // Obtener los datos de la tabla
        const rows = [];
        table.querySelectorAll('tr').forEach(row => {
          const rowData = [];
          row.querySelectorAll('td').forEach((cell, index) => {
            if (index < 5) { // Excluir la columna de acciones (índice 5)
              rowData.push(cell.textContent);
            }
          });
          if (rowData.length > 0) {
            rows.push(rowData);
          }
        });

        // Generar la tabla en el PDF
        doc.autoTable({
          head: [['Nombre / Descripción', 'Tipo de Balance', 'Mes', 'Año', 'Monto']],
          body: rows,
          styles: {
            fontSize: 10,
            cellPadding: 2,
            textColor: [0, 0, 0],
            lineColor: [0, 0, 0],
            lineWidth: 0.1
          },
          headStyles: {
            fillColor: [52, 58, 64], // Color de fondo de la cabecera (#343a40)
            textColor: [255, 255, 255], // Texto blanco
            fontStyle: 'bold'
          },
          margin: { top: 30 },
          didDrawPage: function (data) {
            // Agregar título
            doc.setFontSize(18);
            doc.setFont('helvetica', 'bold');
            doc.text('Gestión de Balance - Lubricentro Cusqueña', 105, 20, { align: 'center' });
          }
        });

        // Agregar el total al final
        doc.setFontSize(12);
        doc.setFont('helvetica', 'bold');
        doc.text(total, doc.internal.pageSize.width - 20, doc.autoTable.previous.finalY + 10, { align: 'right' });

        // Guardar el PDF
        doc.save('BalanceLubricentro.pdf');
      });
    });
  </script>
</body>
</html>