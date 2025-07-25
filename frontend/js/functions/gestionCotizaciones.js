

document.addEventListener('DOMContentLoaded', () => {
    // Corrected API path from your PHP comments in listar.php (assuming this is the correct structure)
    const API_BASE_URL = 'http://localhost/cusquena/backend/api/controllers/vista_gestion_cotizaciones/';

    const tablaCotizaciones = document.getElementById('tablaCotizaciones');
    const formAgregar = document.getElementById('formAgregarCotizacion');
    const formEditar = document.getElementById('formEditarCotizacion');
    const modalAgregar = new bootstrap.Modal(document.getElementById('modalAgregar'));
    const modalEditar = new bootstrap.Modal(document.getElementById('modalEditar'));
    const modalEliminarConfirmacion = new bootstrap.Modal(document.getElementById('modalEliminarConfirmacion'));
    const btnConfirmarEliminar = document.getElementById('btnConfirmarEliminar');
    const totalGeneralPago = document.getElementById('totalGeneral'); 

    
    const toastSuccess = new bootstrap.Toast(document.getElementById('toastSuccess'));
    const toastError = new bootstrap.Toast(document.getElementById('toastError'));
    const toastSuccessBody = document.getElementById('toastSuccessBody');
    const toastErrorBody = document.getElementById('toastErrorBody');

    let cotizacionIdToDelete = null; 

    
    function showToast(type, message) {
        if (type === 'success') {
            toastSuccessBody.textContent = message;
            toastSuccess.show();
        } else if (type === 'error') {
            toastErrorBody.textContent = message;
            toastError.show();
        }
    }

    async function cargarCotizaciones() {
        try {
            const response = await fetch(`${API_BASE_URL}listar.php`);
            const data = await response.json();
            renderizarTabla(data.cotizaciones || []);

       
            if (data.totalGlobal !== undefined) {
                totalGeneralPago.textContent = `Total General: S/. ${parseFloat(data.totalGlobal).toFixed(2)}`;
            } else {
                totalGeneralPago.textContent = 'Total General: S/. 0.00';
            }
          
        } catch (error) {
            console.error('Error al cargar cotizaciones:', error);
            showToast('error', 'Error al cargar las cotizaciones. Inténtalo de nuevo.');
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

            const btnEliminar = document.createElement('button');
            btnEliminar.className = 'btn btn-danger btn-sm';
            btnEliminar.textContent = 'Eliminar';
            btnEliminar.onclick = () => {
                cotizacionIdToDelete = c.id;
                modalEliminarConfirmacion.show();
            };
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
                cargarCotizaciones();
                showToast('success', '✅Cotización registrada exitosamente.');
            } else {
                showToast('error', data.error || 'Error al registrar la cotización.');
            }
        } catch (error) {
            console.error('Error al registrar:', error);
            showToast('error', 'Error de conexión al intentar registrar la cotización.');
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
                showToast('success', '✅ Cotización actualizada exitosamente.');
            } else {
                showToast('error', data.error || 'Error al actualizar la cotización.');
            }
        } catch (error) {
            console.error('Error al actualizar:', error);
            showToast('error', 'Error de conexión al intentar actualizar la cotización.');
        }
    });

    btnConfirmarEliminar.addEventListener('click', async () => {
        if (cotizacionIdToDelete) {
            try {
                const response = await fetch(`${API_BASE_URL}eliminar.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: cotizacionIdToDelete })
                });

                const data = await response.json();
                if (data.success) {
                    modalEliminarConfirmacion.hide();
                    cargarCotizaciones();
                    showToast('success', '✅ Cotización eliminada exitosamente.');
                } else {
                    showToast('error', data.error || 'Error al eliminar la cotización.');
                }
            } catch (error) {
                console.error('Error al eliminar:', error);
                showToast('error', 'Error de conexión al intentar eliminar la cotización.');
            } finally {
                cotizacionIdToDelete = null;
            }
        }
    });

    cargarCotizaciones();

   
    const filterFechaInicio = document.getElementById('filterFechaInicio');
    const filterFechaFin = document.getElementById('filterFechaFin');
    const filterNombre = document.getElementById('filterNombre');
    const btnBuscarCotizaciones = document.getElementById('btnBuscarCotizaciones');

    btnBuscarCotizaciones.addEventListener('click', async () => {
        const fechaInicio = filterFechaInicio.value;
        const fechaFin = filterFechaFin.value;
        const nombreApellido = filterNombre.value.trim();

        let url = `${API_BASE_URL}listar.php?`;
        const params = [];

        if (fechaInicio) {
            params.push(`fecha_inicio=${fechaInicio}`);
        }
        if (fechaFin) {
            params.push(`fecha_fin=${fechaFin}`);
        }
        if (nombreApellido) {
            params.push(`nombre_apellido=${encodeURIComponent(nombreApellido)}`);
        }

        url += params.join('&');

        try {
            const response = await fetch(url);
            const data = await response.json();
            renderizarTabla(data.cotizaciones || []);
            
            if (data.totalGlobal !== undefined) {
                totalGeneralPago.textContent = `Total General: S/. ${parseFloat(data.totalGlobal).toFixed(2)}`;
            } else {
                totalGeneralPago.textContent = 'Total General: S/. 0.00';
            }
           
        } catch (error) {
            console.error('Error al buscar cotizaciones:', error);
            showToast('error', 'Error al buscar cotizaciones. Inténtalo de nuevo.');
        }
    });

  
    document.getElementById('btnPrintTable').addEventListener('click', () => {
        const tableToPrint = document.getElementById('cotizacionesTable').outerHTML;
        const printWindow = window.open('', '', 'height=600,width=800');
        printWindow.document.write('<html><head><title>Cotizaciones</title>');
        printWindow.document.write('<link href="../css/bootstrap.css" rel="stylesheet">');
        printWindow.document.write('<style>');
        printWindow.document.write(`
            body { font-family: sans-serif; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #f2f2f2; }
            .no-print { display: none; }
        `);
        printWindow.document.write('</style></head><body>');
        printWindow.document.write('<h1 style="text-align: center;">Reporte de Cotizaciones</h1>');
        printWindow.document.write(tableToPrint);
        printWindow.document.write(`<p style="text-align: right; margin-top: 20px;">Total General: ${totalGeneralPago.textContent.replace('Total General: ', '')}</p>`);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.print();
    });

    
    document.getElementById('btnExportPdf').addEventListener('click', () => {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();

        doc.text("Reporte de Cotizaciones", 14, 16);

        const table = document.getElementById('cotizacionesTable');
        const rows = Array.from(table.querySelectorAll('tr'));
        const headers = Array.from(table.querySelectorAll('thead th')).map(th => th.innerText);

        const data = rows.slice(1).map(row => {
            const cells = Array.from(row.querySelectorAll('td'));
            
           
            
            return cells.slice(0, -1).map(cell => cell.innerText);
        });

       
        const columns = headers.slice(0, -1); 

        doc.autoTable({
            head: [columns],
            body: data,
            startY: 25,
            theme: 'striped',
            styles: { fontSize: 8, cellPadding: 2, overflow: 'linebreak' },
            headStyles: { fillColor: [33, 37, 41] },
            columnStyles: {
                
                
            }
        });


        doc.text(totalGeneralPago.textContent, 14, doc.autoTable.previous.finalY + 10);
        doc.save("cotizaciones.pdf");
    });
});