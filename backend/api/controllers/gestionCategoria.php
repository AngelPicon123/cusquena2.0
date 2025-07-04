<?php
session_start();
require_once '../includes/conexion.php';

header('Content-Type: application/json');

$accion = isset($_GET['accion']) ? $_GET['accion'] : '';

function responder($success, $message, $data = []) {
    echo json_encode(['success' => $success, 'message' => $message, 'data' => $data]);
    exit;
}

switch ($accion) {
    case 'listar':
        $sql = "SELECT id, nombre FROM categorias ORDER BY nombre";
        $result = $conn->query($sql);
        $categorias = [];
        while ($row = $result->fetch_assoc()) {
            $categorias[] = $row;
        }
        responder(true, 'Categorías listadas correctamente', $categorias);
        break;

    default:
        responder(false, 'Acción no válida');
}
?>