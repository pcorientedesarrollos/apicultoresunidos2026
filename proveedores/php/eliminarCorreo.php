<?php

include_once '../../mensajes/Mensajes.php';
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$conexion = $pdo->conectar();

$mensajes = new Mensajes();
$id = $_GET["idCorreo"];
$sqlEliminarCorreo = "DELETE FROM correos WHERE id  = :id";
$datos = $conexion->prepare($sqlEliminarCorreo);
$datos->bindParam(':id', $id);
$datos->execute();

if ($datos == false) {
    echo json_encode($mensajes->error(mysql_error()));
} else {
    echo json_encode($mensajes->succes("Correo eliminado satisfactoriamente"));
}
