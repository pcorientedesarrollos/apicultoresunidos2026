<?php

include_once '../../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$miInfo = file_get_contents("php://input");
$datos = json_decode($miInfo);

//'$datos->nombre' //  '$datos->telefono'
$sql = "INSERT INTO compradores (nombre, telefono, estado) VALUES (:nombre,:telefono,'1')";

$dats = $con->prepare($sql);
$dats->bindParam(':nombre', $datos->nombre);
$dats->bindParam(':telefono', $datos->telefono);
$dats->execute();
if ($dats == false) {
    echo 'Error al ingresar comprador';
} else {
    echo 'El comprador se agrego exitosamente';
}