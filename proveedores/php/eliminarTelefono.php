<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$conexion = $pdo->conectar();

$id = $_GET["id"];
$slq = "DELETE FROM telefonos WHERE idTelefono = :id";
$datos = $conexion->prepare($slq);
$datos->bindParam(':id', $id);
$datos->execute();


if ($datos == false) {
    echo mysql_error();
} else {
    echo 'Telefono eliminado';
}