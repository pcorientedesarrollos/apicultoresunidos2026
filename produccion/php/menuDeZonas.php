<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

//$sql = "SELECT zona, COUNT( idAlmacen ) AS total
//  FROM almacen
//WHERE estado != 2
//  GROUP BY zona";
$sql = "SELECT zona, COUNT(idAlmacen ) AS total,
SUM(neto) AS netoTotal
  FROM almacen
WHERE estado != 2
  GROUP BY zona";
$datos = $con->prepare($sql);
$datos->execute();

$arrayZ = array();
while ($row = $datos->fetch()) {
    $listaZonas = new stdClass();
    $listaZonas->zona = $row["zona"];
    $listaZonas->total = $row["total"];
    $listaZonas->netoTotal = $row["netoTotal"];
    $arrayZ[] = $listaZonas;
}

echo $json_response = json_encode($arrayZ);
?>