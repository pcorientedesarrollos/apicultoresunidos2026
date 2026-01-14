<?php

include_once '../../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$idlocalidad = $_GET['idlocalidad'];

$sql = "UPDATE localidades SET estado = '0' WHERE localidades.idlocalidad = :idlocalidad ";
$data = $con->prepare($sql);
$data->bindParam(':idlocalidad', $idlocalidad);
$data->execute();

if ($data == FALSE) {
    echo 'Error al borrar localidad';
} else {
    echo 'La localidad fue borrada exitosamente';
}
