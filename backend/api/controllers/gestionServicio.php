<?php
// gestionServicio.php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Content-Type: application/json");

$pdo = new PDO("mysql:host=localhost;dbname=la_cusquena", "root", "");

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
  case 'GET':
    // Verificamos si viene un parámetro ?modo=activos
    if (isset($_GET['modo']) && $_GET['modo'] === 'activos') {
      $stmt = $pdo->query("SELECT idServicio, descripcion, precioUnitario FROM servicio WHERE estado = 'activo'");
    } else {
      $stmt = $pdo->query("SELECT * FROM servicio");
    }
    $servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($servicios);
    break;

  case 'POST':
    $data = json_decode(file_get_contents("php://input"), true);
    $sql = "INSERT INTO servicio (descripcion, precioUnitario, estado) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $success = $stmt->execute([$data['descripcion'], $data['precioUnitario'], $data['estado']]);
    echo json_encode(['success' => $success]);
    break;

  case 'PUT':
    $data = json_decode(file_get_contents("php://input"), true);
    $sql = "UPDATE servicio SET descripcion=?, precioUnitario=?, estado=? WHERE idServicio=?";
    $stmt = $pdo->prepare($sql);
    $success = $stmt->execute([$data['descripcion'], $data['precioUnitario'], $data['estado'], $data['idServicio']]);
    echo json_encode(['success' => $success]);
    break;

  case 'DELETE':
    parse_str(file_get_contents("php://input"), $data);
    $sql = "DELETE FROM servicio WHERE idServicio=?";
    $stmt = $pdo->prepare($sql);
    $success = $stmt->execute([$data['idServicio']]);
    echo json_encode(['success' => $success]);
    break;

  default:
    echo json_encode(['error' => 'Método no soportado']);
    break;
}
?>