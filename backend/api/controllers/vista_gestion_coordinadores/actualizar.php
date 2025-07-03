<?php
require_once __DIR__ . '/../../../includes/db.php';
require_once __DIR__ . '/../../../includes/auth.php';

header('Content-Type: application/json');

verificarPermiso(['Administrador', 'Secretaria']);

$data = json_decode(file_get_contents('php://input'), true);

// Validar que todos los campos necesarios estén presentes, incluyendo 'contacto'
if (!isset($data['id'], $data['nombre'], $data['apellidos'], $data['paradero'], $data['monto_diario'], $data['fecha'], $data['estado'], $data['contacto'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Datos incompletos para actualizar.']);
    exit();
}

$id = $data['id'];
$nombre = $data['nombre'];
$apellidos = $data['apellidos'];
$paradero = $data['paradero'];
$monto_diario = (float)$data['monto_diario'];
$fecha = $data['fecha'];
$estado = $data['estado'];
$contacto = $data['contacto'];

try {
    $stmt = $conn->prepare("UPDATE coordinadores 
                            SET nombre = :nombre, apellidos = :apellidos, paradero = :paradero, monto_diario = :monto_diario, fecha = :fecha, estado = :estado, contacto = :contacto 
                            WHERE id = :id");

    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':apellidos', $apellidos);
    $stmt->bindParam(':paradero', $paradero);
    $stmt->bindParam(':monto_diario', $monto_diario, PDO::PARAM_STR);
    $stmt->bindParam(':fecha', $fecha);
    $stmt->bindParam(':estado', $estado);
    $stmt->bindParam(':contacto', $contacto);

    $stmt->execute();

    echo json_encode(['success' => true, 'message' => 'Coordinador actualizado correctamente.']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al actualizar: ' . $e->getMessage()]);
}
?>
