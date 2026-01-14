<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$sql = 'SELECT idArea, area FROM areas ORDER BY area ASC';
$datos = $con->prepare($sql);
$datos->execute();

$arrayAreas = array();
while ($row = $datos->fetch()) {
    $listaAreas = new stdClass();
    $listaAreas->idArea = $row["idArea"];
    $listaAreas->area = $row["area"];
    $arrayAreas[] = $listaAreas;
}

echo $json_response = json_encode($arrayAreas);
?>