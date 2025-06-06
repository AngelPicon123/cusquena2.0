<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once '../../includes/db.php'; // Ajusta esta ruta si tu archivo está en otra carpeta

try {
    $inicio = isset($_GET['inicio']) ? $_GET['inicio'] : '';
    $fin = isset($_GET['fin']) ? $_GET['fin'] : '';

    if ($inicio && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $inicio)) {
        throw new Exception("Formato de fecha inicio inválido.");
    }
    if ($fin && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fin)) {
        throw new Exception("Formato de fecha fin inválido.");
    }
    if ($inicio && $fin && $inicio > $fin) {
        throw new Exception("La fecha inicio no puede ser mayor que la fecha fin.");
    }

    $where = '';
    $params = [];

    if ($inicio && $fin) {
        $where = "WHERE fecha BETWEEN :inicio AND :fin";
        $params[':inicio'] = $inicio;
        $params[':fin'] = $fin;
    }

    $sql = "SELECT descripcion, precioUnitario, cantidad, subtotal, fecha, total
            FROM ventaproducto
            $where
            ORDER BY fecha DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($result);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
