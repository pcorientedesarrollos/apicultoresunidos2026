<?php

include_once '../../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$sql = 'SELECT localidad FROM localidades WHERE idZona = 3';
$datos = $con->prepare($sql);
$datos->execute();

$arrayOriente = array();
while ($row = $datos->fetch()) {
    $oriente = new stdClass();
    $oriente->localidad = $row["localidad"];
    $arrayOriente[] = $oriente;
}

echo $json_response = json_encode($arrayOriente);
?>