<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$id = $_GET['id'];
$activoInactivo = $_GET['activoInactivo'];

$sql = "UPDATE proveedor SET activoInactivo = :activoInactivo WHERE idProveedor = :id";
$datos = $con->prepare($sql);
$datos->bindParam(':activoInactivo', $activoInactivo);
$datos->bindParam(':id', $id);
$datos->execute();

if ($datos == false) {
    echo 'Error al ingresar ';
} else {
    echo 'Se agregó exitosamente';
}

?>