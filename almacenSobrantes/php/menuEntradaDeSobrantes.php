<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$tipoDeMiel = $_GET['tipoDeMiel'];

$sql = "SELECT ae.idEncabezadoSobrante, ae.fecha, t.tipoDeMiel, ae.tambos, ae.sobrante AS idSobrante,
s.nombre AS sobrante, SUM(ad.neto) AS neto,
CASE WHEN ae.tipo = '1' THEN 'Nacional' WHEN ae.tipo = '2' THEN 'Exportación' END AS tipo
FROM almacensobrantes ad
LEFT JOIN almacensobrantesencabezado ae ON ae.idEncabezadoSobrante = ad.consecutivoEntrada
LEFT JOIN sobrantes s ON s.idSobrante = ae.sobrante
LEFT JOIN tiposdemiel t ON t.idTipoDeMiel = ae.tipoDeMiel
WHERE ae.tipoDeMiel = $tipoDeMiel";
if (isset($_GET['mes'])) {
    $mes = $_GET['mes'];
    $sql .= " AND SUBSTR(ae.fecha FROM 6 FOR 2) = " . $mes;
}
if (isset($_GET['tipoSobrante'])) {
    $sobrante = $_GET['tipoSobrante'];
    $sql .= " AND ae.sobrante = " . $sobrante;
}
$sql .= " 	GROUP BY ae.idEncabezadoSobrante ORDER BY ae.idEncabezadoSobrante DESC";
$datos = $con->prepare($sql);
$datos->execute();

$sobrantes = array();
while ($row = $datos->fetch()) {
    $sobrante = new stdClass();
    $sobrante->idEncabezadoSobrante = $row["idEncabezadoSobrante"];
    $sobrante->fecha = $row["fecha"];
    $sobrante->sobrante = $row["sobrante"];
    $sobrante->tipo = $row["tipo"];
    $sobrante->tipoDeMiel = $row["tipoDeMiel"];
    $sobrante->tambos = $row["tambos"];
    $sobrante->neto = $row["neto"];
    $sobrantes[] = $sobrante;
}

echo $json_response = json_encode($sobrantes);
