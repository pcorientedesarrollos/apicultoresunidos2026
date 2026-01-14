<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$idReporteProceso = $_GET["idReporteProceso"];
$idPersonalOM = $_GET["idPersonalOM"];

if(isset($_GET["organica"])){
    switch ($_GET['organica']) {
        case 0:
            $tabla = 'busquedadefolios_organico';
            break;
        case 1:
            $tabla = 'busquedadefolios_mantequilla';
            break;
        case 2:
            $tabla = 'busquedadefolios_altiplano';
            break;
        case 3:
            $tabla = 'busquedadefolios_naranjo';
            break;
        case 4:
            $tabla = 'busquedadefolios_aguacate';
            break;
        case 5:
            $tabla = 'busquedadefolios_mezquite';
            break;
    }
    $sqlBusca = "INSERT INTO $tabla (idPersonalOM ,idReporteProceso) VALUES (:idPersonalOM, :idReporteProceso)";
    $data = $con->prepare($sqlBusca);
    $data->bindParam(':idPersonalOM', $idPersonalOM);
    $data->bindParam(':idReporteProceso', $idReporteProceso);
    $data->execute();
    if ($data == false) {
        echo mysql_error();
    } else {
        echo 'Nuevo personal agregado';
    }
}else{
    $sqlBusca = "INSERT INTO busquedadefolios (idPersonalOM ,idReporteProceso) VALUES (:idPersonalOM, :idReporteProceso)";
    $data = $con->prepare($sqlBusca);
    $data->bindParam(':idPersonalOM', $idPersonalOM);
    $data->bindParam(':idReporteProceso', $idReporteProceso);
    $data->execute();
    if ($data == false) {
        echo mysql_error();
    } else {
        echo 'Nuevo personal agregado';
    }   
}
?>