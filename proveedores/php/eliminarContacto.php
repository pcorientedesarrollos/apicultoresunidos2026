<?php

include_once '../../mensajes/Mensajes.php';
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$conexion = $pdo->conectar();

$id = $_GET["idContacto"];
$mensajes = new Mensajes();
$sqlEliminarContacto = "DELETE FROM contacto WHERE id = :id";
$datos = $conexion->prepare($sqlEliminarContacto);
$datos->bindParam(':id', $id);
$datos->execute();

if ($datos == false) {
    echo json_encode($mensajes->error(mysql_error()));
} else {
    echo json_encode($mensajes->succes("Contato eliminado satisfactoriamente"));
}