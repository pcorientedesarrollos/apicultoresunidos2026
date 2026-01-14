<?php

include_once '../../../DAOConeccion/conePDO.php';
$pdo = new conePDO;
$con = $pdo->conectar();

$sql = 'SELECT idcomprador, nombre FROM compradores WHERE estado = 1 ORDER BY nombre ASC';

$datos = $con->prepare($sql);
$datos->execute();

$arrayCompradores = array();
while ($row = $datos->fetch()) {
    $nomComprador = new stdClass();
    $nomComprador->idcomprador = $row["idcomprador"];
    $nomComprador->nombre = $row["nombre"];
    $arrayCompradores[] = $nomComprador;
}

echo $json_response = json_encode($arrayCompradores);
