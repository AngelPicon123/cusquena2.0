const tablaProductos = document.getElementById("tablaProductos");
const formAgregar = document.getElementById("formAgregarProducto");
const formEditar = document.getElementById("formEditarProducto");
const formVender = document.getElementById("formVenderProducto");
const buscarProducto = document.getElementById("buscarProducto");
const btnBuscar = document.getElementById("btnBuscar");

// Instancias de los modales para poder controlarlos
const modalEditar = new bootstrap.Modal(document.getElementById('modalEditarProducto'));
const modalVender = new bootstrap.Modal(document.getElementById('modalVenderProducto'));
const modalVerVentas = new bootstrap.Modal(document.getElementById('modalVerVentas'));

let productos = [];

function cargarProductos() {
  fetch("../../backend/api/controllers/gestionProducto.php?action=listar")
    .then((res) => res.json())
    .then((data) => {
      productos = data;
      renderizarTabla(productos);
    })
    .catch((error) => {
      console.error("Error cargando productos:", error);
      alert("Error al cargar productos");
    });
}

function renderizarTabla(lista) {
  tablaProductos.innerHTML = "";
  lista.forEach((producto) => {
    const tr = document.createElement("tr");
    tr.innerHTML = `
      <td>${producto.id}</td>
      <td>${producto.descripcion}</td>
      <td>S/. ${producto.precio_compra}</td>
      <td>S/. ${producto.precio_venta}</td>
      <td>${producto.inicial}</td>
      <td>${producto.ingreso}</td>
      <td>${producto.queda}</td>
      <td>${producto.venta}</td>
      <td>S/. ${producto.monto}</td>
      <td>${producto.categoria}</td>
      <td>
        <button class="btn btn-sm btn-info" onclick="verHistorial(${producto.id}, '${producto.descripcion}')"><i class="fas fa-eye"></i></button>
        <button class="btn btn-sm btn-primary" onclick="abrirModalVenta(${producto.id})"><i class="fas fa-cart-plus"></i></button>
        <button class="btn btn-sm btn-warning" onclick="abrirModalEditar(${producto.id})"><i class="fas fa-edit"></i></button>
        <button class="btn btn-sm btn-danger" onclick="eliminarProducto(${producto.id})"><i class="fas fa-trash"></i></button>
      </td>`;
    tablaProductos.appendChild(tr);
  });
}

formAgregar.addEventListener("submit", (e) => {
  e.preventDefault();
  const datos = new FormData(formAgregar);
  fetch("../../backend/api/controllers/gestionProducto.php?action=agregar", {
    method: "POST",
    body: datos,
  })
    .then((res) => res.json())
    .then((response) => {
      if (response.success) {
        cargarProductos();
        formAgregar.reset();
        bootstrap.Modal.getInstance(document.getElementById("modalAgregarProducto")).hide();
      } else {
        alert("Error al agregar: " + response.message);
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      alert("Error al agregar producto");
    });
});

function eliminarProducto(id) {
  if (!confirm("¿Eliminar producto?")) return;
  fetch(`../../backend/api/controllers/gestionProducto.php?action=eliminar&id=${id}`, {
    method: "DELETE",
  })
    .then((res) => res.json())
    .then((response) => {
      if (response.success) {
        cargarProductos();
      } else {
        alert("Error al eliminar: " + response.error);
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      alert("Error al eliminar producto");
    });
}

btnBuscar.addEventListener("click", () => {
  const termino = buscarProducto.value.toLowerCase();
  const filtrados = productos.filter((p) =>
    p.descripcion.toLowerCase().includes(termino) ||
    p.categoria.toLowerCase().includes(termino)
  );
  renderizarTabla(filtrados);
});

// --- Funciones para manejar los modales que faltaban ---

function abrirModalEditar(id) {
  const producto = productos.find(p => p.id == id);
  if (producto) {
    document.getElementById('editar_idProducto').value = producto.id;
    document.getElementById('editar_descripcion').value = producto.descripcion;
    document.getElementById('editar_precio_compra').value = producto.precio_compra;
    document.getElementById('editar_precio_venta').value = producto.precio_venta;
    document.getElementById('editar_inicial').value = producto.inicial;
    document.getElementById('editar_ingreso').value = producto.ingreso;
    document.getElementById('editar_venta').value = producto.venta;
    document.getElementById('editar_monto').value = producto.monto;
    document.getElementById('editar_categoria').value = producto.categoria;
    // El campo 'queda' no se edita directamente
    
    modalEditar.show();
  }
}

function abrirModalVenta(id) {
  const producto = productos.find(p => p.id == id);
  if (producto) {
    document.getElementById('vender_idProducto').value = producto.id;
    document.getElementById('vender_descripcion').value = producto.descripcion;
    document.getElementById('vender_queda').value = producto.queda;
    modalVender.show();
  }
}

function verHistorial(id, nombre) {
    document.getElementById('nombreProducto').textContent = nombre;
    
    // El fetch ahora espera recibir el 'monto_venta'
    fetch(`../../backend/api/controllers/gestionProducto.php?action=historial&id=${id}`)
        .then(res => res.json())
        .then(ventas => {
            const tablaVentas = document.getElementById('tablaVentasHistorial');
            tablaVentas.innerHTML = '';
            
            if (ventas.length > 0) {
                ventas.forEach(venta => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${venta.fecha_venta}</td>
                        <td>${venta.cantidad_vendida}</td>
                        <td>S/. ${venta.monto_venta}</td> <td>
                            <button class="btn btn-xs btn-danger" onclick="eliminarVenta(${venta.id})">Eliminar</button>
                        </td>
                    `;
                    tablaVentas.appendChild(tr);
                });
            } else {
                tablaVentas.innerHTML = '<tr><td colspan="4">No hay ventas registradas para este producto.</td></tr>';
            }
            modalVerVentas.show();
        })
        .catch(error => {
            console.error('Error al cargar el historial de ventas:', error);
            alert('Error al cargar el historial de ventas');
        });
}

// --- Lógica para el envío de formularios de los modales ---

formEditar.addEventListener('submit', (e) => {
    e.preventDefault();
    const datos = new FormData(formEditar);
    
    fetch("../../backend/api/controllers/gestionProducto.php?action=editar", {
        method: 'POST', // Usamos POST para enviar los datos, ya que PUT tiene un manejo más complejo en PHP
        body: datos,
    })
    .then(res => res.json())
    .then(response => {
        if (response.success) {
            cargarProductos();
            modalEditar.hide();
        } else {
            alert('Error al actualizar: ' + response.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al actualizar el producto');
    });
});

formVender.addEventListener('submit', (e) => {
    e.preventDefault();
    const datos = new FormData(formVender);
    
    fetch("../../backend/api/controllers/gestionProducto.php?action=vender", {
        method: 'POST',
        body: datos,
    })
    .then(res => res.json())
    .then(response => {
        if (response.success) {
            cargarProductos();
            modalVender.hide();
        } else {
            alert('Error al vender: ' + response.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al vender el producto');
    });
});

function eliminarVenta(id_venta) {
  if (!confirm("¿Eliminar esta venta? Esta acción no se puede deshacer.")) return;
  fetch(`../../backend/api/controllers/gestionProducto.php?action=eliminar_venta`, {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({ id_venta: id_venta }),
  })
  .then(res => res.json())
  .then(response => {
    if (response.success) {
      alert("Venta eliminada correctamente.");
      cargarProductos();
      modalVerVentas.hide(); // Oculta el modal de historial
    } else {
      alert("Error al eliminar la venta: " + response.message);
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('Error al eliminar la venta');
  });
}


cargarProductos();