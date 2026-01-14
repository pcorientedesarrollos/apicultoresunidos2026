<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$sql = 'SELECT c.idLoteInterno
FROM calidad c
LEFT OUTER JOIN entradaysalida t
ON c.idLoteInterno = t.idLoteInterno
WHERE t.idLoteInterno IS NULL ';
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