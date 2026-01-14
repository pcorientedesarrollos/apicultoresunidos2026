<?php

include_once '../../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$zonas = file_get_contents("php://input");
$datos = json_decode($zonas);

$sql = "INSERT INTO zonas (zona, idcomprador, estado) VALUES ('$datos->zona', '$datos->idcomprador', '1')";
$dats = $con->prepare($sql);
$dats->execute();

if ($dats === FALSE) {
    echo 'Error al ingresar zona';
} else {
    echo 'La zona se agregó exitosamente';
}
?>