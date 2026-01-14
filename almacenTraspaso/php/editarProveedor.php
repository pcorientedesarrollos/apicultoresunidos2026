<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$idAlmacen = $_GET["idAlmacen"];
$idProveedor = $_GET["idProveedor"];
$miel = $_GET['miel'];

if ($miel == '1') {
    $almacenencabezado = 'almacenencabezadotraspaso';
} else if ($miel == '2') {
    $almacenencabezado = 'almacenencabezadotraspaso_organico';
}

$sqlUp = "UPDATE $almacenencabezado SET idProveedor = :idProveedor WHERE idAlmacen = :idAlmacen";
$dats = $con->prepare($sqlUp);
$dats->bindParam(':idAlmacen', $idAlmacen);
$dats->bindParam(':idProveedor', $idProveedor);
$dats->execute();
