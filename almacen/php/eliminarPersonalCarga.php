<?php

include_once '../../DAOConeccion/conePDO.php';
include_once '../../mensajes/Mensajes.php';
$idPersonalCarga = $_GET["idPersonalCarga"];
$pdo = new conePDO();
$mensajes = new Mensajes();
$con = $pdo->conectar();
$sqlEliminarContacto = "DELETE FROM personalcarga WHERE idPersonalCarga = :idPersonalCarga";
$datos = $con->prepare($sqlEliminarContacto);
$datos->bindParam(':idPersonalCarga', $idPersonalCarga);
$datos->execute();
if ($datos == false) {
    echo json_encode($mensajes->error(mysql_error()));
} else {
    echo json_encode($mensajes->succes("Personal eliminado satisfactoriamente"));
}
?>