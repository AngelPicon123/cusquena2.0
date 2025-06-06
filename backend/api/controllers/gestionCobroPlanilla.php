<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');
include '../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

if ($conn === null) {
    echo json_encode(["error" => "No se pudo establecer la conexión a la base de datos."]);
    exit();
}

// LISTAR o BUSCAR SEGUROS DE PLANILLA
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $query = "SELECT * FROM SeguroPlanilla";
    $conditions = [];
    $params = [];

    if (isset($_GET['socio']) && !empty($_GET['socio'])) {
        $conditions[] = "socio LIKE :socio";
        $params[':socio'] = "%" . $_GET['socio'] . "%";
    }

    if (isset($_GET['inicio']) && !empty($_GET['inicio']) && isset($_GET['fin']) && !empty($_GET['fin'])) {
        $conditions[] = "fechaEmision >= :inicio AND fechaVencimiento <= :fin";
        $params[':inicio'] = $_GET['inicio'];
        $params[':fin'] = $_GET['fin'];
    } elseif (isset($_GET['inicio']) && !empty($_GET['inicio'])) {
        $conditions[] = "fechaEmision >= :inicio";
        $params[':inicio'] = $_GET['inicio'];
    } elseif (isset($_GET['fin']) && !empty($_GET['fin'])) {
        $conditions[] = "fechaVencimiento <= :fin";
        $params[':fin'] = $_GET['fin'];
    }

    if (!empty($conditions)) {
        $query .= " WHERE " . implode(" AND ", $conditions);
        $stmt = $conn->prepare($query);
        $stmt->execute($params);
    } else {
        $stmt = $conn->query($query);
    }

    $seguros = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($seguros);
}

// AGREGAR SEGURO DE PLANILLA
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $socio = $data['socio'];
    $montoTotal = $data['montoTotal'];
    $totalPagado = $data['totalPagado'];
    $pagoPendiente = $data['pagoPendiente'];
    $fechaEmision = $data['fechaEmision'];
    $fechaVencimiento = $data['fechaVencimiento'];
    $estado = $data['estado'];

    $stmt = $conn->prepare("INSERT INTO SeguroPlanilla (socio, montoTotal, totalPagado, pagoPendiente, fechaEmision, fechaVencimiento, estado) VALUES (:socio, :montoTotal, :totalPagado, :pagoPendiente, :fechaEmision, :fechaVencimiento, :estado)");
    $stmt->execute([
        'socio' => $socio,
        'montoTotal' => $montoTotal,
        'totalPagado' => $totalPagado,
        'pagoPendiente' => $pagoPendiente,
        'fechaEmision' => $fechaEmision,
        'fechaVencimiento' => $fechaVencimiento,
        'estado' => $estado
    ]);

    echo json_encode(["message" => "Seguro de planilla agregado correctamente"]);
}

// ACTUALIZAR SEGURO DE PLANILLA
if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);

    $idSeguroPlanilla = $data['idSeguroPlanilla'];
    $socio = $data['socio'];
    $montoTotal = $data['montoTotal'];
    $totalPagado = $data['totalPagado'];
    $pagoPendiente = $data['pagoPendiente'];
    $fechaEmision = $data['fechaEmision'];
    $fechaVencimiento = $data['fechaVencimiento'];
    $estado = $data['estado'];

    $stmt = $conn->prepare("UPDATE SeguroPlanilla SET socio = :socio, montoTotal = :montoTotal, totalPagado = :totalPagado, pagoPendiente = :pagoPendiente, fechaEmision = :fechaEmision, fechaVencimiento = :fechaVencimiento, estado = :estado WHERE idSeguroPlanilla = :idSeguroPlanilla");
    $stmt->execute([
        'idSeguroPlanilla' => $idSeguroPlanilla,
        'socio' => $socio,
        'montoTotal' => $montoTotal,
        'totalPagado' => $totalPagado,
        'pagoPendiente' => $pagoPendiente,
        'fechaEmision' => $fechaEmision,
        'fechaVencimiento' => $fechaVencimiento,
        'estado' => $estado
    ]);

    echo json_encode(["message" => "Seguro de planilla actualizado correctamente"]);
}

// ELIMINAR SEGURO DE PLANILLA

if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['id'])) {
        echo json_encode(["error" => "ID no proporcionado"]);
        exit();
    }

    $stmt = $conn->prepare("DELETE FROM SeguroPlanilla WHERE idSeguroPlanilla = :id");
    $stmt->bindParam(':id', $data['id'], PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Seguro de planilla eliminado correctamente"]);
    } else {
        echo json_encode(["error" => "Error al eliminar el seguro de planilla"]);
    }
    exit();
}

?>