<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$tipo = $_GET['tipoMiel'];

switch($tipo){
    case '1':
        $calidad = 'calidad';
        break;
    case '2':
        $calidad = 'calidad_organico';
        break;
    case '5':
        $calidad = 'calidad_mantequilla';
        break;
    case '6':
        $calidad = 'calidad_altiplano';
        break;
    case '7':
        $calidad = 'calidad_naranjo';
        break;
    case '8':
        $calidad = 'calidad_aguacate';
        break;
    case '9':
        $calidad = 'calidad_mezquite';
        break;
}
// $sql = "SELECT c.idLoteInterno
// FROM $calidad c";
// -- LEFT OUTER JOIN trazabilidadlaboratorio t
// -- ON c.idLoteInterno = t.idLoteInterno
// -- WHERE t.idLoteInterno IS NULL";
$sql = "SELECT c.idLoteInterno
FROM $calidad c
LEFT JOIN trazabilidadlaboratorio t ON t.idLoteInterno = c.idLoteInterno
WHERE CONCAT(c.idLoteInterno,'-',$tipo) NOT IN (SELECT CONCAT(idLoteInterno,'-',tipoMiel) FROM trazabilidadlaboratorio)";
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