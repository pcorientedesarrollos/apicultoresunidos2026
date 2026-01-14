<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$miInfo = file_get_contents("php://input");
$datos = json_decode($miInfo);

foreach ($datos as $i) {
    $sql = "UPDATE disposiciondelpersonal SET observaciones = :observaciones WHERE idDisposicion = :idDisposicion";
    $dato = $con->prepare($sql);
    $dato->bindParam(':observaciones', $i->observaciones);
    $dato->bindParam(':idDisposicion', $i->idDisposicion);
    $dato->execute();
}