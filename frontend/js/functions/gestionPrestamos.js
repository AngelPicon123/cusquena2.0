
document.addEventListener('DOMContentLoaded', () => {
  const API_BASE_URL = 'http://localhost/cusquena/backend/api/controllers/vista_gestion_prestamos/';

  const tablaDeudas = document.getElementById('tablaDeudas');
  const formAgregar = document.getElementById('formAgregar');
  const formEditar = document.getElementById('formEditarDeuda');
  const modalAgregar = new bootstrap.Modal(document.getElementById('modalAgregar'));
  const modalEditar = new bootstrap.Modal(document.getElementById('modalEditarDeuda'));

  async function cargarPrestamos() {
    try {
      const response = await fetch(`${API_BASE_URL}listar.php`);
      const data = await response.json();
      renderizarTabla(data.prestamos || []);
    } catch (error) {
      console.error('Error al cargar préstamos:', error);
    }
  }

  function renderizarTabla(prestamos) {
    tablaDeudas.innerHTML = '';

    if (prestamos.length === 0) {
      tablaDeudas.innerHTML = '<tr><td colspan="7" class="text-center">No hay préstamos registrados.</td></tr>';
      return;
    }

    prestamos.forEach(p => {
      const row = tablaDeudas.insertRow();
      row.insertCell().textContent = p.nombre;
      row.insertCell().textContent = p.tipo_persona;
      row.insertCell().textContent = `S/. ${parseFloat(p.monto_deuda).toFixed(2)}`;
      row.insertCell().textContent = `S/. ${parseFloat(p.saldo_pendiente).toFixed(2)}`;
      row.insertCell().textContent = p.estado;
      row.insertCell().textContent = p.fecha_inicio_deuda;

      const acciones = row.insertCell();
      const btnEditar = document.createElement('button');
      btnEditar.className = 'btn btn-sm btn-warning me-1';
      btnEditar.textContent = 'Editar';
      btnEditar.onclick = () => llenarModalEditar(p);
      acciones.appendChild(btnEditar);

      const btnEliminar = document.createElement('button');
      btnEliminar.className = 'btn btn-sm btn-danger';
      btnEliminar.textContent = 'Eliminar';
    
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
        cargarPrestamos();
      } else {
        alert(data.error || 'Error al registrar.');
      }
    } catch (error) {
      console.error('Error al registrar:', error);
    }
  });

  function llenarModalEditar(p) {
    document.getElementById('nombre').value = p.nombre;
    document.getElementById('tipoPersona').value = p.tipo_persona;
    document.getElementById('montoDeuda').value = parseFloat(p.monto_deuda).toFixed(2);
    document.getElementById('saldoPendiente').value = parseFloat(p.saldo_pendiente).toFixed(2);
    document.getElementById('estado').value = p.estado;
    document.getElementById('fechaInicio').value = p.fecha_inicio_deuda;
  

    modalEditar.show();
  }

  formEditar.addEventListener('submit', async e => {
    e.preventDefault();
    const formData = new FormData(formEditar);
    const datos = Object.fromEntries(formData.entries());
   

    try {
      const response = await fetch(`${API_BASE_URL}actualizar.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(datos)
      });

      const data = await response.json();
      if (data.success) {
        modalEditar.hide();
        cargarPrestamos();
      } else {
        alert(data.error || 'Error al actualizar.');
      }
    } catch (error) {
      console.error('Error al actualizar:', error);
    }
  });

  cargarPrestamos();
});