<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO(); $con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$postdata = file_get_contents('php://input');

try {
    if (!$postdata) {
        throw new Exception('No se recibieron parámetros');
    } else {
        $datosNuevaAuditoria = json_decode($postdata);
    }

    $sql = "INSERT INTO auditorias (fecha, usuario, estado) VALUES (:fecha, :usuario, :estado);";
    $datos = $con->prepare($sql);
    $datos->bindParam(':fecha', $datosNuevaAuditoria->fecha);
    $datos->bindParam(':usuario', $datosNuevaAuditoria->usuario);
    $datos->bindParam(':estado', $datosNuevaAuditoria->estado);
    $datos->execute();
    if ($datos == FALSE) {
        throw new Exception($con->errorInfo());
    }

    echo json_encode(['error'=>false, 'message'=>'Consulta realizada']);

} catch (Exception $e){
    echo json_encode(['error'=>true, 'message'=>$e->getMessage()]);
    exit();
}
