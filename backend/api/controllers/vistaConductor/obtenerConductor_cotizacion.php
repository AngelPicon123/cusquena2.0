<?php
header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "la_cusquena");
if ($conn->connect_error) {
    echo json_encode(["exito" => false, "error" => "Conexión fallida"]);
    exit();
}

try {
    $idConductor = $_GET['idConductor'] ?? null;
    
    if (!$idConductor) {
        throw new Exception("ID de conductor no proporcionado");
    }

    $query = "SELECT nombre, apellido, placa, tipoLicencia ,idTipoConductor FROM conductor WHERE idConductor = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    if (!$stmt) throw new Exception("Error al preparar consulta: " . $conn->error);
    
    $stmt->bind_param("i", $idConductor);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Conductor no encontrado");
    }
    
    $data = $result->fetch_assoc();
    $data['exito'] = true;
    echo json_encode($data);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(["exito" => false, "error" => $e->getMessage()]);
} finally {
    $conn->close();
}
?>