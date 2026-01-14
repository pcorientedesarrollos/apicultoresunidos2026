<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$idMes = $_GET["idMes"];

$sql = "SELECT ca.*,
        m.mes, om.nombre, om.idPersonalOM FROM disposiciondelpersonal ca  
INNER JOIN personaloaxaca om ON om.idPersonalOM = ca.idPersonalOM        
INNER JOIN meses m 
        ON m.idMes = ca.idMes
        WHERE ca.idMes = :idMes";
$datos = $con->prepare($sql);
$datos->bindParam(':idMes', $idMes);
$datos->execute();

if ($datos == false) {
    echo mysql_error();
} else {
    $arrayDisposicion = array();
    while ($respuAbono = $datos->fetch()) {
        $disposicion = new stdClass();
        $disposicion->idDisposicion = $respuAbono["idDisposicion"];
        $disposicion->mes = $respuAbono["mes"];
        $disposicion->idMes = $respuAbono["idMes"];
        $disposicion->idPersonalOM = $respuAbono["idPersonalOM"];
        $disposicion->nombre = $respuAbono["nombre"];
        $disposicion->observaciones = $respuAbono["observaciones"];

        $disposicion->semana1 = $respuAbono["semana1"];
        $disposicion->semana2 = $respuAbono["semana2"];
        $disposicion->semana3 = $respuAbono["semana3"];
        $disposicion->semana4 = $respuAbono["semana4"];
        $disposicion->semana5 = $respuAbono["semana5"];

        $disposicion->semana1A = $respuAbono["semana1A"];
        $disposicion->semana2A = $respuAbono["semana2A"];
        $disposicion->semana3A = $respuAbono["semana3A"];
        $disposicion->semana4A = $respuAbono["semana4A"];
        $disposicion->semana5A = $respuAbono["semana5A"];

        $disposicion->semana1B = $respuAbono["semana1B"];
        $disposicion->semana2B = $respuAbono["semana2B"];
        $disposicion->semana3B = $respuAbono["semana3B"];
        $disposicion->semana4B = $respuAbono["semana4B"];
        $disposicion->semana5B = $respuAbono["semana5B"];

        $disposicion->semana1C = $respuAbono["semana1C"];
        $disposicion->semana2C = $respuAbono["semana2C"];
        $disposicion->semana3C = $respuAbono["semana3C"];
        $disposicion->semana4C = $respuAbono["semana4C"];
        $disposicion->semana5C = $respuAbono["semana5C"];

        $disposicion->semana1D = $respuAbono["semana1D"];
        $disposicion->semana2D = $respuAbono["semana2D"];
        $disposicion->semana3D = $respuAbono["semana3D"];
        $disposicion->semana4D = $respuAbono["semana4D"];
        $disposicion->semana5D = $respuAbono["semana5D"];

        $disposicion->semana1E = $respuAbono["semana1E"];
        $disposicion->semana2E = $respuAbono["semana2E"];
        $disposicion->semana3E = $respuAbono["semana3E"];
        $disposicion->semana4E = $respuAbono["semana4E"];
        $disposicion->semana5E = $respuAbono["semana5E"];

        $disposicion->semana1F = $respuAbono["semana1F"];
        $disposicion->semana2F = $respuAbono["semana2F"];
        $disposicion->semana3F = $respuAbono["semana3F"];
        $disposicion->semana4F = $respuAbono["semana4F"];
        $disposicion->semana5F = $respuAbono["semana5F"];

        $arrayDisposicion[] = $disposicion;
    }

    echo json_encode($arrayDisposicion);
}
?>