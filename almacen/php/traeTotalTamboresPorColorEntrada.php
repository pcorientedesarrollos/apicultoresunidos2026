<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$query = "SELECT SUM(mpd.cantidad) AS totalVerdes, 
(SELECT SUM(mpd.cantidad)FROM materiaprimadetalleentradas mpd
INNER JOIN materiaprimaencabezadoentradas mpe ON mpe.idEntradaMateria = mpd.idEntradaMateria
WHERE mpd.idColor = 2) AS totalAzules,
(SELECT SUM(mpd.cantidad)FROM materiaprimadetalleentradas mpd
INNER JOIN materiaprimaencabezadoentradas mpe ON mpe.idEntradaMateria = mpd.idEntradaMateria
WHERE mpd.idColor = 3) AS totalGrises,
(SELECT SUM(mpd.cantidad)FROM materiaprimadetalleentradas mpd
INNER JOIN materiaprimaencabezadoentradas mpe ON mpe.idEntradaMateria = mpd.idEntradaMateria
WHERE mpd.idColor = 4) AS totalCafes,
(SELECT SUM(mpd.cantidad)FROM materiaprimadetalleentradas mpd
INNER JOIN materiaprimaencabezadoentradas mpe ON mpe.idEntradaMateria = mpd.idEntradaMateria
WHERE mpd.idColor = 5) AS totalBlancos,
(SELECT SUM(mpd.cantidad)FROM materiaprimadetalleentradas mpd
INNER JOIN materiaprimaencabezadoentradas mpe ON mpe.idEntradaMateria = mpd.idEntradaMateria
WHERE mpd.idColor = 6) AS totalCampo
FROM materiaprimadetalleentradas mpd
INNER JOIN materiaprimaencabezadoentradas mpe ON mpe.idEntradaMateria = mpd.idEntradaMateria
WHERE mpd.idColor = 1";
$datos = $con->prepare($query);
$datos->execute();

while ($row = $datos->fetch()) {
    $tamboresEntrada = new stdClass();
    $tamboresEntrada->totalVerdes = $row["totalVerdes"];
    $tamboresEntrada->totalAzules = $row["totalAzules"];
    $tamboresEntrada->totalGrises = $row["totalGrises"];
    $tamboresEntrada->totalCafes = $row["totalCafes"];
    $tamboresEntrada->totalBlancos = $row["totalBlancos"];
    $tamboresEntrada->totalCampo = $row["totalCampo"];
}

$sql = "SELECT SUM(cantidad) as totalEntradas FROM materiaprimadetalleentradas";
$dato = $con->prepare($sql);
$dato->execute();
if ($dato == false) {
    echo mysql_error();
} else {
    while ($resTotal = $dato->fetch()) {
        $tamboresEntrada->totalEntradas = $resTotal["totalEntradas"];
    }
}

echo json_encode($tamboresEntrada);
?>