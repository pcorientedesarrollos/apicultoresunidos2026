<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO(); $con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$postdata = file_get_contents('php://input');

try{
    if (!$postdata) {
        throw new Exception('No se recibió parametro.');
    } else {
        $postdata = json_decode($postdata);
        $idMes = $postdata->idMes;
        $idAlmacen = $postdata->idAlmacen;
    }

    $datos = $con->prepare("SELECT idMes  FROM condicionesalmacenamiento WHERE idMes = :idMes AND almacen = :almacen GROUP BY idMes");
    $datos->bindParam(':idMes', $idMes);
    $datos->bindParam(':almacen', $idAlmacen);
    $datos->execute();
    if($datos->rowCount() >= 1){
        echo json_encode(['error'=>false, 'message'=>'Los registros del mes ya han sido creados']);
    } else {
        for ($i = 1; $i < 8; $i++) {
            $dato = $con->prepare("INSERT INTO condicionesalmacenamiento
                                    (almacen, idMes, idTema, semana1, semana2, semana3, semana4, semana5)
                                    VALUES (:almacen, :idMes, '$i', '1', '1', '1', '1', '1');");
            $dato->bindParam(':idMes', $idMes);
            $dato->bindParam(':almacen', $idAlmacen);
            $dato->execute();
            if($dato->rowCount() != 1){
                throw new Exception('No se ha podido crear uno o más campos en la tabla');
            }
        }
        echo json_encode(['error'=>false, 'message'=>'Se han creado los registros']);
    }
} catch(Exception $e){
    echo json_encode(['error'=>true, 'message'=>$e->getMessage()]);
}

?>