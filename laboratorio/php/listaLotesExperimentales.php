<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$miel = $_GET['miel'];

switch($miel){
    case '1':
        $experimental = 'experimental';
        $codigo = 'EC-';
    break;
    case '2':
        $experimental = 'experimental_organico';
        $codigo = 'EO-';
    break;
}

$sql = "SELECT idLoteExperimental, CONCAT('$codigo', idLoteExperimental) AS experimental FROM $experimental ORDER BY idLoteExperimental ASC";
$data = $con->prepare($sql);
$data->execute();

$experimentales = array();

while ($row = $data->fetch()) {
    $listaExp = new stdClass();
    $listaExp->idLoteExperimental = $row["idLoteExperimental"];
    $listaExp->experimental = $row["experimental"];    
    $experimentales[] = $listaExp;
}

echo $json_response = json_encode($experimentales);
