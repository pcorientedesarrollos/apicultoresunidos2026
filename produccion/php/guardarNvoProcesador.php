<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$idReporteProceso = $_GET["idReporteProceso"];
$idPersonalOM = $_GET["idPersonalOM"];
if(isset($_GET["organica"])){
    switch ($_GET['organica']) {
        case 0:
            $tabla = 'procesozonainocua_organico';
            break;
        case 1:
            $tabla = 'procesozonainocua_mantequilla';
            break;
        case 2:
            $tabla = 'procesozonainocua_altiplano';
            break;
        case 3:
            $tabla = 'procesozonainocua_naranjo';
            break;
        case 4:
            $tabla = 'procesozonainocua_aguacate';
            break;
        case 5:
            $tabla = 'procesozonainocua_mezquite';
            break;
    }
    $sql = "INSERT INTO $tabla (idPersonalOM ,idReporteProceso) VALUES (:idPersonalOM, :idReporteProceso)";
    $data = $con->prepare($sql);
    $data->bindParam(':idPersonalOM', $idPersonalOM);
    $data->bindParam(':idReporteProceso', $idReporteProceso);
    $data->execute();
    if ($data == false) {
        echo mysql_error();
    } else {
        echo 'Nuevo personal agregado';
    }
}else{
    $sql = "INSERT INTO procesozonainocua (idPersonalOM ,idReporteProceso) VALUES (:idPersonalOM, :idReporteProceso)";
    $data = $con->prepare($sql);
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