<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO(); $con = $pdo->conectar();
try {
    $unidades = array();
    $sqlSelect = $con->prepare("SELECT idUnidad, clave, nombre, descripcion FROM `unidadesdemedida`;");
    $sqlSelect->execute();
    if($sqlSelect == FALSE){ throw new Exception($con->errorInfo()); }
    $unidades = $sqlSelect->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['error'=>false, 'unidades'=>$unidades]);
} catch (Exception $e) {
    echo json_encode(['error'=>true, 'message'=>$e->getMessage()]);
}
