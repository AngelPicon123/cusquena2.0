<?php
header("Content-Type: application/json");

// Configuración para mostrar errores (solo en desarrollo)
ini_set('display_errors', 0);
ini_set('log_errors', 1);

$conn = new mysqli("localhost", "root", "", "la_cusquena");

if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode([
        "exito" => false,
        "error" => "Error de conexión a la base de datos",
        "detalles" => $conn->connect_error
    ]));
}

try {
    // Leer y validar input JSON
    $json = file_get_contents('php://input');
    if (empty($json)) {
        throw new Exception("No se recibieron datos");
    }
    
    $input = json_decode($json, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("JSON inválido: " . json_last_error_msg());
    }

    // Validación de campos requeridos (sin apellido)
    $required = ['idConductor', 'fechaMantenimiento', 'fechaProxMantenimiento', 'nombre'];
    foreach ($required as $field) {
        if (!isset($input[$field]) || empty(trim($input[$field]))) {
            throw new Exception("El campo '$field' es requerido y no puede estar vacío");
        }
    }

    // Sanitizar inputs (sin apellido)
    $idConductor = intval($input['idConductor']);
    $nombre = $conn->real_escape_string(trim($input['nombre']));
    $fechaMantenimiento = $conn->real_escape_string(trim($input['fechaMantenimiento']));
    $fechaProxMantenimiento = $conn->real_escape_string(trim($input['fechaProxMantenimiento']));
    $estado = isset($input['estado']) && in_array($input['estado'], ['activo', 'inactivo']) 
               ? $input['estado'] 
               : 'activo';

    // Verificar si ya existe un SOAT para este conductor
    $checkQuery = "SELECT idSoat FROM soat WHERE idConductor = ? LIMIT 1";
    $checkStmt = $conn->prepare($checkQuery);
    if (!$checkStmt) {
        throw new Exception("Error al preparar consulta de verificación: " . $conn->error);
    }
    
    $checkStmt->bind_param("i", $idConductor);
    if (!$checkStmt->execute()) {
        throw new Exception("Error al ejecutar consulta de verificación: " . $checkStmt->error);
    }
    
    $checkStmt->store_result();
    $soatExiste = $checkStmt->num_rows > 0;
    $checkStmt->close();

    if ($soatExiste) {
        // Actualizar SOAT existente
        $query = "UPDATE soat SET 
                 nombre = ?, 
                 fechaMantenimiento = ?, 
                 fechaProxMantenimiento = ?, 
                 estado = ?
                 WHERE idConductor = ?";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Error al preparar actualización: " . $conn->error);
        }
        
        $stmt->bind_param("ssssi", $nombre, $fechaMantenimiento, $fechaProxMantenimiento, $estado, $idConductor);
        $accion = "actualizado";
    } else {
        // Insertar nuevo SOAT (sin apellido)
        $query = "INSERT INTO soat (
                 nombre, 
                 fechaMantenimiento, 
                 fechaProxMantenimiento, 
                 estado, 
                 idConductor
                 ) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Error al preparar inserción: " . $conn->error);
        }
        
        $stmt->bind_param("ssssi", $nombre, $fechaMantenimiento, $fechaProxMantenimiento, $estado, $idConductor);
        $accion = "registrado";
    }

    if (!$stmt->execute()) {
        throw new Exception("Error al ejecutar consulta: " . $stmt->error);
    }

    echo json_encode([
        "exito" => true,
        "mensaje" => "SOAT $accion correctamente",
        "accion" => $accion,
        "idSoat" => $soatExiste ? $idConductor : $conn->insert_id
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        "exito" => false,
        "error" => $e->getMessage()
    ]);
} finally {
    if (isset($conn)) $conn->close();
}
?>