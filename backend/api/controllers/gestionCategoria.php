<?php
// backend/api/controllers/gestionCategoria.php

header('Content-Type: application/json');
require_once '../../includes/db.php'; // Ruta correcta a db_config.php

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta para obtener las categorías
    // Usamos 'categoria_id' como 'id' y 'nombre' como 'nombre' para que coincida con el JS
    $stmt = $pdo->query("SELECT categoria_id as id, nombre FROM categorias ORDER BY nombre ASC");
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($categorias);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Error de base de datos al cargar categorías: ' . $e->getMessage()]);
}
?>