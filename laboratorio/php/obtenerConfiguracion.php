<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO; $con = $pdo->conectar();
$tipoDeMiel = file_get_contents('php://input');
$array = array();
try {
    if(!$tipoDeMiel || !isset($_GET['id'])){
        throw new Exception('No se recibieron los parámetros');
    } else {
        $id = $_GET["id"];
    }
    switch($tipoDeMiel){
        case '1':
        $configuracionlaboratorio_tabla = 'configuracionlaboratorio';
        break;
        case '2':
        $configuracionlaboratorio_tabla = 'configuracionlaboratorio_organico';
        break;
        default:
            throw new Exception('Parámetro de tipo de miel inválido');
        break;
    }

    $data = $con->prepare("SELECT * FROM $configuracionlaboratorio_tabla cl
    INNER JOIN rangos rn ON cl.signo = rn.idRangos
    WHERE idOpcionLab = :id");
    $data->bindParam(':id', $id);
    $data->execute();

    if($data == FALSE){
        throw new Exception($con->errorInfo());
    }

    while ($rs = $data->fetch()) {
        $rangos = new stdClass();
        $rangos->id = $rs["idconfiguracionLaboratorio"];
        $rangos->idConfiguracionLaboratorio = $rs["idconfiguracionLaboratorio"];
        $rangos->idOpcionLab = $rs["idOpcionLab"];
        $rangos->simbolo = $rs["simbolo"];
        $rangos->rango1 = $rs["rango1"];
        $rangos->rango2 = $rs["rango2"];
        $rangos->descripcion = $rs["descripcion"];
        $array[] = $rangos;
    }
    echo json_encode(['error'=>false, 'message'=>'Consulta sin fallos', 'data'=>$array]);
} catch (Exception $e) {
    echo json_encode(['error'=>true, 'message'=>$e->getMessage(), 'data'=>$array]);
    exit();
}
