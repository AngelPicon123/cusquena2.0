// Variables globales para paginación
let currentPage = 1;
const itemsPerPage = 5; // Ajusta según necesidad
let allConductoresData = [];

function aplicarPaginacion() {
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    const paginatedData = allConductoresData.slice(startIndex, endIndex);
    
    const tabla = document.querySelector("tbody");
    tabla.innerHTML = '';
    
    //paginacion
    paginatedData.forEach(c => {
        tabla.innerHTML += `
            <tr>
                <td>${c.idConductor}</td>
                <td>${c.nombre}</td>
                <td>${c.apellido}</td>
                <td>${c.telefono}</td>
                <td>${c.dni}</td>
                <td>${c.placa}</td>
                <td>
                    <span class="badge bg-success" data-bs-toggle="modal" data-bs-target="#modalVer" 
                    data-id-conductor="${c.idConductor}"
                    style="cursor: pointer;">Ver</span>
                </td>
                <td>${c.idTipoConductor}</td> 
                <td>${c.estado}</td>
                <td>${c.detalle}</td>
                <td>
                   <a href="#" 
                      class="btn btn-primary p-1 my-1 cotizacion-btn" 
                      data-bs-toggle="modal" 
                      data-bs-target="#modalCotizacion" 
                      data-id-conductor="${c.idConductor}">
                      Cotización
                    </a>
                    <a href="#" class="btn btn-success p-1 my-1" data-bs-toggle="modal" data-bs-target="#modalEditar">Editar</a>
                    <a href="#" class="btn btn-danger p-1">Eliminar</a>
                </td>
            </tr>
        `;
    });
    
    // Agregar event listeners a los botones de cotización
    document.querySelectorAll('.cotizacion-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const idConductor = this.getAttribute('data-id-conductor');
            cargarDatosConductor(idConductor);
        });
    });
    
    actualizarControlesPaginacion();
}
    
function actualizarControlesPaginacion() {
    const totalPages = Math.ceil(allConductoresData.length / itemsPerPage);
    const pagination = document.querySelector('.pagination');
    
    if (totalPages <= 1) {
        pagination.style.display = 'none';
        return;
    }
    
    pagination.style.display = 'flex';
    pagination.innerHTML = `
        <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>
    `;
    
    for (let i = 1; i <= totalPages; i++) {
        pagination.innerHTML += `
            <li class="page-item ${i === currentPage ? 'active' : ''}">
                <a class="page-link" href="#">${i}</a>
            </li>
        `;
    }
    
    pagination.innerHTML += `
        <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="#" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
            </a>
        </li>
    `;
    
    // Agregar eventos a los controles de paginación
    document.querySelectorAll('.page-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const text = this.textContent.trim();
            
            if (text === '«' && currentPage > 1) {
                currentPage--;
            } else if (text === '»' && currentPage < totalPages) {
                currentPage++;
            } else if (!isNaN(text)) {
                currentPage = parseInt(text);
            }
            
            aplicarPaginacion();
        });
    });
}

function recargarTablaConductores() {
    fetch("http://localhost/cusquena/backend/api/controllers/vistaConductor/obtenerConductores.php")
        .then(res => res.json())
        .then(data => {
            allConductoresData = data;
            aplicarPaginacion();
        })
        .catch(() => console.error("Error al cargar conductores"));
}

function buscarConductores(termino) {
    currentPage = 1; // Resetear a primera página al buscar
    
    if (!termino) {
        aplicarPaginacion();
        return;
    }
    
    termino = termino.toLowerCase();
    const resultados = allConductoresData.filter(conductor => 
        conductor.nombre.toLowerCase().includes(termino) || 
        conductor.apellido.toLowerCase().includes(termino)
    );
    
    // Mostrar resultados paginados
    const startIndex = (currentPage - 1) * itemsPerPage;
    const paginatedData = resultados.slice(startIndex, startIndex + itemsPerPage);
    
    const tabla = document.querySelector("tbody");
    tabla.innerHTML = '';
    
    if (resultados.length === 0) {
        tabla.innerHTML = '<tr><td colspan="11" class="text-center">No se encontraron resultados</td></tr>';
        document.querySelector('.pagination').style.display = 'none';
        return;
    }

    // Agregar event listeners a los botones de cotización
    document.querySelectorAll('.cotizacion-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const idConductor = this.getAttribute('data-id-conductor');
            cargarDatosConductor(idConductor);
        });
    });
    
    actualizarControlesPaginacion();
}

document.addEventListener("DOMContentLoaded", function () {
    const formEditar = document.getElementById("formEditarConductor");
    const formAgregar = document.getElementById("formAgregarConductor");
    const buscadorInput = document.getElementById('buscadorConductor');
    const btnBuscar = document.querySelector('button.btn-primary');

    // Eventos del buscador
    buscadorInput.addEventListener('input', () => {
        buscarConductores(buscadorInput.value.trim());
    });
    
    btnBuscar.addEventListener('click', function(e) {
        e.preventDefault();
        buscarConductores(buscadorInput.value.trim());
    });

    // Delegación de eventos para Editar y Eliminar
    document.addEventListener("click", function (e) {
        const target = e.target;

        // ELIMINAR
        if (target.classList.contains("btn-danger")) {
            const idConductor = target.closest("tr").querySelector("td").innerText;

            if (confirm("¿Estás seguro de que quieres eliminar este conductor?")) {
                const formData = new FormData();
                formData.append("accion", "eliminar");
                formData.append("id_conductor", idConductor);

                fetch("http://localhost/cusquena/backend/api/controllers/vistaConductor/registrar_conductor.php", {
                    method: "POST",
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.exito) {
                        mostrarToast("✅ Conductor eliminado correctamente");
                        recargarTablaConductores();
                    } else {
                        mostrarToast("❌ Error al eliminar conductor", true);
                    }
                })
                .catch(() => mostrarToast("❌ Error de red", true));
            }
        }

        // EDITAR
        if (target.classList.contains("btn-success") && target.dataset.bsTarget === "#modalEditar") {
            const fila = target.closest("tr");
            const celdas = fila.querySelectorAll("td");

            // Datos básicos
            document.getElementById("id_conductor").value = celdas[0].innerText;
            document.getElementById("nombreEditar").value = celdas[1].innerText;
            document.getElementById("apellidoEditar").value = celdas[2].innerText;
            document.getElementById("telefonoEditar").value = celdas[3].innerText;
            document.getElementById("dniEditar").value = celdas[4].innerText;
            document.getElementById("placaEditar").value = celdas[5].innerText;
            
            // Estado
            document.getElementById("estadoEditar").value = celdas[8].innerText.trim().toLowerCase();
            
            // Detalle
            document.getElementById("detalleEditar").value = celdas[9].innerText;
            
            // TIPO DE CONDUCTOR
            const tipoConductorId = celdas[7].innerText.trim();
            console.log("ID Tipo Conductor a cargar:", tipoConductorId);

            // Cargar y seleccionar el tipo
            cargarTiposConductor('idTipoConductorEditar').then(() => {
                const select = document.getElementById("idTipoConductorEditar");
                select.value = tipoConductorId;
                
                console.log("Valor seleccionado:", select.value);
            });
        }
    });

    // ENVÍO FORMULARIO DE EDICIÓN
    formEditar.addEventListener("submit", function (e) {
        e.preventDefault();
        const formData = new FormData(formEditar);

        fetch("http://localhost/cusquena/backend/api/controllers/vistaConductor/registrar_conductor.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.exito) {
                bootstrap.Modal.getInstance(document.getElementById("modalEditar")).hide();
                mostrarToast("✅ Conductor actualizado correctamente");
                recargarTablaConductores();
            } else {
                mostrarToast("❌ Error al actualizar conductor", true);
            }
        })
        .catch(() => mostrarToast("❌ Error de red", true));
    });

    // Función para cargar tipos de conductor
    function cargarTiposConductor(selectId) {
        return new Promise((resolve, reject) => {
            fetch("../../backend/api/controllers/vistaConductor/obtener_tipos_conductor.php")
                .then(response => response.json())
                .then(data => {
                    const select = document.getElementById(selectId);
                    if (!select) {
                        reject("Elemento select no encontrado");
                        return;
                    }
                    
                    select.innerHTML = '<option value="" disabled selected>Seleccionar tipo...</option>';
                    data.forEach(tipo => {
                        const option = document.createElement("option");
                        option.value = tipo.id;
                        option.textContent = tipo.nombre;
                        select.appendChild(option);
                    });
                    resolve();
                })
                .catch(error => {
                    console.error("Error al cargar tipos de conductor:", error);
                    mostrarToast("❌ Error al cargar tipos de conductor", true);
                    reject(error);
                });
        });
    }

    // cargar tipo de conductor para el modal registro
    document.getElementById("miModal").addEventListener("show.bs.modal", function() {
        cargarTiposConductor('idTipoConductor');
    });

    // Para el modal de edición
    document.getElementById("modalEditar").addEventListener("show.bs.modal", function() {
        cargarTiposConductor('idTipoConductorEditar');
    });

    // REGISTRAR CONDUCTOR
    formAgregar.addEventListener("submit", function (e) {
        e.preventDefault();
        const formData = new FormData(formAgregar);

        fetch("../../backend/api/controllers/vistaConductor/registrar_conductor.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.exito) {
                mostrarToast("✅ Conductor registrado correctamente");
                formAgregar.reset();
                bootstrap.Modal.getInstance(document.getElementById("miModal")).hide();
                recargarTablaConductores();
            } else {
                mostrarToast("❌ Error al registrar: " + (data.error || "Error desconocido"), true);
            }
        })
        .catch(() => mostrarToast("❌ Error de conexión", true));
    });

    // CARGAR TABLA INICIAL
    recargarTablaConductores();
  
    // FUNCIÓN MOSTRAR TOAST 
    function mostrarToast(mensaje, esError = false) {
        const toast = document.createElement("div");
        toast.className = `toast align-items-center text-white ${esError ? 'bg-danger' : 'bg-success'} border-0 position-fixed bottom-0 end-0 m-4`;
        toast.style.zIndex = "9999";
        toast.innerHTML = `
          <div class="d-flex">
            <div class="toast-body">${mensaje}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
          </div>`;
        document.body.appendChild(toast);
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
        setTimeout(() => toast.remove(), 4000);
    }
});

// VER conductor para AGREGAR/EDITAR SOAT 
modalVer.addEventListener('show.bs.modal', async function(event) {
    const button = event.relatedTarget;
    const idConductor = button.getAttribute('data-id-conductor');
    
    console.log("ID Conductor capturado:", idConductor);
    
    if (!idConductor) {
        console.error("Botón clickeado:", button);
        alert("Error: No se encontró el ID del conductor en el botón");
        return;
    }
    
    // Guarda el ID en el campo oculto
    document.getElementById('idConductorHidden').value = idConductor;
    
    try {
        // 1. Primero verificar si tiene SOAT registrado
        const soatResponse = await fetch(`http://localhost/cusquena/backend/api/controllers/vistaConductor/verificarSoat.php?idConductor=${idConductor}`);
        if (!soatResponse.ok) throw new Error("Error al verificar SOAT");
        const soatData = await soatResponse.json();
        
        // 2. Mostrar confirmación SI existe SOAT
        if (soatData.existe) {
            const confirmarEdicion = confirm("Este conductor ya tiene un SOAT registrado. ¿Desea editarlo?");
            if (!confirmarEdicion) {
                bootstrap.Modal.getInstance(document.getElementById('modalVer')).hide();
                return;
            }
        }
        
        // 3. Cargar datos del conductor
        const conductorResponse = await fetch(`http://localhost/cusquena/backend/api/controllers/vistaConductor/obtenerSoat.php?idConductor=${idConductor}`);
        if (!conductorResponse.ok) throw new Error(`HTTP error! status: ${conductorResponse.status}`);
        const conductorData = await conductorResponse.json();
        
        console.log("Datos del conductor recibidos:", conductorData);
        
        // Llenar campos del conductor
        document.getElementById('placaVer').value = conductorData.placa || 'N/A';
        document.getElementById('nombreVer').value = conductorData.nombre || 'N/A';
        document.getElementById('apellidoVer').value = conductorData.apellido || 'N/A';
        document.getElementById('estadoVer').value = conductorData.estado || 'N/A';
        document.getElementById('dniVer').value = conductorData.dni || 'N/A';
        document.getElementById('telefonoVer').value = conductorData.telefono || 'N/A';
        
        // 4. Manejar campos del SOAT según corresponda
        if (soatData.existe) {
            // Prellenar campos del SOAT si existe
            document.getElementById('emisionVer').value = soatData.fechaMantenimiento || '';
            document.getElementById('vencimientoVer').value = soatData.fechaProxMantenimiento || '';
            document.getElementById('numsoatVer').value = soatData.nombre || '';
            
            // Cambiar texto del botón
            const btnGuardar = document.getElementById('btnGuardarSoat');
            const tituloModal = document.getElementById('tituloModalSoat');
            
            if (btnGuardar) btnGuardar.textContent = 'Actualizar SOAT';
            if (tituloModal) tituloModal.textContent = 'Editar SOAT Existente';
        } else {
            // Limpiar campos si no existe SOAT
            document.getElementById('emisionVer').value = '';
            document.getElementById('vencimientoVer').value = '';
            document.getElementById('numsoatVer').value = '';
            
            // Cambiar texto del botón
            const btnGuardar = document.getElementById('btnGuardarSoat');
            const tituloModal = document.getElementById('tituloModalSoat');
            
            if (btnGuardar) btnGuardar.textContent = 'Registrar SOAT';
            if (tituloModal) tituloModal.textContent = 'Registrar Nuevo SOAT';
        }
    } catch (error) {
        console.error("Error completo:", error);
        alert("Error al cargar datos: " + error.message);
    }
});

document.getElementById('formVerSoat').addEventListener('submit', async function(e) {
    e.preventDefault();

    try {
        const formData = {
            idConductor: document.getElementById('idConductorHidden').value,
            fechaMantenimiento: document.getElementById('emisionVer').value,
            fechaProxMantenimiento: document.getElementById('vencimientoVer').value,
            nombre: document.getElementById('numsoatVer').value,
            apellido: document.getElementById('apellidoVer').value,
            estado: 'activo'
        };

        // Validación básica
        if (!formData.fechaMantenimiento || !formData.fechaProxMantenimiento || !formData.nombre) {
            throw new Error("Todos los campos del SOAT son requeridos");
        }

        const response = await fetch('http://localhost/cusquena/backend/api/controllers/vistaConductor/registrarSoat.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData)
        });

        const data = await response.json();

        if (!data.exito) {
            throw new Error(data.error || "Error al procesar el SOAT");
        }

        alert(data.mensaje);
        bootstrap.Modal.getInstance(document.getElementById('modalVer')).hide();
        
    } catch (error) {
        console.error('Error:', error);
        alert('Error: ' + error.message);
    }
});

//cotizacion
// Función para cargar datos del conductor en el modal
async function cargarDatosConductor(idConductor) {
    try {
        // 1. Obtener datos del conductor
        const conductorResponse = await fetch(`http://localhost/cusquena/backend/api/controllers/vistaConductor/obtenerConductor_cotizacion.php?idConductor=${idConductor}`);
        const conductorData = await conductorResponse.json();
        
        // 2. Obtener tipos de conductor
        const tiposResponse = await fetch('http://localhost/cusquena/backend/api/controllers/vistaConductor/obtener_tipos_conductor.php');
        const tiposData = await tiposResponse.json();
        
        // 3. Buscar el tipo específico
        const tipoConductor = tiposData.find(tipo => 
            tipo.id === conductorData.idTipoConductor
        );
        
        // 4. Llenar el modal
        document.getElementById('nombreCotizacion').value = conductorData.nombre || '';
        document.getElementById('apellidoCotizacion').value = conductorData.apellido || '';
        document.getElementById('placaCotizacion').value = conductorData.placa || '';
        document.getElementById('fechaCotizacion').value = new Date().toISOString().split('T')[0];
        document.getElementById('id_conductor_cotizacion').value = idConductor;
        
        if (tipoConductor) {
            document.getElementById('tipoCotizacion').value = tipoConductor.nombre;
            document.getElementById('idTipoConductorHidden').value = tipoConductor.id;
            document.getElementById('Cotizacion').value = `S/. ${tipoConductor.monto_paga} (${tipoConductor.tipo_paga})`;
            document.getElementById('descripcion').value = tipoConductor.descripcion;
        }

    } catch (error) {
        console.error('Error:', error);
        mostrarToast('Error al cargar datos del conductor', true);
    }
}

//realizar el insert cotizacion
document.getElementById('formCotizacion').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    // Obtener datos del formulario
    const formData = {
        idConductor: document.getElementById('id_conductor_cotizacion').value,
        nombre: document.getElementById('nombreCotizacion').value,
        apellido: document.getElementById('apellidoCotizacion').value,
        cotizacion: document.getElementById('Cotizacion').value,
        placa: document.getElementById('placaCotizacion').value,
        fecha: document.getElementById('fechaCotizacion').value,
        idTipoConductor: document.getElementById('idTipoConductorHidden').value,
        descripcion: document.getElementById('descripcion').value
    };

    // Validación básica
    if (!formData.idConductor || !formData.nombre || !formData.cotizacion) {
        alert('Por favor complete todos los campos requeridos');
        return;
    }

    try {
        console.log("Enviando datos:", formData);
        
        const response = await fetch('http://localhost/cusquena/backend/api/controllers/vistaConductor/registrar_cotizacion.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData)
        });
        
        const responseText = await response.text();
        console.log("Respuesta del servidor:", responseText);
        
        let data;
        try {
            data = JSON.parse(responseText);
        } catch (e) {
            throw new Error(`Respuesta no válida del servidor: ${responseText}`);
        }
        
        if (!response.ok || !data.exito) {
            const errorMsg = data.debug || data.error || "Error en el servidor";
            throw new Error(errorMsg);
        }
        
        alert(data.mensaje || '✅ Cotización registrada correctamente');
        const modalElement = document.getElementById('modalCotizacion');
        const modalInstance = bootstrap.Modal.getInstance(modalElement);
        modalInstance.hide();
        
    } catch (error) {
        console.error('Error completo:', error);
        alert(`❌ Error: ${error.message}`);
    }
});