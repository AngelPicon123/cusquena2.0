<?php
header('Content-Type: application/json');
// Conexión a la base de datos
$conn = new mysqli("localhost", "root", "", "la_cusquena");

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$sql = "SELECT * FROM conductor";
$result = $conn->query($sql);

$conductores = [];

while ($row = $result->fetch_assoc()) {
    $conductores[] = $row;
}

echo json_encode($conductores);
$conn->close();
?>
