<?php
include_once '../../../DAOConeccion/conePDO.php';
$pdo = new conePDO(); $con = $pdo->conectar();
$post = file_get_contents('php://input');

try {
    if(!$post) { throw new Exception('No se recibieron datos'); } else { $nuevoPropio = json_decode($post); }

    $sqlInsert = $con->prepare("INSERT INTO propios (nombre) VALUES (:nombre)");
    $sqlInsert->bindParam(':nombre', $nuevoPropio->nombre);
    $sqlInsert->execute();

    if($sqlInsert == FALSE) { throw new Exception($con->errorInfo());}
    echo json_encode(['error'=>false, 'message'=>'Se ha agregado un nuevo registro']);

} catch (Exception $e) {
    echo json_encode(['error'=>true, 'message'=>$e->getMessage()]);
}

