<?php

include_once '../../DAOConeccion/conePDO.php';
include_once '../../mensajes/Mensajes.php';
$idPlaca = $_GET["idPlaca"];
$pdo = new conePDO();
$mensajes = new Mensajes();
$con = $pdo->conectar();
$sqlEliminar = "DELETE FROM placaschoferes WHERE idPlaca = :idPlaca";
$datos = $con->prepare($sqlEliminarContacto);
$datos->bindParam(':idPlaca', $idPlaca);
$datos->execute();
if ($datos == false) {
    echo json_encode($mensajes->error(mysql_error()));
} else {
    echo json_encode($mensajes->succes("Placa eliminada satisfactoriamente"));
}
?>