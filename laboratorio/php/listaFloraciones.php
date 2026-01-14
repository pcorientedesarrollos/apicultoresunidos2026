<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO;
$con = $pdo->conectar();

$sql = 'SELECT idFloracion, floracion FROM floraciones ORDER BY floracion ASC';
$data = $con->prepare($sql);
$data->execute();

$arrayFloracion = array();
while ($row = $data->fetch()) {
    $floracion = new stdClass();
    $floracion->idFloracion = $row["idFloracion"];
    $floracion->floracion = $row["floracion"];
    $arrayFloracion[] = $floracion;
}

echo $json_response = json_encode($arrayFloracion);
?>