<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO(); $con = $pdo->conectar();
$tipoDeMiel = file_get_contents('php://input');
try {
    if(!$tipoDeMiel || !isset($_GET['id'])) {
        throw new Exception('No se recibieron parámetros');
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

    $data = $con->prepare("DELETE FROM $configuracionlaboratorio_tabla WHERE idconfiguracionLaboratorio = :id ");
    $data->bindParam(':id', $id);
    $data->execute();
    
    if($data == FALSE) {
        throw new Exception($con->errorInfo());
    }
    echo json_encode(['error'=>false, 'message'=>'Se ha eliminado la configuración']);
} catch (Exception $e) {
    echo json_encode(['error'=>true, 'message'=>$e->getMessage()]);
}
