<?php

include_once '../../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$idConcepto = file_get_contents('php://input');

try {

    $listaAcreedores = array();
    $traeAcreedores = $con->prepare("SELECT * FROM acreedores");
    $traeAcreedores->execute();
    if($traeAcreedores == FALSE){
        throw new Exception($con->errorInfo());
    }
    $listaAcreedores = $traeAcreedores->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['error'=>false, 'listaAcreedores'=>$listaAcreedores]);
} catch (Exception $e) {
    echo json_encode(['error'=>true, 'message'=>$e->getMessage()]);
}

?>