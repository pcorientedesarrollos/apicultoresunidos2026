<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$sql = 'SELECT idCuentaConcepto, cuenta FROM cuentas ORDER BY cuenta ASC';
$datos = $con->prepare($sql);
$datos->execute();

$arrayBancos = array();
while ($row = $datos->fetch()) {
    $listaBanco = new stdClass();
    $listaBanco->idCuentaConcepto = $row["idCuentaConcepto"];
    $listaBanco->cuenta = $row["cuenta"];
    $arrayBancos[] = $listaBanco;
}

echo $json_response = json_encode($arrayBancos);
?>