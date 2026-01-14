<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO;
$con = $pdo->conectar();

$sql = 'SELECT idLaboratorio, nombre FROM laboratoriosexternos ORDER BY nombre ASC';
$data = $con->prepare($sql);
$data->execute();

$array = array();
while ($row = $data->fetch()) {
    $nombre = new stdClass();
    $nombre->idLaboratorio = $row["idLaboratorio"];
    $nombre->nombre = $row["nombre"];
    $array[] = $nombre;
}

echo $json_response = json_encode($array);
?>