const API_CATEGORIA = 'http://localhost/cusquena/backend/api/controllers/gestionCategoria.php';
  
document.addEventListener('DOMContentLoaded', () => {
  listarCategorias();

  // Buscar categoría
  document.querySelector('.btn-primary').addEventListener('click', () => {
    const termino = document.querySelector('input[placeholder="Buscar categoria"]').value;
    listarCategorias(termino);
  });

  // Agregar categoría
  document.querySelector('#modalAgregar form').addEventListener('submit', (e) => {
    e.preventDefault();

    const data = {
      descripcion: document.getElementById('descripcion').value,
      estado: document.querySelector('input[name="estado"]:checked').value
    };

    fetch(API_CATEGORIA, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data)
    })
      .then(res => res.json())
      .then(res => {
        alert(res.message);
        listarCategorias();
        document.querySelector('#modalAgregar .btn-close').click();
      });
  });

  // Editar categoría
  document.querySelector('#modalEditar form').addEventListener('submit', (e) => {
    e.preventDefault();

    const data = {
      idCategoria: document.getElementById('editarId').value,
      descripcion: document.getElementById('editarDescripcion').value,
      estado: document.querySelector('input[name="editarEstado"]:checked').value
    };

    fetch(API_CATEGORIA, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data)
    })
      .then(res => res.json())
      .then(res => {
        alert(res.message);
        listarCategorias();
        document.querySelector('#modalEditar .btn-close').click();
      });
  });
});

// Listar Categorías
function listarCategorias(buscar = '') {
  const tbody = document.querySelector('tbody');
  tbody.innerHTML = '';

  const url = buscar ? `${API_CATEGORIA}?buscar=${encodeURIComponent(buscar)}` : API_CATEGORIA;

  fetch(url)
    .then(res => res.json())
    .then(data => {
      data.forEach(cat => {
        const fila = document.createElement('tr');
        fila.innerHTML = `
          <td>${cat.idCategoria}</td>
          <td>${cat.descripcion}</td>
          <td><span class="badge ${cat.estado === 'activo' ? 'bg-success' : 'bg-danger'}">${cat.estado}</span></td>
          <td>
            <button class="btn btn-success btn-sm" onclick='llenarModalEditar(${JSON.stringify(cat)})' data-bs-toggle="modal" data-bs-target="#modalEditar">Editar</button>
            <button class="btn btn-danger btn-sm" onclick="eliminarCategoria(${cat.idCategoria})">Eliminar</button>
          </td>
        `;
        tbody.appendChild(fila);
      });
    });
}

// Llenar Modal Editar
function llenarModalEditar(cat) {
  document.getElementById('editarId').value = cat.idCategoria;
  document.getElementById('editarDescripcion').value = cat.descripcion;
  document.getElementById('editarEstadoActivo').checked = cat.estado === 'activo';
  document.getElementById('editarEstadoInactivo').checked = cat.estado === 'inactivo';
}

// Eliminar Categoría
function eliminarCategoria(idCategoria) {
  if (confirm('¿Estás seguro de eliminar esta categoría?')) {
    fetch(API_CATEGORIA, {
      method: 'DELETE',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ idCategoria })
    })
      .then(res => res.json())
      .then(res => {
        alert(res.message);
        listarCategorias();
      });
  }
}