<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$conexion = $pdo->conectar();

$id = $_GET["id"];
$nombreImg = "";
$sqlI = "SELECT archivoCarta FROM archivoscartas WHERE idCarta = :id";
$dato = $conexion->prepare($sqlI);
$dato->bindParam(':id', $id);
$dato->execute();
while ($row = $dato->fetch()) {
    $nombreImg = $row["archivoCarta"];
}
$sql = "DELETE FROM archivoscartas WHERE archivoscartas.idCarta =:id";
$datos = $conexion->prepare($sql);
$datos->bindParam(':id', $id);
$datos->execute();

unlink("../" . $nombreImg);
?>