<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$json = file_get_contents("php://input");

$idLoteExperimental = $_GET["idLoteExperimental"];
$datos = json_decode($json);

switch($_GET['miel']){
    case 1:
    $experimental = 'experimental';
    break;
    case 2:
    $experimental = 'experimental_organico';    
    break;
    case 5:
    $experimental = 'experimental_mantequilla';    
    break;
    case 6:
    $experimental = 'experimental_altiplano';    
    break;
    case 7:
    $experimental = 'experimental_naranjo';    
    break;
    case 8:
    $experimental = 'experimental_aguacate';    
    break;
    case 9:
    $experimental = 'experimental_mezquite';    
    break;
}

$sqlUp = "UPDATE $experimental SET contrato = :contrato, numContrato = :numContrato WHERE idLoteExperimental = :idLoteExperimental";
    $dats = $con->prepare($sqlUp);
    $dats->bindParam(':contrato', $datos->contrato);
    $dats->bindParam(':numContrato', $datos->numContrato);
    $dats->bindParam(':idLoteExperimental', $idLoteExperimental);
    $dats->execute();

?>