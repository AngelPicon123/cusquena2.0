document.addEventListener('DOMContentLoaded', () => {
    const API_BASE_URL = 'http://localhost/cusquena/backend/api/controllers/vista_gestion_coordinadores/';

    const tablaCoordinadores = document.getElementById('tablaCoordinadores');
    const formAgregar = document.getElementById('formAgregarCoordinador');
    const formEditar = document.getElementById('formEditarCoordinador');
    const modalAgregar = new bootstrap.Modal(document.getElementById('modalAgregarCoordinador'));
    const modalEditar = new bootstrap.Modal(document.getElementById('modalEditarCoordinador'));
    const modalEliminar = new bootstrap.Modal(document.getElementById('modalEliminarConfirmacion')); // Tu modal de confirmación
    const btnEliminarConfirmado = document.getElementById('btnEliminarConfirmado');

    // Elementos para los Toasts
    const toastSuccess = new bootstrap.Toast(document.getElementById('toastSuccess'));
    const toastError = new bootstrap.Toast(document.getElementById('toastError'));
    const toastSuccessBody = document.getElementById('toastSuccessBody');
    const toastErrorBody = document.getElementById('toastErrorBody');

    let idAEliminar = null; // Variable para almacenar el ID del coordinador a eliminar

    // --- Funciones de Utilidad ---

    /**
     * Muestra un mensaje de notificación (toast) en la esquina inferior derecha.
     * @param {string} type - 'success' para éxito, 'error' para error.
     * @param {string} message - El mensaje a mostrar.
     */
    function showToast(type, message) {
        if (type === 'success') {
            toastSuccessBody.textContent = message;
            toastSuccess.show();
        } else {
            toastErrorBody.textContent = message;
            toastError.show();
        }
    }

    /**
     * Resetea los campos de un formulario.
     * @param {HTMLFormElement} form - El elemento del formulario a resetear.
     */
    function resetForm(form) {
        form.reset();
        form.querySelectorAll('select').forEach(select => {
            if (select.options.length > 0) {
                select.selectedIndex = 0; // Asegura que el select se restablezca a la primera opción
            }
        });
    }

    // --- Carga y Renderizado de la Tabla ---

    async function cargarCoordinadores() {
        try {
            const response = await fetch(`${API_BASE_URL}listar.php`);
            const data = await response.json();
            // Asumiendo que 'listar.php' siempre devuelve un array 'coordinadores'
            renderizarTabla(data.coordinadores || []);
        } catch (error) {
            console.error('Error al cargar coordinadores:', error);
            showToast('error', '❌ Error al cargar los coordinadores. Intenta de nuevo.');
            renderizarTabla([]);
        }
    }

    function renderizarTabla(coordinadores) {
        tablaCoordinadores.innerHTML = '';

        if (coordinadores.length === 0) {
            tablaCoordinadores.innerHTML = '<tr><td colspan="8" class="text-center">No hay coordinadores registrados.</td></tr>';
            return;
        }

        coordinadores.forEach(c => {
            const row = tablaCoordinadores.insertRow();

            row.insertCell().textContent = c.nombre;
            row.insertCell().textContent = c.apellidos;
            row.insertCell().textContent = c.paradero;
            row.insertCell().textContent = `S/. ${parseFloat(c.monto_diario).toFixed(2)}`;
            row.insertCell().textContent = c.fecha;
            row.insertCell().textContent = c.estado;
            row.insertCell().textContent = c.contacto || '';

            const acciones = row.insertCell();

            const btnEditar = document.createElement('button');
            btnEditar.className = 'btn btn-warning btn-sm me-1';
            btnEditar.textContent = 'Editar';
            btnEditar.addEventListener('click', () => llenarModalEditar(c));
            acciones.appendChild(btnEditar);

            const btnEliminar = document.createElement('button');
            btnEliminar.className = 'btn btn-danger btn-sm';
            btnEliminar.textContent = 'Eliminar';
            btnEliminar.addEventListener('click', () => {
                idAEliminar = c.id; 
                modalEliminar.show(); 
            });
            acciones.appendChild(btnEliminar);
        });
    }


    function llenarModalEditar(coordinador) {
        document.getElementById('editCoordinadorId').value = coordinador.id;
        document.getElementById('edit_nombre').value = coordinador.nombre;
        document.getElementById('edit_apellidos').value = coordinador.apellidos;
        document.getElementById('edit_paradero').value = coordinador.paradero;
        document.getElementById('edit_montoDiario').value = parseFloat(coordinador.monto_diario).toFixed(2);
        document.getElementById('edit_fecha').value = coordinador.fecha;
        document.getElementById('edit_estado').value = coordinador.estado;
        document.getElementById('edit_contacto').value = coordinador.contacto || '';

        modalEditar.show();
    }


    // Evento para el formulario de Agregar Coordinador
    formAgregar.addEventListener('submit', async e => {
        e.preventDefault();
        const formData = new FormData(formAgregar);
        const datos = Object.fromEntries(formData.entries());

        try {
            const response = await fetch(`${API_BASE_URL}registrar.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(datos)
            });

            const data = await response.json();

            if (data.success) {
                showToast('success', '✅ Coordinador registrado exitosamente!');
                resetForm(formAgregar); 
                modalAgregar.hide();
                cargarCoordinadores();
            } else {
                showToast('error', `❌ Error al registrar: ${data.error || 'Mensaje de error desconocido.'}`);
            }
        } catch (error) {
            console.error('Error al registrar coordinador:', error);
            showToast('error', '❌ Hubo un problema de conexión al intentar registrar el coordinador.');
        }
    });

    // Evento para el formulario de Editar Coordinador
    formEditar.addEventListener('submit', async e => {
        e.preventDefault();
        const formData = new FormData(formEditar);
        const datos = Object.fromEntries(formData.entries());

        try {
            const response = await fetch(`${API_BASE_URL}actualizar.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(datos)
            });

            const data = await response.json();

            if (data.success) {
                showToast('success', '✅ Coordinador actualizado exitosamente!');
                modalEditar.hide();
                cargarCoordinadores();
            } else {
                showToast('error', `❌ Error al actualizar: ${data.error || 'Mensaje de error desconocido.'}`);
            }
        } catch (error) {
            console.error('Error al actualizar coordinador:', error);
            showToast('error', '❌ Hubo un problema de conexión al intentar actualizar el coordinador.');
        }
    });

    // Evento para el botón de Confirmar Eliminación dentro del modal
    btnEliminarConfirmado.addEventListener('click', async () => {
        if (!idAEliminar) return; 

        try {
            const response = await fetch(`${API_BASE_URL}eliminar.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: idAEliminar }) 
            });

            const data = await response.json();
            modalEliminar.hide(); 

            if (data.success) {
                showToast('success', '✅ Coordinador eliminado exitosamente!');
                idAEliminar = null; 
                cargarCoordinadores();
            } else {
                showToast('error', `❌ Error al eliminar: ${data.error || 'Mensaje de error desconocido.'}`);
            }
        } catch (error) {
            console.error('Error al eliminar coordinador:', error);
            showToast('error', '❌ Hubo un problema de conexión al intentar eliminar el coordinador.');
        }
    });

    // Evento para el botón de búsqueda
    document.getElementById('btnBuscar').addEventListener('click', async () => {
        const searchTerm = document.getElementById('buscarCoordinador').value;
        const fechaFiltro = document.getElementById('fechaFiltro').value;
        const paraderoFiltro = document.getElementById('paraderoFiltro').value;


        let searchUrl = `${API_BASE_URL}listar.php?`;
        if (searchTerm) searchUrl += `nombre=${encodeURIComponent(searchTerm)}&`;
        if (fechaFiltro) searchUrl += `fecha=${encodeURIComponent(fechaFiltro)}&`;
        if (paraderoFiltro) searchUrl += `paradero=${encodeURIComponent(paraderoFiltro)}&`;

      
        if (searchUrl.endsWith('&') || searchUrl.endsWith('?')) {
            searchUrl = searchUrl.slice(0, -1);
        }

        try {
            const response = await fetch(searchUrl);
            const data = await response.json();
            if (data.success) { 
                renderizarTabla(data.coordinadores || []);
            } else {
                showToast('error', `❌ Error en la búsqueda: ${data.error || 'No se encontraron resultados.'}`);
                renderizarTabla([]);
            }
        } catch (error) {
            console.error('Error al buscar coordinadores:', error);
            showToast('error', '❌ Hubo un problema de conexión al buscar coordinadores.');
            renderizarTabla([]);
        }
    });

    // --- Inicialización ---
    cargarCoordinadores();
});