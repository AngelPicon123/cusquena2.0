<?php
header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "la_cusquena");
if ($conn->connect_error) {
    echo json_encode(["exito" => false, "error" => "Conexión fallida"]);
    exit();
}


try {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) throw new Exception("Datos JSON inválidos");

    // Validar campos obligatorios
    $required = ['idSoat', 'nombre', 'fechaMantenimiento', 'fechaProxMantenimiento', 'estado', 'dni', 'nombre_conductor', 'apellido', 'telefono', 'placa'];
    foreach ($required as $field) {
        if (!isset($input[$field]) || $input[$field] === '') {
            throw new Exception("El campo $field es requerido");
        }
    }

    // Iniciar transacción
    $conn->begin_transaction();

    // Actualizar SOAT
    $querySoat = "UPDATE soat SET 
                  nombre = ?, 
                  fechaMantenimiento = ?, 
                  fechaProxMantenimiento = ?, 
                  estado = ?
                  WHERE idSoat = ?";
    $stmtSoat = $conn->prepare($querySoat);
    if (!$stmtSoat) throw new Exception("Error al preparar actualización de SOAT: " . $conn->error);

    $stmtSoat->bind_param("ssssi",
        $input['nombre'],
        $input['fechaMantenimiento'],
        $input['fechaProxMantenimiento'],
        $input['estado'],
        $input['idSoat']
    );
    if (!$stmtSoat->execute()) throw new Exception("Error al ejecutar actualización de SOAT: " . $stmtSoat->error);

    // Obtener idConductor desde el idSoat
    $result = $conn->query("SELECT idConductor FROM soat WHERE idSoat = " . intval($input['idSoat']));
    if (!$result || $result->num_rows === 0) throw new Exception("No se encontró el idConductor relacionado al SOAT");

    $idConductor = $result->fetch_assoc()['idConductor'];

    // Actualizar datos del conductor
    $queryConductor = "UPDATE conductor SET 
                       dni = ?, 
                       nombre = ?, 
                       apellido = ?, 
                       telefono = ?, 
                       placa = ?
                       WHERE idConductor = ?";
    $stmtConductor = $conn->prepare($queryConductor);
    if (!$stmtConductor) throw new Exception("Error al preparar actualización de conductor: " . $conn->error);

    $stmtConductor->bind_param("sssssi",
        $input['dni'],
        $input['nombre_conductor'],
        $input['apellido'],
        $input['telefono'],
        $input['placa'],
        $idConductor
    );
    if (!$stmtConductor->execute()) throw new Exception("Error al ejecutar actualización de conductor: " . $stmtConductor->error);

    // Confirmar transacción
    $conn->commit();

    echo json_encode(["exito" => true, "mensaje" => "SOAT y conductor actualizados correctamente"]);

} catch (Exception $e) {
    $conn->rollback(); // Revertir en caso de error
    http_response_code(400);
    echo json_encode(["exito" => false, "error" => $e->getMessage()]);
} finally {
    $conn->close();
}
?>