// gestionDominical.js

// Espera a que el DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', () => {
    const API_BASE_URL = 'http://localhost/cusquena/backend/api/controllers/vista_gestion_dominical/';

    // Elementos de la tabla
    const tablaDominical = document.getElementById('tablaDominical');
    // 'tablaPagosHistorial' se obtiene dentro de cargarPagosDominical ya que está en el modal

    // Elementos de formularios y modales
    const formAgregar = document.getElementById('formAgregarDominical');
    const formEditar = document.getElementById('formEditarDominical');
    const formAgregarPago = document.getElementById('formAgregarPago');
    
    // Referencias para el modal de edición de pago
    const modalEditarPago = new bootstrap.Modal(document.getElementById('modalEditarPago'));
    const formEditarPago = document.getElementById('formEditarPago');

    const modalAgregar = new bootstrap.Modal(document.getElementById('modalAgregarDominical'));
    const modalEditar = new bootstrap.Modal(document.getElementById('modalEditarDominical'));
    const modalEliminarConfirmacion = new bootstrap.Modal(document.getElementById('modalEliminarDominicalConfirmacion')); 
    const modalVerPagos = new bootstrap.Modal(document.getElementById('modalVerPagos'));

    // NUEVAS referencias para el modal de eliminación de PAGOS
    const modalEliminarPagoConfirmacion = new bootstrap.Modal(document.getElementById('modalEliminarPagoConfirmacion'));
    const btnConfirmarEliminarPago = document.getElementById('btnConfirmarEliminarPago'); 

    const btnConfirmarEliminarDominical = document.getElementById('btnConfirmarEliminarDominical');

    const buscarDominicalInput = document.getElementById('buscarDominical');
    const semanaInicioFiltroInput = document.getElementById('semanaInicioFiltro');
    const semanaFinFiltroInput = document.getElementById('semanaFinFiltro');
    const btnBuscar = document.getElementById('btnBuscar');

    // Elementos para los Toasts
    const toastSuccess = new bootstrap.Toast(document.getElementById('toastSuccess'));
    const toastError = new bootstrap.Toast(document.getElementById('toastError'));
    const toastSuccessBody = document.getElementById('toastSuccessBody');
    const toastErrorBody = document.getElementById('toastErrorBody');

    let dominicalIdAEliminar = null; // Para guardar el ID del dominical a eliminar (principal)

    // --- Funciones de Utilidad ---

    /**
     * Muestra un mensaje Toast de éxito o error.
     * @param {string} type 
     * @param {string} message 
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
     * @param {HTMLFormElement} form - El formulario a resetear.
     */
    function resetForm(form) {
        form.reset();
        form.querySelectorAll('select').forEach(select => {
            if (select.options.length > 0) {
                select.selectedIndex = 0; 
            }
        });
    }

    // --- Carga y Renderizado de la Tabla Principal (Dominicales) ---

    async function cargarDominicales(filtros = {}) {
        try {
            const params = new URLSearchParams(filtros);
            const response = await fetch(`${API_BASE_URL}listar.php?${params.toString()}`);
            const data = await response.json();
            
            renderizarTabla(data.dominicales || []);
        } catch (error) {
            console.error('Error al cargar datos dominicales:', error);
            showToast('error', '❌ Error al cargar los dominicales. Intenta de nuevo.');
            renderizarTabla([]);
        }
    }

    function renderizarTabla(lista) {
        tablaDominical.innerHTML = ''; 

        if (!lista || lista.length === 0) {
            tablaDominical.innerHTML = '<tr><td colspan="9" class="text-center">No hay datos registrados.</td></tr>';
            return;
        }

        lista.forEach(d => {
            const row = tablaDominical.insertRow();
            row.insertCell().textContent = d.nombre;
            row.insertCell().textContent = d.apellidos;
            row.insertCell().textContent = d.fecha_domingo;
            row.insertCell().textContent = d.semana_inicio;
            row.insertCell().textContent = d.semana_fin;
            row.insertCell().textContent = `S/. ${parseFloat(d.monto_dominical).toFixed(2)}`;
            row.insertCell().textContent = d.estado;
            row.insertCell().textContent = `S/. ${parseFloat(d.diferencia).toFixed(2)}`;

            const accionesCell = row.insertCell();

            // Botón Editar Dominical
            const btnEditar = document.createElement('button');
            btnEditar.className = 'btn btn-warning btn-sm me-1';
            btnEditar.innerHTML = '<i class="fas fa-edit"></i>';
            btnEditar.title = 'Editar';
            btnEditar.addEventListener('click', () => llenarModalEditar(d));
            accionesCell.appendChild(btnEditar);

            // Botón Eliminar Dominical (principal)
            const btnEliminar = document.createElement('button');
            btnEliminar.className = 'btn btn-danger btn-sm me-1';
            btnEliminar.innerHTML = '<i class="fas fa-trash-alt"></i>';
            btnEliminar.title = 'Eliminar';
            btnEliminar.addEventListener('click', () => {
                dominicalIdAEliminar = d.id;
                document.getElementById('dominicalIdParaConfirmarEliminar').value = d.id; 
                modalEliminarConfirmacion.show(); 
            });
            accionesCell.appendChild(btnEliminar);

            // Botón Ver Pagos del Dominical
            const btnVerPagos = document.createElement('button');
            btnVerPagos.className = 'btn btn-info btn-sm';
            btnVerPagos.innerHTML = '<i class="fas fa-eye"></i>';
            btnVerPagos.title = 'Ver Pagos';
            btnVerPagos.addEventListener('click', () => {
                dominicalIdAEliminar = d.id; 
                document.getElementById('nombreDominical').textContent = `${d.nombre} ${d.apellidos}`;
                document.getElementById('pagoDominicalId').value = d.id;
                cargarPagosDominical(d.id); 
                modalVerPagos.show();
            });
            accionesCell.appendChild(btnVerPagos);
        });
    }

    // Manejo del formulario de agregar dominical
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
                modalAgregar.hide();
                resetForm(formAgregar);
                showToast('success', '✅ Dominical registrado exitosamente.');
                cargarDominicales();
            } else {
                showToast('error', data.error || '❌ Error al registrar el dominical.');
            }
        } catch (error) {
            console.error('Error al registrar:', error);
            showToast('error', '❌ Error de conexión al registrar el dominical.');
        }
    });

    // Función para llenar el modal de edición de Dominical
    function llenarModalEditar(d) {
        document.getElementById('editDominicalId').value = d.id;
        document.getElementById('edit_nombre').value = d.nombre;
        document.getElementById('edit_apellidos').value = d.apellidos;
        document.getElementById('edit_fechaDomingo').value = d.fecha_domingo;
        document.getElementById('edit_semanaInicio').value = d.semana_inicio;
        document.getElementById('edit_semanaFin').value = d.semana_fin;
        document.getElementById('edit_montoDominical').value = parseFloat(d.monto_dominical).toFixed(2);
        document.getElementById('edit_estado').value = d.estado;
        document.getElementById('edit_diferencia').value = parseFloat(d.diferencia).toFixed(2);
        modalEditar.show();
    }

    // Manejo del formulario de editar Dominical
    formEditar.addEventListener('submit', async e => {
        e.preventDefault();
        const formData = new FormData(formEditar);
        const datos = Object.fromEntries(formData.entries());
        datos.id = document.getElementById('editDominicalId').value; 

        try {
            const response = await fetch(`${API_BASE_URL}actualizar.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(datos)
            });
            const data = await response.json();

            if (data.success) {
                modalEditar.hide();
                showToast('success', '✅ Dominical actualizado exitosamente.');
                cargarDominicales();
            } else {
                showToast('error', data.error || '❌ Error al actualizar el dominical.');
            }
        } catch (error) {
            console.error('Error al actualizar:', error);
            showToast('error', '❌ Error de conexión al actualizar el dominical.');
        }
    });

    // Evento para el botón de Confirmar Eliminación del Dominical principal (en su propio modal)
    btnConfirmarEliminarDominical.addEventListener('click', async () => {
        const idToDelete = document.getElementById('dominicalIdParaConfirmarEliminar').value; 
        if (!idToDelete) return;

        try {
            const response = await fetch(`${API_BASE_URL}eliminar.php`, { 
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: idToDelete })
            });

            const data = await response.json();
            modalEliminarConfirmacion.hide(); 

            if (data.success) {
                showToast('success', '✅ Dominical eliminado exitosamente!');
                dominicalIdAEliminar = null;
                cargarDominicales();
            } else {
                showToast('error', data.error || '❌ Error al eliminar el dominical.');
            }
        } catch (error) {
            console.error('Error al eliminar dominical:', error);
            showToast('error', '❌ Hubo un problema de conexión al intentar eliminar el dominical.');
        }
    });

    // --- Funciones de Gestión de Pagos Individuales ---

    // Función para cargar los pagos de un dominical específico en el modal
    async function cargarPagosDominical(dominicalId) {
        const tablaPagosHistorial = document.getElementById('tablaPagosHistorial');
        tablaPagosHistorial.innerHTML = ''; 

        try {
            const response = await fetch(`${API_BASE_URL}listar_pagos.php?dominical_id=${dominicalId}`);
            const data = await response.json();

            if (data.success && data.pagos.length > 0) {
                data.pagos.forEach(pago => {
                    const row = tablaPagosHistorial.insertRow();
                    row.insertCell().textContent = pago.fecha_pago;
                    row.insertCell().textContent = `S/. ${parseFloat(pago.monto_pagado).toFixed(2)}`;

                    const accionesCell = row.insertCell();

                    // Botón Editar Pago
                    const btnEditarPago = document.createElement('button');
                    btnEditarPago.className = 'btn btn-warning btn-sm me-1'; 
                    btnEditarPago.innerHTML = '<i class="fas fa-edit"></i>';
                    btnEditarPago.title = 'Editar Pago';
                    // Al hacer clic, llena el modal de edición de pago con los datos actuales
                    btnEditarPago.addEventListener('click', () => llenarModalEditarPago(pago, dominicalId)); 
                    accionesCell.appendChild(btnEditarPago);

                    // Botón Eliminar Pago (dentro del historial de pagos)
                    const btnEliminarPago = document.createElement('button');
                    btnEliminarPago.className = 'btn btn-danger btn-sm';
                    btnEliminarPago.innerHTML = '<i class="fas fa-trash-alt"></i>';
                    btnEliminarPago.title = 'Eliminar Pago';
                    btnEliminarPago.addEventListener('click', () => {
                        // Pasamos los IDs al MODAL ESPECÍFICO DE ELIMINAR PAGO
                        document.getElementById('pagoIdParaEliminarConfirmacion').value = pago.id;
                        document.getElementById('dominicalIdParaPagoEliminarConfirmacion').value = dominicalId;
                        modalEliminarPagoConfirmacion.show(); 
                    });
                    accionesCell.appendChild(btnEliminarPago);
                });
            } else {
                tablaPagosHistorial.innerHTML = '<tr><td colspan="3" class="text-center">No hay pagos registrados para este dominical.</td></tr>';
            }
        } catch (error) {
            console.error('Error al cargar pagos del dominical:', error);
            showToast('error', '❌ Error al cargar el historial de pagos.');
        }
    }

    // Función para llenar el modal de edición de Pago Individual
    
        function llenarModalEditarPago(pago, dominicalId) {
           
            modalVerPagos.hide(); 

            document.getElementById('editPagoId').value = pago.id;
            document.getElementById('editPagoDominicalId').value = dominicalId; 
            document.getElementById('editFechaPago').value = pago.fecha_pago;
            document.getElementById('editMontoPago').value = parseFloat(pago.monto_pagado).toFixed(2);

            modalEditarPago.show(); 
        }

    // Manejo del formulario para agregar un nuevo pago
    formAgregarPago.addEventListener('submit', async e => {
        e.preventDefault();
        const dominicalId = document.getElementById('pagoDominicalId').value;
        const fechaPago = document.getElementById('fechaPago').value;
        const montoPago = document.getElementById('montoPago').value;

        try {
            const response = await fetch(`${API_BASE_URL}registrar_pago.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ dominical_id: dominicalId, fecha_pago: fechaPago, monto_pagado: montoPago })
            });
            const data = await response.json();

            if (data.success) {
                resetForm(formAgregarPago);
                showToast('success', '✅ Pago registrado exitosamente.');
                cargarPagosDominical(dominicalId); 
                cargarDominicales(); 
            } else {
                showToast('error', data.error || '❌ Error al registrar el pago.');
            }
        } catch (error) {
            console.error('Error al registrar pago:', error);
            showToast('error', '❌ Error de conexión al registrar el pago.');
        }
    });

            // Manejo del formulario de editar Pago Individual
        formEditarPago.addEventListener('submit', async e => {
            e.preventDefault();
            const formData = new FormData(formEditarPago);
            const datos = Object.fromEntries(formData.entries());

            try {
                const response = await fetch(`${API_BASE_URL}actualizar_pago.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(datos)
                });
                const data = await response.json();

                if (data.success) {
                    modalEditarPago.hide();
                    showToast('success', '✅ Pago actualizado exitosamente.');
                    cargarPagosDominical(datos.dominical_id); 
                    cargarDominicales(); 

                  
                    modalVerPagos.show(); 

                } else {
                    showToast('error', data.error || '❌ Error al actualizar el pago.');
                }
            } catch (error) {
                console.error('Error al actualizar pago:', error);
                showToast('error', '❌ Error de conexión al actualizar el pago.');
            }
        });

        modalEditarPago._element.addEventListener('hidden.bs.modal', () => {
   
         modalVerPagos.show();
        });
            
  
    // Se ha movido la lógica de confirmación al event listener de btnConfirmarEliminarPago
    async function eliminarPago(pagoId, dominicalId) {
        try {
            const response = await fetch(`${API_BASE_URL}eliminar_pago.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: pagoId })
            });
            const data = await response.json();

            

            if (data.success) {
                showToast('success', '✅ Pago eliminado exitosamente.');
                cargarPagosDominical(dominicalId); 
                cargarDominicales(); 
            } else {
                showToast('error', data.error || '❌ Error al eliminar el pago.');
            }
        } catch (error) {
            console.error('Error al eliminar pago:', error);
            showToast('error', '❌ Error de conexión al eliminar el pago.');
        }
    }

    // NUEVO Evento para el botón de Confirmar Eliminación del PAGO (en el modal de pago)
    btnConfirmarEliminarPago.addEventListener('click', async () => {
        const pagoIdToDelete = document.getElementById('pagoIdParaEliminarConfirmacion').value;
        const dominicalIdAssociated = document.getElementById('dominicalIdParaPagoEliminarConfirmacion').value;

        if (!pagoIdToDelete || !dominicalIdAssociated) {
            showToast('error', '❌ No se encontró el ID del pago o dominical asociado para eliminar.');
            modalEliminarPagoConfirmacion.hide();
            return;
        }

     
        await eliminarPago(pagoIdToDelete, dominicalIdAssociated);
        modalEliminarPagoConfirmacion.hide(); 
    });


    // --- Manejo de Filtros ---
    btnBuscar.addEventListener('click', () => {
        const filtros = {
            nombre: buscarDominicalInput.value.trim(),
            semana_inicio: semanaInicioFiltroInput.value,
            semana_fin: semanaFinFiltroInput.value
        };
        cargarDominicales(filtros);
    });

    // Carga inicial de datos
    cargarDominicales();

    // Inicializar el sidebar toggle (Bootstrap SB Admin template related)
    if (document.body.classList.contains('sb-nav-fixed')) {
        const sidebarToggle = document.body.querySelector('#sidebarToggle');
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', event => {
                event.preventDefault();
                document.body.classList.toggle('sb-sidenav-toggled');
            });
        }
    }
});