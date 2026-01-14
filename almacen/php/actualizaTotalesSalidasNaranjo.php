<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$idSalidaMateria = $_GET["idSalidaMateria"];
$cantidadTotal = $_GET["cantidadTotal"];
$importeTotal = $_GET["importeTotal"];

$sql = "UPDATE materiaprimaencabezadosalidas_naranjo SET cantidadTotal = :cantidadTotal, importeTotal = :importeTotal WHERE materiaprimaencabezadosalidas_naranjo.idSalidaMateria = :idSalidaMateria";
$datos = $con->prepare($sql);
$datos->bindParam(':cantidadTotal', $cantidadTotal);
$datos->bindParam(':importeTotal', $importeTotal);
$datos->bindParam(':idSalidaMateria', $idSalidaMateria);
$datos->execute();
