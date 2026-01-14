<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO(); $con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$datos = file_get_contents('php://input');
$resultado = array();
$almacen = 'NO DEFINIDO';
try {
     if(!$datos){
         throw new Exception('No se recibieron los parámetros');
     } else {
         $datos = json_decode($datos);
     }

    $idMes = $datos->idMes;
    $idAlmacen = $datos->idAlmacen;
    $datos = $con->prepare("SELECT ca.idCondicionAlmacenamiento, ca.almacen, ca.idMes, ca.semana1, ca.semana2, ca.semana3, ca.semana4, ca.semana5,
                            t.idTema, t.tema, m.mes FROM condicionesalmacenamiento ca INNER JOIN temas t ON t.idTema = ca.idTema INNER JOIN meses m 
                            ON m.idMes = ca.idMes WHERE ca.idMes = :idMes AND ca.almacen = :idAlmacen");
    $datos->bindParam(':idMes', $idMes);
    $datos->bindParam(':idAlmacen', $idAlmacen);
    $datos->execute();
    if($datos == FALSE){
        throw new Exception('No se han podido traer los datos');
    }
    $resultado = $datos->fetchAll(PDO::FETCH_ASSOC);

    // Consultar el nombre del almacén
    $consulta = $con->prepare("SELECT CONCAT(subarea, ' - ', nombre) as nombre FROM almacenestemporales WHERE idSubarea = :almacen");
    $consulta->bindParam(':almacen', $idAlmacen);
    $consulta->execute();
    $consulta->bindColumn('nombre', $almacen);
    if($consulta == FALSE){
        throw new Exception($con->erroInfo());
    } else {
        $consulta->fetch(PDO::FETCH_BOUND);
    }
    echo json_encode(['error'=>false, 'message'=>'Consulta satisfactoria', 'data'=>$resultado, 'nombreAlmacen'=>$almacen]);
} catch(Exception $e){
    echo json_encode(['error'=>true, 'message'=>$e->getMessage(), 'data'=>$resultado, 'nombreAlmacen'=>$almacen]);
}
