```html
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
    .total-container {
      margin-bottom: 10px;
      font-weight: bold;
      text-align: right;
    }
    .btn-custom {
      background-color: #f0f0f0;
      border: 1px solid #ccc;
      color: #333;
      padding: 6px 12px;
      font-size: 14px;
      border-radius: 4px;
      text-decoration: none;
    }
    .btn-custom:hover {
      background-color: #e0e0e0;
      border-color: #999;
      color: #000;
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
          <h1 class="mb-4 text-center">Gestión de Balance</h1>
          <div class="row">
            <div class="col-12 d-flex justify-content-between align-items-center">
              <div class="d-flex">
                <input type="text" class="form-control me-2" id="buscarNombre" placeholder="Nombre">
                <select class="form-control me-2" id="buscarMes" style="width: 120px;">
                  <option value="">Mes</option>
                  <option value="01">Enero</option>
                  <option value="02">Febrero</option>
                  <option value="03">Marzo</option>
                  <option value="04">Abril</option>
                  <option value="05">Mayo</option>
                  <option value="06">Junio</option>
                  <option value="07">Julio</option>
                  <option value="08">Agosto</option>
                  <option value="09">Septiembre</option>
                  <option value="10">Octubre</option>
                  <option value="11">Noviembre</option>
                  <option value="12">Diciembre</option>
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
                      <label for="tipoBalance" class="form-label fw-bold">Tipo de Balance:</label>
                      <select class="form-select" id="tipoBalance" name="tipoBalance" required>
                        <option value="" disabled selected>Seleccione un tipo</option>
                        <option value="servicios">Servicios</option>
                        <option value="productos">Productos</option>
                        <option value="deudas">Deudas</option>
                        <option value="gastos">Gastos</option>
                      </select>
                    </div>
                    <div class="mb-3">
                      <label for="mes" class="form-label fw-bold">Mes:</label>
                      <input type="month" class="form-control" id="mes" name="mes" required>
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
                      <label for="edit_tipoBalance" class="form-label fw-bold">Tipo de Balance:</label>
                      <select class="form-select" id="edit_tipoBalance" name="tipoBalance" required>
                        <option value="servicios">Servicios</option>
                        <option value="productos">Productos</option>
                        <option value="deudas">Deudas</option>
                        <option value="gastos">Gastos</option>
                      </select>
                    </div>
                    <div class="mb-3">
                      <label for="edit_mes" class="form-label fw-bold">Mes:</label>
                      <input type="month" class="form-control" id="edit_mes" name="mes" required>
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
          <div class="table-responsive my-4">
            <table class="table table-striped table-bordered">
              <thead class="table-dark">
                <tr>
                  <th scope="col">Nombre / Descripción</th>
                  <th scope="col">Tipo de Balance</th>
                  <th scope="col">Mes</th>
                  <th scope="col">Monto</th>
                  <th scope="col">Acciones</th>
                </tr>
              </thead>
              <tbody id="tablaBalance">
                <tr>
                  <td>Balance General Mensual</td>
                  <td>Deudas</td>
                  <td>Mayo 2025</td>
                  <td>S/. 1270</td>
                  <td>
                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEditarBalance" onclick="editarBalance(1)">Editar</button>
                    <button class="btn btn-sm btn-danger" onclick="eliminarBalance(1)">Eliminar</button>
                  </td>
                </tr>
                <tr>
                  <td>Total Ingresos</td>
                  <td>Productos</td>
                  <td>Abril 2025</td>
                  <td>S/. 3500</td>
                  <td>
                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEditarBalance" onclick="editarBalance(2)">Editar</button>
                    <button class="btn btn-sm btn-danger" onclick="eliminarBalance(2)">Eliminar</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="total-container" id="totalGeneral">Total General: S/. 0.00</div>
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
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const buscarNombre = document.getElementById('buscarNombre');
      const buscarMes = document.getElementById('buscarMes');
      const buscarAnio = document.getElementById('buscarAnio');
      const btnBuscar = document.getElementById('btnBuscar');
      const btnImprimir = document.getElementById('btnImprimir');
      const btnExportarPDF = document.getElementById('btnExportarPDF');
      const tablaBalance = document.getElementById('tablaBalance');
      const totalGeneral = document.getElementById('totalGeneral');
      const modalEditar = new bootstrap.Modal(document.getElementById('modalEditarBalance'));

      // Datos de ejemplo (simulados, reemplazar con datos reales de la base de datos)
      let balances = [
        { id: 1, nombre: 'Balance General Mensual', tipo: 'Deudas', mes: '2025-05', monto: 1270 },
        { id: 2, nombre: 'Total Ingresos', tipo: 'Productos', mes: '2025-04', monto: 3500 }
      ];

      // Función para mostrar los balances en la tabla
      function mostrarBalances(filtrados) {
        tablaBalance.innerHTML = '';
        let total = 0;

        filtrados.forEach(balance => {
          const [anio, mes] = balance.mes.split('-');
          const mesNombre = new Date(anio, mes - 1).toLocaleString('es-ES', { month: 'long', year: 'numeric' });
          const row = document.createElement('tr');
          row.innerHTML = `
            <td>${balance.nombre}</td>
            <td>${balance.tipo}</td>
            <td>${mesNombre.charAt(0).toUpperCase() + mesNombre.slice(1)}</td>
            <td>S/. ${balance.monto.toFixed(2)}</td>
            <td>
              <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEditarBalance" onclick="editarBalance(${balance.id})">Editar</button>
              <button class="btn btn-sm btn-danger" onclick="eliminarBalance(${balance.id})">Eliminar</button>
            </td>
          `;
          tablaBalance.appendChild(row);
          total += balance.monto;
        });

        totalGeneral.textContent = `Total General: S/. ${total.toFixed(2)}`;
      }

      // Función de búsqueda
      function buscarBalances() {
        const nombre = buscarNombre.value.toLowerCase();
        const mes = buscarMes.value;
        const anio = buscarAnio.value;

        const filtrados = balances.filter(balance => {
          const [balanceAnio, balanceMes] = balance.mes.split('-');
          return (
            (nombre === '' || balance.nombre.toLowerCase().includes(nombre)) &&
            (mes === '' || balanceMes === mes) &&
            (anio === '' || balanceAnio === anio)
          );
        });

        mostrarBalances(filtrados);
      }

      // Función para abrir el modal y cargar datos
      window.editarBalance = (id) => {
        const balance = balances.find(b => b.id === id);
        if (balance) {
          document.getElementById('editBalanceId').value = balance.id;
          document.getElementById('edit_nombre').value = balance.nombre;
          document.getElementById('edit_tipoBalance').value = balance.tipo;
          document.getElementById('edit_mes').value = balance.mes;
          document.getElementById('edit_monto').value = balance.monto;
          modalEditar.show(); // Abrir el modal manualmente
        }
      };

      // Función para eliminar
      window.eliminarBalance = (id) => {
        if (confirm('¿Está seguro de eliminar este balance?')) {
          balances = balances.filter(b => b.id !== id);
          buscarBalances();
        }
      };

      // Función para imprimir
      btnImprimir.addEventListener('click', () => {
        window.print();
      });

      // Función para exportar a PDF
      btnExportarPDF.addEventListener('click', () => {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        doc.setFontSize(18);
        doc.text('Gestión de Balance - Lubricentro Cusqueña', 14, 15);
        doc.setFontSize(12);
        doc.text(`Fecha: ${new Date().toLocaleDateString('es-ES')}`, 14, 25);

        const tableData = [];
        const rows = tablaBalance.querySelectorAll('tr');
        rows.forEach(row => {
          const rowData = [];
          row.querySelectorAll('td').forEach(cell => rowData.push(cell.textContent));
          if (rowData.length > 0) tableData.push(rowData.slice(0, 4)); // Excluir columna de acciones
        });

        doc.autoTable({
          head: [['Nombre / Descripción', 'Tipo de Balance', 'Mes', 'Monto']],
          body: tableData,
          startY: 35
        });

        doc.text(totalGeneral.textContent, 14, doc.autoTable.previous.finalY + 10);
        doc.save('balance.pdf');
      });

      // Evento de búsqueda
      btnBuscar.addEventListener('click', buscarBalances);

      // Inicializar la tabla
      mostrarBalances(balances);
    });
  </script>
</body>
</html>
