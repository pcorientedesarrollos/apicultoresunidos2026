<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$query = "SELECT COALESCE(SUM(saldoIngresos),0)) AS totalIngresos,
(SELECT COALESCE(SUM(saldoEgresos),0)) AS totalEgresos,
(SELECT saldoIngresos - saldoEgresos) AS totalSaldos";
$datos = $con->prepare($query);
$datos->execute();

while ($row = $datos->fetch()) {
    $totalesEnBanco = new stdClass();
    $totalesEnBanco->totalIngresos = $row["totalIngresos"];
    $totalesEnBanco->totalEgresos = $row["totalEgresos"];
    $totalesEnBanco->totalSaldos = $row["totalSaldos"];   

   }
echo $json_response = json_encode($totalesEnBanco);
?>