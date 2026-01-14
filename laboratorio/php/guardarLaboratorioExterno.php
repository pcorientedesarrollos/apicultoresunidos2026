<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$post = file_get_contents('php://input');

try {
    if (!$post) {
        throw new Exception('No se recibieron datos');
    } else {
        $laboratorio = json_decode($post);
    }

    if (isset($laboratorio->idLaboratorio)) {
        $success_message = 'Se ha editado el registro';
        $sqlInsert = $con->prepare("UPDATE laboratoriosexternos SET nombre = :nombre WHERE idLaboratorio = :idLaboratorio");
        $sqlInsert->bindParam(':idLaboratorio', $laboratorio->idLaboratorio);
    } else {
        $success_message = 'Se ha agregado un nuevo laboratorio';
        $sqlInsert = $con->prepare("INSERT INTO laboratoriosexternos (nombre) VALUES (:nombre)");
    }

    $sqlInsert->bindParam(':nombre', $laboratorio->nombre);
    $sqlInsert->execute();

    if ($sqlInsert == false) {
        throw new Exception($con->errorInfo());
    }
    echo json_encode(['error' => false, 'message' => $success_message]);

} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}

