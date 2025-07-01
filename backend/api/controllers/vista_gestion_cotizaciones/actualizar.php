<?php
// C:\xampp\htdocs\cusquena\backend\api\controllers\vista_cotizaciones\actualizar.php

require_once __DIR__ . '/../../../includes/db.php';
require_once __DIR__ . '/../../../includes/auth.php';

header('Content-Type: application/json');

verificarPermiso(['Administrador', 'Secretaria']);

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id'], $data['nombre'], $data['apellido'], $data['tipoCotizacion'], $data['pago'], $data['fecha'], $data['estado'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Datos incompletos para actualizar la cotización.']);
    exit();
}

$id = (int)$data['id'];
$nombre = $data['nombre'];
$apellido = $data['apellido'];
$tipo_cotizacion = $data['tipoCotizacion'];
$pago = (float)$data['pago'];
$fecha = $data['fecha'];
$estado = $data['estado'];

// Calcular el día de la semana
$dia_semana_raw = date('N', strtotime($fecha)); // 1 (para Lunes) a 7 (para Domingo)
$dias_map = [
    1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'
];
$dia_semana = $dias_map[$dia_semana_raw];

// Validar enums
$allowed_tipos = ['servicios', 'producto'];
$allowed_estados = ['Pagada', 'Pendiente'];

if (!in_array($tipo_cotizacion, $allowed_tipos)) {
    http_response_code(400);
    echo json_encode(['error' => 'Tipo de cotización inválido.']);
    exit();
}
if (!in_array($estado, $allowed_estados)) {
    http_response_code(400);
    echo json_encode(['error' => 'Estado de cotización inválido.']);
    exit();
}

try {
    $stmt = $conn->prepare("UPDATE cotizaciones SET nombre = :nombre, apellido = :apellido, tipo_cotizacion = :tipo_cotizacion, pago = :pago, fecha = :fecha, dia_semana = :dia_semana, estado = :estado WHERE id = :id");
    if ($stmt === false) {
        throw new Exception("Error al preparar la consulta: " . implode(" ", $conn->errorInfo()));
    }

    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':apellido', $apellido);
    $stmt->bindParam(':tipo_cotizacion', $tipo_cotizacion);
    $stmt->bindParam(':pago', $pago, PDO::PARAM_STR); // PDO::PARAM_STR para DECIMAL
    $stmt->bindParam(':fecha', $fecha);
    $stmt->bindParam(':dia_semana', $dia_semana);
    $stmt->bindParam(':estado', $estado);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Cotización actualizada exitosamente!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Cotización no encontrada o no se realizaron cambios.']);
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
