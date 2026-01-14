<?php

include_once '../../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$json = file_get_contents("php://input");
$datos = json_decode($json);
$info = $datos->valor;
$datosEncabezado = $info;
$idEntradaEnvases = $_GET["idEntradaEnvases"];

$sqlEncabezado = "UPDATE envasesfrascosencabezadoentradas SET fecha = :fecha, idProveedor = :idProveedor, cantidadTotal = :cantidadTotal, importeTotal = :importeTotal, tipoCliente = :tipoCliente WHERE idEntradaEnvases = :idEntradaEnvases";
$dato = $con->prepare($sqlEncabezado);
$dato->bindParam(':fecha', $info->fecha);
$dato->bindParam(':idProveedor', $info->idProveedor);
$dato->bindParam(':cantidadTotal', $info->cantidadTotal);
$dato->bindParam(':importeTotal', $info->importeTotal);
$dato->bindParam(':tipoCliente', $info->tipoCliente);
$dato->bindParam(':idEntradaEnvases', $idEntradaEnvases);
$dato->execute();