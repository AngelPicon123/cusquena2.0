document.addEventListener("DOMContentLoaded", () => {
  const tablaDatos     = document.getElementById("tablaDatos");
  const inputTotal     = document.getElementById("total");
  const btnBuscar      = document.getElementById("btnBuscar");
  const btnExportarPdf = document.getElementById("exportarPdf");
  const btnImprimir    = document.getElementById("btnImprimir");
  const paginacionNode = document.querySelector(".pagination");

  const registrosPorPagina = 10;

  // Carga los datos con paginación y filtros
  async function cargarBalance(inicio = "", fin = "", pagina = 1) {
    try {
      let url = `../../backend/controllers/balanceProductoDetalle.php?pagina=${pagina}&limite=${registrosPorPagina}`;
      if (inicio && fin) {
        url += `&inicio=${encodeURIComponent(inicio)}&fin=${encodeURIComponent(fin)}`;
      }

      const resp = await fetch(url);
      const result = await resp.json();

      if (result.error) {
        alert("Error: " + result.error);
        return;
      }

      // Limpia tabla y calcula total
      tablaDatos.innerHTML = "";
      let total = 0;

      result.data.forEach(item => {
        const tr = document.createElement("tr");
        tr.innerHTML = `
          <td>${item.descripcion}</td>
          <td>S/ ${parseFloat(item.precioUnitario).toFixed(2)}</td>
          <td>${item.cantidad}</td>
          <td>S/ ${parseFloat(item.subtotal).toFixed(2)}</td>
          <td>${item.fecha}</td>
        `;
        tablaDatos.appendChild(tr);
        total += parseFloat(item.total);
      });

      inputTotal.value = `S/ ${total.toFixed(2)}`;

      // Renderiza paginación según total de registros
      renderizarPaginacion(result.total, result.pagina);

    } catch (error) {
      console.error("Error al cargar el balance:", error);
      alert("Ocurrió un error al obtener los datos del balance.");
    }
  }

  // Construye los botones de paginación
  function renderizarPaginacion(totalRegistros, paginaActual) {
    const totalPaginas = Math.ceil(totalRegistros / registrosPorPagina);
    paginacionNode.innerHTML = "";

    const crearItem = (pagina, texto, activa = false, deshabilitada = false) => {
      const li = document.createElement("li");
      li.className = `page-item ${activa ? "active" : ""} ${deshabilitada ? "disabled" : ""}`;
      const a = document.createElement("a");
      a.className = "page-link";
      a.href = "#";
      a.textContent = texto;
      a.addEventListener("click", e => {
        e.preventDefault();
        if (!deshabilitada && pagina !== paginaActual) {
          cargarBalance(
            document.getElementById("inicio").value,
            document.getElementById("fin").value,
            pagina
          );
        }
      });
      li.appendChild(a);
      return li;
    };

    // « Anterior
    paginacionNode.appendChild(
      crearItem(paginaActual - 1, "«", false, paginaActual === 1)
    );

    // Números de página
    for (let i = 1; i <= totalPaginas; i++) {
      paginacionNode.appendChild(crearItem(i, i, i === paginaActual));
    }

    // Siguiente »
    paginacionNode.appendChild(
      crearItem(paginaActual + 1, "»", false, paginaActual === totalPaginas)
    );
  }

  // Exportar a PDF
  btnExportarPdf.addEventListener("click", () => {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();
    doc.text("Balance de Productos", 14, 20);

    // Extrae filas de la tabla
    const filas = [];
    tablaDatos.querySelectorAll("tr").forEach(tr => {
      const cols = Array.from(tr.querySelectorAll("td")).map(td => td.textContent);
      filas.push(cols);
    });

    doc.autoTable({
      head: [["Descripción", "Precio Unitario", "Cantidad", "Subtotal", "Fecha"]],
      body: filas,
      startY: 30
    });

    doc.text(`TOTAL: ${inputTotal.value}`, 14, doc.lastAutoTable.finalY + 10);
    doc.save("balance_productos.pdf");
  });

  // Imprimir tabla
  btnImprimir.addEventListener("click", () => {
    const tabla = document.querySelector("table");
    if (!tabla) {
      alert("No hay datos para imprimir");
      return;
    }
    const ventana = window.open("", "", "width=800,height=600");
    ventana.document.write(`
      <html>
        <head>
          <title>Imprimir Balance de Productos</title>
          <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th, td { border: 1px solid black; padding: 8px; text-align: center; }
            th { background-color: #333; color: white; }
            .total { margin-top: 20px; font-size: 16px; font-weight: bold; }
          </style>
        </head>
        <body>
          <h2>Balance de Productos</h2>
          ${tabla.outerHTML}
          <div class="total">TOTAL: ${inputTotal.value}</div>
          <script>
            window.onload = () => { window.print(); window.onafterprint = ()=>window.close(); }
          </script>
        </body>
      </html>
    `);
  });

  // Validaciones y evento Buscar
  btnBuscar.addEventListener("click", () => {
    const inicio = document.getElementById("inicio").value;
    const fin    = document.getElementById("fin").value;
    if ((inicio && !fin) || (!inicio && fin)) {
      return alert("Selecciona ambas fechas o déjalas vacías.");
    }
    if (inicio && fin && inicio > fin) {
      return alert("La fecha de inicio no puede ser mayor que la fecha de fin.");
    }
    cargarBalance(inicio, fin, 1);
  });

  // Carga inicial sin filtro, página 1
  cargarBalance("", "", 1);
});
