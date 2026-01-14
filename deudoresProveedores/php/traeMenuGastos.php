<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$query = "SELECT g.idProveedor, p.nombre 
FROM gastosrealizados g 
INNER JOIN proveedor p ON p.idProveedor = g.idProveedor
GROUP BY g.idProveedor
ORDER BY p.nombre ASC";
$datos = $con->prepare($query);
$datos->execute();
$array = array();
while ($row = $datos->fetch()) {
    $info = new stdClass();
    $info->idProveedor = $row["idProveedor"];
    $info->nombre = $row["nombre"];
    $lista[] = $info;
}
echo $json_response = json_encode($lista);
?>