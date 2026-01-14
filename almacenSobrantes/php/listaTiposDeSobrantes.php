<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$sql = 'SELECT idSobrante, nombre FROM sobrantes ORDER BY nombre ASC';
$data = $con->prepare($sql);
$data->execute();

$sobrantes = array();

while ($row = $data->fetch()) {
    $listaSobrante = new stdClass();
    $listaSobrante->idSobrante = $row["idSobrante"];
    $listaSobrante->nombre = $row["nombre"];    
    $sobrantes[] = $listaSobrante;
}

echo $json_response = json_encode($sobrantes);
?>