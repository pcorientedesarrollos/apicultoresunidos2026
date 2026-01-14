<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$idLoteInterno = $_GET["idLoteInterno"];

$autorizado = "";

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

$sqlMax = "SELECT MAX(idLoteInterno) AS folio FROM $calidad";
$datMax = $con->prepare($sqlMax);
$datMax->execute();
$datMax->bindColumn('folio', $respuesta);
$datMax->fetch(PDO::FETCH_BOUND);
$comparar = $idLoteInterno - $respuesta;

if ($comparar == 0) {
    $autorizado = 1;
} else {
    $autorizado = 2;
}
echo json_encode($autorizado);

?>