<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$post = file_get_contents('php://input');
if ($post) {
    $idMes = json_decode($post);
    $datos = $con->prepare("SELECT idCajaChica FROM cajachica WHERE idMes > $idMes LIMIT 1");
    $datos->execute();
    
    if ($datos->rowCount() == 1) {
        echo json_encode( ['block'=>true] );
    } else {
        echo json_encode( ['block'=>false] );
    }
}
