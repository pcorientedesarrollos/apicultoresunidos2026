<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$idReporteProceso = $_GET["idReporteProceso"];
$idPersonalOM = $_GET["idPersonalOM"];
if(isset($_GET["organica"])){
    switch ($_GET['organica']) {
        case 0:
            $tabla = 'conformaciondelotes_organico';
            break;
        case 1:
            $tabla = 'conformaciondelotes_mantequilla';
            break;
        case 2:
            $tabla = 'conformaciondelotes_altiplano';
            break;
        case 3:
            $tabla = 'conformaciondelotes_naranjo';
            break;
        case 4:
            $tabla = 'conformaciondelotes_aguacate';
            break;
        case 5:
            $tabla = 'conformaciondelotes_mezquite';
            break;
    }
    $sqlConfo = "INSERT INTO $tabla (idPersonalOM ,idReporteProceso) VALUES (:idPersonalOM, :idReporteProceso)";
    $data = $con->prepare($sqlConfo);
    $data->bindParam(':idPersonalOM', $idPersonalOM);
    $data->bindParam(':idReporteProceso', $idReporteProceso);
    $data->execute();
    if ($data == false) {
        echo mysql_error();
    } else {
        echo 'Nuevo personal agregado';
    }
}else{
    $sqlConfo = "INSERT INTO conformaciondelotes (idPersonalOM ,idReporteProceso) VALUES (:idPersonalOM, :idReporteProceso)";
    $data = $con->prepare($sqlConfo);
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