<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$sql = 'SELECT idPersonalOM, nombre FROM personaloaxaca WHERE estado = 0 ORDER BY nombre ASC';
$datos = $con->prepare($sql);
$datos->execute();

$arrayPersonalE = array();
while ($row = $datos->fetch()) {
    $listaPersonalOM = new stdClass();
    $listaPersonalOM->idPersonalOM = $row["idPersonalOM"];
    $listaPersonalOM->nombre = $row["nombre"];
    $arrayPersonalE[] = $listaPersonalOM;
}

echo $json_response = json_encode($arrayPersonalE);
?>