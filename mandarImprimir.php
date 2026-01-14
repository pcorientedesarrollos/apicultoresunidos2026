<?php

include_once './DAOConeccion/conePDO.php';
$pdo = new conePDO(); $con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$data = file_get_contents('php://input');

try {
    if (!$data) {
        throw new Exception('No se recibió parámetros');
    } else {
        $datos = json_decode($data);
        $idTipoDeMiel = $datos->idTipoDeMiel;
        $idAlmacen = $datos->idAlmacen;
        $estado = $datos->estado;
    }

    $sql = 'INSERT INTO impresion (idAlmacen, estado, idTipoDeMiel) VALUES (:idAlmacen, :estado, :idTipoDeMiel)';
    $insert = $con->prepare($sql);
    $insert->bindParam(':idAlmacen', $idAlmacen);
    $insert->bindParam(':estado', $estado);
    $insert->bindParam(':idTipoDeMiel', $idTipoDeMiel);
    $insert->execute();

    if ($insert == FALSE) {
        throw new Exception($con->errorInfo());
    }

    echo json_encode(['error'=>false, 'message'=>'Impresión registrada']);
} catch (Exception $e) {
    echo json_encode(['error'=>true, 'message'=>$e->getMessage()]);
    exit();
}
