<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$sql = 'SELECT idParametro, parametro FROM parametroscertificado ORDER BY parametro ASC';
$data = $con->prepare($sql);
$data->execute();

$parametros = array();

while ($row = $data->fetch()) {
    $listaParametros = new stdClass();
    $listaParametros->idParametro = $row["idParametro"];
    $listaParametros->parametro = $row["parametro"];    
    $parametros[] = $listaParametros;
}

echo $json_response = json_encode($parametros);
?>