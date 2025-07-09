<?php
require_once __DIR__ . '/../../../includes/db.php';
require_once __DIR__ . '/../../../includes/auth.php';

header('Content-Type: application/json');
verificarPermiso(['Administrador', 'Secretaria']);

$nombre_apellido = $_GET['nombre'] ?? '';
$semana_inicio = $_GET['fecha_inicio'] ?? '';
$semana_fin = $_GET['fecha_fin'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$offset = ($page - 1) * $limit;

try {
    $conditions = [];
    $params = [];

    // Filtro por nombre o apellidos
    if (!empty($nombre_apellido)) {
        $conditions[] = "(nombre LIKE ? OR apellidos LIKE ?)";
        $params[] = '%' . $nombre_apellido . '%';
        $params[] = '%' . $nombre_apellido . '%';
    }

    // Filtro por fechas
    if (!empty($semana_inicio)) {
        $conditions[] = "semana_inicio >= ?";
        $params[] = $semana_inicio;
    }

    if (!empty($semana_fin)) {
        $conditions[] = "semana_fin <= ?";
        $params[] = $semana_fin;
    }

    $whereClause = "WHERE 1=1";
    if (!empty($conditions)) {
        $whereClause .= " AND " . implode(" AND ", $conditions);
    }

    // Total registros
    $sqlTotal = "SELECT COUNT(*) FROM dominical $whereClause";
    $stmtTotal = $conn->prepare($sqlTotal);
    $stmtTotal->execute($params);
    $totalRecords = $stmtTotal->fetchColumn();
    $stmtTotal = null;

    // Total monto dominical
    $sqlMontoTotal = "SELECT SUM(monto_dominical) FROM dominical $whereClause";
    $stmtMonto = $conn->prepare($sqlMontoTotal);
    $stmtMonto->execute($params);
    $totalGeneralMonto = $stmtMonto->fetchColumn();
    $totalGeneralMonto = $totalGeneralMonto !== null ? (float)$totalGeneralMonto : 0.00;
    $stmtMonto = null;

    // Datos paginados
    $sql = "SELECT id, nombre, apellidos, fecha_domingo, semana_inicio, semana_fin, monto_dominical, estado, diferencia
            FROM dominical 
            $whereClause 
            ORDER BY fecha_domingo DESC 
            LIMIT ? OFFSET ?";
    
    $stmt = $conn->prepare($sql);

    $paramIndex = 1;
    foreach ($params as $param) {
        $stmt->bindValue($paramIndex++, $param);
    }
    $stmt->bindValue($paramIndex++, $limit, PDO::PARAM_INT);
    $stmt->bindValue($paramIndex++, $offset, PDO::PARAM_INT);

    $stmt->execute();
    $dominicales = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'dominicales' => $dominicales,
        'total' => $totalRecords,
        'total_general_monto' => $totalGeneralMonto
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al listar dominicales: ' . $e->getMessage()]);
}
