const API_URL_SEGURO_PLANILLA =
  "http://localhost/cusquena/backend/api/controllers/gestionCobroPlanilla.php";

document.addEventListener("DOMContentLoaded", function () {
  listarSegurosPlanilla();

  // Buscar seguro de planilla por socio o rango de fechas
  document.querySelector(".btn-primary").addEventListener("click", function () {
    const inicio = document.getElementById("inicio").value;
    const fin = document.getElementById("fin").value;
    const socio = document.querySelector('input[placeholder="Buscar Socio"]').value;
    listarSegurosPlanilla(socio, inicio, fin);
  });

  // Agregar seguro de planilla
  document
    .querySelector("#miModal form")
    .addEventListener("submit", function (e) {
      e.preventDefault();

      const data = {
        socio: document.getElementById("socio").value,
        montoTotal: document.getElementById("montoTotal").value,
        totalPagado: document.getElementById("totalPagado").value,
        pagoPendiente: document.getElementById("pagoPendiente").value,
        fechaEmision: document.getElementById("fechaEmision").value,
        fechaVencimiento: document.getElementById("fechaVencimiento").value,
        estado: document.getElementById("estado").value,
      };

      fetch(API_URL_SEGURO_PLANILLA, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data),
      })
        .then((res) => res.json())
        .then((res) => {
          showToastAgregar(res.message);
          listarSegurosPlanilla();
          document.querySelector("#miModal .btn-close").click();
          document.querySelector("#miModal form").reset();
        });
    });

  // Editar seguro de planilla
  document
    .querySelector("#modalEditar form")
    .addEventListener("submit", function (e) {
      e.preventDefault();

      const idSeguroPlanilla = document.getElementById("editarId").value;
      const data = {
        idSeguroPlanilla,
        socio: document.getElementById("editarSocio").value,
        montoTotal: document.getElementById("editarMontoTotal").value,
        totalPagado: document.getElementById("editarTotalPagado").value,
        pagoPendiente: document.getElementById("editarPagoPendiente").value,
        fechaEmision: document.getElementById("editarFechaEmision").value,
        fechaVencimiento: document.getElementById("editarFechaVencimiento").value,
        estado: document.getElementById("editarEstado").value,
      };

      fetch(API_URL_SEGURO_PLANILLA, {
        method: "PUT",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data),
      })
        .then((res) => res.json())
        .then((res) => {
          showToastEditar(res.message);
          listarSegurosPlanilla();
          document.querySelector("#modalEditar .btn-close").click();
        });
    });
});

// Función para listar seguros de planilla
function listarSegurosPlanilla(buscarSocio = "", inicio = "", fin = "") {
  const tbody = document.querySelector(".table-responsive tbody");
  tbody.innerHTML = "";

  let url = API_URL_SEGURO_PLANILLA;
  const params = new URLSearchParams();
  if (buscarSocio) {
    params.append("socio", buscarSocio);
  }
  if (inicio) {
    params.append("inicio", inicio);
  }
  if (fin) {
    params.append("fin", fin);
  }

  if (params.toString()) {
    url += `?${params.toString()}`;
  }

  fetch(url)
    .then((res) => res.json())
    .then((data) => {
      data.forEach((seguro) => {
        const fila = document.createElement("tr");
        fila.innerHTML = `
          <td>${seguro.idSeguroPlanilla}</td>
          <td>${seguro.socio}</td>
          <td>${seguro.montoTotal}</td>
          <td>${seguro.totalPagado}</td>
          <td>${seguro.pagoPendiente}</td>
          <td>${seguro.fechaEmision}</td>
          <td>${seguro.fechaVencimiento}</td>
          <td><span class="badge ${
            seguro.estado === "Pendiente" ? "bg-warning" : "bg-success"
          }">${seguro.estado}</span></td>
          <td>
            <button class="btn btn-success btn-sm mb-1" onclick='llenarModalEditarSeguroPlanilla(${JSON.stringify(
              seguro
            )})' data-bs-toggle="modal" data-bs-target="#modalEditar">Editar</button>
            <button class="btn btn-danger btn-sm" onclick="confirmarEliminacionSeguroPlanilla(${
              seguro.idSeguroPlanilla
            })">Eliminar</button>
          </td>
        `;
        tbody.appendChild(fila);
      });
    });
}

// Llenar modal de edición de seguro de planilla
function llenarModalEditarSeguroPlanilla(seguro) {
  document.getElementById("editarId").value = seguro.idSeguroPlanilla;
  document.getElementById("editarSocio").value = seguro.socio;
  document.getElementById("editarMontoTotal").value = seguro.montoTotal;
  document.getElementById("editarTotalPagado").value = seguro.totalPagado;
  document.getElementById("editarPagoPendiente").value = seguro.pagoPendiente;
  document.getElementById("editarFechaEmision").value = seguro.fechaEmision;
  document.getElementById("editarFechaVencimiento").value = seguro.fechaVencimiento;
  document.getElementById("editarEstado").value = seguro.estado;
}

// Eliminar seguro de planilla
function eliminarSeguroPlanilla(id) {
  fetch(API_URL_SEGURO_PLANILLA, {
    method: "DELETE",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ id }),
  })
    .then((res) => res.json())
    .then((res) => {
      showToastEliminar(res.message);
      listarSegurosPlanilla();
    })
    .catch((error) => {
      console.error("Error al eliminar:", error);
    });
}

// toast message Agregar
function showToastAgregar(message) {
  const toastElement = document.getElementById("toastAgregar");
  const toastMessage = document.getElementById("toastMessageAgregar");

  toastMessage.textContent = message;

  const toast = new bootstrap.Toast(toastElement);
  toast.show();
}

// toast message Editar
function showToastEditar(message) {
  const toastElement = document.getElementById("toastEditar");
  const toastMessage = document.getElementById("toastMessageEditar");

  toastMessage.textContent = message;

  const toast = new bootstrap.Toast(toastElement);
  toast.show();
}

// toast message Eliminar
function showToastEliminar(message) {
  const toastElement = document.getElementById("toastEliminar");
  const toastMessage = document.getElementById("toastMessageEliminar");

  toastMessage.textContent = message;

  const toast = new bootstrap.Toast(toastElement);
  toast.show();
}

// ventana modal de confirmacion para eliminar seguro de planilla
function confirmarEliminacionSeguroPlanilla(id) {
  const modalElement = document.getElementById("modalEliminarConfirmacion");
  const modal = new bootstrap.Modal(modalElement);
  modal.show();

  const btnConfirmar = document.getElementById("btnConfirmarEliminar");

  // Elimina cualquier evento anterior para evitar múltiples ejecuciones
  const nuevoBoton = btnConfirmar.cloneNode(true);
  btnConfirmar.parentNode.replaceChild(nuevoBoton, btnConfirmar);

  // Botón "Sí, eliminar"
  nuevoBoton.addEventListener("click", () => {
    eliminarSeguroPlanilla(id); // Ejecuta la eliminación
    modal.hide(); // Cierra el modal
  });
}