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

    $query = "SELECT nombre, fechaMantenimiento, fechaProxMantenimiento FROM soat WHERE idConductor = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $idConductor);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $soat = $result->fetch_assoc();
        echo json_encode([
            "exito" => true,
            "existe" => true,
            "nombre" => $soat['nombre'],
            "fechaMantenimiento" => $soat['fechaMantenimiento'],
            "fechaProxMantenimiento" => $soat['fechaProxMantenimiento']
        ]);
    } else {
        echo json_encode([
            "exito" => true,
            "existe" => false
        ]);
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(["exito" => false, "error" => $e->getMessage()]);
} finally {
    $conn->close();
}
?>