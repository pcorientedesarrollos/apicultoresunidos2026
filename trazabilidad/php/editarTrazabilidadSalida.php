<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$idLoteInterno = $_GET["idLoteInterno"];
$miInfo = file_get_contents("php://input");
$datos = json_decode($miInfo);
//$fechaSalida = date("Y-m-d", strtotime($datos->fechaSalida));


$sqlUp = "UPDATE trazabilidadsalida
SET homogeneizado = :homogeneizado,
kilosSalida = :kilosSalida,
idEmpresaPais = :idEmpresaPais,
kgExportar = :kgExportar,
fechaSalida = :fechaSalida
WHERE idLoteInterno = :idLoteInterno AND tipoMiel = :tipoMiel";
$dats = $con->prepare($sqlUp);
$dats->bindParam(':idLoteInterno', $idLoteInterno);
$dats->bindParam(':homogeneizado', $datos->homogeneizado);
$dats->bindParam(':kilosSalida', $datos->kilosSalida);
$dats->bindParam(':idEmpresaPais', $datos->idEmpresaPais);
$dats->bindParam(':kgExportar', $datos->kgExportar);
//$dats->bindParam(':fechaSalida', $fechaSalida);
$dats->bindParam(':fechaSalida', $datos->fechaSalida);
$dats->bindParam(':tipoMiel', $datos->tipoMiel);

$dats->execute();
?>