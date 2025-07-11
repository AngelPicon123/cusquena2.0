<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit(0);
header('Content-Type: application/json');

require_once '../../includes/db.php'; // Asegúrate de que esta ruta a tu conexión DB es correcta.

if ($conn === null) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Error de conexión a la base de datos"]);
    exit;
}

$accion = $_REQUEST['accion'] ?? '';

switch ($accion) {
    case 'listar':
        listarCategorias($conn);
        break;
    // Puedes añadir más acciones como registrar, modificar, eliminar categorías si lo necesitas.

    default:
        echo json_encode(["success" => false, "message" => "Acción no válida."]);
}

function listarCategorias($conn) {
    // Asumimos que tu tabla de categorías se llama 'categorias' y tiene al menos 'id' y 'nombre'
    $sql = "SELECT id, nombre FROM categorias ORDER BY nombre ASC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(["success" => true, "data" => $categorias]);
}
?>