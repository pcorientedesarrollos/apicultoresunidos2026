<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$idOperador = $_GET['idOperador'];

$query = "SELECT idPlaca,placa FROM placaschoferes WHERE idOperador = :idOperador";
$datos = $con->prepare($query);
$datos->bindParam(':idOperador', $idOperador);
$datos->execute();

$arrayPlacas = array();
while ($row = $datos->fetch()) {
    $placasC = new stdClass();
    $placasC->idPlaca = $row["idPlaca"];
    $placasC->placa = $row["placa"];
    $arrayPlacas[] = $placasC;
}

# JSON-encode the response
echo $json_response = json_encode($arrayPlacas);
?>