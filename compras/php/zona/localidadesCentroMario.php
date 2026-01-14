<?php

include_once '../../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$sql = 'SELECT localidad FROM localidades WHERE idZona = 5';
$datos = $con->prepare($sql);
$datos->execute();

$arrayCentoM = array();
while ($row = $datos->fetch()) {
    $centroM = new stdClass();
    $centroM->localidad = $row["localidad"];
    $arrayCentroM[] = $centroM;
}

echo $json_response = json_encode($arrayCentroM);
?>