<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

header('Content-Type: application/json');
include '../../includes/db.php';

if ($conn === null) {
    echo json_encode(["error" => "No se pudo establecer la conexión a la base de datos."]);
    exit();
}

// LISTAR o BUSCAR
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $query = "SELECT * FROM categoria";

    if (isset($_GET['buscar']) && !empty($_GET['buscar'])) {
        $buscar = "%" . $_GET['buscar'] . "%";
        $stmt = $conn->prepare("SELECT * FROM categoria WHERE idCategoria LIKE :buscar OR descripcion LIKE :buscar OR estado LIKE :buscar");
        $stmt->execute(['buscar' => $buscar]);
    } else {
        $stmt = $conn->query($query);
    }

    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($categorias);
}

// AGREGAR CATEGORÍA
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $descripcion = $data['descripcion'];
    $estado = $data['estado'];

    $stmt = $conn->prepare("INSERT INTO categoria (descripcion, estado) VALUES (:descripcion, :estado)");
    $stmt->execute([
        'descripcion' => $descripcion,
        'estado' => $estado
    ]);

    echo json_encode(["message" => "Categoría agregada correctamente"]);
}

// ACTUALIZAR CATEGORÍA
if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);

    $idCategoria = $data['idCategoria'];
    $descripcion = $data['descripcion'];
    $estado = $data['estado'];

    $stmt = $conn->prepare("UPDATE categoria SET descripcion = :descripcion, estado = :estado WHERE idCategoria = :idCategoria");
    $stmt->execute([
        'idCategoria' => $idCategoria,
        'descripcion' => $descripcion,
        'estado' => $estado
    ]);

    echo json_encode(["message" => "Categoría actualizada correctamente"]);
}

// ELIMINAR CATEGORÍA
if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    $data = json_decode(file_get_contents("php://input"), true);
    $idCategoria = $data['idCategoria'];

    $stmt = $conn->prepare("DELETE FROM categoria WHERE idCategoria = :idCategoria");
    $stmt->execute(['idCategoria' => $idCategoria]);

    echo json_encode(["message" => "Categoría eliminada correctamente"]);
}
?>