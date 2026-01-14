<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$idLoteInterno = $_GET["idLoteInterno"];

$tipo = $_GET['tipoMiel'];

switch($tipo){
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

$sqlP = "SELECT marcaFinalCliente FROM $calidad WHERE idLoteInterno = :idLoteInterno";
$dato = $con->prepare($sqlP);
$dato->bindParam(':idLoteInterno', $idLoteInterno);
$dato->execute();

if ($dato == false) {
    echo mysql_error();
} else {
    $tAnalisis = new stdClass();
    while ($rs = $dato->fetch()) {
        $tAnalisis->marcaFinalCliente = $rs["marcaFinalCliente"];
    }
    echo json_encode($tAnalisis);
}
?>