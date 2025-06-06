<?php
header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "la_cusquena");
if ($conn->connect_error) {
    echo json_encode(["exito" => false, "error" => "Conexión fallida"]);
    exit();
}

try {
    $idSoat = $_GET['idSoat'] ?? null;
    if (!$idSoat) throw new Exception("ID de SOAT no proporcionado");
    
    $query = "DELETE FROM soat WHERE idSoat = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) throw new Exception("Error al preparar consulta: " . $conn->error);
    
    $stmt->bind_param("i", $idSoat);
    
    if ($stmt->execute()) {
        echo json_encode(["exito" => true, "mensaje" => "SOAT eliminado correctamente"]);
    } else {
        throw new Exception("Error al ejecutar: " . $stmt->error);
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(["exito" => false, "error" => $e->getMessage()]);
} finally {
    $conn->close();
}
?>