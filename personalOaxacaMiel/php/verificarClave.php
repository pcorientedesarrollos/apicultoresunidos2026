<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$clave = $_GET["clave"];
$existe = "";

$sql = "SELECT clave FROM personaloaxaca WHERE clave = :clave";
$datos = $con->prepare($sql);
$datos->bindParam(':clave', $clave);
$datos->execute();

while ($row = $datos->fetch()) {
    $existe = $row["clave"];
};

if ($existe == null) {
    $respuesta = 0;
} else {
    $respuesta = 1;
}

echo json_encode($respuesta);
?>