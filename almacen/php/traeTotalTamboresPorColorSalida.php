<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$query = "SELECT SUM(mpd.cantidad) AS totalDeVerdes, 
(SELECT SUM(mpd.cantidad)FROM materiaprimadetallesalidas mpd
INNER JOIN materiaprimaencabezadosalidas mpe ON mpe.idSalidaMateria = mpd.idSalidaMateria
WHERE mpd.idColor = 2) AS totalDeAzules,
(SELECT SUM(mpd.cantidad)FROM materiaprimadetallesalidas mpd
INNER JOIN materiaprimaencabezadosalidas mpe ON mpe.idSalidaMateria = mpd.idSalidaMateria
WHERE mpd.idColor = 3) AS totalDeGrises,
(SELECT SUM(mpd.cantidad)FROM materiaprimadetallesalidas mpd
INNER JOIN materiaprimaencabezadosalidas mpe ON mpe.idSalidaMateria = mpd.idSalidaMateria
WHERE mpd.idColor = 4) AS totalDeCafes,
(SELECT SUM(mpd.cantidad)FROM materiaprimadetallesalidas mpd
INNER JOIN materiaprimaencabezadosalidas mpe ON mpe.idSalidaMateria = mpd.idSalidaMateria
WHERE mpd.idColor = 5) AS totalDeBlancos,
(SELECT SUM(mpd.cantidad)FROM materiaprimadetallesalidas mpd
INNER JOIN materiaprimaencabezadosalidas mpe ON mpe.idSalidaMateria = mpd.idSalidaMateria
WHERE mpd.idColor = 6) AS totalDeCampo
FROM materiaprimadetallesalidas mpd
INNER JOIN materiaprimaencabezadosalidas mpe ON mpe.idSalidaMateria = mpd.idSalidaMateria
WHERE mpd.idColor = 1";
$datos = $con->prepare($query);
$datos->execute();

while ($row = $datos->fetch()) {
    $tamboresSalida = new stdClass();
    $tamboresSalida->totalDeVerdes = $row["totalDeVerdes"];
    $tamboresSalida->totalDeAzules = $row["totalDeAzules"];
    $tamboresSalida->totalDeGrises = $row["totalDeGrises"];
    $tamboresSalida->totalDeCafes = $row["totalDeCafes"];
    $tamboresSalida->totalDeBlancos = $row["totalDeBlancos"];
    $tamboresSalida->totalDeCampo = $row["totalDeCampo"];
}

$sql = "SELECT SUM(cantidad) as totalSalidas FROM materiaprimadetallesalidas";
$dato = $con->prepare($sql);
$dato->execute();
if ($dato == false) {
    echo mysql_error();
} else {
    while ($resTotal = $dato->fetch()) {
        $tamboresSalida->totalSalidas = $resTotal["totalSalidas"];
    }
}
echo json_encode($tamboresSalida);
?>