<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$miInfo = file_get_contents("php://input");

$datos = json_decode($miInfo);

$sqlUp = "UPDATE chequesentregados_encabezado SET fechaEntrega = :fechaEntrega, idBanco = :idBanco, tipoCatalogo = :tipoCatalogo, nombreRecibe = :nombreRecibe WHERE idEncabezado = :idEncabezado";
$dats = $con->prepare($sqlUp);
$dats->bindParam(':fechaEntrega', $datos->fechaEntrega);
$dats->bindParam(':idBanco', $datos->idBanco);
$dats->bindParam(':tipoCatalogo', $datos->tipoCatalogo);
$dats->bindParam(':nombreRecibe', $datos->nombreRecibe);
$dats->bindParam(':idEncabezado', $datos->idEncabezado);
$dats->execute();
if ($dats == false) {
    echo 0;
} else {
    echo 1;
}
 