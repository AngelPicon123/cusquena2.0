// balanceEmpresa.js

// Espera a que el DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', () => {
    // URL base para las peticiones a la API de balance
    const API_BASE_URL = 'http://localhost/cusquena/backend/api/controllers/vista_balance_empresa/';

    // --- Elementos del DOM ---
    const tablaBalance = document.getElementById('tablaBalance').querySelector('tbody');
    const formAgregarBalance = document.getElementById('formAgregarBalance');
    const formEditarBalance = document.getElementById('formEditarBalance');
    const modalAgregarBalance = new bootstrap.Modal(document.getElementById('modalAgregarBalance'));
    const modalEditarBalance = new bootstrap.Modal(document.getElementById('modalEditarBalance'));
    const eliminarModalBalance = new bootstrap.Modal(document.getElementById('eliminarModalBalance'));
    const btnConfirmarEliminarBalance = document.getElementById('confirmarEliminarBalance');

    // Elementos de búsqueda/filtrado
    const buscarNombreInput = document.getElementById('buscarNombre');
    const buscarMesSelect = document.getElementById('buscarMes');
    const buscarAnioInput = document.getElementById('buscarAnio');
    const filterTipoBalanceSelect = document.getElementById('filterTipoBalance'); // Nuevo selector para el tipo de balance
    const btnBuscar = document.getElementById('btnBuscar');

    // Elementos para notificaciones
    const toastSuccess = new bootstrap.Toast(document.getElementById('toastSuccess') || {});
    const toastError = new bootstrap.Toast(document.getElementById('toastError') || {});
    const toastSuccessBody = document.getElementById('toastSuccessBody');
    const toastErrorBody = document.getElementById('toastErrorBody');

    // Elemento para el total general
    const totalGeneralElement = document.getElementById('totalGeneral');

    let balanceIdAEliminar = null;

    // --- Funciones de Utilidad ---
    function showToast(type, message) {
        if (type === 'success' && toastSuccessBody) {
            toastSuccessBody.textContent = message;
            toastSuccess.show();
        } else if (type === 'error' && toastErrorBody) {
            toastErrorBody.textContent = message;
            toastError.show();
        } else {
            console.warn(`Toast no disponible o tipo incorrecto: ${type}. Mensaje: ${message}`);
        }
    }

    function resetForm(form) {
        form.reset();
        form.querySelectorAll('select').forEach(select => {
            select.selectedIndex = 0;
        });
    }

    function actualizarTotalGeneral(total) {
        if (totalGeneralElement) {
            totalGeneralElement.textContent = `Total General: S/. ${parseFloat(total).toFixed(2)}`;
        }
    }

    // --- Carga y Renderizado de la Tabla de Balance ---
    async function cargarBalances(filtros = {}) {
        try {
            const params = new URLSearchParams(filtros);
            
            // Construir la URL de forma segura, agregando solo los filtros que tienen valor
            let queryString = '';
            const filtroArray = [];
            for (const key in filtros) {
                if (filtros[key] && filtros[key].trim() !== '') {
                    filtroArray.push(`${encodeURIComponent(key)}=${encodeURIComponent(filtros[key])}`);
                }
            }
            if (filtroArray.length > 0) {
                queryString = `?${filtroArray.join('&')}`;
            }

            const response = await fetch(`${API_BASE_URL}listar.php${queryString}`);
            if (!response.ok) {
                const errorText = await response.text();
                throw new Error(`Error HTTP: ${response.status} - ${errorText}`);
            }

            const data = await response.json();

            if (data.success) {
                renderizarTablaBalance(data.balances || []);
                actualizarTotalGeneral(data.total_general_monto || 0);
            } else {
                showToast('error', data.error || '❌ Error al obtener los balances.');
                renderizarTablaBalance([]);
                actualizarTotalGeneral(0);
            }
        } catch (error) {
            console.error('Error al cargar balances:', error);
            showToast('error', '❌ Error de conexión al cargar los balances. Intenta de nuevo.');
            renderizarTablaBalance([]);
            actualizarTotalGeneral(0);
        }
    }

    function renderizarTablaBalance(lista) {
        tablaBalance.innerHTML = '';

        if (!lista || lista.length === 0) {
            const row = tablaBalance.insertRow();
            row.innerHTML = '<td colspan="5" class="text-center">No hay balances registrados.</td>';
            return;
        }

        lista.forEach(b => {
            const row = tablaBalance.insertRow();
            row.insertCell().textContent = b.nombre_descripcion;
            row.insertCell().textContent = b.tipo_balance;
            row.insertCell().textContent = `${b.mes || ''} ${b.anio || ''}`.trim();
            row.insertCell().textContent = `S/. ${parseFloat(b.monto).toFixed(2)}`;

            const accionesCell = row.insertCell();

            // Botones de acción
            const btnEditar = document.createElement('button');
            btnEditar.className = 'btn btn-warning btn-sm me-1';
            btnEditar.innerHTML = '<i class="fas fa-edit"></i>';
            btnEditar.title = 'Editar Balance';
            btnEditar.addEventListener('click', () => llenarModalEditar(b));
            accionesCell.appendChild(btnEditar);

            const btnEliminar = document.createElement('button');
            btnEliminar.className = 'btn btn-danger btn-sm';
            btnEliminar.innerHTML = '<i class="fas fa-trash-alt"></i>';
            btnEliminar.title = 'Eliminar Balance';
            btnEliminar.addEventListener('click', () => {
                balanceIdAEliminar = b.id;
                eliminarModalBalance.show();
            });
            accionesCell.appendChild(btnEliminar);
        });
    }

    // Función unificada para aplicar filtros
    function aplicarFiltros() {
        const filtros = {
            nombre: buscarNombreInput.value.trim(),
            mes: buscarMesSelect.value,
            anio: buscarAnioInput.value,
            tipo_balance: filterTipoBalanceSelect.value // Incluimos el nuevo filtro
        };
        cargarBalances(filtros);
    }

    // --- Manejo de Eventos ---

    // Manejo del formulario de agregar balance
    formAgregarBalance.addEventListener('submit', async e => {
        e.preventDefault();
        const formData = new FormData(formAgregarBalance);
        const datos = Object.fromEntries(formData.entries());

        try {
            const response = await fetch(`${API_BASE_URL}registrar.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(datos)
            });
            const data = await response.json();

            if (data.success) {
                modalAgregarBalance.hide();
                resetForm(formAgregarBalance);
                showToast('success', '✅ Balance registrado exitosamente.');
                aplicarFiltros(); // Recargamos la tabla con los filtros actuales
            } else {
                showToast('error', data.error || '❌ Error al registrar el balance.');
            }
        } catch (error) {
            console.error('Error al registrar balance:', error);
            showToast('error', '❌ Error de conexión al registrar el balance.');
        }
    });

    /**
     * Llena el modal de edición con los datos del balance seleccionado.
     * @param {object} balance - Objeto con los datos del balance a editar.
     */
    function llenarModalEditar(balance) {
        document.getElementById('editBalanceId').value = balance.id;
        document.getElementById('edit_nombre').value = balance.nombre_descripcion;
        document.getElementById('edit_tipoBalance').value = balance.tipo_balance;
        
        const mesesNumericos = {
            'Enero': '01', 'Febrero': '02', 'Marzo': '03', 'Abril': '04',
            'Mayo': '05', 'Junio': '06', 'Julio': '07', 'Agosto': '08',
            'Septiembre': '09', 'Octubre': '10', 'Noviembre': '11', 'Diciembre': '12'
        };
        const mesNumerico = mesesNumericos[balance.mes];
        
        if (mesNumerico && balance.anio) {
            document.getElementById('edit_mes').value = `${balance.anio}-${mesNumerico}`;
        } else {
            document.getElementById('edit_mes').value = ''; 
            console.warn("Mes o año no disponibles para pre-llenar el input de edición.", balance);
        }
        
        document.getElementById('edit_monto').value = parseFloat(balance.monto).toFixed(2);
        modalEditarBalance.show();
    }

    // Manejo del formulario de editar balance
    formEditarBalance.addEventListener('submit', async e => {
        e.preventDefault();
        const formData = new FormData(formEditarBalance);
        const datos = Object.fromEntries(formData.entries());

        try {
            const response = await fetch(`${API_BASE_URL}actualizar.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(datos)
            });
            const data = await response.json();

            if (data.success) {
                modalEditarBalance.hide();
                showToast('success', '✅ Balance actualizado exitosamente.');
                aplicarFiltros(); // Recargar la tabla para ver los cambios
            } else {
                showToast('error', data.error || '❌ Error al actualizar el balance.');
            }
        } catch (error) {
            console.error('Error al actualizar balance:', error);
            showToast('error', '❌ Error de conexión al actualizar el balance.');
        }
    });

    // Manejo del botón de confirmación de eliminación
    btnConfirmarEliminarBalance.addEventListener('click', async () => {
        if (!balanceIdAEliminar) {
            showToast('error', '❌ No se encontró el ID del balance a eliminar.');
            eliminarModalBalance.hide();
            return;
        }

        try {
            const response = await fetch(`${API_BASE_URL}eliminar.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: balanceIdAEliminar })
            });

            const data = await response.json();
            eliminarModalBalance.hide();

            if (data.success) {
                showToast('success', '✅ Balance eliminado exitosamente!');
                balanceIdAEliminar = null;
                aplicarFiltros();
            } else {
                showToast('error', data.error || '❌ Error al eliminar el balance.');
            }
        } catch (error) {
            console.error('Error al eliminar balance:', error);
            showToast('error', '❌ Hubo un problema de conexión al intentar eliminar el balance.');
        }
    });

    // Eventos de los filtros para que la tabla se actualice dinámicamente
    btnBuscar.addEventListener('click', aplicarFiltros);
    buscarNombreInput.addEventListener('input', aplicarFiltros);
    buscarMesSelect.addEventListener('change', aplicarFiltros);
    buscarAnioInput.addEventListener('input', aplicarFiltros);
    filterTipoBalanceSelect.addEventListener('change', aplicarFiltros); // Escucha el cambio en el nuevo filtro

    // Carga inicial de la tabla al cargar la página
    cargarBalances();

    // Lógica del sidebar de bootstrap
    const sidebarToggle = document.body.querySelector('#sidebarToggle');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', event => {
            event.preventDefault();
            document.body.classList.toggle('sb-sidenav-toggled');
        });
    }

    // Funcionalidad de impresión y exportar a PDF
    const btnImprimir = document.getElementById('btnImprimir');
    const btnExportarPDF = document.getElementById('btnExportarPDF');

    if (btnImprimir) {
        btnImprimir.addEventListener('click', () => {
            window.print();
        });
    }

    if (btnExportarPDF) {
        btnExportarPDF.addEventListener('click', () => {
            exportarBalanceAPDF();
        });
    }
    
    function exportarBalanceAPDF() {
        if (typeof window.jspdf === 'undefined' || typeof window.jspdf.jsPDF === 'undefined') {
            showToast('error', 'Error: jspdf library not loaded. Make sure the script is included.');
            console.error('jsPDF library not found.');
            return;
        }

        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        
        doc.setFontSize(18);
        doc.text("Reporte de Balance de Empresa", 14, 20);

        const head = [['Nombre / Descripción', 'Tipo de Balance', 'Mes', 'Monto']];
        const body = [];
        const rows = tablaBalance.querySelectorAll('tr');
        rows.forEach(row => {
            if (row.cells.length > 1) {
                const rowData = [];
                rowData.push(row.cells[0].textContent);
                rowData.push(row.cells[1].textContent);
                rowData.push(row.cells[2].textContent);
                rowData.push(row.cells[3].textContent);
                body.push(rowData);
            }
        });

        doc.autoTable({
            head: head,
            body: body,
            startY: 30,
            theme: 'striped',
            headStyles: {
                fillColor: [33, 37, 41],
                textColor: [255, 255, 255]
            },
            styles: {
                fontSize: 10,
                cellPadding: 3
            },
            columnStyles: {
                0: { cellWidth: 'auto' },
                1: { cellWidth: 'auto' },
                2: { cellWidth: 'auto' },
                3: { cellWidth: 'auto' }
            }
        });

        const finalY = doc.autoTable.previous.finalY;
        const totalText = totalGeneralElement ? totalGeneralElement.textContent : 'Total General: S/. 0.00';
        doc.text(totalText, 14, finalY + 10);

        doc.save('reporte_balance_empresa.pdf');
        showToast('success', 'PDF exportado exitosamente!');
    }
});