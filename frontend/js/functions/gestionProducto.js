document.addEventListener('DOMContentLoaded', () => {
    const tablaProductos = document.getElementById('tablaProductos');
    const formAgregarProducto = document.getElementById('formAgregarProducto');
    const formEditarProducto = document.getElementById('formEditarProducto');
    const formVenderProducto = document.getElementById('formVenderProducto');
    const buscarProductoInput = document.getElementById('buscarProducto');
    const btnBuscar = document.getElementById('btnBuscar');

    const modalAgregarProducto = new bootstrap.Modal(document.getElementById('modalAgregarProducto'));
    const modalEditarProducto = new bootstrap.Modal(document.getElementById('modalEditarProducto'));
    const modalVenderProducto = new bootstrap.Modal(document.getElementById('modalVenderProducto'));

    // Función para cargar categorías en los selects
    const cargarCategorias = async () => {
        try {
            // Ruta corregida para las categorías
            const response = await fetch('../../backend/api/controllers/gestionCategoria.php');
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const categorias = await response.json();

            const selectAgregar = document.getElementById('agregar_categoria');
            const selectEditar = document.getElementById('editar_categoria');

            selectAgregar.innerHTML = '<option value="">Seleccione</option>';
            selectEditar.innerHTML = '<option value="">Seleccione</option>';

            categorias.forEach(categoria => {
                const optionAgregar = document.createElement('option');
                optionAgregar.value = categoria.id; // categoria_id de la BD
                optionAgregar.textContent = categoria.nombre;
                selectAgregar.appendChild(optionAgregar);

                const optionEditar = document.createElement('option');
                optionEditar.value = categoria.id; // categoria_id de la BD
                optionEditar.textContent = categoria.nombre;
                selectEditar.appendChild(optionEditar);
            });
        } catch (error) {
            console.error('Error al cargar categorías:', error);
            alert('No se pudieron cargar las categorías. Inténtalo de nuevo más tarde.');
        }
    };

    // Función para cargar los productos
    const cargarProductos = async (query = '') => {
        try {
            // Ruta corregida para los productos
            const url = query ? `../../backend/api/controllers/gestionProducto.php?search=${encodeURIComponent(query)}` : '../../backend/api/controllers/gestionProducto.php';
            const response = await fetch(url);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const productos = await response.json();
            mostrarProductos(productos);
        } catch (error) {
            console.error('Error al cargar productos:', error);
            alert('No se pudieron cargar los productos. Inténtalo de nuevo más tarde.');
        }
    };

    // Función para mostrar los productos en la tabla
    const mostrarProductos = (productos) => {
        tablaProductos.innerHTML = '';
        if (productos.length === 0) {
            tablaProductos.innerHTML = '<tr><td colspan="11">No se encontraron productos.</td></tr>';
            return;
        }
        productos.forEach(producto => {
            const row = document.createElement('tr');
            // Asegúrate que los nombres de las propiedades coincidan con los alias en el SELECT de PHP
            row.innerHTML = `
                <td>${producto.idProducto}</td>
                <td>${producto.descripcion}</td>
                <td>${parseFloat(producto.precio_compra).toFixed(2)}</td>
                <td>${parseFloat(producto.precio_venta).toFixed(2)}</td>
                <td>${producto.inicial}</td>
                <td>${producto.ingreso}</td>
                <td>${producto.queda}</td>
                <td>${producto.venta}</td>
                <td>${parseFloat(producto.monto).toFixed(2)}</td>
                <td>${producto.categoria_nombre}</td>
                <td>
                    <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'Administrador'): ?>
                        <button class="btn btn-warning btn-sm btn-editar me-1" data-id="${producto.idProducto}">
                            <i class="fas fa-edit"></i>
                        </button>
                    <?php endif; ?>
                    <button class="btn btn-primary btn-sm btn-vender" data-id="${producto.idProducto}">
                        <i class="fas fa-shopping-cart"></i>
                    </button>
                </td>
            `;
            tablaProductos.appendChild(row);
        });

        // Solo agregar event listeners si el botón existe (para el rol de Administrador)
        document.querySelectorAll('.btn-editar').forEach(button => {
            button.addEventListener('click', (e) => {
                const idProducto = e.currentTarget.dataset.id;
                abrirModalEditar(idProducto);
            });
        });

        document.querySelectorAll('.btn-vender').forEach(button => {
            button.addEventListener('click', (e) => {
                const idProducto = e.currentTarget.dataset.id;
                abrirModalVender(idProducto);
            });
        });
    };

    // --- Funcionalidad de Agregar Producto ---
    formAgregarProducto.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(formAgregarProducto);
        const data = Object.fromEntries(formData.entries());

        try {
            const response = await fetch('../../backend/api/controllers/gestionProducto.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Error al agregar producto');
            }

            const result = await response.json();
            alert(result.message);
            modalAgregarProducto.hide();
            formAgregarProducto.reset();
            cargarProductos(); // Recargar la tabla
        } catch (error) {
            console.error('Error al agregar producto:', error);
            alert(`Error al agregar producto: ${error.message}`);
        }
    });

    // --- Funcionalidad de Buscar Producto ---
    btnBuscar.addEventListener('click', () => {
        const query = buscarProductoInput.value.trim();
        cargarProductos(query);
    });

    buscarProductoInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            btnBuscar.click();
        }
    });

    // --- Funcionalidad de Editar Producto ---
    const abrirModalEditar = async (idProducto) => {
        try {
            const response = await fetch(`../../backend/api/controllers/gestionProducto.php?id=${idProducto}`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const producto = await response.json();

            // Asegúrate que los IDs de los campos HTML coincidan con las propiedades del objeto 'producto'
            document.getElementById('editar_idProducto').value = producto.idProducto;
            document.getElementById('editar_descripcion').value = producto.descripcion;
            document.getElementById('editar_precio_compra').value = producto.precio_compra;
            document.getElementById('editar_precio_venta').value = producto.precio_venta;
            document.getElementById('editar_inicial').value = producto.inicial;
            document.getElementById('editar_ingreso').value = producto.ingreso;
            document.getElementById('editar_queda').value = producto.queda;
            document.getElementById('editar_venta').value = producto.venta;
            document.getElementById('editar_monto').value = producto.monto;
            document.getElementById('editar_categoria').value = producto.idCategoria; // Usa idCategoria del backend

            modalEditarProducto.show();
        } catch (error) {
            console.error('Error al cargar datos del producto para editar:', error);
            alert('No se pudo cargar la información del producto. Inténtalo de nuevo.');
        }
    };

    formEditarProducto.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(formEditarProducto);
        const data = Object.fromEntries(formData.entries());
        const idProducto = data.idProducto;

        try {
            const response = await fetch(`../../backend/api/controllers/gestionProducto.php?id=${idProducto}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Error al actualizar producto');
            }

            const result = await response.json();
            alert(result.message);
            modalEditarProducto.hide();
            cargarProductos(); // Recargar la tabla
        } catch (error) {
            console.error('Error al actualizar producto:', error);
            alert(`Error al actualizar producto: ${error.message}`);
        }
    });

    // --- Funcionalidad de Vender Producto ---
    const abrirModalVender = async (idProducto) => {
        try {
            const response = await fetch(`../../backend/api/controllers/gestionProducto.php?id=${idProducto}`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const producto = await response.json();

            document.getElementById('vender_idProducto').value = producto.idProducto;
            document.getElementById('vender_descripcion').value = producto.descripcion;
            document.getElementById('vender_queda').value = producto.queda;
            document.getElementById('vender_cantidad').max = producto.queda; // Establecer el máximo para la cantidad a vender
            document.getElementById('vender_cantidad').value = 1; // Valor por defecto

            modalVenderProducto.show();
        } catch (error) {
            console.error('Error al cargar datos del producto para vender:', error);
            alert('No se pudo cargar la información del producto para la venta. Inténtalo de nuevo.');
        }
    };

    formVenderProducto.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(formVenderProducto);
        const idProducto = formData.get('idProducto');
        const cantidadVender = parseInt(formData.get('cantidad'));
        const stockActual = parseInt(formData.get('queda'));

        if (cantidadVender <= 0 || isNaN(cantidadVender)) {
            alert('La cantidad a vender debe ser un número positivo.');
            return;
        }

        if (cantidadVender > stockActual) {
            alert(`No hay suficiente stock. Solo quedan ${stockActual} unidades.`);
            return;
        }

        try {
            const response = await fetch(`../../backend/api/controllers/gestionProducto.php?id=${idProducto}&action=sell`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ cantidad: cantidadVender })
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Error al realizar la venta');
            }

            const result = await response.json();
            alert(result.message);
            modalVenderProducto.hide();
            cargarProductos(); // Recargar la tabla
        } catch (error) {
            console.error('Error al vender producto:', error);
            alert(`Error al vender producto: ${error.message}`);
        }
    });

    // Cargar productos y categorías al iniciar la página
    cargarCategorias();
    cargarProductos();
});