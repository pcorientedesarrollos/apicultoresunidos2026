<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$tipo = $_GET['tipoMiel'];

if($tipo == '1'){
    $calidad = 'calidad';
}else if($tipo == '2'){
    $calidad = 'calidad_organico';
}else if($tipo == '5'){
    $calidad = 'calidad_mantequilla';
}else if($tipo == '6'){
    $calidad = 'calidad_altiplano';
}else if($tipo == '7'){
    $calidad = 'calidad_naranjo';
}else if($tipo == '8'){
    $calidad = 'calidad_aguacate';
}else if($tipo == '9'){
    $calidad = 'calidad_mezquite';
}

$query = "SELECT t.idLoteInterno, c.marcaFinalCliente, t.tipoMiel 
FROM trazabilidadlaboratorio t
LEFT JOIN $calidad c ON c.idLoteInterno = t.idLoteInterno
WHERE t.tipoMiel = '" . $tipo . "'
ORDER BY t.idLoteInterno DESC";
$datos = $con->prepare($query);
$datos->execute();

$arrayTLab = array();
while ($row = $datos->fetch()) {
    $infoLab = new stdClass();
    $infoLab->idLoteInterno = $row["idLoteInterno"];
    $infoLab->marcaFinalCliente = $row["marcaFinalCliente"];
    $infoLab->tipoMiel = $row["tipoMiel"];
    $arrayTLab[] = $infoLab;
}
echo json_encode($arrayTLab);
?>