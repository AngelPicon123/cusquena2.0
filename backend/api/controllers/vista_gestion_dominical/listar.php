<?php
// C:\xampp\htdocs\cusquena\backend\api\controllers\vista_gestion_dominical\listar.php

require_once __DIR__ . '/../../../includes/db.php';
require_once __DIR__ . '/../../../includes/auth.php';

header('Content-Type: application/json');

try {
    verificarPermiso(['Administrador', 'Secretaria']);

    $nombre_apellido = $_GET['nombre'] ?? '';
    $semana_inicio_filtro = $_GET['semana_inicio'] ?? '';
    $semana_fin_filtro = $_GET['semana_fin'] ?? '';
    $estado = $_GET['estado'] ?? '';

    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $offset = ($page - 1) * $limit;

    $conditions = [];
    $params = [];

    // Filtro por nombre o apellidos
    if (!empty($nombre_apellido)) {
        $conditions[] = "(nombre LIKE ? OR apellidos LIKE ?)";
        $params[] = '%' . $nombre_apellido . '%';
        $params[] = '%' . $nombre_apellido . '%';
    }

    // Filtro por fechas
    if (!empty($semana_inicio_filtro)) {
        $conditions[] = "semana_inicio >= ?";
        $params[] = $semana_inicio_filtro;
    }
    if (!empty($semana_fin_filtro)) {
        $conditions[] = "semana_fin <= ?";
        $params[] = $semana_fin_filtro;
    }

    // Filtro por Estado
    if (!empty($estado)) {
        $conditions[] = "estado = ?";
        $params[] = $estado;
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

    // --- NUEVO: Cálculo del monto total de la diferencia ---
    $sqlTotalDiferencia = "SELECT SUM(diferencia) FROM dominical $whereClause";
    $stmtTotalDiferencia = $conn->prepare($sqlTotalDiferencia);
    $stmtTotalDiferencia->execute($params);
    $totalDiferencia = $stmtTotalDiferencia->fetchColumn();
    $totalDiferencia = $totalDiferencia !== null ? (float)$totalDiferencia : 0.00;
    $stmtTotalDiferencia = null;
    // ----------------------------------------------------

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
        'total_general_monto' => $totalGeneralMonto,
        'total_diferencia' => $totalDiferencia // <-- NUEVO: Retornamos el total de la diferencia
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al listar dominicales: ' . $e->getMessage()]);
}
?>