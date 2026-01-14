<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$miInfo = file_get_contents("php://input");
$info = json_decode($miInfo);

$sqlUp = "UPDATE listadepesos SET folioFactura = :folioFactura WHERE idTamborPeso = :idTamborPeso";
$datos = $con->prepare($sqlUp);
$datos->bindParam(':folioFactura', $info->folioFactura);
$datos->bindParam(':idTamborPeso', $info->idTamborPeso);
$datos->execute();
if ($datos == false) {
    echo 'Hubo un error';
} else {
    echo 'Se modificó';
}

?>