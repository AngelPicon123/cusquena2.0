<?php
header("Content-Type: application/json"); // Siempre responder con JSON

$conn = new mysqli("localhost", "root", "", "la_cusquena");
if ($conn->connect_error) {
    echo json_encode(["exito" => false, "error" => "Conexión fallida"]);
    exit();
}

$accion = $_POST['accion'] ?? ''; // Nueva variable para verificar si es una acción de actualización o eliminación

if ($accion == 'eliminar') {
    $id = $_POST['id_conductor'] ?? null;

    if (!$id) {
        echo json_encode(["exito" => false, "error" => "ID de conductor no proporcionado"]);
        exit();
    }

    // Primero elimina los registros relacionados en la tabla soat
    $sqlSoat = "DELETE FROM soat WHERE idConductor = ?";
    $stmtSoat = $conn->prepare($sqlSoat);
    $stmtSoat->bind_param("i", $id);

    if (!$stmtSoat->execute()) {
        echo json_encode(["exito" => false, "error" => "Error al eliminar soat: " . $stmtSoat->error]);
        $stmtSoat->close();
        $conn->close();
        exit();
    }
    $stmtSoat->close();

    // Luego elimina el conductor
    $sql = "DELETE FROM conductor WHERE idConductor = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["exito" => true]);
    } else {
        echo json_encode(["exito" => false, "error" => "Error al eliminar conductor: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
    exit();
}

// REGISTRAR Y EDITAR
$id = $_POST['id_conductor'] ?? null;
$nombre = $_POST['nombre'] ?? '';
$apellido = $_POST['apellido'] ?? '';
$telefono = $_POST['telefono'] ?? '';
$dni = $_POST['dni'] ?? '';
$placa = $_POST['placa'] ?? '';
$detalle = $_POST['detalle'] ?? '';
$estado = $_POST['estado'] ?? '';
$idTipoConductor = $_POST['idTipoConductor'] ?? ''; 

// VALIDACIÓN MODIFICADA PARA TIPO DE CONDUCTOR
if (empty($nombre) || empty($apellido) || empty($telefono) || empty($dni) || 
    empty($placa) || empty($estado) || ($idTipoConductor === '' && !$id)) {
    echo json_encode([
        "exito" => false, 
        "error" => "Campos incompletos",
        "debug" => $_POST // Para ver qué llega realmente
    ]);
    exit();
}

// CONVERTIR A NULL SI ESTÁ VACÍO (para registros existentes)
if ($idTipoConductor === '' && $id) {
    $idTipoConductor = null;
}

// ACTUALIZAR CONDUCTOR
if ($id) {
    $sql = "UPDATE conductor SET 
                nombre = ?, 
                apellido = ?, 
                telefono = ?, 
                dni = ?, 
                placa = ?, 
                detalle = ?, 
                estado = LOWER(?),  
                idTipoConductor = ?  
            WHERE idConductor = ?";
    
    $stmt = $conn->prepare($sql);
    // Bind especial para manejar NULL
    if ($idTipoConductor === null) {
        $stmt->bind_param("ssssssssi", 
            $nombre, $apellido, $telefono, $dni, $placa,
            $detalle, $estado, null, $id
        );
    } else {
        $stmt->bind_param("ssssssssi", 
            $nombre, $apellido, $telefono, $dni, $placa,
            $detalle, $estado, $idTipoConductor, $id
        );
    }
} else {  // <--- Aquí estaba el error (había una llave { adicional)
    // REGISTRAR NUEVO CONDUCTOR
    $sql = "INSERT INTO conductor (
                nombre, apellido, telefono, dni, placa, estado, detalle, idTipoConductor
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssss", $nombre, $apellido, $telefono, $dni, $placa, $estado, $detalle, $idTipoConductor);
}

// Ejecutar la consulta y devolver el resultado
if ($stmt->execute()) {
    echo json_encode(["exito" => true]);
} else {
    echo json_encode(["exito" => false, "error" => $stmt->error]);
}

$stmt->close();
$conn->close();
