<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$conexion = $pdo->conectar();

$id = $_GET["id"];
$nombreImg = "";
$sqlI = "SELECT archivoApiario FROM archivosapiarios WHERE idApiario = :id";
$dato = $conexion->prepare($sqlI);
$dato->bindParam(':id', $id);
$dato->execute();
while ($row = $dato->fetch()) {
    $nombreImg = $row["archivoApiario"];
}

$sql = "DELETE FROM archivosapiarios WHERE archivosapiarios.idApiario =:id";
$datos = $conexion->prepare($sql);
$datos->bindParam(':id', $id);
$datos->execute();

unlink("../" . $nombreImg);

?>