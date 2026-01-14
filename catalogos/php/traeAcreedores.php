<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
try {
    $acreedores = array();
    $sqlSelect = $con->prepare("SELECT idAcreedor, nombre FROM acreedores");
    $sqlSelect->execute();
    if ($sqlSelect == false) {
        throw new Exception($con->errorInfo());
    }
    $acreedores = $sqlSelect->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['error' => false, 'acreedores' => $acreedores]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
