<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$query = "SELECT p.idProveedorMantto AS idProveedor, UPPER(p.nombreProveedor)  AS nombre FROM proveedoresmantto p
UNION
SELECT m.idProveedor AS idProveedor, UPPER(m.nombre) FROM proveedor m 
ORDER BY nombre ASC";
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