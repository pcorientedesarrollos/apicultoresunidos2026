<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$totalPago = $_GET['totalPago'];
$idMantenimiento = $_GET['idMantenimiento'];

    $sqlUp = "UPDATE controlmantenimiento SET totalPago = :totalPago WHERE idMantenimiento = :idMantenimiento";
    $datos = $con->prepare($sqlUp);
    $datos->bindParam(':totalPago', $totalPago);
    $datos->bindParam(':idMantenimiento', $idMantenimiento);
    $datos->execute();

    echo 'Nuevo equipo agregado';

?>