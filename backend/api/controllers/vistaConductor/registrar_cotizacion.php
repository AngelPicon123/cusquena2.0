<?php
header("Content-Type: application/json");
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/cotizacion_errors.log');

$response = ["exito" => false, "error" => ""];

try {
    // 1. Conexión a la base de datos
    $conn = new mysqli("localhost", "root", "", "la_cusquena");
    if ($conn->connect_error) {
        throw new Exception("Error de conexión: " . $conn->connect_error);
    }
    $conn->set_charset("utf8");

    // 2. Obtener los datos enviados por JSON (desde fetch)
    $json = file_get_contents("php://input");
    $datos = json_decode($json, true);

    if (!$datos) {
        throw new Exception("No se recibieron datos válidos");
    }

    // 3. Validar campos mínimos requeridos
    $requeridos = ['fecha', 'nombre', 'apellido', 'cotizacion', 'placa', 'idConductor', 'idTipoConductor', 'descripcion'];
    foreach ($requeridos as $campo) {
        if (!isset($datos[$campo]) || $datos[$campo] === '') {
            throw new Exception("Falta el campo requerido: $campo");
        }
    }

    // 4. Extraer monto de la cotización (ej: "S/. 150.50" → 150.50)
           $partes = explode(' ', $datos['cotizacion']); // Separa "S/.", "25.00", "(diario)"
            $monto = (float)$partes[1]; // Toma directamente el segundo elemento

    // 5. Preparar consulta SQL
    $sql = "INSERT INTO cotizacion (
                fechaCotizacion, 
                nombre, 
                apellido, 
                cotizacion, 
                placa, 
                idConductor,
                idTipoConductor,
                detalle,
                total
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Error al preparar consulta: " . $conn->error);
    }

    // 6. Enlazar parámetros
    $stmt->bind_param(
        "sssssissd",
        $datos['fecha'],
        $datos['nombre'],
        $datos['apellido'],
        $datos['cotizacion'],
        $datos['placa'],
        $datos['idConductor'],
        $datos['idTipoConductor'],
        $datos['descripcion'],
        $monto
    );

    // 7. Ejecutar
    if ($stmt->execute()) {
        $response = [
            "exito" => true,
            "idCotizacion" => $conn->insert_id,
            "mensaje" => "✅ Cotización registrada correctamente"
        ];
    } else {
        throw new Exception("Error al ejecutar consulta: " . $stmt->error);
    }

} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    $response["error"] = "Error al insertar cotización";

    // Para debug en entorno local
    if (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
        $response["debug"] = $e->getMessage();
    }

    http_response_code(500);
} finally {
    if (isset($stmt)) $stmt->close();
    if (isset($conn)) $conn->close();
    echo json_encode($response);
    exit;
}
?>
