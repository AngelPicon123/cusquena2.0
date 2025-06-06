<?php
header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "la_cusquena");
if ($conn->connect_error) {
    echo json_encode(["exito" => false, "error" => "Conexión fallida"]);
    exit();
}

// Consulta mejorada para incluir todos los datos necesarios
$sql = "SELECT idTipoConductor, tipo_paga, monto_paga, descripcion FROM tipo_de_conductor";
$result = $conn->query($sql);

if (!$result) {
    die(json_encode(["error" => "Error en la consulta: " . $conn->error]));
}

$tipos = [];
while ($row = $result->fetch_assoc()) {
    $tipos[] = [
        'id' => $row['idTipoConductor'],
        'nombre' => $row['idTipoConductor'],
        'tipo_paga' => $row['tipo_paga'],
        'monto_paga' => $row['monto_paga'],
        'descripcion' => $row['descripcion']
    ];
}

echo json_encode($tipos);
$conn->close();
?>
