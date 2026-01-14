<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$sql = 'SELECT idPuesto, puesto FROM puestos ORDER BY puesto ASC';
$datos = $con->prepare($sql);
$datos->execute();

$arrayPuestos = array();
while ($row = $datos->fetch()) {
    $listaPuestos = new stdClass();
    $listaPuestos->idPuesto = $row["idPuesto"];
    $listaPuestos->puesto = $row["puesto"];
    $arrayPuestos[] = $listaPuestos;
}

echo $json_response = json_encode($arrayPuestos);
?>