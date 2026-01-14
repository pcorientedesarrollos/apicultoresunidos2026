<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$sql = 'SELECT idBanco, banco FROM bancos ORDER BY banco ASC';
$datos = $con->prepare($sql);
$datos->execute();

$arrayBancos = array();
while ($row = $datos->fetch()) {
    $listaBanco = new stdClass();
    $listaBanco->idBanco = $row["idBanco"];
    $listaBanco->banco = $row["banco"];
    $arrayBancos[] = $listaBanco;
}

echo $json_response = json_encode($arrayBancos);
?>