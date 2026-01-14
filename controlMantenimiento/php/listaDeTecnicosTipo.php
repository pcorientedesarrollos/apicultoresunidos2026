<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$idTipoProveedor = $_GET['idTipoProveedor'];

$sql = "SELECT idProveedorMantto, nombreProveedor FROM proveedoresmantto WHERE idTipoProveedor = :idTipoProveedor ORDER BY nombreProveedor ASC";
$datos = $con->prepare($sql);
$datos->bindParam(':idTipoProveedor', $idTipoProveedor);
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