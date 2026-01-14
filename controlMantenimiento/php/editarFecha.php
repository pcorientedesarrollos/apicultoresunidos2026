<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

//$idLoteExperimental = $_GET["idLoteExperimental"];
//$humedad = $_GET["humedad"];
$miInfo = file_get_contents("php://input");
$datos = json_decode($miInfo);

$sqlUp = "UPDATE programaciondefechas SET fechaProgramada = :fechaProgramada WHERE idProgramacion = :idProgramacion";
$dats = $con->prepare($sqlUp);
$dats->bindParam(':fechaProgramada', $datos->fechaProgramada);
$dats->bindParam(':idProgramacion', $datos->idProgramacion);
$dats->execute();
?>