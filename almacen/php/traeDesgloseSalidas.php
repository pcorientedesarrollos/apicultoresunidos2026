<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$query = "SELECT * FROM materiaprimaencabezado m
INNER JOIN condicionesdesalidas c ON c.idCondicionSalida = m.idCondicionSalida
WHERE estado = '1'";
$datos = $con->prepare($query);
$datos->execute();

$arraySalida = array();
while ($row = $datos->fetch()) {
    $infoSalida = new stdClass();
    $infoSalida->idMateriaPrima = $row["idMateriaPrima"];
    $infoSalida->fecha = $row["fecha"];
    $infoSalida->cantidadTotal = $row["cantidadTotal"];
    $infoSalida->importeTotal = $row["importeTotal"];
    $infoSalida->proveedor = $row["proveedor"];
    $infoSalida->condicionSalida = $row["condicionSalida"];

    $arraySalida[] = $infoSalida;
}
echo json_encode($arraySalida);
?>