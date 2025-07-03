document.addEventListener('DOMContentLoaded', () => {
    const API_BASE_URL = 'http://localhost/cusquena/backend/api/controllers/vista_gestion_coordinadores/';

    const tablaCoordinadores = document.getElementById('tablaCoordinadores');
    const formAgregar = document.getElementById('formAgregarCoordinador');
    const formEditar = document.getElementById('formEditarCoordinador');
    const modalAgregar = new bootstrap.Modal(document.getElementById('modalAgregarCoordinador'));
    const modalEditar = new bootstrap.Modal(document.getElementById('modalEditarCoordinador'));

    // Si tienes modalEliminar en tu HTML, descomenta esto:
    // const modalEliminar = new bootstrap.Modal(document.getElementById('modalEliminarConfirmacion'));
    // const btnEliminar = document.getElementById('btnConfirmarEliminar');

    let idAEliminar = null;

    async function cargarCoordinadores() {
        try {
            const response = await fetch(`${API_BASE_URL}listar.php`);
            const data = await response.json();
            renderizarTabla(data.coordinadores);
        } catch (error) {
            console.error('Error al cargar coordinadores:', error);
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
            btnEditar.onclick = () => llenarModalEditar(c);
            acciones.appendChild(btnEditar);

            // Solo si tienes modalEliminar implementado en tu HTML
            /*
            const btnEliminar = document.createElement('button');
            btnEliminar.className = 'btn btn-danger btn-sm';
            btnEliminar.textContent = 'Eliminar';
            btnEliminar.onclick = () => {
                idAEliminar = c.id;
                modalEliminar.show();
            };
            acciones.appendChild(btnEliminar);
            */
        });
    }

  formAgregar.addEventListener('submit', async e => {
    e.preventDefault();
    const formData = new FormData(formAgregar);
    const datos = Object.fromEntries(formData.entries());

    console.log('Datos enviados:', datos); // ✅ Aquí está bien

    try {
        const response = await fetch(`${API_BASE_URL}registrar.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(datos)
        });

        const data = await response.json();
        if (data.success) {
            modalAgregar.hide();
            formAgregar.reset();
            cargarCoordinadores();
        } else {
            alert(data.error || 'Error al registrar.');
        }
    } catch (error) {
        console.error('Error al registrar:', error);
    }
});

    function llenarModalEditar(c) {
        document.getElementById('editCoordinadorId').value = c.id;
        document.getElementById('edit_nombre').value = c.nombre;
        document.getElementById('edit_apellidos').value = c.apellidos;
        document.getElementById('edit_paradero').value = c.paradero;
        document.getElementById('edit_montoDiario').value = parseFloat(c.monto_diario).toFixed(2);
        document.getElementById('edit_fecha').value = c.fecha;
        document.getElementById('edit_estado').value = c.estado;
        document.getElementById('edit_contacto').value = c.contacto || '';

        modalEditar.show();
    }

    formEditar.addEventListener('submit', async e => {
        e.preventDefault();
        const formData = new FormData(formEditar);
        const datos = Object.fromEntries(formData.entries());
        datos.id = document.getElementById('editCoordinadorId').value;

        try {
            const response = await fetch(`${API_BASE_URL}actualizar.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(datos)
            });

            const data = await response.json();
            if (data.success) {
                modalEditar.hide();
                cargarCoordinadores();
            } else {
                alert(data.error || 'Error al actualizar.');
            }
        } catch (error) {
            console.error('Error al actualizar:', error);
        }
    });

    cargarCoordinadores();
});
