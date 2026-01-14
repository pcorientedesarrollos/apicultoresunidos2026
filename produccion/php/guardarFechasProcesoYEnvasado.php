<?php

$json = file_get_contents("php://input");
$datos = json_decode($json);
$idLoteInterno = $_GET["idLoteInterno"];

$proceso = $_GET["fechaProceso"];
//$fechaProceso = $_GET["fechaProceso"];
//$fProceso = DateTime::createFromFormat('d/m/Y', $fechaProceso);
//$proceso = $fProceso->format('Y-m-d');

$envasado = $_GET["fechaEnvasado"];
//$fechaEnvasado = $_GET["fechaEnvasado"];
//$fEnvasado = DateTime::createFromFormat('d/m/Y', $fechaEnvasado);
//$envasado = $fEnvasado->format('Y-m-d');

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

if(isset($_GET["organica"])){
    switch($_GET['organica']){
        case '0':
            $calidad = 'calidad_organico';
        break;
        case '1':
            $calidad = 'calidad_mantequilla';
        break;
        case '2':
            $calidad = 'calidad_altiplano';
        break;
        case '3':
            $calidad = 'calidad_naranjo';
        break;
        case '4':
            $calidad = 'calidad_aguacate';
        break;
        case '5':
            $calidad = 'calidad_mezquite';
        break;
    }
    $sql = "UPDATE  $calidad SET fechaProceso = :proceso, fechaEnvasado = :envasado WHERE idLoteInterno = :idLoteInterno";
    $data = $con->prepare($sql);
    $data->bindParam(':proceso', $proceso);
    $data->bindParam(':envasado', $envasado);
    $data->bindParam(':idLoteInterno', $idLoteInterno);
    $data->execute();
    if ($data == false) {
        echo mysql_error();
    } else {
        echo "Fechas disponible";
    }
}else{
    $sql = "UPDATE calidad SET fechaProceso = :proceso, fechaEnvasado = :envasado WHERE idLoteInterno = :idLoteInterno";
    $data = $con->prepare($sql);
    $data->bindParam(':proceso', $proceso);
    $data->bindParam(':envasado', $envasado);
    $data->bindParam(':idLoteInterno', $idLoteInterno);
    $data->execute();
    if ($data == false) {
        echo mysql_error();
    } else {
        echo "Fechas disponible";
    }
}
?>