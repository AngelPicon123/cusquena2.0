<?php
// C:\xampp\htdocs\cusquena\backend\api\controllers\vista_gastos_empresa\registrar.php

require_once __DIR__ . '/../../../includes/db.php';
require_once __DIR__ . '/../../../includes/auth.php';

header('Content-Type: application/json');

verificarPermiso(['Administrador']); // Solo administradores pueden registrar gastos

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['descripcion'], $data['tipoGasto'], $data['monto'], $data['fecha'], $data['detalle'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Datos incompletos para registrar el gasto.']);
    exit();
}

$descripcion = $data['descripcion'];
$tipo_gasto = $data['tipoGasto'];
$monto = (float)$data['monto'];
$fecha = $data['fecha'];
$detalle = $data['detalle'];

// Validar que el tipo de gasto sea uno de los permitidos por el ENUM
$allowed_types = ['operativo', 'administrativo', 'mantenimiento', 'otro'];
if (!in_array($tipo_gasto, $allowed_types)) {
    http_response_code(400);
    echo json_encode(['error' => 'Tipo de gasto inválido.']);
    exit();
}

try {
    $stmt = $conn->prepare("INSERT INTO gastos_empresa (descripcion, tipo_gasto, monto, fecha, detalle) VALUES (:descripcion, :tipo_gasto, :monto, :fecha, :detalle)");
    if ($stmt === false) {
        throw new Exception("Error al preparar la consulta: " . implode(" ", $conn->errorInfo()));
    }
    $stmt->bindParam(':descripcion', $descripcion);
    $stmt->bindParam(':tipo_gasto', $tipo_gasto);
    $stmt->bindParam(':monto', $monto, PDO::PARAM_STR); // PDO::PARAM_STR para DECIMAL
    $stmt->bindParam(':fecha', $fecha);
    $stmt->bindParam(':detalle', $detalle);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Gasto de empresa registrado exitosamente!', 'id' => $conn->lastInsertId()]);
    } else {
        throw new Exception("Error al ejecutar la consulta: " . implode(" ", $stmt->errorInfo()));
    }

    $stmt = null;
    $conn = null;

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
