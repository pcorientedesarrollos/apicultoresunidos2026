<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$post = file_get_contents('php://input');

try {
    if (!$post) {
        throw new Exception('No se recibieron datos');
    } else {
        $datosEmpresa = json_decode($post);
    }

    if (isset($datosEmpresa->idExterno)) {
        $success_message = 'Se ha editado el registro';
        $sqlInsert = $con->prepare("UPDATE empresasexternas SET nombre = :nombre, idLocalidad = :idLocalidad, contacto = :contacto, telefono = :telefono WHERE idExterno = :idExterno");
        $sqlInsert->bindParam(':idExterno', $datosEmpresa->idExterno);

    } else {
        $success_message = 'Se ha agregado un nuevo acreedor';
        $sqlInsert = $con->prepare("INSERT INTO empresasexternas (nombre, idLocalidad, contacto, telefono) VALUES (:nombre, :idLocalidad, :contacto, :telefono)");
    }

    $sqlInsert->bindParam(':nombre', $datosEmpresa->nombre);
    $sqlInsert->bindParam(':idLocalidad', $datosEmpresa->idLocalidad);
    $sqlInsert->bindParam(':contacto', $datosEmpresa->contacto);
    $sqlInsert->bindParam(':telefono', $datosEmpresa->telefono);
    $sqlInsert->execute();

    if ($sqlInsert == false) {
        throw new Exception($con->errorInfo());
    }
    echo json_encode(['error' => false, 'message' => $success_message]);

} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}

