<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$post = file_get_contents('php://input');

try {
    if (!$post) {
        throw new Exception('No se recibieron datos');
    } else {
        $tipo = json_decode($post);
    }

    if (isset($tipo->idMovimiento)) {
        $success_message = 'Se ha editado el registro';
        $sqlInsert = $con->prepare("UPDATE tiposdemovimientos SET movimiento = :movimiento WHERE idMovimiento = :idMovimiento");
        $sqlInsert->bindParam(':idMovimiento', $tipo->idMovimiento);

    } else {
        $success_message = 'Se ha agregado un nuevo tipo de movimiento';
        $sqlInsert = $con->prepare("INSERT INTO tiposdemovimientos (movimiento) VALUES (:movimiento)");
    }

    $sqlInsert->bindParam(':movimiento', $tipo->movimiento);
    $sqlInsert->execute();

    if ($sqlInsert == false) {
        throw new Exception($con->errorInfo());
    }
    
    echo json_encode(['error' => false, 'message' => $success_message]);

} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}

