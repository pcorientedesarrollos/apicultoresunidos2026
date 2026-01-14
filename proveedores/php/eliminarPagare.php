<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$conexion = $pdo->conectar();

$id = $_GET["id"];
$nombreImg = "";
$sqlI = "SELECT archivoPagare FROM archivospagares WHERE idPagare = :id";
$dato = $conexion->prepare($sqlI);
$dato->bindParam(':id', $id);
$dato->execute();
while ($row = $dato->fetch()) {
    $nombreImg = $row["archivoPagare"];
}

$sql = "DELETE FROM archivospagares WHERE archivospagares.idPagare =:id";
$datos = $conexion->prepare($sql);
$datos->bindParam(":id", $id);
$datos->execute();

unlink("../" . $nombreImg);

?>