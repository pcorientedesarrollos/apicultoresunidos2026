<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
try {
    $opcionSalidas = array();
    $sqlSelect = $con->prepare("SELECT idConcepto, concepto FROM otrosconceptossalida");
    $sqlSelect->execute();
    if ($sqlSelect == false) {
        throw new Exception($con->errorInfo());
    }
    $opcionSalidas = $sqlSelect->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['error' => false, 'opcionSalidas' => $opcionSalidas]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
