<?php
header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "la_cusquena");
if ($conn->connect_error) {
    echo json_encode(["exito" => false, "error" => "Conexión fallida"]);
    exit();
}

try {
    $search = $_GET['search'] ?? '';
    
    $query = "SELECT 
                    s.*, 
                    c.dni AS dni_conductor, 
                    c.nombre AS nombre_conductor, 
                    c.apellido AS apellido_conductor, 
                    c.telefono AS telefono_conductor, 
                    c.placa AS placa_conductor 
                FROM soat s
                JOIN conductor c ON s.idConductor = c.idConductor
                WHERE c.nombre LIKE ? 
                    OR c.apellido LIKE ? 
                    OR c.dni LIKE ? 
                    OR c.placa LIKE ? 
                    OR s.nombre LIKE ?
                ORDER BY s.fechaProxMantenimiento DESC";


    $stmt = $conn->prepare($query);
    if (!$stmt) throw new Exception("Error al preparar consulta: " . $conn->error);
    
    $searchTerm = "%$search%";
    $stmt->bind_param("sssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $soats = [];
    while ($row = $result->fetch_assoc()) {
        $soats[] = $row;
    }
    
    echo json_encode($soats);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(["exito" => false, "error" => $e->getMessage()]);
} finally {
    $conn->close();
}
?>