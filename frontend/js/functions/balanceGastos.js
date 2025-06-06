document.addEventListener('DOMContentLoaded', () => {
  const btnBuscar      = document.getElementById('btnBuscar');
  const tablaDatos     = document.getElementById('tablaDatos');
  const totalInput     = document.getElementById('total');
  const btnExportarPdf = document.getElementById('exportarPdf');
  const paginacionNode = document.querySelector('.pagination');
  const registrosPorPagina = 10;

  async function cargarBalance(inicio = '', fin = '', pagina = 1) {
    try {
      // Construir URL con paginación y filtro
      let url = `../../backend/api/controllers/balancesGasto.php?pagina=${pagina}&limite=${registrosPorPagina}`;
      if (inicio && fin) {
        url += `&inicio=${encodeURIComponent(inicio)}&fin=${encodeURIComponent(fin)}`;
      }

      const resp = await fetch(url);
      const result = await resp.json();

      if (result.error) {
        alert('Error: ' + result.error);
        return;
      }

      // Limpiar tabla y acumular total
      tablaDatos.innerHTML = '';
      let total = 0;
      result.data.forEach(row => {
        const monto = parseFloat(row.monto);
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>${row.tipo}</td>
          <td>${isNaN(monto) ? '0.00' : monto.toFixed(2)}</td>
          <td>${row.fecha}</td>
        `;
        tablaDatos.appendChild(tr);
        if (!isNaN(monto)) total += monto;
      });
      totalInput.value = total.toFixed(2);

      // Renderizar controles de paginación
      renderizarPaginacion(result.total, result.pagina);

    } catch (error) {
      console.error('Error cargando balance:', error);
      alert('Error al cargar datos.');
    }
  }

  function renderizarPaginacion(totalRegistros, paginaActual) {
    const totalPaginas = Math.ceil(totalRegistros / registrosPorPagina);
    paginacionNode.innerHTML = '';

    const crearItem = (pagina, texto, activa = false, deshabilitada = false) => {
      const li = document.createElement('li');
      li.className = `page-item ${activa ? 'active' : ''} ${deshabilitada ? 'disabled' : ''}`;
      li.innerHTML = `<a class="page-link" href="#">${texto}</a>`;
      if (!deshabilitada) {
        li.addEventListener('click', e => {
          e.preventDefault();
          cargarBalance(
            document.getElementById('inicio').value,
            document.getElementById('fin').value,
            pagina
          );
        });
      }
      return li;
    };

    // Botón « Anterior
    paginacionNode.appendChild(
      crearItem(paginaActual - 1, '«', false, paginaActual === 1)
    );

    // Números de página
    for (let i = 1; i <= totalPaginas; i++) {
      paginacionNode.appendChild(
        crearItem(i, i, paginaActual === i)
      );
    }

    // Botón Siguiente »
    paginacionNode.appendChild(
      crearItem(paginaActual + 1, '»', false, paginaActual === totalPaginas)
    );
  }

  // Evento Buscar: reinicia en página 1
  btnBuscar.addEventListener('click', () => {
    const inicio = document.getElementById('inicio').value;
    const fin    = document.getElementById('fin').value;
    if ((inicio && !fin) || (!inicio && fin)) {
      return alert('Selecciona ambas fechas o déjalas vacías.');
    }
    if (inicio && fin && inicio > fin) {
      return alert('La fecha inicio no puede ser mayor que la fecha fin.');
    }
    cargarBalance(inicio, fin, 1);
  });

  // Carga inicial sin filtro y en página 1
  cargarBalance('', '', 1);

  // ——————————————
  // Resto de eventos (Exportar PDF, Imprimir) idénticos a los tuyos:
  // btnExportarPdf.addEventListener('click', …)
  // document.getElementById('btnImprimir').addEventListener('click', …)
  // ——————————————
});
