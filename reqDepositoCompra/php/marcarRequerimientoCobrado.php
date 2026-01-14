<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

try {

    if (!isset($_GET['idDetalle'])) {
        throw new Exception('No se recibió parámetro');
    } else {
        $idDetalle = $_GET['idDetalle'];
    }

    $sql = "UPDATE requisiciondetalle SET cobrado = '1' WHERE idDetalle = :idDetalle";
    $data = $con->prepare($sql);
    $data->bindParam(':idDetalle', $idDetalle);
    $data->execute();
    if (!$data) {
        throw new Exception($con->errorInfo());
    }

    echo json_encode(['error' => false, 'message' => 'Se cambió el estado del requerimiento']);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}