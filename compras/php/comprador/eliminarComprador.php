<?php

include_once '../../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$idcomprador = $_GET['idcomprador'];

$sql = "UPDATE compradores SET estado='0' WHERE compradores.idcomprador = :idcomprador ";

$data = $con->prepare($sql);
$data->bindParam(':idcomprador', $idcomprador);
$data->execute();

if ($data == false) {
    echo 'Error al ingresar localidad';
} else {
    echo 'El comprador fue borrado exitosamente';
}
