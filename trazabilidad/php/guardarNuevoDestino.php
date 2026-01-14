<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$miInfo = file_get_contents("php://input");
$datos = json_decode($miInfo);

$sql = "INSERT INTO empresasypaises (empresa, pais) VALUES (:empresa,:pais)";

$dats = $con->prepare($sql);
$dats->bindParam(':empresa', $datos->empresa);
$dats->bindParam(':pais', $datos->pais);
$dats->execute();
if ($dats == false) {
    echo 'Error al ingresar';
} else {
    echo 'Agregado exitosamente';
}
?>