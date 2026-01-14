<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

try {

    $querySeleccionaCompradores = $con->prepare('SELECT idcomprador,
        nombre FROM compradores WHERE estado = 1 ORDER BY nombre ASC');
    $querySeleccionaCompradores->execute();
    if (!$querySeleccionaCompradores) {
        throw new Exception($con->errorInfo());
    }
    $resultado = $querySeleccionaCompradores->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['error' => false, 'resultado' => $resultado]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}