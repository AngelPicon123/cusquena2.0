<?php
header('Content-Type: application/json');

// Conexión MySQLi (ya la tienes)
$conn = new mysqli("localhost", "root", "", "la_cusquena");

// Verificar conexión
if ($conn->connect_error) {
    die(json_encode(['error' => 'Error de conexión: ' . $conn->connect_error]));
}

try {
    $idConductor = $_GET['idConductor'] ?? null;
    
    if (!$idConductor) {
        throw new Exception("ID de conductor no proporcionado");
    }

    // Consulta usando MySQLi
    $query = "SELECT placa, nombre, apellido, estado, dni, telefono FROM Conductor WHERE idConductor = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $idConductor); // "i" = integer
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    if (!$data) {
        throw new Exception("Conductor no encontrado");
    }

    echo json_encode($data);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    $conn->close();
}
?>