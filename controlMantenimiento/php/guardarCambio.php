<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$miInfo = file_get_contents("php://input");
$info = json_decode($miInfo);

$sql = "INSERT INTO historiales (areaAntesCambio, areaDespuesCambio, autoriza, entrega, recibe, fecha, idEquipo)"
        . " VALUES (:areaAntesCambio, :areaDespuesCambio, :autoriza, :entrega, :recibe, :fecha, :idEquipo)";
$dato = $con->prepare($sql);
$dato->bindParam(':areaAntesCambio', $info->areaAntesCambio);
$dato->bindParam(':areaDespuesCambio', $info->areaDespuesCambio);
$dato->bindParam(':autoriza', $info->autoriza);
$dato->bindParam(':entrega', $info->entrega);
$dato->bindParam(':recibe', $info->recibe);
$dato->bindParam(':fecha', $info->fecha);
$dato->bindParam(':idEquipo', $info->idEquipo);
$dato->execute();
if ($dato == false) {
    echo mysql_error();
} else {

    $sqlUp = "UPDATE equipos SET idArea = :idArea WHERE idEquipo = :idEquipo";
    $datos = $con->prepare($sqlUp);
    $datos->bindParam(':idArea', $info->areaDespuesCambio);
    $datos->bindParam(':idEquipo', $info->idEquipo);
    $datos->execute();

    echo 'Nuevo equipo agregado';
}
?>