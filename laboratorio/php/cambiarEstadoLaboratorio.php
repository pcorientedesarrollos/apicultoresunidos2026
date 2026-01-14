<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$valor = $_GET["valor"];
$id = $_GET["id"];

$sql = "UPDATE laboratorio set estado = :valor WHERE idAlmacen = :id ";
$data = $con->prepare($sql);
$data->bindParam(':valor', $valor);
$data->bindParam(':id', $id);
$data->execute();

if ($data == false) {
    echo mysql_error();
} else {
    echo "Estado cambiado satisfactoriamente";
}