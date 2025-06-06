<?php
include("conexion.php"); // tu archivo de conexión a base de datos

if (isset($_GET['q'])) {
  $q = $_GET['q'];
  $sql = "SELECT * FROM conductor WHERE nombre LIKE ? OR apellido LIKE ? OR dni LIKE ? OR placa LIKE ?";
  $stmt = $conn->prepare($sql);
  $search = "%$q%";
  $stmt->bind_param("ssss", $search, $search, $search, $search);
  $stmt->execute();
  $resultado = $stmt->get_result();

  if ($resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
      echo "
        <div class='list-group-item'>
          <strong>{$fila['nombre']} {$fila['apellido']}</strong><br>
          DNI: {$fila['dni']}<br>
          Teléfono: {$fila['telefono']}<br>
          Placa: {$fila['placa']}<br>
          Estado: <span class='badge bg-".($fila['estado'] == 'activo' ? 'success' : 'secondary')."'>{$fila['estado']}</span>
        </div>
      ";
    }
  } else {
    echo "<div class='text-muted'>No se encontraron resultados</div>";
  }
}
?>
