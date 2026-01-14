<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$miInfo = file_get_contents("php://input");
$datos = json_decode($miInfo);
//foreach ($datos as $i) {

$sql = "INSERT INTO trazabilidadlaboratorio (idLoteInterno, nombreLaboratorio, fechaProtocolo, folioProtocolo, tipoMiel) VALUES (:idLoteInterno, :nombreLaboratorio, :fechaProtocolo, :folioProtocolo, :tipoMiel)";
$dato = $con->prepare($sql);
$dato->bindParam(':idLoteInterno', $datos->idLoteInterno);
$dato->bindParam(':nombreLaboratorio', $datos->nombreLaboratorio);
$dato->bindParam(':fechaProtocolo', $datos->fechaProtocolo);
$dato->bindParam(':folioProtocolo', $datos->folioProtocolo);
$dato->bindParam(':tipoMiel', $datos->tipoMiel);
$dato->execute();
//}