<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$query = "SELECT e.nombre, mt.idEquipo, mt.idArea, a.area
         FROM equipos e 
         INNER JOIN controlmantenimiento mt ON mt.idEquipo = e.idEquipo
         INNER JOIN areas a ON a.idArea = mt.idArea
         GROUP BY mt.idEquipo
         ORDER BY a.area ASC"
;
$datos = $con->prepare($query);
$datos->execute();

$arrayMantto = array();
while ($row = $datos->fetch()) {
    $infoMantto = new stdClass();
    $infoMantto->idEquipo = $row["idEquipo"];
    $infoMantto->nombre = $row["nombre"];
    $infoMantto->area = $row["area"];
    $arrayMantto[] = $infoMantto;
}
echo json_encode($arrayMantto);
?>