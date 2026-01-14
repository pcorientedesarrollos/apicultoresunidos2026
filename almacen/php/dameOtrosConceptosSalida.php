<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO(); $con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    $sqlSelecciona = $con->prepare("SELECT * FROM `otrosconceptossalida`;");
    $sqlSelecciona->execute();
    if($sqlSelecciona == FALSE) {
        throw new Exception($con->errorInfo());
    }

    $conceptos = $sqlSelecciona->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['error'=>false, 'conceptos'=>$conceptos]);
} catch (Exception $e) {
    echo json_encode(['error'=>true, 'message'=>$e->getMessage()]);
}