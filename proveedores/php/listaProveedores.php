<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$conexion = $pdo->conectar();


// $sql = 'SELECT idProveedor, nombre FROM proveedor ORDER BY nombre ASC';
$sql = 'SELECT idProveedor, nombre FROM proveedor WHERE activoInactivo = 0 ORDER BY nombre ASC';


$result = $conexion->prepare($sql);
$result->execute();

$arrayProveedorr = array();

while ($row = $result->fetch()) {
    $nombresDeProveedores = new stdClass();
    $nombresDeProveedores->idProveedor = $row["idProveedor"];
    $nombresDeProveedores->nombre = $row["nombre"];
    $arrayProveedorr[] = $nombresDeProveedores;
}

echo $json_response = json_encode($arrayProveedorr);

