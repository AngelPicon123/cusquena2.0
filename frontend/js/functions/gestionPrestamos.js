document.addEventListener('DOMContentLoaded', () => {
    // La URL base ahora maneja tanto préstamos como sus pagos
    const API_BASE_URL = 'http://localhost/cusquena/backend/api/controllers/vista_gestion_prestamos/';

    const tablaDeudas = document.getElementById('tablaDeudas');
    const formAgregar = document.getElementById('formAgregar');
    const formEditar = document.getElementById('formEditarDeuda');
    const formAgregarPagoPrestamo = document.getElementById('formAgregarPagoPrestamo');
    const tablaPagosHistorialPrestamo = document.getElementById('tablaPagosHistorialPrestamo');

    const modalAgregar = new bootstrap.Modal(document.getElementById('modalAgregar'));
    const modalEditar = new bootstrap.Modal(document.getElementById('modalEditarDeuda'));
    const modalEliminar = new bootstrap.Modal(document.getElementById('eliminarModal'));
    const modalVerPagosPrestamo = new bootstrap.Modal(document.getElementById('modalVerPagosPrestamo'));
    const modalEliminarPagoPrestamoConfirmacion = new bootstrap.Modal(document.getElementById('modalEliminarPagoPrestamoConfirmacion'));

    // Elementos para los Toasts
    const toastSuccess = new bootstrap.Toast(document.getElementById('toastSuccess'));
    const toastError = new bootstrap.Toast(document.getElementById('toastError'));
    const toastSuccessBody = document.getElementById('toastSuccessBody');
    const toastErrorBody = document.getElementById('toastErrorBody');

    let idAEliminar = null; // ID del préstamo a eliminar
    let idAEditar = null; // ID del préstamo a editar
    let idPrestamoParaPago = null; // ID del préstamo para el cual se gestionan los pagos
    let idPagoParaEliminar = null; // ID de un pago específico a eliminar

    // --- Funciones de Utilidad ---

    /**
     * Muestra un toast de éxito o error.
     * @param {string} type - 'success' o 'error'.
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

    // --- Carga y Renderizado de la Tabla Principal de Préstamos ---

    /**
     * Carga todos los préstamos desde la API y los renderiza en la tabla.
     */
    async function cargarPrestamos() {
        try {
            const response = await fetch(`${API_BASE_URL}listar.php`);
            const data = await response.json();
            renderizarTabla(data.prestamos || []);
        } catch (error) {
            console.error('Error al cargar préstamos:', error);
            showToast('error', '❌ Error al cargar los préstamos. Intenta de nuevo.');
            renderizarTabla([]); // Renderiza una tabla vacía en caso de error
        }
    }

    /**
     * Renderiza los préstamos en la tabla principal.
     * @param {Array<Object>} prestamos - Array de objetos de préstamos.
     */
  function renderizarTabla(prestamos) {
        tablaDeudas.innerHTML = ''; // Limpia la tabla antes de renderizar

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

            // Botón Editar con icono (AHORA VA PRIMERO)
            const btnEditar = document.createElement('button');
            btnEditar.className = 'btn btn-warning btn-sm me-1';
            btnEditar.innerHTML = '<i class="fas fa-edit"></i>';
            btnEditar.title = 'Editar Préstamo';
            btnEditar.onclick = () => llenarModalEditar(p);
            acciones.appendChild(btnEditar); // Añadimos primero el de editar

            // Botón Eliminar con icono (AHORA VA SEGUNDO)
            const btnEliminar = document.createElement('button');
            btnEliminar.className = 'btn btn-danger btn-sm me-1'; // Agregamos me-1 para un pequeño margen
            btnEliminar.innerHTML = '<i class="fas fa-trash-alt"></i>';
            btnEliminar.title = 'Eliminar Préstamo';
            btnEliminar.onclick = () => {
                idAEliminar = p.id;
                modalEliminar.show();
            };
            acciones.appendChild(btnEliminar); // Añadimos el de eliminar

            // Botón Vista Pago Préstamo con icono (AHORA VA TERCERO, AL FINAL)
            const btnVistaPago = document.createElement('button');
            btnVistaPago.className = 'btn btn-info btn-sm'; // Quitamos me-1 ya que es el último
            btnVistaPago.innerHTML = '<i class="fas fa-eye"></i>';
            btnVistaPago.title = 'Ver/Registrar Pago';
            btnVistaPago.onclick = () => {
                idPrestamoParaPago = p.id;
                document.getElementById('nombrePrestamo').textContent = p.nombre;
                document.getElementById('pagoPrestamoId').value = p.id;
                cargarHistorialPagosPrestamo(p.id);
                modalVerPagosPrestamo.show();
            };
            acciones.appendChild(btnVistaPago); // Añadimos al final el de ver pagos
        });
    }

    /**
     * Carga el historial de pagos para un préstamo específico desde la API.
     * @param {number} prestamoId - El ID del préstamo.
     */
    async function cargarHistorialPagosPrestamo(prestamoId) {
        try {
            const response = await fetch(`${API_BASE_URL}listar_pagos.php?id_prestamo=${prestamoId}`);
            const data = await response.json();
            renderizarHistorialPagos(data.pagos || []);
        } catch (error) {
            console.error('Error al cargar historial de pagos:', error);
            showToast('error', '❌ Error al cargar el historial de pagos. Intenta de nuevo.');
            renderizarHistorialPagos([]);
        }
    }

    /**
     * Renderiza el historial de pagos en la tabla dentro del modal.
     * @param {Array<Object>} pagos 
     */
    function renderizarHistorialPagos(pagos) {
        tablaPagosHistorialPrestamo.innerHTML = ''; 

        if (pagos.length === 0) {
            tablaPagosHistorialPrestamo.innerHTML = '<tr><td colspan="3" class="text-center">No hay pagos registrados para este préstamo.</td></tr>';
            return;
        }

        pagos.forEach(pago => {
            const row = tablaPagosHistorialPrestamo.insertRow();
            row.insertCell().textContent = pago.fecha_pago;
            row.insertCell().textContent = `S/. ${parseFloat(pago.monto_pagado).toFixed(2)}`;

            const acciones = row.insertCell();

            const btnEliminarPago = document.createElement('button');
            btnEliminarPago.className = 'btn btn-danger btn-sm';
            btnEliminarPago.innerHTML = '<i class="fas fa-trash-alt"></i>';
            btnEliminarPago.title = 'Eliminar Pago';
            btnEliminarPago.onclick = () => {
                // Almacena el ID del pago y el ID del préstamo para la confirmación de eliminación
                document.getElementById('pagoIdParaEliminarPrestamoConfirmacion').value = pago.id;
                document.getElementById('prestamoIdParaPagoEliminarConfirmacion').value = pago.prestamo_id;

              
                console.log("DEBUG JS (renderizarHistorialPagos): ID de pago al hacer clic:", pago.id);
                console.log("DEBUG JS (renderizarHistorialPagos): ID de préstamo asociado:", pago.id_prestamo);
                

                modalEliminarPagoPrestamoConfirmacion.show();
            };
            acciones.appendChild(btnEliminarPago);
        });
    }

    // --- Llenar Modales ---

    /**
     * 
     * @param {Object} prestamo 
     */
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

    // --- Event Listeners de Formularios y Botones ---

    
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

    // Evento para el formulario de Registro de Pago de Préstamo
    formAgregarPagoPrestamo.addEventListener('submit', async e => {
        e.preventDefault();
        const fechaNuevoPago = document.getElementById('fechaNuevoPago').value;
        const montoNuevoPago = document.getElementById('montoNuevoPago').value;

        if (!idPrestamoParaPago) {
            showToast('error', '❌ No se ha seleccionado un préstamo para registrar el pago.');
            return;
        }
        if (!fechaNuevoPago || !montoNuevoPago) {
            showToast('error', '❌ Por favor, completa la fecha y el monto del pago.');
            return;
        }

        const datosPago = {
            id_prestamo: idPrestamoParaPago,
            fecha_pago: fechaNuevoPago,
            monto_pagado: montoNuevoPago
        };

        try {
            const response = await fetch(`${API_BASE_URL}registrar_pago.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(datosPago)
            });

            const data = await response.json();

            if (data.success) {
                showToast('success', '✅ Pago de préstamo registrado exitosamente!');
                resetForm(formAgregarPagoPrestamo); // Resetea el formulario de nuevo pago
                cargarHistorialPagosPrestamo(idPrestamoParaPago); // Recarga el historial de pagos dentro del modal
                cargarPrestamos(); // Recarga la tabla principal para reflejar los cambios en el saldo
            } else {
                showToast('error', `❌ Error al registrar pago: ${data.error || 'Mensaje de error desconocido.'}`);
            }
        } catch (error) {
            console.error('Error al registrar pago de préstamo:', error);
            showToast('error', '❌ Hubo un problema de conexión al intentar registrar el pago.');
        }
    });

    // Evento para el botón de Confirmar Eliminación de Préstamo (completo)
    document.getElementById('confirmarEliminarPrestamo').addEventListener('click', async () => {
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


    // Evento para el botón de Confirmar Eliminación de un Pago específico de Préstamo
    document.getElementById('btnConfirmarEliminarPagoPrestamo').addEventListener('click', async () => {
        const pagoId = document.getElementById('pagoIdParaEliminarPrestamoConfirmacion').value;
        const prestamoId = document.getElementById('prestamoIdParaPagoEliminarConfirmacion').value;

        
        console.log("DEBUG JS (btnConfirmarEliminarPagoPrestamo): ID de pago a enviar:", pagoId);
        console.log("DEBUG JS (btnConfirmarEliminarPagoPrestamo): ID de préstamo asociado a enviar:", prestamoId);
        

        if (!pagoId || !prestamoId) {
            showToast('error', '❌ No se pudo determinar el pago o préstamo a eliminar.');
            return;
        }

        try {
            const response = await fetch(`${API_BASE_URL}eliminar_pago.php`, {
                method: 'POST', // Aunque PHP maneja DELETE, POST es más universal para formularios
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: pagoId }) 
            });

            const data = await response.json();
            modalEliminarPagoPrestamoConfirmacion.hide(); 

            if (data.success) {
                showToast('success', '✅ Pago eliminado exitosamente! Saldo actualizado.');
                // Recargar el historial de pagos y la tabla principal para reflejar los cambios
                cargarHistorialPagosPrestamo(prestamoId); // Usas prestamoId aquí para recargar, lo cual es correcto
                cargarPrestamos();
            } else {
                showToast('error', `❌ Error al eliminar pago: ${data.error || 'Mensaje de error desconocido.'}`);
            }
        } catch (error) {
            console.error('Error al eliminar pago de préstamo:', error);
            showToast('error', '❌ Hubo un problema de conexión al intentar eliminar el pago.');
        }
    });

    // Event listener para el botón de búsqueda de préstamos
    document.getElementById('btnBuscar').addEventListener('click', async () => {
        const searchTerm = document.getElementById('buscarPrestamo').value;
        try {
            // Envía el término de búsqueda como parámetro en la URL
            const response = await fetch(`${API_BASE_URL}listar.php?nombre=${encodeURIComponent(searchTerm)}`);
            const data = await response.json();
            renderizarTabla(data.prestamos || []); // Renderiza los resultados de la búsqueda
        } catch (error) {
            console.error('Error al buscar préstamos:', error);
            showToast('error', '❌ Error al realizar la búsqueda de préstamos.');
            renderizarTabla([]);
        }
    });


    cargarPrestamos();
});