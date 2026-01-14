<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$idRequisicion = $_GET["idRequisicion"];
$totalTambores = $_GET["totalTambores"];
$totalKilos = $_GET["totalKilos"];
$importeTotal = $_GET["importeTotal"];

$sql = "UPDATE requisicionencabezado SET totalTambores = :totalTambores, totalKilos = :totalKilos, importeTotal = :importeTotal WHERE requisicionencabezado.idRequisicion = :idRequisicion";
$data = $con->prepare($sql);
$data->bindParam(':idRequisicion', $idRequisicion);
$data->bindParam(':totalTambores', $totalTambores);
$data->bindParam(':totalKilos', $totalKilos);
$data->bindParam(':importeTotal', $importeTotal);
$data->execute();
?>