<?php

include_once '../../DAOConeccion/conePDO.php';
date_default_timezone_set('America/Merida');
$pdo = new conePDO();
$con = $pdo->conectar();

// Este archivo verifica si existe movimientos en auxiliar de bancos en cierto mes

$mes = file_get_contents('php://input');
$respuesta = false;
try {

    if(!$mes) {
        throw new Exception('No se recibió el parámetro esperado');
    }


    $dato = $con->prepare("SELECT idAuxiliar FROM auxiliardebancos WHERE idMes = :idMes ORDER BY idAuxiliar");
    $dato->bindParam(':idMes', $mes);
    $dato->execute();
    
    if ($dato->rowCount() >= 1) {
        $respuesta = 1; // Puede continuar
    } else {
        $dato = $con->prepare("SELECT idMes FROM auxiliardebancos WHERE idMes < :idMes ORDER BY idAuxiliar");
        $dato->bindParam(':idMes', $mes);
        $dato->execute();
        if ($dato->rowCount() == 0) {
            $respuesta = 0; // Pida saldo inicial
        } else {
            $respuesta = 2; // No puede continuar
        }
    }

    echo json_encode(['error'=>false, 'message'=>'Consulta realizada', 'resultado'=>$respuesta]);

} catch(Exception $e) {
    echo json_encode(['error'=>true, 'message'=>$e->getMessage()]);
}