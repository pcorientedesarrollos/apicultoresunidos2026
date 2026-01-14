<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$id = $_GET['id'];
$deleteProve = $_GET['deleteProve'];

$sql = "UPDATE proveedor SET deleteProve = :deleteProve WHERE idProveedor = :id";
$datos = $con->prepare($sql);
$datos->bindParam(':deleteProve', $deleteProve);
$datos->bindParam(':id', $id);
$datos->execute();

if ($datos == false) {
    echo 'Error al ingresar ';
} else {
    echo 'Se agregó exitosamente';
}

?>