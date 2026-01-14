<?php

include_once '../../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$json = file_get_contents("php://input");
$datos = json_decode($json);
$info = $datos->valor;

$datosEncabezado = $info;
$idSalidaEnvases = $_GET["idSalidaEnvases"];

$sqlEncabezado = "UPDATE envasesfrascosencabezadosalidas SET fecha = :fecha, idProveedor = :idProveedor, cantidadTotal = :cantidadTotal, importeTotal = :importeTotal, tipoCliente = :tipoCliente WHERE idSalidaEnvases = :idSalidaEnvases";
$dato = $con->prepare($sqlEncabezado);
$dato->bindParam(':fecha', $info->fecha);
$dato->bindParam(':idProveedor', $info->idProveedor);
$dato->bindParam(':cantidadTotal', $info->cantidadTotal);
$dato->bindParam(':importeTotal', $info->importeTotal);
$dato->bindParam(':tipoCliente', $info->tipoCliente);
$dato->bindParam(':idSalidaEnvases', $idSalidaEnvases);
$dato->execute();
