<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO(); $con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$resultado = array();
try {
    if (!isset($_GET['tipo'])) {
        throw new Exception('No se recibieron parámetros');
    } else {
        $tipoDeClasificacion = $_GET['tipo'];
    }

    $consulta = $con->prepare("SELECT * FROM clasificacionescera WHERE tipo = :tipo");
    $consulta->bindParam(':tipo', $tipoDeClasificacion);
    $consulta->execute();
    if($consulta == FALSE) {
        throw new Exception($con->errorInfo());
    } else {
        $resultado = $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    echo json_encode(['error'=>false, 'clasificaciones'=>$resultado]);
} catch (Exception $e) {
    echo json_encode(['error'=>true, 'message'=>$e->getMessage(), 'clasificaciones'=>$resultado]);
    exit();
}