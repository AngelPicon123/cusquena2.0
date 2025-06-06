<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST");
header("Content-Type: application/json");

// Incluir archivo de conexión (ajusta la ruta si está en otra carpeta)
require_once __DIR__ . '/../../includes/db.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $inicio = isset($_GET['inicio']) ? $_GET['inicio'] : '';
    $fin = isset($_GET['fin']) ? $_GET['fin'] : '';

    if ($inicio && $fin) {
        $stmt = $conn->prepare("SELECT idVenta, idServicio, descripcion, precioUnitario, fechaVenta, total 
                                FROM venta_servicio 
                                WHERE fechaVenta BETWEEN ? AND ?");
        $stmt->execute([$inicio, $fin]);
    } else {
        $stmt = $conn->query("SELECT idVenta, idServicio, descripcion, precioUnitario, fechaVenta, total 
                              FROM venta_servicio");
    }

    $ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($ventas);

} elseif ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $sql = "INSERT INTO venta_servicio (idServicio, descripcion, precioUnitario, fechaVenta, total)
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $success = $stmt->execute([
        $data['idServicio'],
        $data['descripcion'],
        $data['precioUnitario'],
        $data['fechaVenta'],
        $data['total']
    ]);
    echo json_encode(['success' => $success]);

} else {
    echo json_encode(['error' => 'Método no soportado']);
}
