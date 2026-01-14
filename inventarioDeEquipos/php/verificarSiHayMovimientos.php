<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$conexion = $pdo->conectar();

$idEquipo = $_GET["idEquipo"];

$existe = "";

$sql = "SELECT idEquipo FROM historiales WHERE idEquipo = :idEquipo";

$sqlExiste = $conexion->prepare($sql);
$sqlExiste->bindParam(':idEquipo', $idEquipo);
$sqlExiste->execute();

while ($row = $sqlExiste->fetch()) {
    $existe = $row["idEquipo"];
}

if ($existe == null) {
    $respuesta = 0;
} else {
    $respuesta = 1;
}

echo json_encode($respuesta);
?>