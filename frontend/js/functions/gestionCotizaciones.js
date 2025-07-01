document.addEventListener('DOMContentLoaded', () => {
    // Definimos la URL base de tu API para facilitar la gestión de rutas
    const API_BASE_URL = 'http://localhost/cusquena/backend/api/controllers/vista_gestion_cotizaciones/';

    // Referencias a elementos del DOM
    const tablaCotizaciones = document.getElementById('tablaCotizaciones');
    const formAgregarCotizacion = document.getElementById('formAgregarCotizacion');
    const formEditarCotizacion = document.getElementById('formEditarCotizacion');
    const modalAgregar = new bootstrap.Modal(document.getElementById('modalAgregar'));
    const modalEditar = new bootstrap.Modal(document.getElementById('modalEditar'));
    const modalEliminarConfirmacion = new bootstrap.Modal(document.getElementById('modalEliminarConfirmacion'));
    const btnConfirmarEliminar = document.getElementById('btnConfirmarEliminar');

    const filterFechaInicio = document.getElementById('filterFechaInicio');
    const filterFechaFin = document.getElementById('filterFechaFin');
    const filterNombre = document.getElementById('filterNombre');
    const btnBuscarCotizaciones = document.getElementById('btnBuscarCotizaciones');

    const paginationContainer = document.getElementById('pagination');
    const totalGeneralSpan = document.getElementById('totalGeneral');
    const btnPrintTable = document.getElementById('btnPrintTable');
    const btnExportPdf = document.getElementById('btnExportPdf');

    // Toasts de Bootstrap para mensajes al usuario
    const toastSuccess = new bootstrap.Toast(document.getElementById('toastSuccess'));
    const toastError = new bootstrap.Toast(document.getElementById('toastError'));
    const toastSuccessBody = document.getElementById('toastSuccessBody');
    const toastErrorBody = document.getElementById('toastErrorBody');

    let cotizacionIdToDelete = null; // Variable para almacenar el ID de la cotización a eliminar

    // --- Funciones de Utilidad ---

    /**
     * Muestra un toast de Bootstrap.
     * @param {string} type - Tipo de toast ('success' o 'error').
     * @param {string} message - Mensaje a mostrar.
     */
    function showToast(type, message) {
        if (type === 'success') {
            toastSuccessBody.textContent = message;
            toastSuccess.show();
        } else if (type === 'error') {
            toastErrorBody.textContent = message;
            toastError.show();
        }
    }

    /**
     * Resetea un formulario.
     * @param {HTMLFormElement} form - El formulario a resetear.
     */
    function resetForm(form) {
        form.reset();
        const selects = form.querySelectorAll('select');
        selects.forEach(select => select.value = '');
    }

    // --- Funciones CRUD (Interfaz con el Backend) ---

    /**
     * Fetches cotizaciones data from the backend.
     * @param {object} filters - Object containing filter parameters (nombre, fecha_inicio, fecha_fin, page, limit).
     * @returns {Promise<Array>} - A promise that resolves to an array of cotizacion objects.
     */
    async function fetchCotizaciones(filters = {}) {
        const queryParams = new URLSearchParams(filters).toString();
        try {
            const response = await fetch(`${API_BASE_URL}listar.php?${queryParams}`);
            if (!response.ok) {
                const errorText = await response.text();
                throw new Error(`HTTP error! status: ${response.status}, message: ${errorText}`);
            }
            const data = await response.json();
            if (data.error) {
                throw new Error(data.error);
            }
            return data;
        } catch (error) {
            console.error('Error al cargar cotizaciones:', error);
            showToast('error', `Error al cargar cotizaciones: ${error.message}`);
            return { cotizaciones: [], total: 0, total_general_monto: 0 };
        }
    }

    /**
     * Adds a new cotizacion to the backend.
     * @param {object} cotizacionData - Data of the cotizacion to add.
     * @returns {Promise<object>} - A promise that resolves to the new cotizacion object or an error.
     */
    async function addCotizacion(cotizacionData) {
        try {
            const response = await fetch(`${API_BASE_URL}registrar.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(cotizacionData)
            });
            if (!response.ok) {
                const errorText = await response.text();
                throw new Error(`HTTP error! status: ${response.status}, message: ${errorText}`);
            }
            const data = await response.json();
            if (data.error) {
                throw new Error(data.error);
            }
            return data;
        } catch (error) {
            console.error('Error al agregar cotización:', error);
            showToast('error', `Error al agregar cotización: ${error.message}`);
            return { success: false, message: error.message };
        }
    }

    /**
     * Updates an existing cotizacion in the backend.
     * @param {object} cotizacionData - Data of the cotizacion to update (must include ID).
     * @returns {Promise<object>} - A promise that resolves to a success message or an error.
     */
    async function updateCotizacion(cotizacionData) {
        try {
            const response = await fetch(`${API_BASE_URL}actualizar.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(cotizacionData)
            });
            if (!response.ok) {
                const errorText = await response.text();
                throw new Error(`HTTP error! status: ${response.status}, message: ${errorText}`);
            }
            const data = await response.json();
            if (data.error) {
                throw new Error(data.error);
            }
            return data;
        } catch (error) {
            console.error('Error al actualizar cotización:', error);
            showToast('error', `Error al actualizar cotización: ${error.message}`);
            return { success: false, message: error.message };
        }
    }

    /**
     * Deletes a cotizacion from the backend.
     * @param {number} id - ID of the cotizacion to delete.
     * @returns {Promise<object>} - A promise that resolves to a success message or an error.
     */
    async function deleteCotizacion(id) {
        try {
            const response = await fetch(`${API_BASE_URL}eliminar.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id })
            });
            if (!response.ok) {
                const errorText = await response.text();
                throw new Error(`HTTP error! status: ${response.status}, message: ${errorText}`);
            }
            const data = await response.json();
            if (data.error) {
                throw new Error(data.error);
            }
            return data;
        } catch (error) {
            console.error('Error al eliminar cotización:', error);
            showToast('error', `Error al eliminar cotización: ${error.message}`);
            return { success: false, message: error.message };
        }
    }

    // --- Renderizado de la Tabla y Total General ---

    /**
     * Renders the cotizaciones data into the table and updates the total.
     * @param {Array<object>} cotizaciones - Array of cotizacion objects to display.
     * @param {number} totalGeneralMonto - The total sum of payments.
     */
    function renderCotizaciones(cotizaciones, totalGeneralMonto) {
        tablaCotizaciones.innerHTML = ''; // Limpiar tabla antes de renderizar
        if (cotizaciones.length === 0) {
            tablaCotizaciones.innerHTML = '<tr><td colspan="9" class="text-center">No hay cotizaciones para mostrar.</td></tr>';
        }

        cotizaciones.forEach(cotizacion => {
            const row = tablaCotizaciones.insertRow();
            row.insertCell().textContent = cotizacion.id;
            row.insertCell().textContent = cotizacion.nombre;
            row.insertCell().textContent = cotizacion.apellido;
            row.insertCell().textContent = cotizacion.tipo_cotizacion.charAt(0).toUpperCase() + cotizacion.tipo_cotizacion.slice(1);
            row.insertCell().textContent = `S/. ${parseFloat(cotizacion.pago).toFixed(2)}`;
            row.insertCell().textContent = cotizacion.fecha;
            row.insertCell().textContent = cotizacion.estado;
            row.insertCell().textContent = cotizacion.dia_semana;

            const actionsCell = row.insertCell();
            const editButton = document.createElement('button');
            editButton.className = 'btn btn-sm btn-warning me-2';
            editButton.textContent = 'Editar';
            editButton.dataset.id = cotizacion.id;
            editButton.addEventListener('click', () => populateEditModal(cotizacion));
            actionsCell.appendChild(editButton);

            const deleteButton = document.createElement('button');
            deleteButton.className = 'btn btn-sm btn-danger';
            deleteButton.textContent = 'Eliminar';
            deleteButton.dataset.id = cotizacion.id;
            deleteButton.addEventListener('click', () => {
                cotizacionIdToDelete = cotizacion.id;
                modalEliminarConfirmacion.show();
            });
            actionsCell.appendChild(deleteButton);
        });

        totalGeneralSpan.textContent = `Total General: S/. ${parseFloat(totalGeneralMonto).toFixed(2)}`;
    }

    /**
     * Fills the edit modal with the selected cotizacion's data.
     * @param {object} cotizacion - The cotizacion object to edit.
     */
    function populateEditModal(cotizacion) {
        document.getElementById('editCotizacionId').value = cotizacion.id;
        document.getElementById('editNombre').value = cotizacion.nombre;
        document.getElementById('editApellido').value = cotizacion.apellido;
        document.getElementById('editTipoCotizacion').value = cotizacion.tipo_cotizacion;
        document.getElementById('editPago').value = parseFloat(cotizacion.pago).toFixed(2);
        document.getElementById('editFecha').value = cotizacion.fecha;
        document.getElementById('editEstado').value = cotizacion.estado;
        modalEditar.show();
    }

    // --- Paginación ---
    let currentPage = 1;
    const recordsPerPage = 10;

    /**
     * Sets up pagination links.
     * @param {number} totalRecords - Total number of records.
     * @param {number} currentPage - Current page number.
     * @param {number} recordsPerPage - Number of records per page.
     */
    function setupPagination(totalRecords, currentPage, recordsPerPage) {
        paginationContainer.innerHTML = '';
        const totalPages = Math.ceil(totalRecords / recordsPerPage);

        if (totalPages <= 1) {
            return;
        }

        const prevLi = document.createElement('li');
        prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
        prevLi.innerHTML = `<a class="page-link" href="#" aria-label="Previous"><span aria-hidden="true">«</span></a>`;
        prevLi.addEventListener('click', (e) => {
            e.preventDefault();
            if (currentPage > 1) {
                currentPage--;
                loadCotizaciones();
            }
        });
        paginationContainer.appendChild(prevLi);

        for (let i = 1; i <= totalPages; i++) {
            const pageLi = document.createElement('li');
            pageLi.className = `page-item ${currentPage === i ? 'active' : ''}`;
            pageLi.innerHTML = `<a class="page-link" href="#">${i}</a>`;
            pageLi.addEventListener('click', (e) => {
                e.preventDefault();
                currentPage = i;
                loadCotizaciones();
            });
            paginationContainer.appendChild(pageLi);
        }

        const nextLi = document.createElement('li');
        nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
        nextLi.innerHTML = `<a class="page-link" href="#" aria-label="Next"><span aria-hidden="true">»</span></a>`;
        nextLi.addEventListener('click', (e) => {
            e.preventDefault();
            if (currentPage < totalPages) {
                currentPage++;
                loadCotizaciones();
            }
        });
        paginationContainer.appendChild(nextLi);
    }

    /**
     * Carga las cotizaciones aplicando filtros y paginación.
     */
    async function loadCotizaciones() {
        const filters = {
            nombre: filterNombre.value,
            fecha_inicio: filterFechaInicio.value,
            fecha_fin: filterFechaFin.value,
            page: currentPage,
            limit: recordsPerPage
        };

        const result = await fetchCotizaciones(filters);
        if (result && result.cotizaciones) {
            renderCotizaciones(result.cotizaciones, result.total_general_monto);
            setupPagination(result.total, currentPage, recordsPerPage);
        }
    }

    // --- Funciones de Impresión y Exportación PDF ---

    // La función window.jspdf.jsPDF viene del CDN en el HTML
    /**
     * Imprime la tabla actual en la vista.
     */
    btnPrintTable.addEventListener('click', () => {
        // Ocultar elementos que no quieres que aparezcan en la impresión
        const elementsToHide = document.querySelectorAll('.sb-topnav, #layoutSidenav_nav, .btn, .pagination, .d-flex.align-items-center label, .form-control');
        elementsToHide.forEach(el => el.classList.add('d-print-none')); // d-print-none es una clase de Bootstrap para ocultar al imprimir

        window.print();

        // Mostrar elementos de nuevo después de la impresión
        elementsToHide.forEach(el => el.classList.remove('d-print-none'));
    });

    /**
     * Exporta la tabla de cotizaciones a un PDF.
     */
    btnExportPdf.addEventListener('click', async () => {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('p', 'pt', 'letter'); // 'p' for portrait, 'pt' for points, 'letter' for paper size

        // Obtener la tabla HTML
        const table = document.getElementById('cotizacionesTable');

        // Clonar la tabla para eliminar la columna de acciones antes de la captura
        const clonedTable = table.cloneNode(true);
        const headerRow = clonedTable.querySelector('thead tr');
        const bodyRows = clonedTable.querySelectorAll('tbody tr');

        // Eliminar la columna de "Acciones" (la última columna)
        if (headerRow) {
            const lastTh = headerRow.lastElementChild;
            if (lastTh && lastTh.textContent.trim() === 'Acciones') {
                lastTh.remove();
            }
        }
        bodyRows.forEach(row => {
            const lastTd = row.lastElementChild;
            if (lastTd) {
                lastTd.remove();
            }
        });

        // Convertir la tabla HTML clonada a una imagen (o a un formato que jsPDF pueda manejar)
        // Usamos html2canvas para renderizar la tabla a un canvas, luego a imagen
        html2canvas(clonedTable, {
            scale: 2, // Aumenta la escala para mejor calidad en PDF
            logging: false, // Desactiva el log para consola
            useCORS: true // Importante si tienes imágenes de otros dominios
        }).then(canvas => {
            const imgData = canvas.toDataURL('image/png');
            const imgWidth = 550; // Ancho para la imagen en PDF
            const pageHeight = doc.internal.pageSize.height;
            const imgHeight = canvas.height * imgWidth / canvas.width;
            let heightLeft = imgHeight;
            let position = 10; // Margen superior

            doc.addImage(imgData, 'PNG', 20, position, imgWidth, imgHeight);
            heightLeft -= pageHeight;

            while (heightLeft >= 0) {
                position = heightLeft - imgHeight;
                doc.addPage();
                doc.addImage(imgData, 'PNG', 20, position, imgWidth, imgHeight);
                heightLeft -= pageHeight;
            }

            // Añadir el total general (opcional, puede que ya esté en la imagen)
            const totalText = totalGeneralSpan.textContent;
            doc.text(totalText, doc.internal.pageSize.getWidth() - doc.getTextWidth(totalText) - 20, pageHeight - 20); // Posiciona al final de la página

            doc.save('cotizaciones.pdf');
            showToast('success', 'PDF de cotizaciones exportado exitosamente!');
        }).catch(error => {
            console.error('Error al exportar PDF:', error);
            showToast('error', `Error al exportar PDF: ${error.message}`);
        });
    });


    // --- Event Listeners CRUD ---

    formAgregarCotizacion.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(formAgregarCotizacion);
        const cotizacionData = Object.fromEntries(formData.entries());

        cotizacionData.tipoCotizacion = document.getElementById('tipoCotizacion').value;
        cotizacionData.estado = document.getElementById('estado').value; // Asegurarse de capturar el estado

        const result = await addCotizacion(cotizacionData);
        if (result && result.success) {
            showToast('success', 'Cotización agregada exitosamente!');
            resetForm(formAgregarCotizacion);
            modalAgregar.hide();
            loadCotizaciones();
        }
    });

    formEditarCotizacion.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(formEditarCotizacion);
        const cotizacionData = Object.fromEntries(formData.entries());
        cotizacionData.id = document.getElementById('editCotizacionId').value;

        cotizacionData.tipoCotizacion = document.getElementById('editTipoCotizacion').value;
        cotizacionData.estado = document.getElementById('editEstado').value;

        const result = await updateCotizacion(cotizacionData);
        if (result && result.success) {
            showToast('success', 'Cotización actualizada exitosamente!');
            modalEditar.hide();
            loadCotizaciones();
        }
    });

    btnConfirmarEliminar.addEventListener('click', async () => {
        if (cotizacionIdToDelete) {
            const result = await deleteCotizacion(cotizacionIdToDelete);
            if (result && result.success) {
                showToast('success', 'Cotización eliminada exitosamente!');
                modalEliminarConfirmacion.hide();
                cotizacionIdToDelete = null;
                loadCotizaciones();
            }
        }
    });

    btnBuscarCotizaciones.addEventListener('click', () => {
        currentPage = 1;
        loadCotizaciones();
    });

    // Carga inicial de cotizaciones al cargar la página
    loadCotizaciones();
});
