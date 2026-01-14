<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$idPersonal = $_GET['idPersonal'];
$estado = $_GET['estado'];

$sql = "UPDATE personaloaxaca SET estado = :estado WHERE idPersonalOM = :idPersonal";
$datos = $con->prepare($sql);
$datos->bindParam(':estado', $estado);
$datos->bindParam(':idPersonal', $idPersonal);
$datos->execute();

if ($datos == false) {
    echo 'Error al ingresar ';
} else {
    echo 'Se agregó exitosamente';
}

?>