<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$sql = 'SELECT idMovimiento, movimiento FROM tiposdemovimientos WHERE idMovimiento != 8 ORDER BY movimiento ASC';
$datos = $con->prepare($sql);
$datos->execute();

$arrayMovimiento = array();
while ($row = $datos->fetch()) {
    $lstMovimiento = new stdClass();
    $lstMovimiento->idMovimiento = $row["idMovimiento"];
    $lstMovimiento->movimiento = $row["movimiento"];
    $arrayMovimiento[] = $lstMovimiento;
}

echo $json_response = json_encode($arrayMovimiento);
?>