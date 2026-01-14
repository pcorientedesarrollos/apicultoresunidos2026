<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$miInfo = file_get_contents("php://input");
$datos = json_decode($miInfo);
//$fechaSalida = date("Y-m-d", strtotime($datos->fechaSalida));

$sql = "INSERT INTO trazabilidadsalida (idLoteInterno, homogeneizado, kilosSalida, idEmpresaPais, kgExportar, fechaSalida, tipoMiel) VALUES (:idLoteInterno, :homogeneizado, :kilosSalida, :idEmpresaPais, :kgExportar, :fechaSalida, :tipoMiel)";
$dato = $con->prepare($sql);
$dato->bindParam(':idLoteInterno', $datos->idLoteInterno);
$dato->bindParam(':homogeneizado', $datos->homogeneizado);
$dato->bindParam(':kilosSalida', $datos->kilosSalida);
$dato->bindParam(':idEmpresaPais', $datos->idEmpresaPais);
$dato->bindParam(':kgExportar', $datos->kgExportar);
//$dato->bindParam(':fechaSalida', $fechaSalida);
$dato->bindParam(':fechaSalida', $datos->fechaSalida);
$dato->bindParam(':tipoMiel', $datos->tipoMiel);
$dato->execute();
