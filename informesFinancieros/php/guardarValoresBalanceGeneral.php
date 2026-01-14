<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

// Guarda los datos del usuario del sistema incluyendo la clave de acceso
$postdata = file_get_contents('php://input');

try {

    $con->beginTransaction();

    if (!$postdata) {
        throw new Exception('No se recibieron datos');
    } else {
        $arreglo_valores = json_decode($postdata);
    }

    // Primero borramos los valores que ya tiene guardado para guardar los nuevos

    $queryEliminarValores = $con->prepare('DELETE FROM valoresbalancegeneral');
    $queryEliminarValores->execute();
    if ($queryEliminarValores == false) {
        throw new Exception($con->errorInfo());
    }

    foreach ($arreglo_valores as $concepto) {

        $queryInsert = $con->prepare('INSERT INTO valoresbalancegeneral (idCampo, valor) VALUES (:idCampo, :valor)');
        $queryInsert->bindParam(':idCampo', $concepto->idCampo);
        $queryInsert->bindParam(':valor', $concepto->cantidad);
        $queryInsert->execute();
        if ($queryInsert == false) {
            throw new Exception($con->errorInfo());
        }

    }

    $con->commit();
    echo json_encode(['error' => false, 'message' => 'Se guardaron los valores registrados']);
} catch (Exception $e) {
    $con->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
