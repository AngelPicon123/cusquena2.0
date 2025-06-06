
      // VARIABLES GLOBALES

      let allData = [];       // Todos los datos sin filtrar
      let filteredData = [];  // Datos filtrados por fecha/búsqueda
      let currentPage = 1;    // Página actual
      const itemsPerPage = 5; // Registros por página
      
      // FUNCIONES PRINCIPALES    
      async function init() {
        try {
          allData = await obtenerDatosCotizaciones();
          filteredData = [...allData]; // Copia inicial sin filtrar
          
          actualizarVista();
          configurarEventListeners();
        } catch (error) {
          console.error("Error al cargar cotizaciones:", error);
          alert("Error al obtener cotizaciones: " + error.message);
        }
      }
        
      //Actualiza toda la vista (tabla, paginación y total)
      function actualizarVista() {
        mostrarDatosEnTabla(obtenerDatosPagina());
        updatePagination();
        mostrarTotalGeneral(filteredData);
      }
      
      // FUNCIONES DE DATOS
      async function obtenerDatosCotizaciones() {
        const response = await fetch('http://localhost/cusquena/backend/api/controllers/vistabalanceCotizaciones/obtenerCotizaciones.php');
        const result = await response.json();
        if (!result.exito) throw new Error(result.error || "Error al obtener datos");
        return result.data;
      }
      function obtenerDatosPagina() {
        const start = (currentPage - 1) * itemsPerPage;
        return filteredData.slice(start, start + itemsPerPage);
      }
  
       // Muestra datos en la tabla HTML
      function mostrarDatosEnTabla(datos) {
        const tbody = document.getElementById('tablaCotizaciones');
        tbody.innerHTML = datos.length ? 
          datos.map(c => `
            <tr>
              <td>${c.id}</td>
              <td>${c.nombre}</td>
              <td>${c.apellido}</td>
              <td>${c.idTipoConductor}</td>
              <td>${c.total}</td>
              <td>${c.placa}</td>
              <td>${c.fechaCotizacion}</td>
            </tr>
          `).join('') : '<tr><td colspan="7">No hay datos disponibles</td></tr>';
      }
      
      /**
       * Calcula y muestra el total general
       */
      function mostrarTotalGeneral(datos) {
        const totalInput = document.getElementById('totalGeneral');
        const sumaTotal = datos.reduce((total, c) => {
          const monto = parseFloat(c.total.toString().replace(/[^\d.-]/g, '')) || 0;
          return total + monto;
        }, 0);
        totalInput.value = `S/ ${sumaTotal.toFixed(2)}`;
      }
      
      /**
       * Actualiza la paginación
       */
      function updatePagination() {
        const pagination = document.getElementById('pagination');
        const totalPages = Math.ceil(filteredData.length / itemsPerPage);
        
        // Limpiar paginación (excepto botones anterior/siguiente)
        const pageItems = [...pagination.querySelectorAll('.page-item')];
        pageItems.forEach(item => {
          if (!item.querySelector('#prev-page') && !item.querySelector('#next-page')) {
            item.remove();
          }
        });
        
        // Insertar números de página
        const nextBtn = document.getElementById('next-page').parentElement;
        for (let i = 1; i <= totalPages; i++) {
          const pageItem = document.createElement('li');
          pageItem.className = `page-item ${i === currentPage ? 'active' : ''}`;
          pageItem.innerHTML = `<a class="page-link" href="#">${i}</a>`;
          nextBtn.before(pageItem);
        }
        
        // Deshabilitar botones si es necesario
        document.getElementById('prev-page').parentElement.classList.toggle('disabled', currentPage === 1);
        document.getElementById('next-page').parentElement.classList.toggle('disabled', currentPage === totalPages);
      }
      
      // EVENT LISTENERS Y MANEJADORES
      
      function configurarEventListeners() {
        const pagination = document.getElementById('pagination');
        
        // Paginación
        pagination.addEventListener('click', (e) => {
          e.preventDefault();
          const target = e.target.closest('a');
          if (!target) return;
          
          if (target.id === 'prev-page' && currentPage > 1) {
            currentPage--;
          } else if (target.id === 'next-page' && currentPage < Math.ceil(filteredData.length / itemsPerPage)) {
            currentPage++;
          } else if (target.textContent && !isNaN(target.textContent)) {
            currentPage = parseInt(target.textContent);
          }
          
          actualizarVista();
        });
        // Buscador por fechas (implementación básica)
        document.getElementById('btnBuscar').addEventListener('click', () => {
          const fechaInicio = document.getElementById('fechaInicio').value;
          const fechaFin = document.getElementById('fechaFin').value;
          
          filteredData = allData.filter(c => {
            const fechaCotiz = new Date(c.fechaCotizacion);
            const inicio = fechaInicio ? new Date(fechaInicio) : null;
            const fin = fechaFin ? new Date(fechaFin) : null;
            if (fin) fin.setHours(23, 59, 59, 999); // Incluir todo el día
            
            return (
              (!inicio || fechaCotiz >= inicio) &&
              (!fin || fechaCotiz <= fin)
            );
          });
          
          currentPage = 1; // Resetear a primera página
          actualizarVista();
        });
      }

// FILTRADO POR FECHAS (FUNCIONES INDEPENDIENTES)
function configurarBuscadorFechas() {
  const btnBuscar = document.getElementById('btnBuscar');
  const btnReset = document.getElementById('btnReset');
  const fechaInicioInput = document.getElementById('fechaInicio');
  const fechaFinInput = document.getElementById('fechaFin');

  // Evento para el botón Buscar
  btnBuscar.addEventListener('click', () => {
    aplicarFiltroFechas();
  });

  // Evento para el botón Resetear
  btnReset.addEventListener('click', () => {
    resetearFiltroFechas();
  });

}

 // Aplica el filtro por fechas y actualiza la vista

function aplicarFiltroFechas() {
  const fechaInicio = document.getElementById('fechaInicio').value;
  const fechaFin = document.getElementById('fechaFin').value;

  // Validación básica de fechas
  if (fechaInicio && fechaFin && new Date(fechaInicio) > new Date(fechaFin)) {
    alert('La fecha de inicio no puede ser mayor a la fecha final');
    return;
  }

  // Filtrar los datos
  filteredData = filtrarDatosPorFechas(allData, fechaInicio, fechaFin);
  
  // Reiniciar a la primera página y actualizar vista
  currentPage = 1;
  actualizarVista();
  
  // Opcional: Mostrar mensaje con cantidad de resultados
  mostrarMensajeResultados();
}

//Función pura para filtrar datos por rango de fechas
function filtrarDatosPorFechas(datos, fechaInicio, fechaFin) {
  if (!fechaInicio && !fechaFin) return [...datos]; // No hay filtro

  return datos.filter(item => {
    const fechaItem = new Date(item.fechaCotizacion);
    const inicio = fechaInicio ? new Date(fechaInicio) : null;
    const fin = fechaFin ? new Date(fechaFin) : null;

    // Ajustar para incluir todo el día de la fecha final
    if (fin) fin.setHours(23, 59, 59, 999);

    return (
      (!inicio || fechaItem >= inicio) &&
      (!fin || fechaItem <= fin)
    );
  });
}


//Resetea el filtro de fechas
function resetearFiltroFechas() {
  document.getElementById('fechaInicio').value = '';
  document.getElementById('fechaFin').value = '';
  
  filteredData = [...allData]; // Restaurar datos sin filtrar
  currentPage = 1;
  actualizarVista();
}


 // Muestra mensaje con cantidad de resultados (opcional)
function mostrarMensajeResultados() {
  const mensaje = `Mostrando ${filteredData.length} de ${allData.length} registros`;
  console.log(mensaje); // Puedes reemplazar esto con un toast o alerta visual
}

// Inicializar el buscador al cargar la página
document.addEventListener('DOMContentLoaded', configurarBuscadorFechas);
      // INICIALIZACIÓN
      document.addEventListener('DOMContentLoaded', init);



//FECHAS DOMINICALES///////////////////////////////////////////////

// Configuración inicial de eventos
function configurarFiltroDomingos() {
  const btnFiltrarDomingos = document.getElementById('btnFiltrarDomingos');
  const btnResetFiltros = document.getElementById('btnResetFiltros');

  if (btnFiltrarDomingos) {
    btnFiltrarDomingos.addEventListener('click', filtrarDomingos);
  } else {
    console.error('Botón btnFiltrarDomingos no encontrado');
  }

  if (btnResetFiltros) {
    btnResetFiltros.addEventListener('click', resetearFiltros);
  }
}

// Función mejorada para filtrar domingos
function filtrarDomingos() {
  if (!allData || allData.length === 0) {
    mostrarMensaje('No hay datos disponibles para filtrar');
    return;
  }

  // Limpiar otros filtros
  document.getElementById('fechaInicio').value = '';
  document.getElementById('fechaFin').value = '';
  
  // Filtrar domingos con validación robusta
  filteredData = allData.filter(item => {
    if (!item.fechaCotizacion) return false;
    
    try {
      const [year, month, day] = item.fechaCotizacion.split('-').map(Number);
      const date = new Date(year, month - 1, day);
      
      // Validar fecha y día domingo (0)
      return !isNaN(date.getTime()) && date.getDay() === 0;
    } catch (error) {
      console.error('Error al procesar fecha:', item.fechaCotizacion, error);
      return false;
    }
  });

  // Actualizar vista
  currentPage = 1;
  actualizarVista();
  
  // Feedback al usuario
  if (filteredData.length === 0) {
    mostrarMensaje('No se encontraron registros para domingos');
  } else {
    console.log('Domingos encontrados:', filteredData.map(i => i.fechaCotizacion));
    mostrarMensaje(`Mostrando ${filteredData.length} registros de domingos`);
  }
}

// Función para resetear filtros
function resetearFiltros() {
  // Limpiar inputs de fecha
  document.getElementById('fechaInicio').value = '';
  document.getElementById('fechaFin').value = '';
  
  // Restaurar datos sin filtrar
  filteredData = [...allData];
  
  // Actualizar vista
  currentPage = 1;
  actualizarVista();
  
  // Feedback visual
  mostrarMensaje('Mostrando todos los registros');
}

// Función para mostrar mensajes (mejorada)
function mostrarMensaje(mensaje) {
  // Opción 1: Console log para depuración
  console.log(mensaje);
  
  // Opción 2: Toast de Bootstrap (asegúrate de tener el CSS)
  const toastContainer = document.createElement('div');
  toastContainer.innerHTML = `
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
      <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header bg-primary text-white">
          <strong class="me-auto">Sistema</strong>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
          ${mensaje}
        </div>
      </div>
    </div>
  `;
  
  // Eliminar toasts anteriores
  document.querySelectorAll('.toast-container').forEach(el => el.remove());
  
  // Agregar nuevo toast
  document.body.appendChild(toastContainer.firstChild);
  
  // Auto-ocultar después de 3 segundos
  setTimeout(() => {
    const toast = document.querySelector('.toast');
    if (toast) toast.classList.remove('show');
  }, 3000);
}

// Inicializar al cargar la página
document.addEventListener('DOMContentLoaded', configurarFiltroDomingos);