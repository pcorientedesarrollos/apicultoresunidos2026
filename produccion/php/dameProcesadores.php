<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$idReporteProceso = $_GET["idReporteProceso"];

$array = array();
if(isset($_GET["organica"])){
    $sqlPersonalFolios = "SELECT pd.idProceso, pd.idPersonalOM, om.nombre 
    FROM procesozonainocua_organico pd 
    LEFT JOIN personaloaxaca om ON om.idPersonalOM = pd.idPersonalOM
    WHERE idReporteProceso = idReporteProceso";
$datos = $con->prepare($sqlPersonalFolios);
$datos->bindParam(':idReporteProceso', $idReporteProceso);
$datos->execute();

if ($datos == false) {
echo mysql_error();
} else {
while ($rs = $datos->fetch()) {
$pProceso = new stdClass();
$pProceso->idProceso = $rs["idProceso"];
$pProceso->idPersonalOM = $rs["idPersonalOM"];
$pProceso->nombre = $rs["nombre"];
$array[] = $pProceso;
}
}

echo json_encode($array);
}else{
    $sqlPersonalFolios = "SELECT pd.idProceso, pd.idPersonalOM, om.nombre 
    FROM procesozonainocua pd 
    LEFT JOIN personaloaxaca om ON om.idPersonalOM = pd.idPersonalOM
    WHERE idReporteProceso = idReporteProceso";
$datos = $con->prepare($sqlPersonalFolios);
$datos->bindParam(':idReporteProceso', $idReporteProceso);
$datos->execute();

if ($datos == false) {
echo mysql_error();
} else {
while ($rs = $datos->fetch()) {
$pProceso = new stdClass();
$pProceso->idProceso = $rs["idProceso"];
$pProceso->idPersonalOM = $rs["idPersonalOM"];
$pProceso->nombre = $rs["nombre"];
$array[] = $pProceso;
}
}

echo json_encode($array);
}
?>