<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$post = file_get_contents('php://input');

try {
    if (!$post) {
        throw new Exception('No se recibieron datos');
    } else {
        $floracion = json_decode($post);
    }

    if (isset($floracion->idFloracion)) {
        $success_message = 'Se ha editado el registro';
        $sqlInsert = $con->prepare("UPDATE floraciones SET floracion = :floracion WHERE idFloracion = :idFloracion");
        $sqlInsert->bindParam(':idFloracion', $floracion->idFloracion);
    } else {
        $success_message = 'Se ha agregado una nueva floración';
        $sqlInsert = $con->prepare("INSERT INTO floraciones (floracion) VALUES (:floracion)");
    }

    $sqlInsert->bindParam(':floracion', $floracion->floracion);
    // $sqlInsert->bindParam(':codigo', $floracion->codigo);            
    $sqlInsert->execute();

    if ($sqlInsert == false) {
        throw new Exception($con->errorInfo());
    }
    echo json_encode(['error' => false, 'message' => $success_message]);

} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}

