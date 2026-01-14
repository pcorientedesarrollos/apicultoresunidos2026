<?php

include_once '../../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$sql = 'SELECT zona FROM zonas ORDER BY zona ASC';
$datos = $con->prepare($sql);
$datos->execute();

$arrayZon = array();
while ($row = $datos->fetch()) {
    $sector = new stdClass();
    $sector->sector = $row["zona"];
    $arrayZonas[] = $sector;
}

echo $json_response = json_encode($arrayZon);
?>