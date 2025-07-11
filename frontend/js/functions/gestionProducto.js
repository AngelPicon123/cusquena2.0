document.addEventListener('DOMContentLoaded', function () {
    const tablaProductos = document.getElementById('tablaProductos');
    const formAgregarProducto = document.getElementById('formAgregarProducto');
    const formEditarProducto = document.getElementById('formEditarProducto');
    const formVenderProducto = document.getElementById('formVenderProducto');
    const buscarProducto = document.getElementById('buscarProducto');
    const btnBuscar = document.getElementById('btnBuscar');

    let productos = [];
    let categorias = [];

    // Función para cargar las categorías en los selects
    async function cargarCategorias() {
        try {
            const res = await fetch('../../backend/api/controllers/gestionCategoria.php?accion=listar'); // Asume un endpoint para listar categorías
            const data = await res.json();
            if (data.success) {
                categorias = data.data; // Asume que el JSON de categorías tiene una propiedad 'data'
                const selectAgregar = document.getElementById('agregar_categoria');
                const selectEditar = document.getElementById('editar_categoria');
                
                // Limpiar opciones existentes
                selectAgregar.innerHTML = '<option value="">Seleccione</option>';
                selectEditar.innerHTML = '<option value="">Seleccione</option>';

                categorias.forEach(categoria => {
                    const optionAgregar = document.createElement('option');
                    optionAgregar.value = categoria.id; // Suponiendo que el ID de la categoría se llama 'id'
                    optionAgregar.textContent = categoria.nombre; // Suponiendo que el nombre de la categoría se llama 'nombre'
                    selectAgregar.appendChild(optionAgregar);

                    const optionEditar = document.createElement('option');
                    optionEditar.value = categoria.id;
                    optionEditar.textContent = categoria.nombre;
                    selectEditar.appendChild(optionEditar);
                });
            } else {
                console.error('Error al cargar categorías:', data.message);
                alert('Error al cargar categorías.');
            }
        } catch (err) {
            console.error('Error en la solicitud de categorías:', err);
            alert('Error de conexión al cargar categorías.');
        }
    }

    // Función para cargar productos
    async function cargarProductos(filtro = '') {
        try {
            const res = await fetch(`../../backend/api/controllers/gestionProducto.php?accion=listar&buscar=${encodeURIComponent(filtro)}`);
            const data = await res.json();
            if (data.success) {
                productos = data.data; // Asume que el JSON de productos tiene una propiedad 'data'
                renderTabla(productos);
            } else {
                console.error('Error al cargar productos:', data.message);
                alert('Error al cargar productos.');
            }
        } catch (err) {
            console.error('Error en la solicitud de productos:', err);
            alert('Error de conexión al cargar productos.');
        }
    }

    // Función para renderizar la tabla de productos
    function renderTabla(data) {
        tablaProductos.innerHTML = '';
        if (data.length === 0) {
            tablaProductos.innerHTML = `<tr><td colspan="11">No se encontraron productos</td></tr>`;
            return;
        }

        data.forEach(producto => {
            const fila = document.createElement('tr');
            const nombreCategoria = categorias.find(cat => cat.id == producto.categoria_id)?.nombre || 'N/A'; // Busca el nombre de la categoría

            fila.innerHTML = `
                <td>${producto.id}</td>
                <td>${producto.descripcion}</td>
                <td>S/ ${parseFloat(producto.precio_compra).toFixed(2)}</td>
                <td>S/ ${parseFloat(producto.precio_venta).toFixed(2)}</td>
                <td>${producto.inicial}</td>
                <td>${producto.ingreso}</td>
                <td>${producto.queda}</td>
                <td>${producto.venta}</td>
                <td>S/ ${parseFloat(producto.monto).toFixed(2)}</td>
                <td>${nombreCategoria}</td>
                <td>
                    <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'Administrador'): ?>
                        <button class="btn btn-sm btn-warning me-1 btn-editar" 
                                data-id="${producto.id}"
                                data-descripcion="${producto.descripcion}"
                                data-precio_compra="${producto.precio_compra}"
                                data-precio_venta="${producto.precio_venta}"
                                data-inicial="${producto.inicial}"
                                data-ingreso="${producto.ingreso}"
                                data-queda="${producto.queda}"
                                data-venta="${producto.venta}"
                                data-monto="${producto.monto}"
                                data-categoria="${producto.categoria_id}"
                                data-bs-toggle="modal" data-bs-target="#modalEditarProducto">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger btn-eliminar" data-id="${producto.id}"><i class="fas fa-trash-alt"></i></button>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['rol']) && ($_SESSION['rol'] === 'Administrador' || $_SESSION['rol'] === 'Secretaria')): ?>
                        <button class="btn btn-sm btn-primary btn-vender" 
                                data-id="${producto.id}"
                                data-descripcion="${producto.descripcion}"
                                data-queda="${producto.queda}"
                                data-bs-toggle="modal" data-bs-target="#modalVenderProducto">
                            <i class="fas fa-cart-plus"></i>
                        </button>
                    <?php endif; ?>
                </td>
            `;
            tablaProductos.appendChild(fila);
        });
    }

    // Event listener para el formulario de agregar producto
    formAgregarProducto.addEventListener('submit', async function (e) {
        e.preventDefault();
        const formData = new FormData(formAgregarProducto);
        formData.append('accion', 'registrar');

        try {
            const res = await fetch('../../backend/api/controllers/gestionProducto.php', {
                method: 'POST',
                body: formData
            });
            const data = await res.json();
            if (data.success) {
                formAgregarProducto.reset();
                bootstrap.Modal.getInstance(document.getElementById('modalAgregarProducto')).hide();
                cargarProductos();
            } else {
                alert('Error al registrar producto: ' + (data.message || ''));
            }
        } catch (err) {
            console.error('Error en la solicitud de agregar producto:', err);
            alert('Error de conexión al registrar producto.');
        }
    });

    // Event listener para el formulario de editar producto
    formEditarProducto.addEventListener('submit', async function (e) {
        e.preventDefault();
        const formData = new FormData(formEditarProducto);
        formData.append('accion', 'modificar');

        try {
            const res = await fetch('../../backend/api/controllers/gestionProducto.php', {
                method: 'POST',
                body: formData
            });
            const data = await res.json();
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalEditarProducto')).hide();
                cargarProductos();
            } else {
                alert('Error al modificar producto: ' + (data.message || ''));
            }
        } catch (err) {
            console.error('Error en la solicitud de modificar producto:', err);
            alert('Error de conexión al modificar producto.');
        }
    });

    // Event listener para el formulario de vender producto
    formVenderProducto.addEventListener('submit', async function (e) {
        e.preventDefault();
        const formData = new FormData(formVenderProducto);
        formData.append('accion', 'vender'); // Nueva acción para vender

        const cantidadAVender = parseInt(formData.get('cantidad'));
        const quedaDisponible = parseInt(document.getElementById('vender_queda').value);

        if (cantidadAVender <= 0 || cantidadAVender > quedaDisponible) {
            alert('La cantidad a vender debe ser mayor que 0 y no puede exceder la cantidad disponible.');
            return;
        }

        try {
            const res = await fetch('../../backend/api/controllers/gestionProducto.php', {
                method: 'POST',
                body: formData
            });
            const data = await res.json();
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalVenderProducto')).hide();
                cargarProductos();
            } else {
                alert('Error al vender producto: ' + (data.message || ''));
            }
        } catch (err) {
            console.error('Error en la solicitud de vender producto:', err);
            alert('Error de conexión al vender producto.');
        }
    });

    // Event listener para los botones de editar, eliminar y vender en la tabla
    tablaProductos.addEventListener('click', async function (e) {
        // Botón Editar
        if (e.target.closest('.btn-editar')) {
            const button = e.target.closest('.btn-editar');
            llenarModalEditar(
                button.dataset.id,
                button.dataset.descripcion,
                button.dataset.precio_compra,
                button.dataset.precio_venta,
                button.dataset.inicial,
                button.dataset.ingreso,
                button.dataset.queda,
                button.dataset.venta,
                button.dataset.monto,
                button.dataset.categoria
            );
        }

        // Botón Eliminar
        if (e.target.closest('.btn-eliminar')) {
            const id = e.target.closest('.btn-eliminar').dataset.id;
            if (confirm('¿Está seguro de eliminar este producto? Esta acción no se puede deshacer.')) {
                try {
                    const res = await fetch(`../../backend/api/controllers/gestionProducto.php?accion=eliminar&id=${id}`, {
                        method: 'GET' // O POST si prefieres enviar DELETE por POST
                    });
                    const data = await res.json();
                    if (data.success) {
                        cargarProductos();
                    } else {
                        alert('Error al eliminar producto: ' + (data.message || ''));
                    }
                } catch (err) {
                    console.error('Error en la solicitud de eliminar producto:', err);
                    alert('Error de conexión al eliminar producto.');
                }
            }
        }

        // Botón Vender
        if (e.target.closest('.btn-vender')) {
            const button = e.target.closest('.btn-vender');
            llenarModalVender(
                button.dataset.id,
                button.dataset.descripcion,
                button.dataset.queda
            );
        }
    });

    // Event listener para el botón de búsqueda
    btnBuscar.addEventListener('click', () => {
        const filtro = buscarProducto.value.trim();
        cargarProductos(filtro);
    });

    // Event listener para la tecla Enter en el campo de búsqueda
    buscarProducto.addEventListener('keyup', function (e) {
        if (e.key === 'Enter') {
            btnBuscar.click();
        }
    });

    // Carga inicial de productos y categorías
    cargarCategorias(); // Cargar categorías primero para que los selects estén listos
    cargarProductos(); // Luego cargar los productos
});

// Las funciones llenarModalEditar y llenarModalVender son globales para que el PHP pueda llamarlas
function llenarModalEditar(id, descripcion, precioCompra, precioVenta, inicial, ingreso, queda, venta, monto, categoria) {
    document.getElementById('editar_idProducto').value = id;
    document.getElementById('editar_descripcion').value = descripcion;
    document.getElementById('editar_precio_compra').value = precioCompra;
    document.getElementById('editar_precio_venta').value = precioVenta;
    document.getElementById('editar_inicial').value = inicial;
    document.getElementById('editar_ingreso').value = ingreso;
    document.getElementById('editar_queda').value = queda;
    document.getElementById('editar_venta').value = venta;
    document.getElementById('editar_monto').value = monto;
    document.getElementById('editar_categoria').value = categoria;
}

function llenarModalVender(id, descripcion, queda) {
    document.getElementById('vender_idProducto').value = id;
    document.getElementById('vender_descripcion').value = descripcion;
    document.getElementById('vender_queda').value = queda;
    document.getElementById('vender_cantidad').value = 1; // Establecer valor predeterminado de 1
    document.getElementById('vender_cantidad').max = queda; // Establecer el máximo según la cantidad que queda
}