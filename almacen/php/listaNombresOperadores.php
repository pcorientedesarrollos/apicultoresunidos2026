<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$sql = 'SELECT idOperador, operador FROM choferes ORDER BY operador ASC';
$datos = $con->prepare($sql);
$datos->execute();

$arrayOperador = array();
while ($row = $datos->fetch()) {
    $operadoresC = new stdClass();
    $operadoresC->idOperador = $row["idOperador"];
    $operadoresC->operador = $row["operador"];
    $arrayOperador[] = $operadoresC;
}

echo $json_response = json_encode($arrayOperador);
?>