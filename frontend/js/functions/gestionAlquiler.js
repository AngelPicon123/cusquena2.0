const apiUrl = 'http://localhost/cusquena/backend/api/controllers/gestionAlquiler.php';
  
// Agregar alquiler
document.querySelector('#modalAgregar form').addEventListener('submit', async function(e) {
  e.preventDefault();

  const data = {
    nombre: document.getElementById('nombre').value,
    tipo: document.getElementById('tipo').value,
    fechaInicio: document.getElementById('fechaInicio').value,
    periodicidad: document.getElementById('periodicidad').value,
    pago: document.getElementById('pago').value,
    estado: document.querySelector('input[name="estado"]:checked').value
  };

  const response = await fetch(apiUrl, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data)
  });

  const result = await response.json();
  alert(result.message);
  location.reload(); // Recarga tabla
});

// Lógica para cargar datos y mostrar en tabla
async function cargarAlquileres() {
  const res = await fetch(apiUrl);
  const data = await res.json();
  const tbody = document.querySelector('tbody');
  tbody.innerHTML = '';

  data.forEach(item => {
    const estadoBadge = item.estado === 'activo' ? 'success' : 'danger';
    const fila = `
      <tr>
        <td>${item.idAlquiler}</td>
        <td>${item.nombre}</td>
        <td>${item.tipo}</td>
        <td>${item.fechaInicio}</td>
        <td>${item.periodicidad}</td>
        <td>S/${item.pago}</td>
        <td><span class="badge bg-${estadoBadge}">${item.estado}</span></td>
        <td>
          <button class="btn btn-success p-1" onclick='editarAlquiler(${JSON.stringify(item)})' data-bs-toggle="modal" data-bs-target="#modalEditar">Editar</button>
          <button class="btn btn-danger p-1" onclick='eliminarAlquiler(${item.idAlquiler})'>Eliminar</button>
        </td>
      </tr>`;
    tbody.insertAdjacentHTML('beforeend', fila);
  });
}

// Eliminar alquiler
async function eliminarAlquiler(id) {
  if (!confirm('¿Seguro de eliminar este alquiler?')) return;

  const res = await fetch(apiUrl, {
    method: 'DELETE',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ idAlquiler: id })
  });

  const result = await res.json();
  alert(result.message);
  location.reload();
}

// Editar alquiler (cargar datos al modal)
function editarAlquiler(data) {
  document.getElementById('editarId').value = data.idAlquiler;
  document.getElementById('editarNombre').value = data.nombre;
  document.getElementById('editarTipo').value = data.tipo;
  document.getElementById('editarFechaInicio').value = data.fechaInicio;
  document.getElementById('editarPeriodicidad').value = data.periodicidad;
  document.getElementById('editarPago').value = data.pago;
  document.getElementById('editarEstadoActivo').checked = data.estado === 'activo';
  document.getElementById('editarEstadoInactivo').checked = data.estado === 'inactivo';
}

// Guardar edición
document.querySelector('#modalEditar form').addEventListener('submit', async function(e) {
  e.preventDefault();

  const data = {
    idAlquiler: document.getElementById('editarId').value,
    nombre: document.getElementById('editarNombre').value,
    tipo: document.getElementById('editarTipo').value,
    fechaInicio: document.getElementById('editarFechaInicio').value,
    periodicidad: document.getElementById('editarPeriodicidad').value,
    pago: document.getElementById('editarPago').value,
    estado: document.querySelector('input[name="editarEstado"]:checked').value
  };

  const response = await fetch(apiUrl, {
    method: 'PUT',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data)
  });

  const result = await response.json();
  alert(result.message);
  location.reload();
});

// Cargar lista al cargar página
window.addEventListener('DOMContentLoaded', cargarAlquileres);