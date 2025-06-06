<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

header('Content-Type: application/json');
include '../../includes/db.php'; // Asegúrate que este path esté correcto

if ($conn === null) {
    echo json_encode(["error" => "No se pudo conectar a la base de datos"]);
    exit();
}

switch ($_SERVER['REQUEST_METHOD']) {

    case 'GET':
        // Buscar por ID o listar todo
        if (isset($_GET['buscar']) && !empty($_GET['buscar'])) {
            $buscar = "%" . $_GET['buscar'] . "%";
            $stmt = $conn->prepare("SELECT * FROM Alquiler WHERE identificador LIKE :buscar OR nombre LIKE :buscar OR telefono LIKE :buscar OR tipo LIKE :buscar OR ubicacion LIKE :buscar OR estado LIKE :buscar");
            $stmt->execute(['buscar' => $buscar]);
        } else {
            $stmt = $conn->query("SELECT * FROM Alquiler");
        }
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($data);
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);

        $stmt = $conn->prepare("INSERT INTO Alquiler (identificador, nombre, telefono, tipo, fechaInicio, periodicidad, pago, ubicacion, estado) 
            VALUES (:identificador, :nombre, :telefono, :tipo, :fechaInicio, :periodicidad, :pago, :ubicacion, :estado)");

        $stmt->execute([
            'identificador' => $data['identificador'],
            'nombre'        => $data['nombre'],
            'telefono'      => $data['telefono'],
            'tipo'          => $data['tipo'],
            'fechaInicio'   => $data['fechaInicio'],
            'periodicidad'  => $data['periodicidad'],
            'pago'          => $data['pago'],
            'ubicacion'     => $data['ubicacion'],
            'estado'        => $data['estado']
        ]);

        echo json_encode(["message" => "Alquiler registrado correctamente"]);
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);

        $stmt = $conn->prepare("UPDATE Alquiler SET 
            identificador = :identificador,
            nombre = :nombre,
            telefono = :telefono,
            tipo = :tipo,
            fechaInicio = :fechaInicio,
            periodicidad = :periodicidad,
            pago = :pago,
            ubicacion = :ubicacion,
            estado = :estado
            WHERE idAlquiler = :id");

        $stmt->execute([
            'id'           => $data['idAlquiler'],
            'identificador'=> $data['identificador'],
            'nombre'       => $data['nombre'],
            'telefono'     => $data['telefono'],
            'tipo'         => $data['tipo'],
            'fechaInicio'  => $data['fechaInicio'],
            'periodicidad' => $data['periodicidad'],
            'pago'         => $data['pago'],
            'ubicacion'    => $data['ubicacion'],
            'estado'       => $data['estado']
        ]);

        echo json_encode(["message" => "Alquiler actualizado correctamente"]);
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"), true);

        $stmt = $conn->prepare("DELETE FROM Alquiler WHERE idAlquiler = :id");
        $stmt->execute(['id' => $data['idAlquiler']]);

        echo json_encode(["message" => "Alquiler eliminado correctamente"]);
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Método no permitido"]);
        break;
}
?>
