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
    http_response_code(500);
    echo json_encode(["error" => "No se pudo establecer la conexión a la base de datos."]);
    exit();
}

// LISTAR O BUSCAR PRODUCTOS
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Obtener un producto por ID
    if (isset($_GET['idProducto'])) {
        $idProducto = $_GET['idProducto'];
        $stmt = $conn->prepare("SELECT p.*, c.descripcion AS nombreCategoria 
                                FROM Producto p 
                                LEFT JOIN categoria c ON p.idCategoria = c.idCategoria 
                                WHERE p.idProducto = :idProducto");
        $stmt->execute(['idProducto' => $idProducto]);
        $producto = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($producto);
        exit();
    }

    if (isset($_GET['buscar']) && !empty($_GET['buscar'])) {
        $buscar = "%" . $_GET['buscar'] . "%";
        $stmt = $conn->prepare("SELECT p.idProducto, p.descripcion, p.precioCompra, p.precioVenta, 
                               p.stock, c.descripcion AS nombreCategoria, p.presentacion,
                               p.estado
                        FROM Producto p 
                        LEFT JOIN categoria c ON p.idCategoria = c.idCategoria 
                        WHERE p.idProducto LIKE :buscar 
                           OR p.descripcion LIKE :buscar 
                           OR p.estado LIKE :buscar 
                           OR c.descripcion LIKE :buscar");
        $stmt->execute(['buscar' => $buscar]);
        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($productos);
        exit();
    }

    if (isset($_GET['categoria'])) {
        $stmt = $conn->query("SELECT * FROM categoria");
        $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($categorias);
        exit();
    }

    $stmt = $conn->query("SELECT p.*, c.descripcion AS nombreCategoria 
                          FROM Producto p 
                          LEFT JOIN categoria c ON p.idCategoria = c.idCategoria");
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($productos);
    exit();
}

// AGREGAR PRODUCTO
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_GET['venta'])) {
    try {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['descripcion']) || !isset($data['precioCompra']) || !isset($data['precioVenta']) || 
            !isset($data['stock']) || !isset($data['idCategoria']) || !isset($data['presentacion']) || !isset($data['estado'])) {
            http_response_code(400);
            echo json_encode(["error" => "Faltan datos requeridos para agregar el producto."]);
            exit();
        }

        $stmt = $conn->prepare("INSERT INTO Producto 
            (descripcion, precioCompra, precioVenta, stock, idCategoria, presentacion, estado) 
            VALUES 
            (:descripcion, :precioCompra, :precioVenta, :stock, :idCategoria, :presentacion, :estado)");
        $stmt->execute([
            'descripcion' => $data['descripcion'],
            'precioCompra' => $data['precioCompra'],
            'precioVenta' => $data['precioVenta'],
            'stock' => $data['stock'],
            'idCategoria' => $data['idCategoria'],
            'presentacion' => $data['presentacion'],
            'estado' => $data['estado']
        ]);

        echo json_encode(["message" => "Producto agregado correctamente"]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Error al agregar el producto: " . $e->getMessage()]);
    }
    exit();
}

// REGISTRAR VENTA
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_GET['venta'])) {
    try {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['idProducto']) || !isset($data['fecha']) || !isset($data['cantidad']) || 
            !isset($data['precioUnitario']) || !isset($data['subtotal'])) {
            http_response_code(400);
            echo json_encode(["error" => "Faltan datos requeridos para registrar la venta."]);
            exit();
        }

        $idProducto = $data['idProducto'];
        $fecha = $data['fecha'];
        $cantidad = $data['cantidad'];
        $precioUnitario = $data['precioUnitario'];
        $subtotal = $data['subtotal'];

        // Iniciar transacción
        $conn->beginTransaction();

        // Bloquear la fila del producto
        $stmt = $conn->prepare("SELECT stock FROM Producto WHERE idProducto = :idProducto FOR UPDATE");
        $stmt->execute(['idProducto' => $idProducto]);
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$producto) {
            $conn->rollBack();
            http_response_code(404);
            echo json_encode(["error" => "Producto no encontrado"]);
            exit();
        }

        if ($cantidad > $producto['stock']) {
            $conn->rollBack();
            http_response_code(400);
            echo json_encode(["error" => "La cantidad a vender excede el stock disponible"]);
            exit();
        }

        // Insertar la venta
        $stmt = $conn->prepare("INSERT INTO ventaproducto 
                                (descripcion, precioUnitario, cantidad, subtotal, fecha, total) 
                                VALUES (:descripcion, :precioUnitario, :cantidad, :subtotal, :fecha, :total)");
        $stmt->execute([
            'descripcion' => $data['descripcion'] ?? 'Venta de producto',
            'precioUnitario' => $precioUnitario,
            'cantidad' => $cantidad,
            'subtotal' => $subtotal,
            'fecha' => $fecha,
            'total' => $subtotal
        ]);

        // Actualizar el stock
        $nuevoStock = $producto['stock'] - $cantidad;
        $stmt = $conn->prepare("UPDATE Producto SET stock = :stock WHERE idProducto = :idProducto");
        $stmt->execute([
            'stock' => $nuevoStock,
            'idProducto' => $idProducto
        ]);

        // Confirmar transacción
        $conn->commit();

        echo json_encode(["message" => "Venta registrada correctamente"]);
    } catch (PDOException $e) {
        $conn->rollBack();
        http_response_code(500);
        echo json_encode(["error" => "Error al registrar la venta: " . $e->getMessage()]);
    } catch (Exception $e) {
        $conn->rollBack();
        http_response_code(500);
        echo json_encode(["error" => "Error inesperado: " . $e->getMessage()]);
    }
    exit();
}

// ACTUALIZAR PRODUCTO
if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    try {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['idProducto']) || !isset($data['descripcion']) || !isset($data['precioCompra']) || 
            !isset($data['precioVenta']) || !isset($data['stock']) || !isset($data['idCategoria']) || 
            !isset($data['presentacion']) || !isset($data['estado'])) {
            http_response_code(400);
            echo json_encode(["error" => "Faltan datos requeridos para actualizar el producto."]);
            exit();
        }

        $stmt = $conn->prepare("UPDATE Producto SET 
            descripcion = :descripcion, 
            precioCompra = :precioCompra, 
            precioVenta = :precioVenta, 
            stock = :stock, 
            idCategoria = :idCategoria, 
            presentacion = :presentacion,
            estado = :estado 
            WHERE idProducto = :idProducto");
        $stmt->execute([
            'idProducto' => $data['idProducto'],
            'descripcion' => $data['descripcion'],
            'precioCompra' => $data['precioCompra'],
            'precioVenta' => $data['precioVenta'],
            'stock' => $data['stock'],
            'idCategoria' => $data['idCategoria'],
            'presentacion' => $data['presentacion'],
            'estado' => $data['estado']
        ]);

        echo json_encode(["message" => "Producto actualizado correctamente"]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Error al actualizar el producto: " . $e->getMessage()]);
    }
    exit();
}
?>