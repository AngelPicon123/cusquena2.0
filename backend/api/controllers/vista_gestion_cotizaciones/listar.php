<?php
// C:\xampp\htdocs\cusquena\backend\api\controllers\vista_cotizaciones\listar.php

require_once __DIR__ . '/../../../includes/db.php';
require_once __DIR__ . '/../../../includes/auth.php';

header('Content-Type: application/json');

verificarPermiso(['Administrador', 'Secretaria']);

$nombre_apellido = $_GET['nombre'] ?? ''; // Usaremos un solo parámetro para buscar en nombre o apellido
$fecha_inicio = $_GET['fecha_inicio'] ?? '';
$fecha_fin = $_GET['fecha_fin'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$offset = ($page - 1) * $limit;

try {
    $conditions = [];
    $params = [];

    // Filtro por nombre o apellido
    if (!empty($nombre_apellido)) {
        $conditions[] = "(nombre LIKE ? OR apellido LIKE ?)";
        $params[] = "%" . $nombre_apellido . "%";
        $params[] = "%" . $nombre_apellido . "%";
    }
    if (!empty($fecha_inicio)) {
        $conditions[] = "fecha >= ?";
        $params[] = $fecha_inicio;
    }
    if (!empty($fecha_fin)) {
        $conditions[] = "fecha <= ?";
        $params[] = $fecha_fin;
    }

    $whereClause = "WHERE 1=1";
    if (!empty($conditions)) {
        $whereClause .= " AND " . implode(" AND ", $conditions);
    }

    // --- Obtener el total de registros para paginación ---
    $sqlTotal = "SELECT COUNT(*) FROM cotizaciones " . $whereClause;
    $stmtTotal = $conn->prepare($sqlTotal);
    if ($stmtTotal === false) {
        throw new Exception("Error al preparar la consulta de conteo: " . implode(" ", $conn->errorInfo()));
    }
    $stmtTotal->execute($params);
    $totalRecords = $stmtTotal->fetchColumn();
    $stmtTotal = null;

    // --- Obtener el total general del monto de cotizaciones filtradas ---
    $sqlTotalMonto = "SELECT SUM(pago) FROM cotizaciones " . $whereClause;
    $stmtTotalMonto = $conn->prepare($sqlTotalMonto);
    if ($stmtTotalMonto === false) {
        throw new Exception("Error al preparar la consulta de suma total: " . implode(" ", $conn->errorInfo()));
    }
    $stmtTotalMonto->execute($params);
    $totalGeneralMonto = $stmtTotalMonto->fetchColumn();
    $totalGeneralMonto = $totalGeneralMonto !== null ? (float)$totalGeneralMonto : 0.00; // Asegurarse que sea float y 0 si es null
    $stmtTotalMonto = null;

    // --- Obtener las cotizaciones con paginación y filtros ---
    $sqlCotizaciones = "SELECT id, nombre, apellido, tipo_cotizacion, pago, fecha, dia_semana, estado FROM cotizaciones " . $whereClause . " ORDER BY fecha DESC, nombre ASC LIMIT ? OFFSET ?";
    $stmtCotizaciones = $conn->prepare($sqlCotizaciones);
    if ($stmtCotizaciones === false) {
        throw new Exception("Error al preparar la consulta de cotizaciones: " . implode(" ", $conn->errorInfo()));
    }

    $paramIndex = 1;
    foreach ($params as $value) {
        $stmtCotizaciones->bindValue($paramIndex++, $value);
    }
    $stmtCotizaciones->bindValue($paramIndex++, $limit, PDO::PARAM_INT);
    $stmtCotizaciones->bindValue($paramIndex++, $offset, PDO::PARAM_INT);

    $stmtCotizaciones->execute();
    $cotizaciones = $stmtCotizaciones->fetchAll(PDO::FETCH_ASSOC);
    $stmtCotizaciones = null;
    $conn = null;

    echo json_encode(['cotizaciones' => $cotizaciones, 'total' => $totalRecords, 'total_general_monto' => $totalGeneralMonto]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
