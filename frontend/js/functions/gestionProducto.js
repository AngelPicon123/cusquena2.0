const API_PRODUCTO = 'http://localhost/cusquena/backend/api/controllers/gestionProducto.php';
const API_CATEGORIA = 'http://localhost/cusquena/backend/api/controllers/gestionCategoria.php';
let totalVentas = 0;

document.addEventListener('DOMContentLoaded', () => {
    flatpickr("#venderFecha", {
        dateFormat: "d/m/Y",
        allowInput: true,
        defaultDate: new Date(),
        placeholder: "dd/mm/aaaa"
    });

    listarProductos();
    cargarCategoriasEnSelect('idCategoria');
    cargarCategoriasEnSelect('editarCategoria');

    const cantidadInput = document.getElementById('venderCantidad');
    cantidadInput.addEventListener('input', () => {
        const cantidad = parseInt(cantidadInput.value) || 0;
        const stockDisponible = parseInt(document.getElementById('venderStockDisponible').value) || 0;
        const btnAgregar = document.getElementById('btnAgregarProducto');

        if (cantidad <= 0 || cantidad > stockDisponible) {
            btnAgregar.disabled = true;
            if (cantidad > stockDisponible) {
                alert('La cantidad excede el stock disponible.');
            }
        } else {
            btnAgregar.disabled = false;
        }
        calcularSubtotal();
    });

    document.getElementById('btnAgregarProducto').addEventListener('click', agregarProducto);

    // Escuchar cambios en el stock desde gestionHistorialIngreso.js
    window.addEventListener('storage', (event) => {
        if (event.key === 'stockActualizado') {
            listarProductos();
        }
    });

    // Verificar al cargar la página
    if (localStorage.getItem('stockActualizado')) {
        listarProductos();
        localStorage.removeItem('stockActualizado');
    }
});

document.getElementById('btnBuscar').addEventListener('click', () => {
    const termino = document.getElementById('buscarProducto').value.trim();
    listarProductos(termino);
});

document.getElementById('formAgregar').addEventListener('submit', async (e) => {
    e.preventDefault();

    const data = {
        descripcion: document.getElementById('descripcion').value,
        precioCompra: parseFloat(document.getElementById('precioCompra').value),
        precioVenta: parseFloat(document.getElementById('precioVenta').value),
        stock: parseInt(document.getElementById('stock').value),
        idCategoria: document.getElementById('idCategoria').value,
        presentacion: document.getElementById('presentacion').value,
        estado: document.querySelector('input[name="estado"]:checked').value
    };

    if (!data.descripcion || !data.precioCompra || !data.precioVenta || !data.stock || !data.idCategoria || !data.presentacion) {
        alert("Completa todos los campos.");
        return;
    }

    try {
        const res = await fetch(API_PRODUCTO, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        const result = await res.json();
        alert(result.message);
    } catch (error) {
        alert('Error al agregar el producto: ' + error.message);
        console.error(error);
    } finally {
        listarProductos();
        document.getElementById('formAgregar').reset();
        document.querySelector('#modalAgregar .btn-close').click();
    }
});

document.getElementById('formEditar').addEventListener('submit', async (e) => {
    e.preventDefault();

    const data = {
        idProducto: document.getElementById('editarId').value,
        descripcion: document.getElementById('editarDescripcion').value,
        precioCompra: parseFloat(document.getElementById('editarPrecioCompra').value),
        precioVenta: parseFloat(document.getElementById('editarPrecioVenta').value),
        stock: parseInt(document.getElementById('editarStock').value),
        idCategoria: document.getElementById('editarCategoria').value,
        presentacion: document.getElementById('editarPresentacion').value,
        estado: document.querySelector('input[name="editarEstado"]:checked').value
    };

    try {
        const res = await fetch(API_PRODUCTO, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        const result = await res.json();
        alert(result.message);
    } catch (error) {
        alert('Error al actualizar el producto: ' + error.message);
        console.error(error);
    } finally {
        listarProductos();
        document.querySelector('#modalEditar .btn-close').click();
    }
});

document.getElementById('formVender').addEventListener('submit', async (e) => {
    e.preventDefault();

    const cantidadVendida = parseInt(document.getElementById('venderCantidad').value);
    const stockDisponible = parseInt(document.getElementById('venderStockDisponible').value);

    if (cantidadVendida <= 0) {
        alert('La cantidad debe ser mayor a 0.');
        return;
    }

    if (cantidadVendida > stockDisponible) {
        alert('La cantidad a vender excede el stock disponible.');
        return;
    }

    const fechaInput = document.getElementById('venderFecha').value;
    let fechaDB;
    if (fechaInput) {
        const [day, month, year] = fechaInput.split('/');
        fechaDB = `${year}-${month}-${day}`;
    } else {
        alert('Por favor, selecciona una fecha válida.');
        return;
    }

    const data = {
        idProducto: document.getElementById('venderIdProducto').value,
        descripcion: document.getElementById('venderDescripcion').value,
        fecha: fechaDB,
        cantidad: cantidadVendida,
        precioUnitario: parseFloat(document.getElementById('venderPrecioUnitario').value),
        subtotal: parseFloat(document.getElementById('venderSubtotal').value),
        total: totalVentas
    };

    try {
        const res = await fetch(`${API_PRODUCTO}?venta=true`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });

        if (!res.ok) {
            const errorText = await res.text();
            throw new Error(`Error del servidor: ${res.status} - ${errorText}`);
        }

        const result = await res.json();
        if (result.error) {
            throw new Error(result.error);
        }

        alert(result.message);
        totalVentas = 0;
        document.getElementById('formVender').reset();
        document.getElementById('venderTotal').value = '';
        document.querySelector('#modalVender .btn-close').click();
    } catch (error) {
        alert('Error al registrar la venta: ' + error.message);
        console.error(error);
    } finally {
        listarProductos();
    }
});

async function listarProductos(buscar = '') {
    const tbody = document.getElementById('tablaProductos');
    tbody.innerHTML = '';

    const url = buscar ? `${API_PRODUCTO}?buscar=${encodeURIComponent(buscar)}` : API_PRODUCTO;

    try {
        const res = await fetch(url);
        const data = await res.json();
        if (data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="9">No se encontraron resultados</td></tr>';
            return;
        }

        data.forEach(prod => {
            const fila = document.createElement('tr');
            fila.className = prod.stock <= 0 ? 'table-danger' : prod.estado === 'inactivo' ? 'table-warning' : '';
            fila.innerHTML = `
                <td>${prod.idProducto}</td>
                <td>${prod.descripcion}</td>
                <td>S/.${parseFloat(prod.precioCompra).toFixed(2)}</td>
                <td>S/.${parseFloat(prod.precioVenta).toFixed(2)}</td>
                <td>${prod.stock}</td>
                <td>${prod.nombreCategoria || prod.idCategoria}</td>
                <td>${prod.presentacion}</td>
                <td><span class="badge ${prod.estado === 'activo' ? 'bg-success' : 'bg-danger'}">${prod.estado.charAt(0).toUpperCase() + prod.estado.slice(1)}</span></td>
                <td>
                    <button class="btn btn-success btn-sm" onclick='llenarModalEditar(${JSON.stringify(prod)})' data-bs-toggle="modal" data-bs-target="#modalEditar">Editar</button>
                    <button class="btn btn-primary btn-sm" onclick='llenarModalVenta(${JSON.stringify(prod)})' data-bs-toggle="modal" data-bs-target="#modalVender" ${prod.estado === 'inactivo' || prod.stock <= 0 ? 'disabled' : ''}>Vender</button>
                </td>
            `;
            tbody.appendChild(fila);
        });
    } catch (error) {
        tbody.innerHTML = '<tr><td colspan="9">Error al cargar los datos</td></tr>';
        console.error(error);
    }
}

function llenarModalEditar(prod) {
    document.getElementById('editarId').value = prod.idProducto;
    document.getElementById('editarDescripcion').value = prod.descripcion;
    document.getElementById('editarPrecioCompra').value = prod.precioCompra;
    document.getElementById('editarPrecioVenta').value = prod.precioVenta;
    document.getElementById('editarStock').value = prod.stock;
    document.getElementById('editarPresentacion').value = prod.presentacion;
    document.getElementById('editarEstadoActivo').checked = prod.estado === 'activo';
    document.getElementById('editarEstadoInactivo').checked = prod.estado === 'inactivo';
    cargarCategoriasEnSelect('editarCategoria', prod.idCategoria);
}

async function llenarModalVenta(prod) {
    try {
        // Obtener el stock actual desde el servidor
        const res = await fetch(`${API_PRODUCTO}?idProducto=${prod.idProducto}`);
        const producto = await res.json();
        const stockActual = producto[0]?.stock || 0;

        document.getElementById('venderIdProducto').value = prod.idProducto;
        document.getElementById('venderStockDisponible').value = stockActual;
        document.getElementById('venderDescripcion').value = prod.descripcion;
        document.getElementById('venderPrecioUnitario').value = parseFloat(prod.precioVenta).toFixed(2);
        document.getElementById('venderCantidad').value = 1;
        document.getElementById('venderTotal').value = '';
        totalVentas = 0;
        calcularSubtotal();

        flatpickr("#venderFecha", {
            dateFormat: "d/m/Y",
            allowInput: true,
            defaultDate: new Date(),
            placeholder: "dd/mm/aaaa"
        });
    } catch (error) {
        alert('Error al cargar el stock actual: ' + error.message);
        console.error(error);
    }
}

function calcularSubtotal() {
    const cantidad = parseInt(document.getElementById('venderCantidad').value) || 0;
    const precioUnitario = parseFloat(document.getElementById('venderPrecioUnitario').value) || 0;
    const subtotal = cantidad * precioUnitario;
    document.getElementById('venderSubtotal').value = subtotal.toFixed(2);
}

function agregarProducto() {
    const subtotal = parseFloat(document.getElementById('venderSubtotal').value) || 0;
    const cantidadVendida = parseInt(document.getElementById('venderCantidad').value);
    const stockDisponible = parseInt(document.getElementById('venderStockDisponible').value);

    if (cantidadVendida <= 0) {
        alert('La cantidad debe ser mayor a 0.');
        return;
    }

    if (cantidadVendida > stockDisponible) {
        alert('La cantidad a vender excede el stock disponible.');
        return;
    }

    totalVentas += subtotal;
    document.getElementById('venderTotal').value = totalVentas.toFixed(2);
    document.getElementById('venderCantidad').value = 1;
    document.getElementById('venderSubtotal').value = '0.00';
}

async function cargarCategoriasEnSelect(idSelect, idSeleccionado = null) {
    try {
        const res = await fetch(API_CATEGORIA);
        const data = await res.json();
        const select = document.getElementById(idSelect);
        select.innerHTML = '<option value="">--SELECCIONAR--</option>';
        data.forEach(cat => {
            const option = document.createElement('option');
            option.value = cat.idCategoria;
            option.textContent = cat.descripcion;
            if (idSeleccionado && cat.idCategoria == idSeleccionado) {
                option.selected = true;
            }
            select.appendChild(option);
        });
    } catch (error) {
        console.error('Error al cargar categorías:', error);
    }
}