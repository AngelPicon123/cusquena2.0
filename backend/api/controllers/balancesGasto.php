<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once '../../includes/db.php';  // Ajusta ruta si es necesario

try {
    // Parámetros de paginación
    $pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
    $limite = isset($_GET['limite']) ? max(1, intval($_GET['limite'])) : 10;
    $offset = ($pagina - 1) * $limite;

    // Filtro de fechas
    $inicio = isset($_GET['inicio']) ? $_GET['inicio'] : '';
    $fin    = isset($_GET['fin'])    ? $_GET['fin']    : '';

    // Validar fechas YYYY-MM-DD
    if ($inicio && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $inicio)) {
        throw new Exception("Formato de fecha inicio inválido.");
    }
    if ($fin && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fin)) {
        throw new Exception("Formato de fecha fin inválido.");
    }
    if ($inicio && $fin && $inicio > $fin) {
        throw new Exception("La fecha inicio no puede ser mayor que la fecha fin.");
    }

    // Construir WHERE dinámico
    $where  = '';
    $params = [];
    if ($inicio && $fin) {
        $where = "WHERE fecha >= :inicio AND fecha < DATE_ADD(:fin, INTERVAL 1 DAY)";
        $params[':inicio'] = $inicio;
        $params[':fin']    = $fin;
    }

    // 1) Obtener datos de la página solicitada
    $sql = "
        SELECT tipo, monto, fecha
        FROM gastos
        $where
        ORDER BY fecha DESC, tipo
        LIMIT :limite OFFSET :offset
    ";
    $stmt = $conn->prepare($sql);
    foreach ($params as $k => $v) {
        $stmt->bindValue($k, $v);
    }
    $stmt->bindValue(':limite',  $limite, PDO::PARAM_INT);
    $stmt->bindValue(':offset',  $offset, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 2) Contar total de filas para calcular número de páginas
    $sqlTotal = "
        SELECT COUNT(*) FROM (
            SELECT 1
            FROM gastos
            $where
            GROUP BY tipo, fecha, monto, id  /* o quitar GROUP BY si no aplica */
        ) AS sub
    ";
    $stmtTotal = $conn->prepare($sqlTotal);
    foreach ($params as $k => $v) {
        $stmtTotal->bindValue($k, $v);
    }
    $stmtTotal->execute();
    $total = (int) $stmtTotal->fetchColumn();

    echo json_encode([
        'data'  => $data,
        'total' => $total,
        'pagina' => $pagina,
        'limite' => $limite
    ]);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
