<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO;
$con = $pdo->conectar();

$idComprador = $_GET['idcomprador'];

$sql = "SELECT l.idlocalidad, l.localidad 
FROM localidades l 
LEFT JOIN zonas z ON z.idzona = l.idzona
WHERE l.estado = 1 AND z.idcomprador = :idComprador
ORDER BY localidad ASC";

$datos = $con->prepare($sql);
$datos->bindParam(':idComprador', $idComprador);
$datos->execute();

$arrayLocalidades = array();
while ($row = $datos->fetch()) {
    $localidadC = new stdClass();
    $localidadC->idlocalidad = $row["idlocalidad"];
    $localidadC->localidad = $row["localidad"];
    $arrayLocalidades[] = $localidadC;
}

echo $json_response = json_encode($arrayLocalidades);
?>