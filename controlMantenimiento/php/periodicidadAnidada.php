<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$idEquipo = $_GET['idEquipo'];

$query = "SELECT idEquipo, periodicidad FROM equipos WHERE idEquipo = :idEquipo";
$datos = $con->prepare($query);
$datos->bindParam(':idEquipo', $idEquipo);
$datos->execute();

while ($row = $datos->fetch()) {
    $infoAnidada = new stdClass();
    $infoAnidada->idEquipo = $row["idEquipo"];
    $infoAnidada->periodicidad = $row["periodicidad"];

    switch ($infoAnidada->periodicidad) {
        case '0':
            $infoAnidada->periodicidad = "Aún no asignada";
            break;
        case '1':
            $infoAnidada->periodicidad = "Mensual";
            break;
        case '2':
            $infoAnidada->periodicidad = "Trimestral";
            break;
        case '3':
            $infoAnidada->periodicidad = "Semestral";
            break;
        case '4':
            $infoAnidada->periodicidad = "Anual";
            break;
    }
}

# JSON-encode the response
echo $json_response = json_encode($infoAnidada);
?>