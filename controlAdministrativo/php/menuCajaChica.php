<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$sql = "SELECT cc.idMes, m.mes FROM cajachica cc 
LEFT JOIN meses m ON m.idMes = cc.idMes
WHERE cc.idMes != 0
GROUP BY m.mes
ORDER BY m.idMes ASC";
$datos = $con->prepare($sql);
$datos->execute();

$arrayCC = array();
while ($row = $datos->fetch()) {
    $cajaC = new stdClass();
    $cajaC->idMes = $row["idMes"];
    $cajaC->mes = $row["mes"];
    $arrayCC[] = $cajaC;
}

echo $json_response = json_encode($arrayCC);
?>