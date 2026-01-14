<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$idDetalleSalida = $_GET["idDetalleSalida"];
$sql = "DELETE FROM materiaprimadetallesalidas_organico WHERE idDetalleSalida = :idDetalleSalida";
$datos = $con->prepare($sql);
$datos->bindParam(':idDetalleSalida', $idDetalleSalida);
$datos->execute();