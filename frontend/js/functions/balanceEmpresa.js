// balanceEmpresa.js

// Espera a que el DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', () => {
    // URL base para las peticiones a la API de balance
    const API_BASE_URL = 'http://localhost/cusquena/backend/api/controllers/vista_balance_empresa/';

    // --- Elementos del DOM ---

    // Tabla principal de balance (le agregamos el ID en el HTML anterior)
    const tablaBalance = document.getElementById('tablaBalance').querySelector('tbody');
    if (!tablaBalance) {
        console.error("Error: No se encontró el tbody de la tabla con ID 'tablaBalance'.");
        return;
    }

    // Elementos de formularios y modales
    const formAgregarBalance = document.getElementById('formAgregarBalance');
    const formEditarBalance = document.getElementById('formEditarBalance'); // Asegúrate de que este ID existe en tu HTML
    if (!formEditarBalance) {
        console.error("Error: No se encontró el formulario de edición con ID 'formEditarBalance'.");
        return;
    }

    // Instancias de modales de Bootstrap
    const modalAgregarBalance = new bootstrap.Modal(document.getElementById('modalAgregarBalance'));
    const modalEditarBalance = new bootstrap.Modal(document.getElementById('modalEditarBalance'));
    const eliminarModalBalance = new bootstrap.Modal(document.getElementById('eliminarModalBalance'));

    // Botones de confirmación de eliminación
    const btnConfirmarEliminarBalance = document.getElementById('confirmarEliminarBalance');

    // Elementos de búsqueda/filtrado
    const buscarNombreInput = document.getElementById('buscarNombre');
    const buscarMesSelect = document.getElementById('buscarMes');
    const buscarAnioInput = document.getElementById('buscarAnio'); // Asumo que tienes un input para el año en tus filtros
    const btnBuscar = document.getElementById('btnBuscar');

    const toastSuccess = new bootstrap.Toast(document.getElementById('toastSuccess') || {});
    const toastError = new bootstrap.Toast(document.getElementById('toastError') || {});
    const toastSuccessBody = document.getElementById('toastSuccessBody');
    const toastErrorBody = document.getElementById('toastErrorBody');

    // Elemento para el total general
    const totalGeneralElement = document.getElementById('totalGeneral');

    // Variable para almacenar el ID del balance a eliminar
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
            if (select.options.length > 0) {
                select.selectedIndex = Array.from(select.options).findIndex(option => option.value === "") !== -1 ?
                                            Array.from(select.options).findIndex(option => option.value === "") :
                                            0;
            }
        });
    }

    // --- Carga y Renderizado de la Tabla de Balance ---

    async function cargarBalances(filtros = {}) {
        try {
            const params = new URLSearchParams(filtros);
            const response = await fetch(`${API_BASE_URL}listar.php?${params.toString()}`);

            if (!response.ok) {
                const errorText = await response.text();
                throw new Error(`Error HTTP: ${response.status} - ${errorText}`);
            }

            const data = await response.json();

            if (data.success) {
                renderizarTablaBalance(data.balances || []);
                // CAMBIO AQUÍ: Usar 'total_general_monto' si es lo que devuelve listar.php
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

            const mesAnioDisplay = `${b.mes || ''} ${b.anio || ''}`.trim();
            row.insertCell().textContent = mesAnioDisplay;

            row.insertCell().textContent = `S/. ${parseFloat(b.monto).toFixed(2)}`;

            const accionesCell = row.insertCell();

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

    function actualizarTotalGeneral(total) {
        if (totalGeneralElement) {
            totalGeneralElement.textContent = `Total General: S/. ${parseFloat(total).toFixed(2)}`;
        }
    }

    // --- Manejo de Formularios ---

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
                cargarBalances();
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
        
        // Asumiendo que `balance.anio` ahora viene de la BD
        if (mesNumerico && balance.anio) {
            document.getElementById('edit_mes').value = `${balance.anio}-${mesNumerico}`;
        } else {
            // Si falta el mes o el año, inicializa con una cadena vacía o un valor por defecto
            document.getElementById('edit_mes').value = ''; 
            console.warn("Mes o año no disponibles para pre-llenar el input de edición.", balance);
        }
        
        document.getElementById('edit_monto').value = parseFloat(balance.monto).toFixed(2);
        modalEditarBalance.show();
    }

    // AÑADIDO: Manejo del formulario de editar balance
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
                cargarBalances(); // Recargar la tabla para ver los cambios
            } else {
                showToast('error', data.error || '❌ Error al actualizar el balance.');
            }
        } catch (error) {
            console.error('Error al actualizar balance:', error);
            showToast('error', '❌ Error de conexión al actualizar el balance.');
        }
    });

    // --- Resto del código (sin cambios importantes para la actualización) ---

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
                cargarBalances();
            } else {
                showToast('error', data.error || '❌ Error al eliminar el balance.');
            }
        } catch (error) {
            console.error('Error al eliminar balance:', error);
            showToast('error', '❌ Hubo un problema de conexión al intentar eliminar el balance.');
        }
    });

    btnBuscar.addEventListener('click', () => {
        const filtros = {
            nombre: buscarNombreInput.value.trim(),
            mes: buscarMesSelect.value,
            anio: buscarAnioInput.value
        };
        cargarBalances(filtros);
    });

    buscarMesSelect.addEventListener('change', () => cargarBalances({
        nombre: buscarNombreInput.value.trim(),
        mes: buscarMesSelect.value,
        anio: buscarAnioInput.value
    }));
    buscarAnioInput.addEventListener('change', () => cargarBalances({
        nombre: buscarNombreInput.value.trim(),
        mes: buscarMesSelect.value,
        anio: buscarAnioInput.value
    }));


    cargarBalances();

    const sidebarToggle = document.body.querySelector('#sidebarToggle');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', event => {
            event.preventDefault();
            document.body.classList.toggle('sb-sidenav-toggled');
        });
    }

    const btnImprimir = document.getElementById('btnImprimir');
    const btnExportarPDF = document.getElementById('btnExportarPDF');

    if (btnImprimir) {
        btnImprimir.addEventListener('click', () => {
            // Se quitó el showToast que decía "Función de Imprimir aún no implementada."
            window.print(); // Esta es la función clave para imprimir
            console.log("Imprimiendo balance...");
        });
    }

    if (btnExportarPDF) {
        btnExportarPDF.addEventListener('click', () => {
            showToast('info', 'Función de Exportar PDF aún no implementada.');
            console.log("Exportar balance a PDF...");
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

        doc.text("Reporte de Balance de Empresa", 14, 20);

        const head = [['Nombre / Descripción', 'Tipo de Balance', 'Mes', 'Monto']];

        const body = [];
        const rows = tablaBalance.querySelectorAll('tr');
        rows.forEach(row => {
            if (row.cells.length > 1) { // Asegúrate de que no estás seleccionando la fila de "No hay balances"
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