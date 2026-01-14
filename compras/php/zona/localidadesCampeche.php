<?php

include_once '../../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$sql = 'SELECT localidad FROM localidades WHERE idZona = 2';
$datos = $con->prepare($sql);
$datos->execute();

$arrayCampeche = array();
while ($row = $datos->fetch()) {
    $campeche = new stdClass();
    $campeche->localidad = $row["localidad"];
    $arrayCampeche[] = $campeche;
}

echo $json_response = json_encode($arrayCampeche);
?>