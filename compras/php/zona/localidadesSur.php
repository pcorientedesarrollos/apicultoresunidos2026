<?php

include_once '../../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$sql = 'SELECT localidad FROM localidades WHERE idZona = 7';
$datos = $con->prepare($sql);
$datos->execute();

$arraySur = array();
while ($row = $datos->fetch()) {
    $sur = new stdClass();
    $sur->localidad = $row["localidad"];
    $arraySur[] = $sur;
}

echo $json_response = json_encode($arraySur);
?>