<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$post = file_get_contents('php://input');

try {
    if (!$post) {
        throw new Exception('No se recibieron datos');
    } else {
        $nuevoAcreedor = json_decode($post);
    }

    if (isset($nuevoAcreedor->idAcreedor)) {
        $success_message = 'Se ha editado el registro';
        $sqlInsert = $con->prepare("UPDATE acreedores SET nombre = :nombre WHERE idAcreedor = :idAcreedor");
        $sqlInsert->bindParam(':idAcreedor', $nuevoAcreedor->idAcreedor);

    } else {
        $success_message = 'Se ha agregado un nuevo acreedor';
        $sqlInsert = $con->prepare("INSERT INTO acreedores (nombre) VALUES (:nombre)");
    }

    $sqlInsert->bindParam(':nombre', $nuevoAcreedor->nombre);
    $sqlInsert->execute();

    if ($sqlInsert == false) {
        throw new Exception($con->errorInfo());
    }
    echo json_encode(['error' => false, 'message' => $success_message]);

} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}

