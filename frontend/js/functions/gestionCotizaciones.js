document.addEventListener('DOMContentLoaded', () => {
  const API_BASE_URL = 'http://localhost/cusquena/backend/api/controllers/vista_gestion_cotizaciones/';

  const tablaCotizaciones = document.getElementById('tablaCotizaciones');
  const formAgregar = document.getElementById('formAgregarCotizacion');
  const formEditar = document.getElementById('formEditarCotizacion');
  const modalAgregar = new bootstrap.Modal(document.getElementById('modalAgregar'));
  const modalEditar = new bootstrap.Modal(document.getElementById('modalEditar'));
  const totalGeneralPago = document.getElementById('totalGeneral');

  async function cargarCotizaciones() {
    try {
      const response = await fetch(`${API_BASE_URL}listar.php`);
      const data = await response.json();
      renderizarTabla(data.cotizaciones || []);

      if (data.total_general_pago !== undefined) {
        totalGeneralPago.textContent = `Total General: S/. ${parseFloat(data.total_general_pago).toFixed(2)}`;
      } else {
        totalGeneralPago.textContent = 'Total General: S/. 0.00';
      }
    } catch (error) {
      console.error('Error al cargar cotizaciones:', error);
    }
  }

  function renderizarTabla(cotizaciones) {
    tablaCotizaciones.innerHTML = '';

    if (cotizaciones.length === 0) {
      tablaCotizaciones.innerHTML = '<tr><td colspan="8" class="text-center">No hay cotizaciones registradas.</td></tr>';
      return;
    }

    cotizaciones.forEach(c => {
      const row = tablaCotizaciones.insertRow();
      row.insertCell().textContent = c.nombre;
      row.insertCell().textContent = c.apellido;
      row.insertCell().textContent = c.tipo_cotizacion;
      row.insertCell().textContent = `S/. ${parseFloat(c.pago).toFixed(2)}`;
      row.insertCell().textContent = c.fecha_inicio;
      row.insertCell().textContent = c.fecha_fin;
      row.insertCell().textContent = c.estado;

      const acciones = row.insertCell();
      const btnEditar = document.createElement('button');
      btnEditar.className = 'btn btn-warning btn-sm me-1';
      btnEditar.textContent = 'Editar';
      btnEditar.onclick = () => llenarModalEditar(c);
      acciones.appendChild(btnEditar);
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
        cargarCotizaciones();
      } else {
        alert(data.error || 'Error al registrar.');
      }
    } catch (error) {
      console.error('Error al registrar:', error);
    }
  });

  function llenarModalEditar(c) {
    document.getElementById('editCotizacionId').value = c.id;
    document.getElementById('editNombre').value = c.nombre;
    document.getElementById('editApellido').value = c.apellido;
    document.getElementById('editTipoCotizacion').value = c.tipo_cotizacion;
    document.getElementById('editPago').value = parseFloat(c.pago).toFixed(2);
    document.getElementById('editFechaInicio').value = c.fecha_inicio;
    document.getElementById('editFechaFin').value = c.fecha_fin;
    document.getElementById('editEstado').value = c.estado;

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
        cargarCotizaciones();
      } else {
        alert(data.error || 'Error al actualizar.');
      }
    } catch (error) {
      console.error('Error al actualizar:', error);
    }
  });

  cargarCotizaciones();
});