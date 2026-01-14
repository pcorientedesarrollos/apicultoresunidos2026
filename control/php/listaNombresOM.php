<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO;
$con = $pdo->conectar();
$idArea = $_GET['idArea'];

$sql = "SELECT idPersonalOM, nombre FROM personaloaxaca WHERE idPersonalOM != 26 AND idArea = '$idArea' AND estado = 0 ORDER BY nombre ASC";
$datos = $con->prepare($sql);
$datos->execute();

$arrayLocalidades = array();
while ($row = $datos->fetch()) {
    $nombresOM = new stdClass();
    $nombresOM->idPersonalOM = $row["idPersonalOM"];
    $nombresOM->nombre = utf8_encode($row["nombre"]);
    $arrayOM[] = $nombresOM;
}

echo $json_response = json_encode($arrayOM);
