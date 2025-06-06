document.addEventListener('DOMContentLoaded', function() {
    // Variables globales
    let currentPage = 1;
    const itemsPerPage = 3;
    let allSoatsData = [];

    // Elementos del DOM
    const searchInput = document.querySelector('input[placeholder="Buscar Conductor"]');
    const searchButton = document.querySelector('a.btn-primary');
    const tableBody = document.querySelector('tbody');
    const pagination = document.querySelector('.pagination');
    const toastEditar = new bootstrap.Toast(document.getElementById('toastEditar'));
    const toastEliminar = new bootstrap.Toast(document.getElementById('toastEliminar'));

    // Función para cargar datos de SOATs
    async function loadSoats(searchTerm = '', page = 1) {
        try {
            const response = await fetch(`http://localhost/cusquena/backend/api/controllers/vistaSoat/listarSoats.php?search=${encodeURIComponent(searchTerm)}`);
            if (!response.ok) throw new Error('Error al cargar los SOATs');

            allSoatsData = await response.json();
            currentPage = page;
            renderTable();
            renderPagination();
        } catch (error) {
            console.error('Error:', error);
            alert('Error al cargar los SOATs: ' + error.message);
        }
    }

    // Buscar en tiempo real mientras el usuario escribe
    searchInput.addEventListener('input', () => {
        loadSoats(searchInput.value.trim(), 1);
    });

    // Función para renderizar la tabla
    function renderTable() {
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    const paginatedData = allSoatsData.slice(startIndex, endIndex);

    tableBody.innerHTML = '';

    if (paginatedData.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="11" class="text-center">No se encontraron SOATs registrados</td></tr>';
        renderPagination();
        return;
    }

    paginatedData.forEach(soat => {
        const row = document.createElement('tr');

        // Definir botones según el rol
        let botones = `
            <button class="btn btn-success p-1 btn-editar" data-id="${soat.idSoat}">Editar</button>
        `;

        if (ROL_USUARIO === 'Administrador') {
            botones += `
                <button class="btn btn-danger p-1 btn-eliminar" data-id="${soat.idSoat}">Eliminar</button>
            `;
        }

        row.innerHTML = `
            <td>${soat.idSoat}</td>
            <td>${soat.dni_conductor || 'N/A'}</td>
            <td>${soat.nombre_conductor || 'N/A'}</td>
            <td>${soat.apellido_conductor || 'N/A'}</td>
            <td>${soat.telefono_conductor || 'N/A'}</td>
            <td>${soat.placa_conductor || 'N/A'}</td>
            <td>${formatDate(soat.fechaMantenimiento)}</td>
            <td>${formatDate(soat.fechaProxMantenimiento)}</td>
            <td>${soat.nombre || 'N/A'}</td>
            <td><span class="badge ${soat.estado === 'activo' ? 'bg-success' : 'bg-danger'}">${soat.estado}</span></td>
            <td>${botones}</td>
        `;

        tableBody.appendChild(row);
    });

    // Event listeners para los botones que sí existen en el DOM
    document.querySelectorAll('.btn-editar').forEach(btn => {
        btn.addEventListener('click', () => openEditModal(btn.dataset.id));
    });

    if (ROL_USUARIO === 'Administrador') {
        document.querySelectorAll('.btn-eliminar').forEach(btn => {
            btn.addEventListener('click', () => confirmDelete(btn.dataset.id));
        });
    }
}

    // Función para renderizar paginación
    function renderPagination() {
        const totalPages = Math.ceil(allSoatsData.length / itemsPerPage);

        if (totalPages <= 1) {
            pagination.style.display = 'none';
            return;
        }

        pagination.style.display = 'flex';
        pagination.innerHTML = '';

        // Botón Anterior
        const prevLi = document.createElement('li');
        prevLi.className = 'page-item' + (currentPage === 1 ? ' disabled' : '');
        prevLi.innerHTML = `
            <a class="page-link" href="#" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
            </a>
        `;
        prevLi.addEventListener('click', (e) => {
            e.preventDefault();
            if (currentPage > 1) loadSoats(searchInput.value, currentPage - 1);
        });
        pagination.appendChild(prevLi);

        // Números de página
        for (let i = 1; i <= totalPages; i++) {
            const pageLi = document.createElement('li');
            pageLi.className = 'page-item' + (i === currentPage ? ' active' : '');
            pageLi.innerHTML = `<a class="page-link" href="#">${i}</a>`;
            pageLi.addEventListener('click', (e) => {
                e.preventDefault();
                loadSoats(searchInput.value, i);
            });
            pagination.appendChild(pageLi);
        }

        // Botón Siguiente
        const nextLi = document.createElement('li');
        nextLi.className = 'page-item' + (currentPage === totalPages ? ' disabled' : '');
        nextLi.innerHTML = `
            <a class="page-link" href="#" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
            </a>
        `;
        nextLi.addEventListener('click', (e) => {
            e.preventDefault();
            if (currentPage < totalPages) loadSoats(searchInput.value, currentPage + 1);
        });
        pagination.appendChild(nextLi);
    }

    // Función para formatear fecha
    function formatDate(dateString) {
        if (!dateString) return 'N/A';
        const date = new Date(dateString);
        return date.toLocaleDateString('es-PE');
    }

    // Función para abrir modal de edición
    async function openEditModal(idSoat) {
        try {
            const response = await fetch(`http://localhost/cusquena/backend/api/controllers/vistaSoat/obtenerSoatCompleto.php?idSoat=${idSoat}`);
            if (!response.ok) throw new Error('Error al cargar datos del SOAT');

            const soatData = await response.json();

            // Llenar el formulario del modal
            document.getElementById('dni').value = soatData.dni_conductor || '';
            document.getElementById('nombre').value = soatData.nombre_conductor || '';
            document.getElementById('apellido').value = soatData.apellido_conductor || '';
            document.getElementById('telefono').value = soatData.telefono_conductor || '';
            document.getElementById('placa').value = soatData.placa_conductor || '';
            document.getElementById('emisionsoat').value = soatData.fechaMantenimiento || '';
            document.getElementById('vencimientosoat').value = soatData.fechaProxMantenimiento || '';
            document.getElementById('numsoat').value = soatData.nombre || '';
            document.getElementById('estado').value = soatData.estado || '';

            // Mostrar el modal
            const modal = new bootstrap.Modal(document.getElementById('modalEditar'));
            modal.show();

            // Configurar el envío del formulario
            const form = document.querySelector('#modalEditar form');
            form.onsubmit = async (e) => {
                e.preventDefault();
                await updateSoat(idSoat);
                modal.hide();
            };
        } catch (error) {
            console.error('Error:', error);
            alert('Error al cargar datos del SOAT: ' + error.message);
        }
    }

    // Función para actualizar SOAT
    async function updateSoat(idSoat) {
        try {
            const formData = {
                idSoat: idSoat,
                nombre: document.getElementById('numsoat').value,
                fechaMantenimiento: document.getElementById('emisionsoat').value,
                fechaProxMantenimiento: document.getElementById('vencimientosoat').value,
                estado: document.getElementById('estado').value,
                dni: document.getElementById('dni').value,
                nombre_conductor: document.getElementById('nombre').value,
                apellido: document.getElementById('apellido').value,
                telefono: document.getElementById('telefono').value,
                placa: document.getElementById('placa').value
            };

            const response = await fetch('http://localhost/cusquena/backend/api/controllers/vistaSoat/actualizarSoat.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            });

            const data = await response.json();

            if (!data.exito) {
                throw new Error(data.error || 'Error al actualizar el SOAT');
            }

            // Mostrar toast de edición exitosa
            document.getElementById('toastTitleEditar').textContent = 'Edición exitosa';
            document.getElementById('toastMessageEditar').textContent = 'El SOAT fue actualizado correctamente.';
            toastEditar.show();

            loadSoats(searchInput.value, currentPage);
        } catch (error) {
            console.error('Error:', error);
            alert('Error al actualizar SOAT: ' + error.message);
        }
    }

    // Función para confirmar eliminación
    function confirmDelete(idSoat) {
        const modal = new bootstrap.Modal(document.getElementById('modalEliminarConfirmacion'));
        modal.show();
        
        document.getElementById('btnConfirmarEliminar').onclick = () => deleteSoat(idSoat);
    }

    // Función para eliminar SOAT
    async function deleteSoat(idSoat) {
        try {
            const response = await fetch(`http://localhost/cusquena/backend/api/controllers/vistaSoat/eliminarSoat.php?idSoat=${idSoat}`, {
                method: 'DELETE'
            });

            const data = await response.json();

            if (!data.exito) {
                throw new Error(data.error || 'Error al eliminar el SOAT');
            }

            // Mostrar toast de eliminación exitosa
            mostrarToastEliminar('El SOAT fue eliminado correctamente.');
            loadSoats(searchInput.value, currentPage);
        } catch (error) {
            console.error('Error:', error);
            alert('Error al eliminar el SOAT: ' + error.message);
        }
    }

    // Inicializar carga de SOATs
    loadSoats();
});
