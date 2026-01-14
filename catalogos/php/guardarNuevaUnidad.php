<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO(); $con = $pdo->conectar();
$post = file_get_contents('php://input');

try {
    if(!$post) { throw new Exception('No se recibieron datos'); } else { $nuevaUnidad = json_decode($post); }

    $sqlInsert = $con->prepare("INSERT INTO unidadesdemedida (clave, nombre, descripcion) VALUES (:clave, :nombre, :descripcion)");
    $sqlInsert->bindParam(':clave', $nuevaUnidad->clave);
    $sqlInsert->bindParam(':nombre', $nuevaUnidad->nombre);
    $sqlInsert->bindParam(':descripcion', $nuevaUnidad->descripcion);
    $sqlInsert->execute();

    if($sqlInsert == FALSE) { throw new Exception($con->errorInfo());}
    echo json_encode(['error'=>false, 'message'=>'Se ha agregado una nueva unidad']);

} catch (Exception $e) {
    echo json_encode(['error'=>true, 'message'=>$e->getMessage()]);
}

