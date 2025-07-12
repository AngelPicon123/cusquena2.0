<?php
// C:\xampp\htdocs\cusquena\backend\api\controllers\gestion_cotizaciones\listar.php

require_once __DIR__ . '/../../../includes/db.php';
require_once __DIR__ . '/../../../includes/auth.php';

header('Content-Type: application/json');

// Verifica el permiso del usuario. Asumo que Administrador y Secretaria pueden listar cotizaciones.
verificarPermiso(['Administrador', 'Secretaria']);

// Obtener parámetros de filtro y paginación de la URL
$nombre_apellido = $_GET['nombre_apellido'] ?? ''; // Ahora buscará en nombre O apellido
$fecha_inicio = $_GET['fecha_inicio'] ?? '';
$fecha_fin = $_GET['fecha_fin'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$offset = ($page - 1) * $limit;

try {
    $conditions = []; // Array para almacenar las condiciones SQL
    $params = [];     // Array para almacenar los parámetros de la consulta

    // Filtro por nombre o apellido
    if (!empty($nombre_apellido)) {
        $conditions[] = "(nombre LIKE ? OR apellido LIKE ?)";
        $params[] = "%" . $nombre_apellido . "%";
        $params[] = "%" . $nombre_apellido . "%";
    }

    // Filtro por fecha de inicio
    if (!empty($fecha_inicio)) {
        $conditions[] = "fecha_inicio >= ?";
        $params[] = $fecha_inicio;
    }

    // Filtro por fecha fin
    if (!empty($fecha_fin)) {
        $conditions[] = "fecha_fin <= ?";
        $params[] = $fecha_fin;
    }

    // Construir la cláusula WHERE
    $whereClause = "WHERE 1=1"; // Siempre true para facilitar la adición de condiciones
    if (!empty($conditions)) {
        $whereClause .= " AND " . implode(" AND ", $conditions);
    }

    // --- Obtener el Total de Registros Filtrados ---
    $sqlTotal = "SELECT COUNT(*) FROM cotizaciones $whereClause";
    $stmtTotal = $conn->prepare($sqlTotal);
    $stmtTotal->execute($params);
    $totalRecords = $stmtTotal->fetchColumn(); // Número total de cotizaciones que cumplen los filtros
    $stmtTotal = null;

    // --- Obtener el Total General de Pagos de los Registros Filtrados ---
    $sqlTotalPago = "SELECT SUM(pago) FROM cotizaciones $whereClause";
    $stmtPago = $conn->prepare($sqlTotalPago);
    $stmtPago->execute($params);
    $totalGeneralPago = $stmtPago->fetchColumn();
    // Asegurarse de que el total sea un flotante y no null
    $totalGeneralPago = $totalGeneralPago !== null ? (float)$totalGeneralPago : 0.00;
    $stmtPago = null;

    // --- Obtener los Datos de Cotizaciones Paginados y Filtrados ---
    $sql = "SELECT id, nombre, apellido, tipo_cotizacion, pago, fecha_inicio, fecha_fin, estado
            FROM cotizaciones $whereClause
            ORDER BY fecha_inicio DESC, apellido ASC
            LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);

    // VINCULAR PARÁMETROS DINÁMICAMENTE:
    // Primero, los parámetros de las condiciones de filtro
    $paramIndex = 1;
    foreach ($params as $val) {
        $stmt->bindValue($paramIndex++, $val);
    }
    // Luego, los parámetros de LIMIT y OFFSET
    $stmt->bindValue($paramIndex++, $limit, PDO::PARAM_INT);
    $stmt->bindValue($paramIndex++, $offset, PDO::PARAM_INT);

    $stmt->execute();
    $cotizaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt = null;
    $conn = null;

    // Calcular el total de páginas
    $totalPages = ceil($totalRecords / $limit);

    // Enviar la respuesta JSON
    echo json_encode([
        'success' => true,
        'message' => 'Cotizaciones listadas correctamente.',
        'cotizaciones' => $cotizaciones,
        'totalRecords' => $totalRecords,
        'totalPages' => $totalPages,
        'currentPage' => $page,
        'totalGlobal' => $totalGeneralPago // Renombrado para coincidir con tu JS
    ]);

} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['success' => false, 'message' => 'Error al listar cotizaciones: ' . $e->getMessage()]);
}
?>