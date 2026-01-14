<?php

include_once '../../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$sql = 'SELECT localidad FROM localidades WHERE idZona = 1';
$datos = $con->prepare($sql);
$datos->execute();

$arrayPoniente = array();
while ($row = $datos->fetch()) {
    $poniente = new stdClass();
    $poniente->localidad = $row["localidad"];
    $arrayPoniente[] = $poniente;
}

echo $json_response = json_encode($arrayPoniente);
?>