<?php

include_once '../../../DAOConeccion/conePDO.php';
$pdo = new conePDO;
$con = $pdo->conectar();

$query = "             
SELECT lo.idlocalidad, lo.localidad, lo.idzona, lo.estado, zo.zona, co.nombre 
FROM localidades lo INNER JOIN zonas zo ON zo.idzona = lo.idzona
INNER JOIN compradores co ON co.idcomprador = zo.idcomprador 
ORDER BY lo.localidad ASC";
$datos = $con->prepare($query);
$datos->execute();

$localidad = array();
while ($row = $datos->fetch()) {
    $local = new stdClass();
    $local->localidad = $row["localidad"];
    $local->idzona = $row["idzona"];
    $local->zona = $row["zona"];
    $local->idlocalidad = $row["idlocalidad"];
    $local->nombre = $row["nombre"];
    $localidad[] = $local;
}
echo json_encode($localidad);
?>