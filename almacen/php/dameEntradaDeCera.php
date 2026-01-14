<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO(); $con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$resultado = new stdClass();
try {

    if (!isset($_GET['idAlmacen'])) {
        throw new Exception('No se recibieron datos');
    } else {
        $idAlmacen = $_GET['idAlmacen'];
    }

    $sql = "SELECT al.*
            FROM almacenencabezadocera al
            WHERE al.idAlmacen = :idAlmacen";
    $datos = $con->prepare($sql);
    $datos->bindParam(':idAlmacen', $idAlmacen);
    $datos->execute();
    if ($datos == FALSE) {
        throw new Exception($con->errorInfo());
    }
    $resultado = $datos->fetch(PDO::FETCH_ASSOC);

    $sql = "SELECT a.*, z.nombre FROM almacencera a 
    LEFT JOIN zonascera z ON z.idZonaCera = a.zona 
    WHERE a.idAlmacenEncabezado = :idAlmacenEncabezado";
    $sqlDetalle = $con->prepare($sql);
    $sqlDetalle->bindParam(':idAlmacenEncabezado', $idAlmacen);
    $sqlDetalle->execute();

    if($sqlDetalle == FALSE) {
        throw new Exception($con->errorInfo());
    } else {
        $resultado['conceptos'] = $sqlDetalle->fetchAll(PDO::FETCH_ASSOC);
    }

    echo json_encode(['error'=>false, 'message'=>'Consulta realizada', 'data'=>$resultado]);

} catch (Exception $e){
    echo json_encode(['error'=>true, 'message'=>$e->getMessage(), 'data'=>$resultado]);
    exit();
}
