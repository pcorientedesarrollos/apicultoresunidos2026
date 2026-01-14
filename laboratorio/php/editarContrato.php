<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$json = file_get_contents("php://input");

$idEnvio = $_GET["idEnvio"];
$datos = json_decode($json);

switch($_GET['miel']){
    case 1:
    $muestras = 'enviomuestras';
    break;
    case 2:
    $muestras = 'enviomuestras_organico';    
    break;
}

$sqlUp = "UPDATE $muestras SET numContrato = :numContrato, contrato = :contrato WHERE idEnvio = :idEnvio";
    $dats = $con->prepare($sqlUp);
    $dats->bindParam(':contrato', $datos->contrato);
    $dats->bindParam(':numContrato', $datos->numContrato);
    $dats->bindParam(':idEnvio', $idEnvio);
    $dats->execute();
