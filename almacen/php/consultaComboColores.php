<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$sql = 'SELECT idColor, color FROM colores ORDER BY color ASC';
$datos = $con->prepare($sql);
$datos->execute();

$arrayColores = array();
while ($row = $datos->fetch()){
    $colores = new stdClass();
    $colores->idColor = $row["idColor"];
    $colores->color = $row["color"];
    $arrayColores[] = $colores;
}

echo $json_response = json_encode($arrayColores);
?>