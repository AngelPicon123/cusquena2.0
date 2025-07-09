// gestionDominical.js

// Espera a que el DOM esté completamente cargado

document.addEventListener('DOMContentLoaded', () => {
  const API_BASE_URL = 'http://localhost/cusquena/backend/api/controllers/vista_gestion_dominical/';

  const tablaDominical = document.getElementById('tablaDominical');
  const formAgregar = document.getElementById('formAgregarDominical');
  const formEditar = document.getElementById('formEditarDominical');
  const modalAgregar = new bootstrap.Modal(document.getElementById('modalAgregarDominical'));
  const modalEditar = new bootstrap.Modal(document.getElementById('modalEditarDominical'));

  async function cargarDominicales() {
    try {
      const response = await fetch(`${API_BASE_URL}listar.php`);
      const data = await response.json();
      renderizarTabla(data.dominicales);
    } catch (error) {
      console.error('Error al cargar datos dominicales:', error);
    }
  }

  function renderizarTabla(lista) {
    tablaDominical.innerHTML = '';

    if (!lista || lista.length === 0) {
      tablaDominical.innerHTML = '<tr><td colspan="9" class="text-center">No hay datos registrados.</td></tr>';
      return;
    }

    lista.forEach(d => {
      const row = tablaDominical.insertRow();
      row.insertCell().textContent = d.nombre;
      row.insertCell().textContent = d.apellidos;
      row.insertCell().textContent = d.fecha_domingo;
      row.insertCell().textContent = d.semana_inicio;
      row.insertCell().textContent = d.semana_fin;
      row.insertCell().textContent = `S/. ${parseFloat(d.monto_dominical).toFixed(2)}`;
      row.insertCell().textContent = d.estado;
      row.insertCell().textContent = parseFloat(d.diferencia).toFixed(2);

      const acciones = row.insertCell();
      const btnEditar = document.createElement('button');
      btnEditar.className = 'btn btn-warning btn-sm me-1';
      btnEditar.textContent = 'Editar';
      btnEditar.onclick = () => llenarModalEditar(d);
      acciones.appendChild(btnEditar);

      const btnEliminar = document.createElement('button');
      btnEliminar.className = 'btn btn-danger btn-sm';
      btnEliminar.textContent = 'Eliminar';
      btnEliminar.onclick = () => eliminarDominical(d.id);
      acciones.appendChild(btnEliminar);
    });
  }

  formAgregar.addEventListener('submit', async e => {
    e.preventDefault();
    const formData = new FormData(formAgregar);
    const datos = Object.fromEntries(formData.entries());

    try {
      const response = await fetch(`${API_BASE_URL}registrar.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(datos)
      });
      const data = await response.json();

      if (data.success) {
        modalAgregar.hide();
        formAgregar.reset();
        cargarDominicales();
      } else {
        alert(data.error || 'Error al registrar.');
      }
    } catch (error) {
      console.error('Error al registrar:', error);
    }
  });

  function llenarModalEditar(d) {
    document.getElementById('editDominicalId').value = d.id;
    document.getElementById('edit_nombre').value = d.nombre;
    document.getElementById('edit_apellidos').value = d.apellidos;
    document.getElementById('edit_fechaDomingo').value = d.fecha_domingo;
    document.getElementById('edit_semanaInicio').value = d.semana_inicio;
    document.getElementById('edit_semanaFin').value = d.semana_fin;
    document.getElementById('edit_montoDominical').value = parseFloat(d.monto_dominical).toFixed(2);
    document.getElementById('edit_estado').value = d.estado;
    document.getElementById('edit_diferencia').value = parseFloat(d.diferencia).toFixed(2);

    modalEditar.show();
  }

  formEditar.addEventListener('submit', async e => {
    e.preventDefault();
    const formData = new FormData(formEditar);
    const datos = Object.fromEntries(formData.entries());
    datos.id = document.getElementById('editDominicalId').value;

    try {
      const response = await fetch(`${API_BASE_URL}actualizar.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(datos)
      });
      const data = await response.json();

      if (data.success) {
        modalEditar.hide();
        cargarDominicales();
      } else {
        alert(data.error || 'Error al actualizar.');
      }
    } catch (error) {
      console.error('Error al actualizar:', error);
    }
  });

  async function eliminarDominical(id) {
    if (!confirm('¿Estás seguro de eliminar este registro?')) return;

    try {
      const response = await fetch(`${API_BASE_URL}eliminar.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id })
      });
      const data = await response.json();
      if (data.success) cargarDominicales();
      else alert(data.error || 'Error al eliminar.');
    } catch (error) {
      console.error('Error al eliminar:', error);
    }
  }

  cargarDominicales();
});