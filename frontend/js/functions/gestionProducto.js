document.addEventListener('DOMContentLoaded', () => {
    cargarCategorias();
    listarProductos();
    document.getElementById('btnBuscar').addEventListener('click', buscarProductos);
    document.getElementById('formAgregarProducto').addEventListener('submit', agregarProducto);
    document.getElementById('formEditarProducto').addEventListener('submit', editarProducto);
    document.getElementById('formVenderProducto').addEventListener('submit', venderProducto);
});

function cargarCategorias() {
    fetch('../../backend/controllers/gestionCategoria.php?accion=listar')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const selectAgregar = document.getElementById('agregar_categoria');
                const selectEditar = document.getElementById('editar_categoria');
                selectAgregar.innerHTML = '<option value="">Seleccione</option>';
                selectEditar.innerHTML = '<option value="">Seleccione</option>';
                data.data.forEach(categoria => {
                    const option = `<option value="${categoria.id}">${categoria.nombre}</option>`;
                    selectAgregar.innerHTML += option;
                    selectEditar.innerHTML += option;
                });
            } else {
                alert('Error al cargar categorías: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cargar categorías');
        });
}

function listarProductos() {
    fetch('../../backend/controllers/gestionProducto.php?accion=listar')
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('tablaProductos');
            tbody.innerHTML = '';
            if (data.success) {
                data.data.forEach(producto => {
                    const total = parseInt(producto.inicial) + parseInt(producto.ingreso);
                    const row = `
                        <tr>
                            <td>${producto.id}</td>
                            <td>${producto.descripcion}</td>
                            <td>${producto.precio_compra}</td>
                            <td>${producto.precio_venta}</td>
                            <td>${producto.inicial}</td>
                            <td>${producto.ingreso}</td>
                            <td>${total}</td>
                            <td>${producto.queda}</td>
                            <td>${producto.venta}</td>
                            <td>${producto.monto}</td>
                            <td>${producto.categoria_nombre}</td>
                            <td>
                                <button class="btn btn-warning btn-sm" onclick="llenarModalEditar(${producto.id}, '${producto.descripcion}', ${producto.precio_compra}, ${producto.precio_venta}, ${producto.inicial}, ${producto.ingreso}, ${producto.queda}, ${producto.venta}, ${producto.monto}, ${producto.categoria_id})" data-bs-toggle="modal" data-bs-target="#modalEditarProducto">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-danger btn-sm" onclick="eliminarProducto(${producto.id})">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <button class="btn btn-primary btn-sm" onclick="llenarModalVender(${producto.id}, '${producto.descripcion}', ${producto.queda})" data-bs-toggle="modal" data-bs-target="#modalVenderProducto">
                                    <i class="fas fa-shopping-cart"></i>
                                </button>
                            </td>
                        </tr>`;
                    tbody.innerHTML += row;
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="12">No se encontraron productos</td></tr>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cargar productos');
        });
}

function buscarProductos() {
    const busqueda = document.getElementById('buscarProducto').value;
    fetch(`../../backend/controllers/gestionProducto.php?accion=buscar&termino=${encodeURIComponent(busqueda)}`)
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('tablaProductos');
            tbody.innerHTML = '';
            if (data.success) {
                data.data.forEach(producto => {
                    const total = parseInt(producto.inicial) + parseInt(producto.ingreso);
                    const row = `
                        <tr>
                            <td>${producto.id}</td>
                            <td>${producto.descripcion}</td>
                            <td>${producto.precio_compra}</td>
                            <td>${producto.precio_venta}</td>
                            <td>${producto.inicial}</td>
                            <td>${producto.ingreso}</td>
                            <td>${total}</td>
                            <td>${producto.queda}</td>
                            <td>${producto.venta}</td>
                            <td>${producto.monto}</td>
                            <td>${producto.categoria_nombre}</td>
                            <td>
                                <button class="btn btn-warning btn-sm" onclick="llenarModalEditar(${producto.id}, '${producto.descripcion}', ${producto.precio_compra}, ${producto.precio_venta}, ${producto.inicial}, ${producto.ingreso}, ${producto.queda}, ${producto.venta}, ${producto.monto}, ${producto.categoria_id})" data-bs-toggle="modal" data-bs-target="#modalEditarProducto">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-danger btn-sm" onclick="eliminarProducto(${producto.id})">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <button class="btn btn-primary btn-sm" onclick="llenarModalVender(${producto.id}, '${producto.descripcion}', ${producto.queda})" data-bs-toggle="modal" data-bs-target="#modalVenderProducto">
                                    <i class="fas fa-shopping-cart"></i>
                                </button>
                            </td>
                        </tr>`;
                    tbody.innerHTML += row;
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="12">No se encontraron productos</td></tr>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al buscar productos');
        });
}

function agregarProducto(e) {
    e.preventDefault();
    const formData = new FormData(document.getElementById('formAgregarProducto'));
    fetch('../../backend/controllers/gestionProducto.php?accion=agregar', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Producto agregado correctamente');
                bootstrap.Modal.getInstance(document.getElementById('modalAgregarProducto')).hide();
                document.getElementById('formAgregarProducto').reset();
                listarProductos();
            } else {
                alert('Error al agregar producto: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al agregar producto');
        });
}

function editarProducto(e) {
    e.preventDefault();
    const formData = new FormData(document.getElementById('formEditarProducto'));
    fetch('../../backend/controllers/gestionProducto.php?accion=editar', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Producto actualizado correctamente');
                bootstrap.Modal.getInstance(document.getElementById('modalEditarProducto')).hide();
                listarProductos();
            } else {
                alert('Error al actualizar producto: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al actualizar producto');
        });
}

function eliminarProducto(id) {
    if (confirm('¿Está seguro de eliminar este producto?')) {
        fetch(`../../backend/controllers/gestionProducto.php?accion=eliminar&id=${id}`, {
            method: 'POST'
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Producto eliminado correctamente');
                    listarProductos();
                } else {
                    alert('Error al eliminar producto: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al eliminar producto');
            });
    }
}

function venderProducto(e) {
    e.preventDefault();
    const formData = new FormData(document.getElementById('formVenderProducto'));
    fetch('../../backend/controllers/gestionProducto.php?accion=vender', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Venta realizada correctamente');
                bootstrap.Modal.getInstance(document.getElementById('modalVenderProducto')).hide();
                listarProductos();
            } else {
                alert('Error al realizar la venta: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al realizar la venta');
        });
}