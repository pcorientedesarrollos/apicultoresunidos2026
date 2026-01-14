<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$parametro = $_GET['valor'];

$sql = 'SELECT rc.* FROM rangoscertificado rc WHERE rc.idParametro = :idParametro';
$data = $con->prepare($sql);
$data->bindParam(':idParametro', $parametro);
$data->execute();

$rangos = array();

while ($row = $data->fetch()) {
    $listaRangos = new stdClass();
    $listaRangos->idRango = $row["idRango"];
    $listaRangos->idAnalisis = $row["idAnalisis"];  
    $listaRangos->idParametro = $row["idParametro"];
    $listaRangos->rango = $row["rango"];    
    $rangos[] = $listaRangos;
}

echo $json_response = json_encode($rangos);
?>