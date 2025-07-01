<?php
// C:\xampp\htdocs\cusquena\backend\api\controllers\vista_gastos_empresa\actualizar.php

require_once __DIR__ . '/../../../includes/db.php';
require_once __DIR__ . '/../../../includes/auth.php';

header('Content-Type: application/json');

verificarPermiso(['Administrador']); // Solo administradores pueden actualizar gastos

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id'], $data['descripcion'], $data['tipoGasto'], $data['monto'], $data['fecha'], $data['detalle'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Datos incompletos para actualizar el gasto.']);
    exit();
}

$id = (int)$data['id'];
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
    $stmt = $conn->prepare("UPDATE gastos_empresa SET descripcion = :descripcion, tipo_gasto = :tipo_gasto, monto = :monto, fecha = :fecha, detalle = :detalle WHERE id = :id");
    if ($stmt === false) {
        throw new Exception("Error al preparar la consulta: " . implode(" ", $conn->errorInfo()));
    }
    $stmt->bindParam(':descripcion', $descripcion);
    $stmt->bindParam(':tipo_gasto', $tipo_gasto);
    $stmt->bindParam(':monto', $monto, PDO::PARAM_STR); // PDO::PARAM_STR para DECIMAL
    $stmt->bindParam(':fecha', $fecha);
    $stmt->bindParam(':detalle', $detalle);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Gasto de empresa actualizado exitosamente!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gasto de empresa no encontrado o no se realizaron cambios.']);
        }
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
