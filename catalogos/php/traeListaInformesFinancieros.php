<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
try {
    $informesFinancieros = array();
    $sqlSelect = $con->prepare("SELECT * FROM informesfinancieros");
    $sqlSelect->execute();
    if ($sqlSelect == false) {
        throw new Exception($con->errorInfo());
    }
    $informesFinancieros = $sqlSelect->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['error' => false, 'informesFinancieros' => $informesFinancieros]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
