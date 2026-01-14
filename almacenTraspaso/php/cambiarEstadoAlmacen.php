<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$valor = $_GET["valor"];
$id = $_GET["id"];
$miel = $_GET["miel"];

if ($miel == '1') {
    $detalle = 'almacentraspaso';
} else if ($miel == '2') {
    $detalle = 'almacentraspaso_organico';
}

$sql = "UPDATE $detalle set autorizado = :valor WHERE idAlmacen = :id";
$datos = $con->prepare($sql);
$datos->bindParam(':valor', $valor);
$datos->bindParam(':id', $id);
$datos->execute();