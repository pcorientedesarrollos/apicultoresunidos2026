<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$idLoteInterno = $_GET["idLoteInterno"];
$miInfo = file_get_contents("php://input");
$datos = json_decode($miInfo);

$sqlUp = "UPDATE trazabilidadlaboratorio SET nombreLaboratorio = :nombreLaboratorio, fechaProtocolo = :fechaProtocolo, folioProtocolo = :folioProtocolo WHERE idLoteInterno = :idLoteInterno AND tipoMiel = :tipoMiel";
$dats = $con->prepare($sqlUp);
$dats->bindParam(':idLoteInterno', $idLoteInterno);
$dats->bindParam(':nombreLaboratorio', $datos->nombreLaboratorio);
$dats->bindParam(':fechaProtocolo', $datos->fechaProtocolo);
$dats->bindParam(':folioProtocolo', $datos->folioProtocolo);
$dats->bindParam(':tipoMiel', $datos->tipoMiel);
$dats->execute();
?>