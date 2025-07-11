<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit(0);
header('Content-Type: application/json');

require_once '../../includes/db.php'; // Asegúrate de que esta ruta a tu conexión DB es correcta.

if ($conn === null) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Error de conexión a la base de datos"]);
    exit;
}

$accion = $_REQUEST['accion'] ?? '';

switch ($accion) {
    case 'listar':
        listarProductos($conn);
        break;

    case 'registrar':
        registrarProducto($conn);
        break;

    case 'modificar':
        modificarProducto($conn);
        break;

    case 'eliminar':
        eliminarProducto($conn);
        break;

    case 'vender':
        venderProducto($conn);
        break;

    default:
        echo json_encode(["success" => false, "message" => "Acción no válida"]);
}

function listarProductos($conn) {
    $buscar = $_GET['buscar'] ?? '';
    $buscar = "%$buscar%";

    // Asumimos que la tabla de productos es 'productos' y tiene las columnas adecuadas
    // y que 'categorias' tiene 'id' y 'nombre'.
    // Si la búsqueda incluye categoría, se debe hacer un JOIN.
    // Esta consulta busca por descripción de producto o por el nombre de la categoría.
    $sql = "SELECT p.*, c.nombre AS nombre_categoria FROM productos p
            LEFT JOIN categorias c ON p.categoria_id = c.id
            WHERE p.descripcion LIKE ? OR c.nombre LIKE ?
            ORDER BY p.id ASC";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$buscar, $buscar]);
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(["success" => true, "data" => $productos]);
}


function registrarProducto($conn) {
    $descripcion = $_POST['descripcion'] ?? '';
    $precio_compra = $_POST['precio_compra'] ?? 0;
    $precio_venta = $_POST['precio_venta'] ?? 0;
    $inicial = $_POST['inicial'] ?? 0;
    $ingreso = $_POST['ingreso'] ?? 0;
    $queda = $_POST['queda'] ?? 0;
    $venta = $_POST['venta'] ?? 0;
    $monto = $_POST['monto'] ?? 0;
    $categoria_id = $_POST['categoria'] ?? null; // Asume que el nombre del campo es 'categoria'

    $sql = "INSERT INTO productos (descripcion, precio_compra, precio_venta, inicial, ingreso, queda, venta, monto, categoria_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $ok = $stmt->execute([$descripcion, $precio_compra, $precio_venta, $inicial, $ingreso, $queda, $venta, $monto, $categoria_id]);

    echo json_encode(["success" => $ok, "message" => $ok ? "Producto registrado con éxito." : $stmt->errorInfo()[2]]);
}

function modificarProducto($conn) {
    $id = $_POST['idProducto'] ?? ''; // Nombre del campo en el formulario JS es 'idProducto'
    $descripcion = $_POST['descripcion'] ?? '';
    $precio_compra = $_POST['precio_compra'] ?? 0;
    $precio_venta = $_POST['precio_venta'] ?? 0;
    $inicial = $_POST['inicial'] ?? 0;
    $ingreso = $_POST['ingreso'] ?? 0;
    $queda = $_POST['queda'] ?? 0;
    $venta = $_POST['venta'] ?? 0;
    $monto = $_POST['monto'] ?? 0;
    $categoria_id = $_POST['categoria'] ?? null; // Asume que el nombre del campo es 'categoria'

    if (!$id) {
        echo json_encode(["success" => false, "message" => "ID de producto no recibido"]);
        return;
    }

    $sql = "UPDATE productos SET descripcion=?, precio_compra=?, precio_venta=?, inicial=?, ingreso=?, queda=?, venta=?, monto=?, categoria_id=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $ok = $stmt->execute([$descripcion, $precio_compra, $precio_venta, $inicial, $ingreso, $queda, $venta, $monto, $categoria_id, $id]);

    echo json_encode(["success" => $ok, "message" => $ok ? "Producto modificado correctamente" : $stmt->errorInfo()[2]]);
}

function eliminarProducto($conn) {
    $id = $_GET['id'] ?? ''; // El JS envía el ID por GET en la URL para eliminar
    if (!$id) {
        echo json_encode(["success" => false, "message" => "ID de producto no recibido para eliminar."]);
        return;
    }

    // Puedes optar por eliminar físicamente o cambiar el estado a 'Inactivo'
    // Siguiendo el modelo de servicios, vamos a actualizar un campo 'estado' si lo tuvieras en productos,
    // o simplemente eliminarlo. Para este ejemplo, asumiremos una eliminación lógica si existiera
    // una columna de 'estado', o una eliminación física si no.
    // Si no tienes un campo 'estado' en tu tabla de productos, usa DELETE FROM.
    // Ejemplo de eliminación física:
    $sql = "DELETE FROM productos WHERE id=?";
    // Ejemplo de eliminación lógica (si tienes un campo 'activo' o 'estado'):
    // $sql = "UPDATE productos SET activo = 0 WHERE id=?";

    $stmt = $conn->prepare($sql);
    $ok = $stmt->execute([$id]);

    echo json_encode(["success" => $ok, "message" => $ok ? "Producto eliminado correctamente." : $stmt->errorInfo()[2]]);
}

function venderProducto($conn) {
    $id_producto = $_POST['idProducto'] ?? '';
    $cantidad_a_vender = $_POST['cantidad'] ?? 0;

    if (!$id_producto || $cantidad_a_vender <= 0) {
        echo json_encode(["success" => false, "message" => "Datos de venta inválidos."]);
        return;
    }

    try {
        $conn->beginTransaction();

        // 1. Obtener la cantidad actual de 'queda' y 'venta'
        $sql_select = "SELECT queda, venta, precio_venta FROM productos WHERE id = ?";
        $stmt_select = $conn->prepare($sql_select);
        $stmt_select->execute([$id_producto]);
        $producto = $stmt_select->fetch(PDO::FETCH_ASSOC);

        if (!$producto) {
            $conn->rollBack();
            echo json_encode(["success" => false, "message" => "Producto no encontrado."]);
            return;
        }

        $queda_actual = $producto['queda'];
        $venta_actual = $producto['venta'];
        $precio_venta_unitario = $producto['precio_venta'];

        if ($queda_actual < $cantidad_a_vender) {
            $conn->rollBack();
            echo json_encode(["success" => false, "message" => "Cantidad a vender excede el stock disponible."]);
            return;
        }

        // 2. Calcular nuevas cantidades
        $nueva_queda = $queda_actual - $cantidad_a_vender;
        $nueva_venta = $venta_actual + $cantidad_a_vender;
        $nuevo_monto = $nueva_venta * $precio_venta_unitario; // Asume que monto es el total de ventas acumuladas

        // 3. Actualizar la tabla de productos
        $sql_update = "UPDATE productos SET queda = ?, venta = ?, monto = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $ok = $stmt_update->execute([$nueva_queda, $nueva_venta, $nuevo_monto, $id_producto]);

        if (!$ok) {
            $conn->rollBack();
            echo json_encode(["success" => false, "message" => "Error al actualizar stock del producto."]);
            return;
        }

        // Opcional: Registrar la venta en una tabla de transacciones/ventas si existe
        // Ejemplo: INSERT INTO ventas (producto_id, cantidad, precio_total, fecha_venta) VALUES (?, ?, ?, NOW())
        // $sql_registro_venta = "INSERT INTO ventas (producto_id, cantidad, precio_total, fecha_venta) VALUES (?, ?, ?, NOW())";
        // $stmt_registro_venta = $conn->prepare($sql_registro_venta);
        // $ok_registro = $stmt_registro_venta->execute([$id_producto, $cantidad_a_vender, ($cantidad_a_vender * $precio_venta_unitario)]);
        // if (!$ok_registro) {
        //     $conn->rollBack();
        //     echo json_encode(["success" => false, "message" => "Error al registrar la transacción de venta."]);
        //     return;
        // }

        $conn->commit();
        echo json_encode(["success" => true, "message" => "Venta registrada con éxito. Stock actualizado."]);

    } catch (PDOException $e) {
        $conn->rollBack();
        error_log("Error en venderProducto: " . $e->getMessage()); // Para depuración
        echo json_encode(["success" => false, "message" => "Error de base de datos al procesar la venta: " . $e->getMessage()]);
    }
}
?>