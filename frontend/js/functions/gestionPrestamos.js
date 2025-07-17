document.addEventListener('DOMContentLoaded', () => {
    const API_BASE_URL = 'http://localhost/cusquena/backend/api/controllers/vista_gestion_prestamos/';

    const tablaDeudas = document.getElementById('tablaDeudas');
    const formAgregar = document.getElementById('formAgregar');
    const formEditar = document.getElementById('formEditarDeuda');
    const modalAgregar = new bootstrap.Modal(document.getElementById('modalAgregar'));
    const modalEditar = new bootstrap.Modal(document.getElementById('modalEditarDeuda'));
    const modalEliminar = new bootstrap.Modal(document.getElementById('eliminarModal'));

    // Elementos para los Toasts
    const toastSuccess = new bootstrap.Toast(document.getElementById('toastSuccess'));
    const toastError = new bootstrap.Toast(document.getElementById('toastError'));
    const toastSuccessBody = document.getElementById('toastSuccessBody');
    const toastErrorBody = document.getElementById('toastErrorBody');

    let idAEliminar = null;
    let idAEditar = null;

    // --- Funciones de Utilidad ---

    function showToast(type, message) {
        if (type === 'success') {
            toastSuccessBody.textContent = message;
            toastSuccess.show();
        } else {
            toastErrorBody.textContent = message;
            toastError.show();
        }
    }

    function resetForm(form) {
        form.reset();
        form.querySelectorAll('select').forEach(select => {
            if (select.options.length > 0) {
                select.selectedIndex = 0; 
            }
        });
    }

    // --- Carga y Renderizado de la Tabla ---

    async function cargarPrestamos() {
        try {
            const response = await fetch(`${API_BASE_URL}listar.php`);
            const data = await response.json();
            renderizarTabla(data.prestamos || []);
        } catch (error) {
            console.error('Error al cargar préstamos:', error);
            showToast('error', '❌ Error al cargar los préstamos. Intenta de nuevo.');
            renderizarTabla([]);
        }
    }

    function renderizarTabla(prestamos) {
        tablaDeudas.innerHTML = '';

        if (prestamos.length === 0) {
            tablaDeudas.innerHTML = '<tr><td colspan="7" class="text-center">No hay préstamos registrados.</td></tr>';
            return;
        }

        prestamos.forEach(p => {
            const row = tablaDeudas.insertRow();
            row.insertCell().textContent = p.nombre;
            row.insertCell().textContent = p.tipo_persona;
            row.insertCell().textContent = `S/. ${parseFloat(p.monto_deuda).toFixed(2)}`;
            row.insertCell().textContent = `S/. ${parseFloat(p.saldo_pendiente).toFixed(2)}`;
            row.insertCell().textContent = p.estado;
            row.insertCell().textContent = p.fecha_inicio_deuda;

            const acciones = row.insertCell();

            const btnEditar = document.createElement('button');
            btnEditar.className = 'btn btn-sm btn-warning me-1';
            btnEditar.textContent = 'Editar';
            btnEditar.onclick = () => llenarModalEditar(p);
            acciones.appendChild(btnEditar);

            const btnEliminar = document.createElement('button');
            btnEliminar.className = 'btn btn-sm btn-danger';
            btnEliminar.textContent = 'Eliminar';
            btnEliminar.onclick = () => {
                idAEliminar = p.id;
                modalEliminar.show();
            };
            acciones.appendChild(btnEliminar);
        });
    }

    // --- Llenar Modales ---

    function llenarModalEditar(prestamo) {
        idAEditar = prestamo.id;
        document.getElementById('editNombre').value = prestamo.nombre;
        document.getElementById('editTipoPersona').value = prestamo.tipo_persona;
        document.getElementById('editMontoDeuda').value = parseFloat(prestamo.monto_deuda).toFixed(2);
        document.getElementById('editSaldoPendiente').value = parseFloat(prestamo.saldo_pendiente).toFixed(2);
        document.getElementById('editEstado').value = prestamo.estado;
        document.getElementById('editFechaInicioDeuda').value = prestamo.fecha_inicio_deuda;

        modalEditar.show();
    }


    // Evento para el formulario de Agregar Préstamo
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
                showToast('success', '✅ Préstamo registrado exitosamente!');
                resetForm(formAgregar); 
                modalAgregar.hide();
                cargarPrestamos();
            } else {
                showToast('error', `❌ Error al registrar: ${data.error || 'Mensaje de error desconocido.'}`);
            }
        } catch (error) {
            console.error('Error al registrar préstamo:', error);
            showToast('error', '❌ Hubo un problema de conexión al intentar registrar el préstamo.');
        }
    });

    // Evento para el formulario de Editar Préstamo
    formEditar.addEventListener('submit', async e => {
        e.preventDefault();
        const formData = new FormData(formEditar);
        const datos = Object.fromEntries(formData.entries());
        datos.id = idAEditar; 

        try {
            const response = await fetch(`${API_BASE_URL}actualizar.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(datos)
            });

            const data = await response.json();

            if (data.success) {
                showToast('success', '✅ Préstamo actualizado exitosamente!');
                modalEditar.hide();
                cargarPrestamos();
            } else {
                showToast('error', `❌ Error al actualizar: ${data.error || 'Mensaje de error desconocido.'}`);
            }
        } catch (error) {
            console.error('Error al actualizar préstamo:', error);
            showToast('error', '❌ Hubo un problema de conexión al intentar actualizar el préstamo.');
        }
    });

    // Evento para el botón de Confirmar Eliminación
    document.getElementById('confirmarEliminar').addEventListener('click', async () => {
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
                showToast('success', '✅ Préstamo eliminado exitosamente!');
                idAEliminar = null;
                cargarPrestamos();
            } else {
                showToast('error', `❌ Error al eliminar: ${data.error || 'Mensaje de error desconocido.'}`);
            }
        } catch (error) {
            console.error('Error al eliminar préstamo:', error);
            showToast('error', '❌ Hubo un problema de conexión al intentar eliminar el préstamo.');
        }
    });

    // Event listener para el botón de búsqueda
    document.getElementById('btnBuscar').addEventListener('click', async () => {
        const searchTerm = document.getElementById('buscarServicio').value;
        try {
            const response = await fetch(`${API_BASE_URL}listar.php?nombre=${encodeURIComponent(searchTerm)}`);
            const data = await response.json();
            renderizarTabla(data.prestamos || []);
        } catch (error) {
            console.error('Error al buscar préstamos:', error);
            showToast('error', '❌ Error al realizar la búsqueda de préstamos.');
            renderizarTabla([]);
        }
    });

    // --- Inicialización ---
    cargarPrestamos();
});