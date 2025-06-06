<?php
include '../../includes/db.php';

$input = json_decode(file_get_contents("php://input"), true);
$fechaVenta = $input["fechaVenta"];
$total = $input["total"];

$sql = "INSERT INTO VentaServicio (fechaVenta, total) VALUES (?, ?)";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("sd", $fechaVenta, $total);

$response = [];
if ($stmt->execute()) {
    $response["success"] = true;
} else {
    $response["success"] = false;
    $response["error"] = $conexion->error;
}

echo json_encode($response);
