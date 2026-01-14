<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$sql = 'SELECT * FROM bancos ORDER BY banco ASC';
$datos = $con->prepare($sql);
$datos->execute();

$arrayBancos = array();
while ($row = $datos->fetch()) {
    $bancoC = new stdClass();
    $bancoC->idBanco = $row["idBanco"];
    $bancoC->banco = $row["banco"];
    $arrayBancos[] = $bancoC;
}

echo $json_response = json_encode($arrayBancos);
?>