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
    
    $query =        "SELECT 
                    s.*, 
                    c.dni AS dni_conductor, 
                    c.nombre AS nombre_conductor, 
                    c.apellido AS apellido_conductor, 
                    c.telefono AS telefono_conductor, 
                    c.placa AS placa_conductor 
                    FROM soat s
                    JOIN conductor c ON s.idConductor = c.idConductor
                    WHERE s.idSoat = ? LIMIT 1";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) throw new Exception("Error al preparar consulta: " . $conn->error);
    
    $stmt->bind_param("i", $idSoat);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("SOAT no encontrado");
    }
    
    echo json_encode($result->fetch_assoc());
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(["exito" => false, "error" => $e->getMessage()]);
} finally {
    $conn->close();
}
?>