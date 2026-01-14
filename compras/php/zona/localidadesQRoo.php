<?php

include_once '../../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$sql = 'SELECT localidad FROM localidades WHERE idZona = 4';
$datos = $con->prepare($sql);
$datos->execute();

$arrayQRoo = array();
while ($row = $datos->fetch()) {
    $qRoo = new stdClass();
    $qRoo->localidad = $row["localidad"];
    $arrayQRoo[] = $qRoo;
}

echo $json_response = json_encode($arrayQRoo);
?>