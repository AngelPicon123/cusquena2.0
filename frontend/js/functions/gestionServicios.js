document.addEventListener('DOMContentLoaded', () => {
    const tbody = document.querySelector("tbody");
    const selectVenderDescripcion = document.getElementById('venderDescripcion');
    const inputVenderPrecio = document.getElementById('venderPrecioUnitario');
    let servicioAEliminar = null; // temporal para el ID a eliminar

    function mostrarToastPersonalizado(tipo, titulo, mensaje) {
        let toastElement, toastTitle, toastMessage;

        switch (tipo) {
            case 'agregar':
                toastElement = document.getElementById('toastAgregar');
                toastTitle = document.getElementById('toastTitleAgregar');
                toastMessage = document.getElementById('toastMessageAgregar');
                break;
            case 'editar':
                toastElement = document.getElementById('toastEditar');
                toastTitle = document.getElementById('toastTitleEditar');
                toastMessage = document.getElementById('toastMessageEditar');
                break;
            case 'eliminar':
                toastElement = document.getElementById('toastEliminar');
                toastTitle = toastElement.querySelector('.me-auto');
                toastMessage = document.getElementById('toastMessageEliminar');
                break;
            default:
                return;
        }

        toastTitle.textContent = titulo;
        toastMessage.textContent = mensaje;

        const toast = new bootstrap.Toast(toastElement);
        toast.show();
    }

    function cargarServicios() {
    fetch('../../backend/api/controllers/gestionServicio.php')
        .then(res => res.json())
        .then(data => {
            tbody.innerHTML = '';
            selectVenderDescripcion.innerHTML = '<option value="">Seleccione un servicio</option>';

            data.forEach(s => {
                let botones = '';

                if (ROL_USUARIO === 'Administrador') {
                    botones += `
                        <button class="btn btn-success p-1" onclick='abrirEditar(${JSON.stringify(s)})' data-bs-toggle="modal" data-bs-target="#modalEditar">Editar</button>
                        <button class="btn btn-danger p-1" onclick="confirmarEliminacion(${s.idServicio})">Eliminar</button>`;
                } 
                tbody.innerHTML += 
                    `<tr>
                        <td>${s.idServicio}</td>
                        <td>${s.descripcion}</td>
                        <td>${s.precioUnitario}</td>
                        <td><span class="badge bg-${s.estado === 'activo' ? 'success' : 'secondary'}">${s.estado}</span></td>
                        <td>${botones}</td>
                    </tr>`;

                if (s.estado === 'activo') {
                    selectVenderDescripcion.innerHTML += `<option value="${s.idServicio}" data-precio="${s.precioUnitario}">${s.descripcion}</option>`;
                }
            });
        });
    }

    cargarServicios();

    selectVenderDescripcion.addEventListener('change', function () {
        const precio = this.options[this.selectedIndex].dataset.precio;
        inputVenderPrecio.value = precio ?? '';
        const inputTotal = document.getElementById('venderTotal');
        if (inputTotal) {
            inputTotal.value = precio ?? '';
        }
    });

    document.querySelector('#modalAgregar form').addEventListener('submit', e => {
        e.preventDefault();
        const descripcion = document.getElementById('descripcion').value;
        const precioUnitario = document.getElementById('precioUnitario').value;
        const estado = document.querySelector('input[name="estado"]:checked').value;

        fetch('../../backend/api/controllers/gestionServicio.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ descripcion, precioUnitario, estado })
        }).then(res => res.json())
          .then(() => {
              cargarServicios();
              document.getElementById('descripcion').value = '';
              document.getElementById('precioUnitario').value = '';
              document.querySelector('#modalAgregar .btn-close').click();
              mostrarToastPersonalizado('agregar', 'Agregado', 'Servicio agregado correctamente');
          });
    });

    window.abrirEditar = (s) => {
        document.getElementById('editarId').value = s.idServicio;
        document.getElementById('editarDescripcion').value = s.descripcion;
        document.getElementById('editarPrecioUnitario').value = s.precioUnitario;
        document.getElementById('editarEstado' + (s.estado === 'activo' ? 'Activo' : 'Inactivo')).checked = true;
    };

    document.querySelector('#modalEditar form').addEventListener('submit', e => {
        e.preventDefault();
        const idServicio = document.getElementById('editarId').value;
        const descripcion = document.getElementById('editarDescripcion').value;
        const precioUnitario = document.getElementById('editarPrecioUnitario').value;
        const estado = document.querySelector('input[name="editarEstado"]:checked').value;

        fetch('../../backend/api/controllers/gestionServicio.php', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ idServicio, descripcion, precioUnitario, estado })
        }).then(res => res.json())
          .then(() => {
              cargarServicios();
              document.querySelector('#modalEditar .btn-close').click();
              mostrarToastPersonalizado('editar', 'Editado', 'Servicio actualizado correctamente');
          });
    });

    window.confirmarEliminacion = (id) => {
        servicioAEliminar = id;
        const modal = new bootstrap.Modal(document.getElementById('modalEliminarConfirmacion'));
        modal.show();
    };

    document.getElementById('btnConfirmarEliminar').addEventListener('click', () => {
        if (!servicioAEliminar) return;

        fetch('../../backend/api/controllers/gestionServicio.php', {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ idServicio: servicioAEliminar })
        }).then(res => res.json())
          .then(() => {
              cargarServicios();
              mostrarToastPersonalizado('eliminar', 'Servicio eliminado', 'El servicio ha sido eliminado correctamente');
              const modal = bootstrap.Modal.getInstance(document.getElementById('modalEliminarConfirmacion'));
              modal.hide();
              servicioAEliminar = null;
          });
    });

    document.getElementById('btnRegistrarVenta').addEventListener('click', () => {
        const select = document.getElementById('venderDescripcion');
        const idServicio = select.value;
        const descripcion = select.options[select.selectedIndex].text;
        const precioUnitario = document.getElementById('venderPrecioUnitario').value;
        const fechaVenta = document.getElementById('venderfechaVenta').value;
        const total = document.getElementById('venderTotal').value;

        if (!idServicio || !fechaVenta) {
            mostrarToastPersonalizado('agregar', 'Campos incompletos', 'Complete todos los campos');
            return;
        }

        fetch('../../backend/api/controllers/ventaServicio.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ idServicio, descripcion, precioUnitario, fechaVenta, total })
        })
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                mostrarToastPersonalizado('agregar', 'Venta registrada', 'La venta fue registrada exitosamente');
                document.querySelector('#modalVender .btn-close').click();
                document.getElementById('venderDescripcion').value = '';
                document.getElementById('venderPrecioUnitario').value = '';
                document.getElementById('venderfechaVenta').value = '';
                document.getElementById('venderTotal').value = '';
            } else {
                mostrarToastPersonalizado('eliminar', 'Error', 'Error al registrar la venta');
            }
        });
    });
});
