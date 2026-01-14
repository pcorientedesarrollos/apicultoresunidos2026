<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
try {
    $externos = array();
    $sqlSelect = $con->prepare("SELECT emx.*, l.localidad
    FROM empresasexternas emx
    LEFT JOIN localidades l ON l.idlocalidad = emx.idLocalidad");
    $sqlSelect->execute();
    if ($sqlSelect == false) {
        throw new Exception($con->errorInfo());
    }
    $externos = $sqlSelect->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['error' => false, 'externos' => $externos]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
