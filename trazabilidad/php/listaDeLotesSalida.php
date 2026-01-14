<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$tipoMiel = $_GET['miel'];

if ($tipoMiel == '1') {
    $calidad_tabla = 'calidad';
} else if ($tipoMiel == '2') {
    $calidad_tabla = 'calidad_organico';
} else if ($tipoMiel == '5') {
    $calidad_tabla = 'calidad_mantequilla';
} else if ($tipoMiel == '6') {
    $calidad_tabla = 'calidad_altiplano';
} else if ($tipoMiel == '7') {
    $calidad_tabla = 'calidad_naranjo';
} else if ($tipoMiel == '8') {
    $calidad_tabla = 'calidad_aguacate';
} else if ($tipoMiel == '9') {
    $calidad_tabla = 'calidad_mezquite';
}


$sql = "SELECT c.idLoteInterno
FROM $calidad_tabla c
LEFT JOIN trazabilidadsalida t ON t.idLoteInterno = c.idLoteInterno
WHERE CONCAT(c.idLoteInterno,'-',$tipoMiel) NOT IN (SELECT CONCAT(idLoteInterno,'-',tipoMiel) FROM trazabilidadsalida)";
$data = $con->prepare($sql);
$data->execute();

$arrayLotes = array();

while ($row = $data->fetch()) {
    $listaLotes = new stdClass();
    $listaLotes->idLoteInterno = $row["idLoteInterno"];
    $arrayLotes[] = $listaLotes;
}

echo $json_response = json_encode($arrayLotes);
?>