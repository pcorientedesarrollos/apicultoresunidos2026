<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$conexion = $pdo->conectar();

$id = $_GET["id"];
$nombreImg = "";
$sqlI = "SELECT archivoInstalacion FROM archivosinstalaciones WHERE idInstalacion = :id";
$dato = $conexion->prepare($sqlI);
$dato->bindParam(':id', $id);
$dato->execute();
while ($row = $dato->fetch()) {
    $nombreImg = $row["archivoInstalacion"];
}

$sql = "DELETE FROM archivosinstalaciones WHERE archivosinstalaciones.idInstalacion =:id";
$datos = $conexion->prepare($sql);
$datos->bindParam(":id", $id);
$datos->execute();

unlink("../" . $nombreImg);

?>