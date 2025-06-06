const API_INGRESO_PRODUCTO = 'http://localhost/cusquena/backend/api/controllers/gestionHistorialIngreso.php';
const API_PRODUCTO = 'http://localhost/cusquena/backend/api/controllers/gestionProducto.php';

// Inicializar Flatpickr para los campos de fecha
document.addEventListener('DOMContentLoaded', () => {
    flatpickr("#fechaIngreso", {
        dateFormat: "d/m/Y",
        allowInput: true,
        placeholder: "dd/mm/aaaa",
        defaultDate: new Date()
    });

    flatpickr("#editarFechaIngreso", {
        dateFormat: "d/m/Y",
        allowInput: true,
        placeholder: "dd/mm/aaaa"
    });

    listarIngresos();
    cargarProductosEnSelect('idProducto');
    cargarProductosEnSelect('editarIdProducto');

    // Validar stock y precioCompra en tiempo real
    const stockInput = document.getElementById('stock');
    const precioCompraInput = document.getElementById('precioCompra');
    stockInput.addEventListener('input', validarCamposAgregar);
    precioCompraInput.addEventListener('input', validarCamposAgregar);
});

// Validar campos del formulario de agregar
function validarCamposAgregar() {
    const stock = parseInt(document.getElementById('stock').value) || 0;
    const precioCompra = parseFloat(document.getElementById('precioCompra').value) || 0;
    const idProducto = document.getElementById('idProducto').value;
    const btnSubmit = document.getElementById('formAgregar').querySelector('button[type="submit"]');
    btnSubmit.disabled = stock <= 0 || precioCompra <= 0 || !idProducto;
}

// Buscar ingreso
document.getElementById('btnBuscar').addEventListener('click', () => {
    const termino = document.getElementById('buscarIngreso').value.trim();
    listarIngresos(termino);
});

// Agregar ingreso
document.getElementById('formAgregar').addEventListener('submit', async (e) => {
    e.preventDefault();
    const fechaInput = document.getElementById('fechaIngreso').value; // dd/mm/yyyy
    let fechaDB;

    if (fechaInput) {
        const [day, month, year] = fechaInput.split('/');
        fechaDB = `${year}-${month}-${day}`; // yyyy-mm-dd
    } else {
        alert('Por favor, selecciona una fecha válida.');
        return;
    }

    const data = {
        fechaIngreso: fechaDB,
        stock: parseInt(document.getElementById('stock').value),
        precioCompra: parseFloat(document.getElementById('precioCompra').value),
        idProducto: document.getElementById('idProducto').value,
        detalle: document.getElementById('detalle').value
    };

    if (!data.fechaIngreso || data.stock <= 0 || data.precioCompra <= 0 || !data.idProducto) {
        alert("Por favor, completa todos los campos requeridos con valores válidos.");
        return;
    }

    try {
        const res = await fetch(API_INGRESO_PRODUCTO, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        const result = await res.json();
        if (result.error) {
            throw new Error(result.error);
        }
        alert(result.message);
        listarIngresos();
        document.getElementById('formAgregar').reset();
        document.querySelector('#miModal .btn-close').click();
    } catch (error) {
        alert('Error al agregar el ingreso: ' + error.message);
        console.error(error);
    }
});

// Editar ingreso
document.getElementById('formEditar').addEventListener('submit', async (e) => {
    e.preventDefault();
    const fechaInput = document.getElementById('editarFechaIngreso').value; // dd/mm/yyyy
    let fechaDB;

    if (fechaInput) {
        const [day, month, year] = fechaInput.split('/');
        fechaDB = `${year}-${month}-${day}`; // yyyy-mm-dd
    } else {
        alert('Por favor, selecciona una fecha válida.');
        return;
    }

    const data = {
        idIngresoProducto: document.getElementById('editarIdIngresoProducto').value,
        fechaIngreso: fechaDB,
        stock: parseInt(document.getElementById('editarstock').value),
        precioCompra: parseFloat(document.getElementById('editarPrecioCompra').value),
        idProducto: document.getElementById('editarIdProducto').value,
        detalle: document.getElementById('editarDetalle').value
    };

    if (!data.idIngresoProducto || !data.fechaIngreso || data.stock <= 0 || data.precioCompra <= 0 || !data.idProducto) {
        alert("Por favor, completa todos los campos requeridos con valores válidos.");
        return;
    }

    try {
        const res = await fetch(API_INGRESO_PRODUCTO, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        const result = await res.json();
        if (result.error) {
            throw new Error(result.error);
        }
        alert(result.message);
        listarIngresos();
        document.querySelector('#modalEditar .btn-close').click();
    } catch (error) {
        alert('Error al actualizar el ingreso: ' + error.message);
        console.error(error);
    }
});

async function listarIngresos(buscar = '') {
    const tbody = document.getElementById('tablaIngresos');
    tbody.innerHTML = '';
    const url = buscar ? `${API_INGRESO_PRODUCTO}?buscar=${encodeURIComponent(buscar)}` : API_INGRESO_PRODUCTO;

    try {
        const res = await fetch(url);
        const data = await res.json();
        
        if (data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7">No se encontraron resultados</td></tr>';
            return;
        }

        data.forEach(ingreso => {
            const fila = document.createElement('tr');
            fila.innerHTML = `
                <td>${ingreso.idIngresoProducto}</td>
                <td>${formatearFechaVista(ingreso.fechaIngreso)}</td>
                <td>${ingreso.stock}</td>
                <td>S/.${parseFloat(ingreso.precioCompra).toFixed(2)}</td>
                <td>${ingreso.productoDescripcion}</td>
                <td>${ingreso.detalle || '-'}</td>
                <td>
                    <button class="btn btn-success btn-sm" onclick='llenarModalEditar(${JSON.stringify(ingreso)})' data-bs-toggle="modal" data-bs-target="#modalEditar">Editar</button>
                    <button class="btn btn-danger btn-sm" onclick="eliminarIngreso(${ingreso.idIngresoProducto})">Eliminar</button>
                </td>
            `;
            tbody.appendChild(fila);
        });
    } catch (error) {
        tbody.innerHTML = '<tr><td colspan="7">Error al cargar los datos</td></tr>';
        console.error(error);
    }
}

function llenarModalEditar(ingreso) {
    document.getElementById('editarIdIngresoProducto').value = ingreso.idIngresoProducto;
    document.getElementById('editarFechaIngreso').value = formatearFechaVista(ingreso.fechaIngreso);
    document.getElementById('editarstock').value = ingreso.stock;
    document.getElementById('editarPrecioCompra').value = parseFloat(ingreso.precioCompra).toFixed(2);
    cargarProductosEnSelect('editarIdProducto', ingreso.idProducto);
    document.getElementById('editarDetalle').value = ingreso.detalle || '';

    // Validar campos del formulario de editar
    const stockInput = document.getElementById('editarstock');
    const precioCompraInput = document.getElementById('editarPrecioCompra');
    const idProductoSelect = document.getElementById('editarIdProducto');
    const btnSubmit = document.getElementById('formEditar').querySelector('button[type="submit"]');

    function validarCamposEditar() {
        const stock = parseInt(stockInput.value) || 0;
        const precioCompra = parseFloat(precioCompraInput.value) || 0;
        const idProducto = idProductoSelect.value;
        btnSubmit.disabled = stock <= 0 || precioCompra <= 0 || !idProducto;
    }

    stockInput.addEventListener('input', validarCamposEditar);
    precioCompraInput.addEventListener('input', validarCamposEditar);
    idProductoSelect.addEventListener('change', validarCamposEditar);
    validarCamposEditar();
}

function formatearFechaVista(fechaBD) {
    const [a, m, d] = fechaBD.split("-");
    return `${d}/${m}/${a}`; // dd/mm/yyyy
}

async function cargarProductosEnSelect(idSelect, idSeleccionado = null) {
    try {
        const res = await fetch(API_PRODUCTO);
        const data = await res.json();
        const select = document.getElementById(idSelect);
        select.innerHTML = '<option value="">--SELECCIONAR--</option>';
        data.forEach(prod => {
            const option = document.createElement('option');
            option.value = prod.idProducto;
            option.textContent = prod.descripcion;
            if (idSeleccionado && prod.idProducto == idSeleccionado) {
                option.selected = true;
            }
            select.appendChild(option);
        });
    } catch (error) {
        console.error('Error al cargar productos:', error);
    }
}

async function eliminarIngreso(idIngresoProducto) {
    if (!confirm('¿Estás seguro de eliminar este ingreso? Esto afectará el stock del producto.')) return;

    try {
        const res = await fetch(API_INGRESO_PRODUCTO, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ idIngresoProducto })
        });
        const result = await res.json();
        if (result.error) {
            throw new Error(result.error);
        }
        alert(result.message);
        listarIngresos();
    } catch (error) {
        alert('Error al eliminar el ingreso: ' + error.message);
        console.error(error);
    }
}