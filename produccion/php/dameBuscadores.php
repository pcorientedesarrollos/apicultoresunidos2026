<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$idReporteProceso = $_GET["idReporteProceso"];

$array = array();
if(isset($_GET["organica"])){
    $sqlPersonalFolios = "SELECT pd.idBusqueda, pd.idPersonalOM, om.nombre 
    FROM busquedadefolios_organico pd 
    LEFT JOIN personaloaxaca om ON om.idPersonalOM = pd.idPersonalOM
    WHERE idReporteProceso = :idReporteProceso";
$datos = $con->prepare($sqlPersonalFolios);
$datos->bindParam(':idReporteProceso', $idReporteProceso);
$datos->execute();

if ($datos == false) {
echo mysql_error();
} else {
while ($rs = $datos->fetch()) {
$pFolios = new stdClass();
$pFolios->idBusqueda = $rs["idBusqueda"];
$pFolios->idPersonalOM = $rs["idPersonalOM"];
$pFolios->nombre = $rs["nombre"];
$array[] = $pFolios;
}
}

echo json_encode($array);
}else{
    $sqlPersonalFolios = "SELECT pd.idBusqueda, pd.idPersonalOM, om.nombre 
    FROM busquedadefolios pd 
    LEFT JOIN personaloaxaca om ON om.idPersonalOM = pd.idPersonalOM
    WHERE idReporteProceso = :idReporteProceso";
$datos = $con->prepare($sqlPersonalFolios);
$datos->bindParam(':idReporteProceso', $idReporteProceso);
$datos->execute();

if ($datos == false) {
echo mysql_error();
} else {
while ($rs = $datos->fetch()) {
$pFolios = new stdClass();
$pFolios->idBusqueda = $rs["idBusqueda"];
$pFolios->idPersonalOM = $rs["idPersonalOM"];
$pFolios->nombre = $rs["nombre"];
$array[] = $pFolios;
}
}

echo json_encode($array);
}

?>