<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$sql = 'SELECT idEquipo, nombre FROM equipos ORDER BY nombre ASC';
$datos = $con->prepare($sql);
$datos->execute();

$arrayEquipos = array();
while ($row = $datos->fetch()) {
    $listaEquipos = new stdClass();
    $listaEquipos->idEquipo = $row["idEquipo"];
    $listaEquipos->nombre = $row["nombre"];
    $arrayEquipos[] = $listaEquipos;
}

echo $json_response = json_encode($arrayEquipos);
?>