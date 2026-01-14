<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$query = "SELECT p.idProveedor, p.nombre, p.cantidad, p.idEstado 
FROM proveedor p
WHERE p.empresa != 1
ORDER BY p.nombre ASC";
$datos = $con->prepare($query);
$datos->execute();
$array = array();
while ($row = $datos->fetch()) {
    $info = new stdClass();
    $info->idProveedor = $row["idProveedor"];
    $info->nombre = $row["nombre"];
    $info->cantidad = $row["cantidad"];
    $info->idEstado = $row["idEstado"];
    $lista[] = $info;
}
echo $json_response = json_encode($lista);
?>