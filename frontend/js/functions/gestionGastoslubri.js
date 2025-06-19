document.addEventListener("DOMContentLoaded", function () {
  const tablaBody = document.querySelector("tbody");
  const buscarBtn = document.querySelector(".btn-primary[href='#']");
  const inputs = document.querySelectorAll("input");
  const selectTipo = document.getElementById("tipo");

  // Función para cargar los gastos
  function cargarGastos(filtro = {}) {
      fetch("../../backend/api/controllers/gestionGastos.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ accion: "listar", ...filtro })
      })
      .then(res => res.json())
      .then(data => {
          tablaBody.innerHTML = "";
          data.forEach(gastos => {
              const fila = document.createElement("tr");
              fila.innerHTML = `
              <td>${gastos.id}</td>
              <td>${gastos.descripcion}</td>
              <td>${gastos.tipo}</td>
              <td>S/ ${gastos.monto}</td>
              <td>${gastos.fecha}</td>
              <td>${gastos.detalle}</td>
              <td>
                  <a href="#" class="btn btn-success btnEditar p-1" data-id="${gastos.id}">Editar</a>
                  <a href="#" class="btn btn-danger btnEliminar p-1" data-id="${gastos.id}">Eliminar</a>
              </td>`;
              tablaBody.appendChild(fila);
          });
      });
  }

  // Agregar gasto
  document.querySelector("#modalAgregar form").addEventListener("submit", function (e) {
      e.preventDefault();
      const data = {
          accion: "agregar",
          descripcion: document.getElementById("descripcion").value,
          tipo: document.getElementById("tipo").value,
          monto: document.getElementById("monto").value,
          fecha: document.getElementById("fecha").value,
          detalle: document.getElementById("detalle").value,
      };
      fetch("../../backend/api/controllers/gestionGastos.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(data)
      })
      .then(res => res.json())
      .then(() => {
          cargarGastos();
          e.target.reset();
          bootstrap.Modal.getInstance(document.getElementById("modalAgregar")).hide();
          mostrarToast("Agregar");  // Muestra el toast de éxito al agregar el gasto
      });
  });

  // Editar gasto
  tablaBody.addEventListener("click", function (e) {
      if (e.target.classList.contains("btnEditar")) {
          const id = e.target.dataset.id;
          fetch("../../backend/api/controllers/gestionGastos.php", {
              method: "POST",
              headers: { "Content-Type": "application/json" },
              body: JSON.stringify({ accion: "obtener", id })
          })
          .then(res => res.json())
          .then(data => {
              document.getElementById("editarId").value = data.id;
              document.getElementById("editardescripcion").value = data.descripcion;
              document.getElementById("editartipo").value = data.tipo;
              document.getElementById("editarmonto").value = data.monto;
              document.getElementById("editarfecha").value = data.fecha;
              document.getElementById("editardetalle").value = data.detalle;
              new bootstrap.Modal(document.getElementById("modalEditar")).show();
          });
      }

      // Eliminar gasto
      if (e.target.classList.contains("btnEliminar")) {
          const id = e.target.dataset.id;
          const modalEliminar = new bootstrap.Modal(document.getElementById("modalEliminarConfirmacion"));
          modalEliminar.show();

          document.getElementById("btnConfirmarEliminar").addEventListener("click", function () {
              fetch("../../backend/api/controllers/gestionGastos.php", {
                  method: "POST",
                  headers: { "Content-Type": "application/json" },
                  body: JSON.stringify({ accion: "eliminar", id })
              })
              .then(res => res.json())
              .then(() => {
                  cargarGastos();
                  mostrarToast("Eliminar");  // Muestra el toast de éxito al eliminar el gasto
                  modalEliminar.hide();
              });
          });
      }
  });

  // Editar el gasto después de la confirmación
  document.querySelector("#modalEditar form").addEventListener("submit", function (e) {
      e.preventDefault();
      const data = {
          accion: "editar",
          id: document.getElementById("editarId").value,
          descripcion: document.getElementById("editardescripcion").value,
          tipo: document.getElementById("editartipo").value,
          monto: document.getElementById("editarmonto").value,
          fecha: document.getElementById("editarfecha").value,
          detalle: document.getElementById("editardetalle").value,
      };
      fetch("../../backend/api/controllers/gestionGastos.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(data)
      })
      .then(res => res.json())
      .then(() => {
          cargarGastos();
          bootstrap.Modal.getInstance(document.getElementById("modalEditar")).hide();
          mostrarToast("Editar");  // Muestra el toast de éxito al editar el gasto
      });
  });

  // Buscar gastos por fecha y descripción
  buscarBtn.addEventListener("click", function () {
      const filtro = {
          accion: "buscar",
          descripcion: inputs[2].value.trim(),
          inicio: inputs[0].value,
          fin: inputs[1].value
      };
      cargarGastos(filtro);
  });

  // Cargar gastos inicialmente
  cargarGastos();

  // Función para mostrar el toast según la acción
  function mostrarToast(accion) {
      let toastId;
      let toastMessage;
      let toastTitle;

      switch (accion) {
          case "Agregar":
              toastId = "toastAgregar";
              toastTitle = "Gasto Agregado";
              toastMessage = "El gasto se agregó correctamente.";
              break;
          case "Editar":
              toastId = "toastEditar";
              toastTitle = "Gasto Editado";
              toastMessage = "El gasto se actualizó correctamente.";
              break;
          case "Eliminar":
              toastId = "toastEliminar";
              toastTitle = "Eliminación Exitosa";
              toastMessage = "El gasto fue eliminado exitosamente.";
              break;
          default:
              toastId = "toastError";
              toastTitle = "Error";
              toastMessage = "Ocurrió un error al procesar la solicitud.";
              break;
      }

      // Verificar si los elementos del toast existen antes de mostrarlos
      const toastTitleElement = document.getElementById(`toastTitle${accion}`);
      const toastMessageElement = document.getElementById(`toastMessage${accion}`);

      if (toastTitleElement && toastMessageElement) {
          toastTitleElement.textContent = toastTitle;
          toastMessageElement.textContent = toastMessage;

          const toastEl = document.getElementById(toastId);
          const toast = new bootstrap.Toast(toastEl);
          toast.show();
      } else {
          console.error(`Elemento de toast con ID ${toastId} no encontrado`);
      }
  }
});
