<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$conexion = $pdo->conectar();

$idArea = $_GET["idArea"];

$existe = "";

$sql = "SELECT idArea FROM subareas WHERE idArea = :idArea";

$sqlExiste = $conexion->prepare($sql);
$sqlExiste->bindParam(':idArea', $idArea);
$sqlExiste->execute();

while ($row = $sqlExiste->fetch()) {
    $existe = $row["idArea"];
}

if ($existe == null) {
    $respuesta = 0;
} else {
    $respuesta = 1;
}

echo json_encode($respuesta);
?>