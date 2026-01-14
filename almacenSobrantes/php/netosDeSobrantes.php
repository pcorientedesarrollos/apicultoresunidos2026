<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$tipoDeMiel = $_GET['tipoDeMiel'];

$sql = "SELECT s.nombre AS sobrante, SUM(alms.neto) AS totalNeto
        FROM almacensobrantes alms 
        INNER JOIN sobrantes s ON s.idSobrante = alms.sobrante
        WHERE alms.tipoDeMiel = $tipoDeMiel
        GROUP BY alms.sobrante";
$datos = $con->prepare($sql);
$datos->execute();

$totalSobrantes = array();
while ($row = $datos->fetch()) {
    $sobranteNeto = new stdClass();
    $sobranteNeto->sobrante = $row["sobrante"];
    $sobranteNeto->totalNeto = $row["totalNeto"];
    $totalSobrantes[] = $sobranteNeto;
}

echo $json_response = json_encode($totalSobrantes);
?>