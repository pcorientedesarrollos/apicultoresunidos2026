<?php

include_once '../../../DAOConeccion/conePDO.php';
$pdo = new conePDO;
$con = $pdo->conectar();

$query = "SELECT * FROM compradores WHERE estado = '1'";

$datos = $con->prepare($query);
$datos->execute();

$compradores = array();
while ($row = $datos->fetch()) {
    $comps = new stdClass();
    $comps->idcomprador = $row["idcomprador"];
    $comps->nombre = utf8_encode($row["nombre"]);
    $comps->telefono = $row["telefono"];
    $compradores[] = $comps;
}
echo json_encode($compradores);
?>