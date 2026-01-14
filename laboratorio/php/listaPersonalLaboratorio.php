<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$sql = 'SELECT idPersonalOM, nombre FROM personaloaxaca WHERE idArea = 3 AND estado = 0 ORDER BY nombre ASC';
$datos = $con->prepare($sql);
$datos->execute();

$arrayPersonal = array();
while ($row = $datos->fetch()) {
    $listaPersonal = new stdClass();
    $listaPersonal->idPersonalOM = $row["idPersonalOM"];
    $listaPersonal->nombre = $row["nombre"];
    $arrayPersonal[] = $listaPersonal;
}

echo $json_response = json_encode($arrayPersonal);
?>