<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$json = file_get_contents("php://input");
$datos = json_decode($json);

$info = $datos->valor;

foreach ($info as $i) {
    $sql = "UPDATE equipos set costo = :costo WHERE equipos.idEquipo = :idEquipo";
    $datosUp1 = $con->prepare($sql);
    $datosUp1->bindParam(':costo', $i->costo);
    $datosUp1->bindParam(':idEquipo', $i->idEquipo);
    $datosUp1->execute();
}
?>