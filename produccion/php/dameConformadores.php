<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$idReporteProceso = $_GET["idReporteProceso"];

$array = array();
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
    $sqlPersonalCon = "SELECT pd.idConformacionLote, pd.idPersonalOM, om.nombre 
    FROM $tabla pd 
    LEFT JOIN personaloaxaca om ON om.idPersonalOM = pd.idPersonalOM
    WHERE idReporteProceso = :idReporteProceso";
$datos = $con->prepare($sqlPersonalCon);
$datos->bindParam(':idReporteProceso', $idReporteProceso);
$datos->execute();

if ($datos == false) {
echo mysql_error();
} else {
while ($rs = $datos->fetch()) {
$pConformacion = new stdClass();
$pConformacion->idConformacionLote = $rs["idConformacionLote"];
$pConformacion->idPersonalOM = $rs["idPersonalOM"];
$pConformacion->nombre = $rs["nombre"];
$array[] = $pConformacion;
}
}

echo json_encode($array);
}else{
    $sqlPersonalCon = "SELECT pd.idConformacionLote, pd.idPersonalOM, om.nombre 
    FROM conformaciondelotes pd 
    LEFT JOIN personaloaxaca om ON om.idPersonalOM = pd.idPersonalOM
    WHERE idReporteProceso = :idReporteProceso";
$datos = $con->prepare($sqlPersonalCon);
$datos->bindParam(':idReporteProceso', $idReporteProceso);
$datos->execute();

if ($datos == false) {
echo mysql_error();
} else {
while ($rs = $datos->fetch()) {
$pConformacion = new stdClass();
$pConformacion->idConformacionLote = $rs["idConformacionLote"];
$pConformacion->idPersonalOM = $rs["idPersonalOM"];
$pConformacion->nombre = $rs["nombre"];
$array[] = $pConformacion;
}
}

echo json_encode($array);
}
?>