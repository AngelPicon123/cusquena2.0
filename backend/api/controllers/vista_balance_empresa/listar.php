<?php
require_once __DIR__ . '/../../../includes/db.php';
require_once __DIR__ . '/../../../includes/auth.php';

header('Content-Type: application/json');

verificarPermiso(['Administrador', 'Secretaria']);

$nombre_filtro = $_GET['nombre'] ?? '';
$mes_filtro_num = $_GET['mes'] ?? '';
$anio_filtro = $_GET['anio'] ?? ''; // AÑADIDO: Para filtrar por año
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$offset = ($page - 1) * $limit;

$mes_map = [
    '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril',
    '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto',
    '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
];
$mes_filtro_nombre = '';
if (!empty($mes_filtro_num) && isset($mes_map[$mes_filtro_num])) {
    $mes_filtro_nombre = $mes_map[$mes_filtro_num];
}

try {
    $conditions = [];
    $params = [];

    if (!empty($nombre_filtro)) {
        $conditions[] = "nombre_descripcion LIKE ?";
        $params[] = "%" . $nombre_filtro . "%";
    }

    if (!empty($mes_filtro_nombre)) {
        $conditions[] = "mes = ?";
        $params[] = $mes_filtro_nombre;
    }

    // AÑADIDO: Filtro por la nueva columna 'anio'
    if (!empty($anio_filtro) && is_numeric($anio_filtro)) {
        $conditions[] = "anio = ?";
        $params[] = (int)$anio_filtro;
    }

    $whereClause = "WHERE 1=1";
    if (!empty($conditions)) {
        $whereClause .= " AND " . implode(" AND ", $conditions);
    }

    $sqlTotal = "SELECT COUNT(*) FROM balances_empresa $whereClause";
    $stmtTotal = $conn->prepare($sqlTotal);
    $stmtTotal->execute($params);
    $totalRecords = $stmtTotal->fetchColumn();
    $stmtTotal = null;

    $sqlTotalMonto = "SELECT SUM(monto) FROM balances_empresa $whereClause";
    $stmtMonto = $conn->prepare($sqlTotalMonto);
    $stmtMonto->execute($params);
    $totalGeneralMonto = $stmtMonto->fetchColumn();
    $totalGeneralMonto = $totalGeneralMonto !== null ? (float)$totalGeneralMonto : 0.00;
    $stmtMonto = null;

    // AÑADIDO: Seleccionar la nueva columna 'anio'
    $sql = "SELECT id, nombre_descripcion, tipo_balance, mes, monto, anio, fecha_creacion
            FROM balances_empresa
            $whereClause
            ORDER BY fecha_creacion DESC, nombre_descripcion ASC
            LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);

    $currentParamIndex = 1;
    foreach ($params as $val) {
        $stmt->bindValue($currentParamIndex++, $val);
    }
    $stmt->bindValue($currentParamIndex++, $limit, PDO::PARAM_INT);
    $stmt->bindValue($currentParamIndex++, $offset, PDO::PARAM_INT);

    $stmt->execute();
    $balances = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'balances' => $balances,
        'total_registros' => $totalRecords,
        'total_general_monto' => $totalGeneralMonto,
        'pagina_actual' => $page,
        'registros_por_pagina' => $limit,
        'total_paginas' => ceil($totalRecords / $limit)
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error de base de datos al listar balances: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error al listar balances: ' . $e->getMessage()]);
}
?>