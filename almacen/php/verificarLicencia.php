<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$licencia = $_GET["licencia"];

$existe = "";

$sql = "SELECT licencia FROM choferes WHERE licencia = :licencia";
$datos = $con->prepare($sql);
$datos->bindParam(':licencia', $licencia);
$datos->execute();

while ($row = $datos->fetch()) {
    $existe = $row["licencia"];
};

if ($existe == null) {
    $respuesta = 0;
} else {
    $respuesta = 1;
}
echo json_encode($respuesta);
?>