<?php
header("Content-Type: application/json");
ini_set('display_errors', 1); // ACTIVADO para debug
ini_set('log_errors', 1);
ini_set('error_log', __DIR__.'/cotizacion_errors.log');

$response = ["exito" => false, "error" => ""];

try {
    // 1. Conexión a la base de datos
    $conn = new mysqli("localhost", "root", "", "la_cusquena");
    if ($conn->connect_error) {
        throw new Exception("Error de conexión: " . $conn->connect_error);
    }
    $conn->set_charset("utf8");

    // 2. Datos de prueba (harcodeados)
    $datos = [
        'fecha' => date('Y-m-d'),
        'nombre' => 'Juan',
        'apellido' => 'Pérez',
        'cotizacion' => 'S/ 150.50',
        'placa' => 'ABC-123',
        'idConductor' => 2,
        'idTipoConductor' => "socio",
        'descripcion' => 'Servicio ficticio para prueba'
    ];

    // 3. Calcular total numérico desde la cotización
    $monto = (float) preg_replace('/[^0-9.]/', '', $datos['cotizacion']);

    // 4. Preparar consulta SQL
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

    // 5. Enlazar parámetros (string, int, double)
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

    // 6. Ejecutar
    if ($stmt->execute()) {
        $response = [
            "exito" => true,
            "idCotizacion" => $conn->insert_id,
            "mensaje" => "Cotización de prueba insertada"
        ];
    } else {
        throw new Exception("Error al ejecutar: " . $stmt->error);
    }

} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    $response["error"] = "Error al insertar cotización de prueba";

    // Mostrar mensaje detallado si estás en localhost
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
