<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$sql = 'SELECT idClasificacion, clasificacion FROM clasificaciones ORDER BY clasificacion ASC';
$datos = $con->prepare($sql);
$datos->execute();

$arrayClasificaciones = array();
while ($row = $datos->fetch()) {
    $clasificaciones = new stdClass();
    $clasificaciones->idClasificacion = $row["idClasificacion"];
    $clasificaciones->clasificacion = $row["clasificacion"];
    $arrayClasificaciones[] = $clasificaciones;
}

echo $json_response = json_encode($arrayClasificaciones);
?>