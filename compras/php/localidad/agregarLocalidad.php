<?php

include_once '../../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$miInfo = file_get_contents("php://input");
$datos = json_decode($miInfo);

$sql = "INSERT INTO localidades (localidad, idzona, estado) VALUES ('$datos->localidad','$datos->idzona', '1')";
$result = $con->prepare($sql);
$result->execute();

if ($result == false) {
    echo 'Error al ingresar localidad';
} else {
    echo 'La localidad se agregó exitosamente';
}
