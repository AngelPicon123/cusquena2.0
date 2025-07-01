document.addEventListener('DOMContentLoaded', () => {
    // Definimos la URL base de tu API para facilitar la gestión de rutas
    const API_BASE_URL = 'http://localhost/cusquena/backend/api/controllers/vista_gestion_alquileres/';

    // Referencias a elementos del DOM
    const tablaAlquileres = document.getElementById('tablaAlquileres');
    const formAgregarAlquiler = document.getElementById('formAgregarAlquiler');
    const formEditarAlquiler = document.getElementById('formEditarAlquiler');
    const modalAgregar = new bootstrap.Modal(document.getElementById('modalAgregar'));
    const modalEditar = new bootstrap.Modal(document.getElementById('modalEditar'));
    const modalEliminarConfirmacion = new bootstrap.Modal(document.getElementById('modalEliminarConfirmacion'));
    const btnConfirmarEliminar = document.getElementById('btnConfirmarEliminar');

    const filterNombre = document.getElementById('filterNombre');
    const btnBuscarAlquileres = document.getElementById('btnBuscarAlquileres');
    const paginationContainer = document.getElementById('pagination');

    // Toasts de Bootstrap para mensajes al usuario
    const toastSuccess = new bootstrap.Toast(document.getElementById('toastSuccess'));
    const toastError = new bootstrap.Toast(document.getElementById('toastError'));
    const toastSuccessBody = document.getElementById('toastSuccessBody');
    const toastErrorBody = document.getElementById('toastErrorBody');

    let alquilerIdToDelete = null; // Variable para almacenar el ID del alquiler a eliminar

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
        // Desmarcar radios si es necesario, o establecer un valor por defecto
        const radioButtons = form.querySelectorAll('input[type="radio"]');
        radioButtons.forEach(radio => radio.checked = false);
    }

    // --- Funciones CRUD (Interfaz con el Backend) ---

    /**
     * Fetches alquileres data from the backend.
     * @param {object} filters - Object containing filter parameters (nombre, page, limit).
     * @returns {Promise<Array>} - A promise that resolves to an array of alquiler objects.
     */
    async function fetchAlquileres(filters = {}) {
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
            console.error('Error al cargar alquileres:', error);
            showToast('error', `Error al cargar alquileres: ${error.message}`);
            return { alquileres: [], total: 0 };
        }
    }

    /**
     * Adds a new alquiler to the backend.
     * @param {object} alquilerData - Data of the alquiler to add.
     * @returns {Promise<object>} - A promise that resolves to the new alquiler object or an error.
     */
    async function addAlquiler(alquilerData) {
        try {
            const response = await fetch(`${API_BASE_URL}registrar.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(alquilerData)
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
            console.error('Error al agregar alquiler:', error);
            showToast('error', `Error al agregar alquiler: ${error.message}`);
            return { success: false, message: error.message };
        }
    }

    /**
     * Updates an existing alquiler in the backend.
     * @param {object} alquilerData - Data of the alquiler to update (must include ID).
     * @returns {Promise<object>} - A promise that resolves to a success message or an error.
     */
    async function updateAlquiler(alquilerData) {
        try {
            const response = await fetch(`${API_BASE_URL}actualizar.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(alquilerData)
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
            console.error('Error al actualizar alquiler:', error);
            showToast('error', `Error al actualizar alquiler: ${error.message}`);
            return { success: false, message: error.message };
        }
    }

    /**
     * Deletes an alquiler from the backend.
     * @param {number} id - ID of the alquiler to delete.
     * @returns {Promise<object>} - A promise that resolves to a success message or an error.
     */
    async function deleteAlquiler(id) {
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
            console.error('Error al eliminar alquiler:', error);
            showToast('error', `Error al eliminar alquiler: ${error.message}`);
            return { success: false, message: error.message };
        }
    }

    // --- Renderizado de la Tabla ---

    /**
     * Renders the alquileres data into the table.
     * @param {Array<object>} alquileres - Array of alquiler objects to display.
     */
    function renderAlquileres(alquileres) {
        tablaAlquileres.innerHTML = ''; // Limpiar tabla antes de renderizar
        if (alquileres.length === 0) {
            tablaAlquileres.innerHTML = '<tr><td colspan="8" class="text-center">No hay alquileres para mostrar.</td></tr>';
            return;
        }

        alquileres.forEach(alquiler => {
            const row = tablaAlquileres.insertRow();
            row.insertCell().textContent = alquiler.id;
            row.insertCell().textContent = alquiler.nombre;
            row.insertCell().textContent = alquiler.tipo;
            row.insertCell().textContent = alquiler.fecha_inicio;
            row.insertCell().textContent = alquiler.periodicidad;
            row.insertCell().textContent = `S/. ${parseFloat(alquiler.pago).toFixed(2)}`;
            row.insertCell().textContent = alquiler.estado;

            const actionsCell = row.insertCell();
            const editButton = document.createElement('button');
            editButton.className = 'btn btn-sm btn-warning me-2';
            editButton.textContent = 'Editar';
            editButton.dataset.id = alquiler.id;
            editButton.addEventListener('click', () => populateEditModal(alquiler));
            actionsCell.appendChild(editButton);

            const deleteButton = document.createElement('button');
            deleteButton.className = 'btn btn-sm btn-danger';
            deleteButton.textContent = 'Eliminar';
            deleteButton.dataset.id = alquiler.id;
            deleteButton.addEventListener('click', () => {
                alquilerIdToDelete = alquiler.id;
                modalEliminarConfirmacion.show();
            });
            actionsCell.appendChild(deleteButton);
        });
    }

    /**
     * Fills the edit modal with the selected alquiler's data.
     * @param {object} alquiler - The alquiler object to edit.
     */
    function populateEditModal(alquiler) {
        document.getElementById('editAlquilerId').value = alquiler.id;
        document.getElementById('editNombre').value = alquiler.nombre;
        document.getElementById('editTipo').value = alquiler.tipo;
        document.getElementById('editFechaInicio').value = alquiler.fecha_inicio;
        document.getElementById('editPeriodicidad').value = alquiler.periodicidad;
        document.getElementById('editPago').value = parseFloat(alquiler.pago).toFixed(2);

        // Seleccionar el radio button de estado correcto
        if (alquiler.estado === 'Activo') {
            document.getElementById('editEstadoActivo').checked = true;
        } else {
            document.getElementById('editEstadoInactivo').checked = true;
        }
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
                loadAlquileres();
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
                loadAlquileres();
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
                loadAlquileres();
            }
        });
        paginationContainer.appendChild(nextLi);
    }

    /**
     * Carga los alquileres aplicando filtros y paginación.
     */
    async function loadAlquileres() {
        const filters = {
            nombre: filterNombre.value,
            page: currentPage,
            limit: recordsPerPage
        };

        const result = await fetchAlquileres(filters);
        if (result && result.alquileres) {
            renderAlquileres(result.alquileres);
            setupPagination(result.total, currentPage, recordsPerPage);
        }
    }

    // --- Event Listeners CRUD ---

    formAgregarAlquiler.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(formAgregarAlquiler);
        const alquilerData = Object.fromEntries(formData.entries());

        // Capturar el valor del radio button de estado
        alquilerData.estado = document.querySelector('input[name="estado"]:checked').value;

        const result = await addAlquiler(alquilerData);
        if (result && result.success) {
            showToast('success', 'Alquiler agregado exitosamente!');
            resetForm(formAgregarAlquiler);
            modalAgregar.hide();
            loadAlquileres();
        }
    });

    formEditarAlquiler.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(formEditarAlquiler);
        const alquilerData = Object.fromEntries(formData.entries());
        alquilerData.id = document.getElementById('editAlquilerId').value;

        // Capturar el valor del radio button de estado en el modal de edición
        alquilerData.estado = document.querySelector('input[name="editEstado"]:checked').value;

        const result = await updateAlquiler(alquilerData);
        if (result && result.success) {
            showToast('success', 'Alquiler actualizado exitosamente!');
            modalEditar.hide();
            loadAlquileres();
        }
    });

    btnConfirmarEliminar.addEventListener('click', async () => {
        if (alquilerIdToDelete) {
            const result = await deleteAlquiler(alquilerIdToDelete);
            if (result && result.success) {
                showToast('success', 'Alquiler eliminado exitosamente!');
                modalEliminarConfirmacion.hide();
                alquilerIdToDelete = null;
                loadAlquileres();
            }
        }
    });

    btnBuscarAlquileres.addEventListener('click', () => {
        currentPage = 1;
        loadAlquileres();
    });

    // Carga inicial de alquileres al cargar la página
    loadAlquileres();
});
