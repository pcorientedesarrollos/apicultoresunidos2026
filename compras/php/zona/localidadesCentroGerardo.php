<?php

include_once '../../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$sql = 'SELECT localidad FROM localidades WHERE idZona = 6';
$datos = $con->prepare($sql);
$datos->execute();

$arrayCentoG = array();
while ($row = $datos->fetch()) {
    $centroG = new stdClass();
    $centroG->localidad = $row["localidad"];
    $arrayCentoG[] = $centroG;
}

echo $json_response = json_encode($arrayCentoG);
?>