<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO(); $con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$tipoAlmacen = file_get_contents('php://input');

$resultado = array();
try{
    if(!$tipoAlmacen){
        throw new Exception('No se recibió un parámetro.');
    }
    $sql = "SELECT ca.idCondicionAlmacenamiento, ca.almacen, ca.idMes, m.mes
            FROM condicionesalmacenamiento ca INNER JOIN meses m ON m.idMes = ca.idMes 
            WHERE ca.almacen = :almacen GROUP BY mes ORDER BY idMes ASC";
    $datos = $con->prepare($sql);
    $datos->bindParam(':almacen', $tipoAlmacen);
    $datos->execute();
    if($datos == FALSE){
        throw new Exception($con->errorInfo());
    }

    foreach($datos->fetchAll(PDO::FETCH_ASSOC) as $res){
        $datosR = $con->prepare("SELECT count(c.idCondicionAlmacenamiento) as registro1,
        (SELECT count(c.idCondicionAlmacenamiento) FROM condicionesalmacenamiento c WHERE c.semana2 = 1 AND c.idMes = :idMes AND c.almacen = :almacen) as registro2,
        (SELECT count(c.idCondicionAlmacenamiento) FROM condicionesalmacenamiento c WHERE c.semana3 = 1 AND c.idMes = :idMes AND c.almacen = :almacen) as registro3,
        (SELECT count(c.idCondicionAlmacenamiento) FROM condicionesalmacenamiento c WHERE c.semana4 = 1 AND c.idMes = :idMes AND c.almacen = :almacen) as registro4,
        (SELECT count(c.idCondicionAlmacenamiento) FROM condicionesalmacenamiento c WHERE c.semana5 = 1 AND c.idMes = :idMes AND c.almacen = :almacen) as registro5
        FROM condicionesalmacenamiento c INNER JOIN meses m ON m.idMes  = c. idMes WHERE c.semana1 = 1 AND c.idMes = :idMes AND c.almacen = :almacen ORDER BY mes ASC");
        $datosR->bindParam(':idMes', $res['idMes']);
        $datosR->bindParam(':almacen', $res['almacen']);
        $datosR->execute();
        if($datosR == FALSE){
            throw new Exception($con->errorInfo());
        }
        $condiciones = $datosR->fetch(PDO::FETCH_ASSOC);
        foreach($condiciones as $key => $value){
            $res[$key] = $condiciones[$key];
        }
        array_push($resultado, $res);
    };
} catch(Exception $e){
    echo json_encode(['error'=>true, 'message'=>$e->getMessage(), 'data'=>$resultado]);
    exit();
}

echo json_encode(['error'=>false, 'message'=>'Consulta de datos satisfactoria', 'data'=>$resultado]);
