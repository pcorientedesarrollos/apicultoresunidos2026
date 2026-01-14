<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$json = file_get_contents("php://input");
$datos = json_decode($json);
$info = $datos->valor;

$fecha = date("Y-m-d");
$idRequisicion = $_GET["idRequisicion"];

$sqlRequerimientoEncabezado = "UPDATE requisicionencabezado "
        . "SET fechaRequisicion = '" . $info->fechaRequisicion . "', fechaImpresion = '$fecha', totalTambores = '" . $info->totalTambores . "', totalKilos = '" . $info->totalKilos . "', importeTotal = '" . $info->importeTotal . "'  "
        . "WHERE requisicionencabezado.idRequisicion = :idRequisicion ";
$data = $con->prepare($sqlRequerimientoEncabezado);
$data->bindParam(':idRequisicion', $idRequisicion);
$data->execute();
if ($data === FALSE) {
    echo 'Error al ingresar';
} else {
    echo 'Se agregó exitosamente';
}
