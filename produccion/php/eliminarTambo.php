<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO(); $con = $pdo->conectar();

try {
    if(!isset($_GET["idPesoEnvasado"])) {
        throw new Exception('No se recibieron parámetros');
    }
    
    $idPesoEnvasado = $_GET["idPesoEnvasado"];
    $sqlEliminarTambo = "DELETE FROM pesosenvasados WHERE idPesoEnvasado = :idPesoEnvasado";
    $datos = $con->prepare($sqlEliminarTambo);
    $datos->bindParam(':idPesoEnvasado', $idPesoEnvasado);
    $datos->execute();
    if ($datos == FALSE) {
        throw new Exception($con->errorInfo());
    }
    echo json_encode(['error'=>false, 'message'=>'Tambor eliminado de forma permanente']);
} catch (Exception $e) {
    echo json_encode(['error'=>true, 'message'=>$e->getMessage()]);
}
