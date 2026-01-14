<?php

include_once '../../../DAOConeccion/conePDO.php';
$pdo = new conePDO;
$con = $pdo->conectar();

$query = "select z.idcomprador, z.zona,z.idzona, c.nombre from zonas z
                 INNER JOIN  compradores c ON c.idcomprador = z.idcomprador 
                 ORDER BY z.zona ASC";
$datos = $con->prepare($query);
$datos->execute();

$zona = array();
while ($row = $datos->fetch()) {
    $zons = new stdClass();
    $zons->idzona = $row["idzona"];
    $zons->zona = $row["zona"];
    $zons->idcomprador = $row["idcomprador"];
    $zons->nombre = $row["nombre"];
    $zona[] = $zons;
}
echo json_encode($zona);
?>