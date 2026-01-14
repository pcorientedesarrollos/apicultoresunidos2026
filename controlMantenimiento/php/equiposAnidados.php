<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$idArea = $_GET['idArea'];

$query = "SELECT idEquipo, CONCAT(nombre,' | ',codigo) AS nombre  FROM equipos WHERE idArea = :idArea AND mantto = 0";
$datos = $con->prepare($query);
$datos->bindParam(':idArea', $idArea);
$datos->execute();

$arrayE = array();
while ($row = $datos->fetch()) {
    $lstEquipos = new stdClass();
    $lstEquipos->idEquipo = $row["idEquipo"];
    $lstEquipos->nombre = $row["nombre"];
    $arrayE[] = $lstEquipos;
}


# JSON-encode the response
echo $json_response = json_encode($arrayE);
?>