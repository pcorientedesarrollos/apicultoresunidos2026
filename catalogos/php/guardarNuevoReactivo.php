<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$post = file_get_contents('php://input');

try {
    if (!$post) {
        throw new Exception('No se recibieron datos');
    } else {
        $nuevoReactivo = json_decode($post);
    }

    if (isset($nuevoReactivo->idReactivo)) {
        $nuevo = false;
        $success_message = 'Se ha editado el registro';
        $sqlInsert = $con->prepare("UPDATE catalogoreactivos SET reactivo = :reactivo WHERE idReactivo = :idReactivo");
        $sqlInsert->bindParam(':idReactivo', $nuevoReactivo->idReactivo);
    } else {
        $nuevo = true;
        $success_message = 'Se ha agregado un nuevo reactivo';
        $sqlInsert = $con->prepare("INSERT INTO catalogoreactivos (reactivo) VALUES (:reactivo)");
    }

    $sqlInsert->bindParam(':reactivo', $nuevoReactivo->reactivo);
    $sqlInsert->execute();

    if ($sqlInsert == false) {
        throw new Exception($con->errorInfo());
    } else {
        if ($nuevo) {
            $insertarInventario = $con->prepare("INSERT INTO saldoinicialinventario (nombre, existenciaPasada, importeAcumuladoPasado, tipo) VALUES (:reactivo, 0, 0, 0)");
            $insertarInventario->bindParam(':reactivo', $nuevoReactivo->reactivo);
            $insertarInventario->execute();
            if ($insertarInventario == false) {
                throw new Exception($con->errorInfo());
            }
        }
    }
    echo json_encode(['error' => false, 'message' => $success_message]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
