<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$conexion = $pdo->conectar();

$id = $_GET["id"];

$nombreImg = "";
$sqlI = "SELECT archivoINE FROM archivosine WHERE idIne = :id";
$dato = $conexion->prepare($sqlI);
$dato->bindParam(':id', $id);
$dato->execute();
while ($row = $dato->fetch()) {
    $nombreImg = $row["archivoINE"];
}

$sql = "DELETE FROM archivosine WHERE archivosine.idIne =:id";
$datos = $conexion->prepare($sql);
$datos->bindParam(':id', $id);
$datos->execute();

unlink("../" . $nombreImg);

?>