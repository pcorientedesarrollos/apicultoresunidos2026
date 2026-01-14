<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$conexion = $pdo->conectar();

$codigo = $_GET["codigo"];

$existe = "";

$sql = "SELECT codigo FROM equipos WHERE codigo = :codigo";

$sqlExiste = $conexion->prepare($sql);
$sqlExiste->bindParam(':codigo', $codigo);
$sqlExiste->execute();

while ($row = $sqlExiste->fetch()) {
    $existe = $row["codigo"];
}

if ($existe == null) {
    $respuesta = 0;
} else {
    $respuesta = 1;
}

echo json_encode($respuesta);
?>