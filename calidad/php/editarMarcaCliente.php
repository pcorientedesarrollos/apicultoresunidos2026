<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$idLoteInterno = $_GET["idLoteInterno"];
$marcaFinalCliente = $_GET["marcaFinalCliente"];

switch($_GET['miel']){
    case '1':
    $calidad = 'calidad';
    break;
    case '2':
    $calidad = 'calidad_organico';
    break;
    case '5':
    $calidad = 'calidad_mantequilla';
    break;
    case '6':
    $calidad = 'calidad_altiplano';
    break; 
    case '7':
    $calidad = 'calidad_naranjo';
    break;
    case '8':
    $calidad = 'calidad_aguacate';
    break; 
    case '9':
    $calidad = 'calidad_mezquite';
    break;       
}

$sqlUp = "UPDATE $calidad SET marcaFinalCliente = :marcaFinalCliente WHERE idLoteInterno = :idLoteInterno";
$dats = $con->prepare($sqlUp);
$dats->bindParam(':marcaFinalCliente', $marcaFinalCliente);
$dats->bindParam(':idLoteInterno', $idLoteInterno);
$dats->execute();
if($dats == false){
    echo 0;
}else{
    echo 1;
}
?>