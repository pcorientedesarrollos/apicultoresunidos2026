<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$miInfo = file_get_contents("php://input");
$datos = json_decode($miInfo);

$idArea = $_GET["idArea"];
$idEquipo = $_GET["idEquipo"];

//$idProgramacion = $_GET["idProgramacion"];
//if (isset($_GET['idProgramacion'])) {
//
//    foreach ($datos as $i) {
//        $sqlUp = "UPDATE programaciondefechas SET fechaProgramada = :fechaProgramada, idMes = :idMes WHERE idProgramacion = :idProgramacion";
//        $data = $con->prepare($sqlUp);
//        $data->bindParam(':fechaProgramada', $i->fechaProgramada);
//        $data->bindParam(':idMes', $i->idMes);
//        $data->bindParam(':idProgramacion', $i->idProgramacion);
//        $data->execute();
//    }
//} else {}

    foreach ($datos as $en) {
        $sql = "INSERT INTO programaciondefechas (idArea, idEquipo, fechaProgramada, idMes) VALUES (:idArea, :idEquipo, :fechaProgramada, :idMes)";
        $dato = $con->prepare($sql);
        $dato->bindParam(':idArea', $idArea);
        $dato->bindParam(':idEquipo', $idEquipo);
        $dato->bindParam(':fechaProgramada', $en->fechaProgramada);
        $dato->bindParam(':idMes', $en->idMes);

        $dato->execute();
    }

    $sqlEquipo = "UPDATE equipos SET programacion = '1' WHERE idEquipo = :idEquipo";
    $elDt = $con->prepare($sqlEquipo);
    $elDt->bindParam(':idEquipo', $idEquipo);
    $elDt->execute();
    