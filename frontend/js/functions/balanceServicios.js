document.addEventListener('DOMContentLoaded', function () {
  // Función para obtener las ventas
  function obtenerVentas(inicio, fin) {
    const url = `../../backend/api/controllers/ventaServicio.php?inicio=${inicio}&fin=${fin}`;

    fetch(url)
      .then(response => response.json())
      .then(data => {
        const tbody = document.querySelector('tbody');
        const totalInput = document.querySelector('input[type="text"]');
        let total = 0;

        tbody.innerHTML = ''; // Limpiar contenido previo

        data.forEach(registro => {
          const precio = parseFloat(registro.precioUnitario) || 0;
          const subtotal = parseFloat(registro.total) || 0; // <-- Aquí usamos "total"
          const fecha = registro.fechaVenta || 'Sin fecha'; // <-- Aquí usamos "fechaVenta"

          const row = document.createElement('tr');
          row.innerHTML = ` 
            <td>${registro.idVenta}</td>
            <td>${registro.descripcion || 'Sin descripción'}</td>
            <td>S/ ${precio.toFixed(2)}</td>
            <td>S/ ${subtotal.toFixed(2)}</td>
            <td>${fecha}</td>
          `;
          total += subtotal;
          tbody.appendChild(row);
        });

        // Actualizamos el valor de totalInput con el valor calculado
        totalInput.value = `S/ ${total.toFixed(2)}`;
      })
      .catch(error => {
        console.error('Error al obtener datos de venta_servicio:', error);
      });
  }

  // Evento para el botón de buscar
  document.querySelector(".btn-primary").addEventListener("click", function () {
    const inicio = document.getElementById("inicio").value;
    const fin = document.getElementById("fin").value;
    obtenerVentas(inicio, fin);
  });

  // Obtener ventas inicialmente (sin filtro)
  obtenerVentas('', '');

  // Función para exportar la tabla a PDF
  document.getElementById("exportarPdf").addEventListener("click", function () {
    console.log('Botón de exportar PDF clickeado');
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    const table = document.querySelector('table');
    const rows = Array.from(table.querySelectorAll('tr'));
    let y = 20; // Posición vertical inicial para el texto

    // Encabezado de la tabla (los nombres de las columnas)
    const headers = ['ID', 'Descripción', 'Precio', 'Subtotal', 'Fecha'];
    headers.forEach((header, index) => {
      doc.text(header, 10 + (index * 40), y);
    });
    y += 10; // Espacio debajo del encabezado

    // Filas de la tabla
    rows.forEach((row, rowIndex) => {
      if (rowIndex === 0) return; // Ignorar la fila de encabezado (si la hay)
      const cols = Array.from(row.querySelectorAll('td'));
      let x = 10; // Posición horizontal inicial

      cols.forEach((col, colIndex) => {
        doc.text(col.textContent, x, y);
        x += 40; // Espacio entre columnas
      });
      y += 10; // Espacio entre filas
    });

    // Obtener el total
    const totalInput = document.querySelector('input[type="text"]');
    if (totalInput) {
      // Agregar el total al PDF
      doc.text(`TOTAL: ${totalInput.value}`, 10, y + 10);
    }

    // Descargar el PDF
    doc.save('balance_servicios.pdf');
  });
});
