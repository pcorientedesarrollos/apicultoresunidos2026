<?php

include_once '../../../DAOConeccion/conePDO.php';
$pdo = new conePDO;
$con = $pdo->conectar();

$sql = 'SELECT idzona, zona FROM zonas WHERE estado = 1 ORDER BY zona ASC';
$datos = $con->prepare($sql);
$datos->execute();

$arrayZonas = array();

while ($row = $datos->fetch()) {
    $nomZonas = new stdClass();
    $nomZonas->idzona = $row["idzona"];
    $nomZonas->zona = $row["zona"];
    $arrayZonas[] = $nomZonas;
}

echo $json_response = json_encode($arrayZonas);
?>