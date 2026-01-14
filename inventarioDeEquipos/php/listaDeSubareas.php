<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$idArea = $_GET["idArea"];

$sql = "SELECT idSubarea, (CONCAT(subarea,' - ',nombre)) AS zona FROM subareas WHERE idArea = :idArea";
$data = $con->prepare($sql);
$data->bindParam('idArea', $idArea);
$data->execute();

$arraySubareas = array();

while ($row = $data->fetch()) {
    $listaSub = new stdClass();
    $listaSub->idSubarea = $row["idSubarea"];
    $listaSub->zona = $row["zona"];
    $arraySubareas[] = $listaSub;
}

echo $json_response = json_encode($arraySubareas);
?>