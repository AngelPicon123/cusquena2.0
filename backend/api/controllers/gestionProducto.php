<?php
// backend/api/controllers/gestionProducto.php

header('Content-Type: application/json');
require_once '../../includes/db.php'; // Ruta correcta a db_config.php

$method = $_SERVER['REQUEST_METHOD'];

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    switch ($method) {
        case 'GET':
            if (isset($_GET['id'])) {
                // Obtener un producto específico por su 'id'
                $idProducto = $_GET['id'];
                // Asegurarse de seleccionar las columnas correctas de la tabla 'productos'
                // y hacer el JOIN con 'categorias' para obtener el nombre de la categoría
                $stmt = $pdo->prepare("SELECT p.id as idProducto, p.descripcion, p.precio_compra, p.precio_venta, p.inicial, p.ingreso, p.queda, p.venta, p.monto, p.categoria_id as idCategoria, c.nombre as categoria_nombre FROM productos p JOIN categorias c ON p.categoria_id = c.categoria_id WHERE p.id = ?");
                $stmt->execute([$idProducto]);
                $producto = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($producto) {
                    echo json_encode($producto);
                } else {
                    http_response_code(404);
                    echo json_encode(['message' => 'Producto no encontrado.']);
                }
            } elseif (isset($_GET['search'])) {
                // Buscar productos por descripción o nombre de categoría
                $searchTerm = '%' . $_GET['search'] . '%';
                $stmt = $pdo->prepare("SELECT p.id as idProducto, p.descripcion, p.precio_compra, p.precio_venta, p.inicial, p.ingreso, p.queda, p.venta, p.monto, p.categoria_id as idCategoria, c.nombre as categoria_nombre FROM productos p JOIN categorias c ON p.categoria_id = c.categoria_id WHERE p.descripcion LIKE ? OR c.nombre LIKE ? ORDER BY p.id DESC");
                $stmt->execute([$searchTerm, $searchTerm]);
                $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($productos);
            } else {
                // Obtener todos los productos
                $stmt = $pdo->query("SELECT p.id as idProducto, p.descripcion, p.precio_compra, p.precio_venta, p.inicial, p.ingreso, p.queda, p.venta, p.monto, p.categoria_id as idCategoria, c.nombre as categoria_nombre FROM productos p JOIN categorias c ON p.categoria_id = c.categoria_id ORDER BY p.id DESC");
                $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($productos);
            }
            break;

        case 'POST':
            // Agregar un nuevo producto
            $data = json_decode(file_get_contents('php://input'), true);

            // Calcula 'queda' basado en 'inicial' e 'ingreso' (si es necesario)
            // Aquí asumimos que 'queda' se calcula como inicial + ingreso (o se maneja de otra forma)
            // Y 'venta' y 'monto' inician en 0 para un nuevo producto
            $queda = $data['inicial'] + $data['ingreso']; // O simplemente $data['queda'] si lo envías directamente

            $stmt = $pdo->prepare("INSERT INTO productos (descripcion, precio_compra, precio_venta, inicial, ingreso, queda, venta, monto, categoria_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['descripcion'],
                $data['precio_compra'],
                $data['precio_venta'],
                $data['inicial'],
                $data['ingreso'],
                $queda, // Usar el valor calculado o enviado
                0, // Venta inicial 0
                0.00, // Monto inicial 0.00
                $data['categoria'] // categoria_id
            ]);
            echo json_encode(['message' => 'Producto agregado exitosamente.', 'id' => $pdo->lastInsertId()]);
            break;

        case 'PUT':
            // Actualizar un producto existente o realizar una venta
            $data = json_decode(file_get_contents('php://input'), true);
            $idProducto = $_GET['id'];

            if (isset($_GET['action']) && $_GET['action'] === 'sell') {
                // Vender producto
                $cantidadVender = $data['cantidad'];

                // Obtener stock actual y precio de venta
                $stmt = $pdo->prepare("SELECT queda, venta, monto, precio_venta FROM productos WHERE id = ?");
                $stmt->execute([$idProducto]);
                $producto = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($producto) {
                    $nuevaQueda = $producto['queda'] - $cantidadVender;
                    $nuevaVenta = $producto['venta'] + $cantidadVender;
                    $nuevoMonto = $producto['monto'] + ($cantidadVender * $producto['precio_venta']);

                    if ($nuevaQueda >= 0) {
                        $stmt = $pdo->prepare("UPDATE productos SET queda = ?, venta = ?, monto = ? WHERE id = ?");
                        $stmt->execute([$nuevaQueda, $nuevaVenta, $nuevoMonto, $idProducto]);
                        echo json_encode(['message' => 'Venta realizada exitosamente.']);
                    } else {
                        http_response_code(400);
                        echo json_encode(['message' => 'Cantidad a vender excede el stock disponible.']);
                    }
                } else {
                    http_response_code(404);
                    echo json_encode(['message' => 'Producto no encontrado para la venta.']);
                }

            } else {
                // Actualizar producto (edición normal)
                // Es importante que los campos se correspondan con los de tu tabla 'productos'
                $stmt = $pdo->prepare("UPDATE productos SET descripcion = ?, precio_compra = ?, precio_venta = ?, inicial = ?, ingreso = ?, queda = ?, venta = ?, monto = ?, categoria_id = ? WHERE id = ?");
                $stmt->execute([
                    $data['descripcion'],
                    $data['precio_compra'],
                    $data['precio_venta'],
                    $data['inicial'],
                    $data['ingreso'],
                    $data['queda'],
                    $data['venta'],
                    $data['monto'],
                    $data['categoria'], // categoria_id
                    $idProducto
                ]);
                echo json_encode(['message' => 'Producto actualizado exitosamente.']);
            }
            break;

        // Opcional: DELETE (si quieres añadir funcionalidad de eliminación)
        /*
        case 'DELETE':
            if (isset($_GET['id'])) {
                $idProducto = $_GET['id'];
                $stmt = $pdo->prepare("DELETE FROM productos WHERE id = ?");
                $stmt->execute([$idProducto]);
                echo json_encode(['message' => 'Producto eliminado exitosamente.']);
            } else {
                http_response_code(400);
                echo json_encode(['message' => 'ID de producto no proporcionado para eliminar.']);
            }
            break;
        */

        default:
            http_response_code(405);
            echo json_encode(['message' => 'Método no permitido.']);
            break;
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Error de base de datos: ' . $e->getMessage()]);
}
?>