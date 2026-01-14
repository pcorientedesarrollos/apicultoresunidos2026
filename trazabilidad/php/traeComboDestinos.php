<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$sql = "SELECT idEmpresaPais, CONCAT(empresa,' - ',pais) AS destino  FROM empresasypaises";
$data = $con->prepare($sql);
$data->execute();

$arrayDestino = array();

while ($row = $data->fetch()) {
    $listaDestinos = new stdClass();
    $listaDestinos->idEmpresaPais = $row["idEmpresaPais"];
    $listaDestinos->destino = $row["destino"];
    $arrayDestino[] = $listaDestinos;
}

echo $json_response = json_encode($arrayDestino);
?>