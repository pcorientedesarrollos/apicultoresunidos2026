<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$sql = 'SELECT idProveedorMantto, nombreProveedor FROM proveedoresmantto ORDER BY nombreProveedor ASC';
$datos = $con->prepare($sql);
$datos->execute();

$arrayPersonalE = array();
while ($row = $datos->fetch()) {
    $listaTecnicos = new stdClass();
    $listaTecnicos->idProveedorMantto = $row["idProveedorMantto"];
    $listaTecnicos->nombreProveedor = $row["nombreProveedor"];
    $arrayPersonalE[] = $listaTecnicos;
}

echo $json_response = json_encode($arrayPersonalE);
?>