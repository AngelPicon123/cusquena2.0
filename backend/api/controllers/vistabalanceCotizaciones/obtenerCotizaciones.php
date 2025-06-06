
<?php
header('Content-Type: application/json');
// Conexión a la base de datos
$conn = new mysqli("localhost", "root", "", "la_cusquena");

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$sql = "SELECT 
            idCotizacion AS id,
            nombre,
            apellido,
            idTipoConductor,
            total,
            placa,
            fechaCotizacion
        FROM cotizacion";

$result = $conn->query($sql);

$cotizaciones = [];

while ($row = $result->fetch_assoc()) {
    $cotizaciones[] = $row;
}

echo json_encode([
    "exito" => true,
    "data" => $cotizaciones
]);

$conn->close();
?>
