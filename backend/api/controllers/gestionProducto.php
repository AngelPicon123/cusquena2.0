<?php
session_start();
require_once '../../includes/db.php'; 

header('Content-Type: application/json');

$accion = isset($_GET['accion']) ? $_GET['accion'] : '';

function responder($success, $message, $data = []) {
    echo json_encode(['success' => $success, 'message' => $message, 'data' => $data]);
    exit;
}

switch ($accion) {
    case 'listar':
        $sql = "SELECT p.*, c.nombre as categoria_nombre, c.id as categoria_id 
                FROM productos p 
                LEFT JOIN categorias c ON p.categoria_id = c.id";
        $result = $conn->query($sql);
        $productos = [];
        while ($row = $result->fetch_assoc()) {
            $productos[] = $row;
        }
        responder(true, 'Productos listados correctamente', $productos);
        break;

    case 'buscar':
        $termino = isset($_GET['termino']) ? $conn->real_escape_string($_GET['termino']) : '';
        $sql = "SELECT p.*, c.nombre as categoria_nombre, c.id as categoria_id 
                FROM productos p 
                LEFT JOIN categorias c ON p.categoria_id = c.id 
                WHERE p.descripcion LIKE '%$termino%' OR c.nombre LIKE '%$termino%'";
        $result = $conn->query($sql);
        $productos = [];
        while ($row = $result->fetch_assoc()) {
            $productos[] = $row;
        }
        responder(true, 'Productos buscados correctamente', $productos);
        break;

    case 'agregar':
        if ($_SESSION['rol'] !== 'Administrador') {
            responder(false, 'No tiene permisos para agregar productos');
        }
        $descripcion = $conn->real_escape_string($_POST['descripcion']);
        $precio_compra = floatval($_POST['precio_compra']);
        $precio_venta = floatval($_POST['precio_venta']);
        $inicial = intval($_POST['inicial']);
        $ingreso = intval($_POST['ingreso']);
        $queda = intval($_POST['queda']);
        $venta = intval($_POST['venta']);
        $monto = floatval($_POST['monto']);
        $categoria_id = intval($_POST['categoria']);
        
        $sql = "INSERT INTO productos (descripcion, precio_compra, precio_venta, inicial, ingreso, queda, venta, monto, categoria_id) 
                VALUES ('$descripcion', $precio_compra, $precio_venta, $inicial, $ingreso, $queda, $venta, $monto, $categoria_id)";
        if ($conn->query($sql)) {
            responder(true, 'Producto agregado correctamente');
        } else {
            responder(false, 'Error al agregar producto: ' . $conn->error);
        }
        break;

    case 'editar':
        if ($_SESSION['rol'] !== 'Administrador') {
            responder(false, 'No tiene permisos para editar productos');
        }
        $id = intval($_POST['idProducto']);
        $descripcion = $conn->real_escape_string($_POST['descripcion']);
        $precio_compra = floatval($_POST['precio_compra']);
        $precio_venta = floatval($_POST['precio_venta']);
        $inicial = intval($_POST['inicial']);
        $ingreso = intval($_POST['ingreso']);
        $queda = intval($_POST['queda']);
        $venta = intval($_POST['venta']);
        $monto = floatval($_POST['monto']);
        $categoria_id = intval($_POST['categoria']);
        
        $sql = "UPDATE productos SET 
                descripcion='$descripcion', 
                precio_compra=$precio_compra, 
                precio_venta=$precio_venta, 
                inicial=$inicial, 
                ingreso=$ingreso, 
                queda=$queda, 
                venta=$venta, 
                monto=$monto, 
                categoria_id=$categoria_id 
                WHERE id=$id";
        if ($conn->query($sql)) {
            responder(true, 'Producto actualizado correctamente');
        } else {
            responder(false, 'Error al actualizar producto: ' . $conn->error);
        }
        break;

    case 'eliminar':
        if ($_SESSION['rol'] !== 'Administrador') {
            responder(false, 'No tiene permisos para eliminar productos');
        }
        $id = intval($_GET['id']);
        $sql = "DELETE FROM productos WHERE id=$id";
        if ($conn->query($sql)) {
            responder(true, 'Producto eliminado correctamente');
        } else {
            responder(false, 'Error al eliminar producto: ' . $conn->error);
        }
        break;

    case 'vender':
        $id = intval($_POST['idProducto']);
        $cantidad = intval($_POST['cantidad']);
        
        $sql = "SELECT queda, precio_venta, venta, monto FROM productos WHERE id=$id";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $producto = $result->fetch_assoc();
            if ($producto['queda'] >= $cantidad) {
                $nuevo_queda = $producto['queda'] - $cantidad;
                $nueva_venta = intval($producto['venta']) + $cantidad;
                $nuevo_monto = floatval($producto['monto']) + ($cantidad * $producto['precio_venta']);
                
                $sql = "UPDATE productos SET 
                        queda=$nuevo_queda, 
                        venta=$nueva_venta, 
                        monto=$nuevo_monto 
                        WHERE id=$id";
                if ($conn->query($sql)) {
                    responder(true, 'Venta realizada correctamente');
                } else {
                    responder(false, 'Error al realizar la venta: ' . $conn->error);
                }
            } else {
                responder(false, 'No hay suficiente stock para la venta');
            }
        } else {
            responder(false, 'Producto no encontrado');
        }
        break;

    default:
        responder(false, 'Acción no válida');
}
?>