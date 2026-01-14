<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$sql = 'SELECT * FROM condicionesdesalidas ORDER BY condicionSalida ASC';
$datos = $con->prepare($sql);
$datos->execute();

$arrayCondiciones = array();
while ($row = $datos->fetch()) {
    $condiciones = new stdClass();
    $condiciones->idCondicionSalida = $row["idCondicionSalida"];
    $condiciones->condicionSalida = $row["condicionSalida"];
    $arrayCondiciones[] = $condiciones;
}

echo $json_response = json_encode($arrayCondiciones);
?>