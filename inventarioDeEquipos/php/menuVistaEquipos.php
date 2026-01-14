<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$sql = "SELECT idArea, area FROM areas ORDER BY area ASC";
$datos = $con->prepare($sql);
$datos->execute();

$arrayAreas = array();
while ($row = $datos->fetch()) {
    $listaAreas = new stdClass();
    $listaAreas->idArea = $row["idArea"];
    $listaAreas->area = $row["area"];

    $sqlTotalC = "SELECT COUNT(idEquipo) AS totalArticulosC FROM equipos WHERE idArea = :idArea AND mantto = 0";
    $dato = $con->prepare($sqlTotalC);
    $dato->bindParam(':idArea', $listaAreas->idArea);
    $dato->execute();
    while ($row = $dato->fetch()) {
        $listaAreas->totalArticulosC = $row["totalArticulosC"];
    }

    $sqlTotalS = "SELECT COUNT(idEquipo) AS totalArticulosS FROM equipos WHERE idArea = :idArea AND mantto = 1";
    $datoS = $con->prepare($sqlTotalS);
    $datoS->bindParam(':idArea', $listaAreas->idArea);
    $datoS->execute();
    while ($row = $datoS->fetch()) {
        $listaAreas->totalArticulosS = $row["totalArticulosS"];
    }

    $sqlTotal = "SELECT COUNT(idEquipo) AS granTotal FROM equipos WHERE idArea = :idArea";
    $datoGT = $con->prepare($sqlTotal);
    $datoGT->bindParam(':idArea', $listaAreas->idArea);
    $datoGT->execute();
    while ($row = $datoGT->fetch()) {
        $listaAreas->granTotal = $row["granTotal"];
    }

    $arrayAreas[] = $listaAreas;
}

echo $json_response = json_encode($arrayAreas);
?>