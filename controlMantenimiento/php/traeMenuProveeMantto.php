<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$query = "SELECT pm.idProveedorMantto, pm.idTipoProveedor, a.area, pm.nombreProveedor, pm.nombreContacto, pm.telefono
FROM proveedoresmantto pm
LEFT JOIN areas a ON a.idArea = pm.idArea
ORDER BY a.area ASC";
$datos = $con->prepare($query);
$datos->execute();

$arrayR = array();
while ($row = $datos->fetch()) {
    $menuTecnicos = new stdClass();
    $menuTecnicos->idProveedorMantto = $row["idProveedorMantto"];
    $menuTecnicos->idTipoProveedor = $row["idTipoProveedor"];
    $menuTecnicos->area = $row["area"];
    $menuTecnicos->nombreProveedor = $row["nombreProveedor"];
    $menuTecnicos->nombreContacto = $row["nombreContacto"];
    $menuTecnicos->telefono = $row["telefono"];

    if ($menuTecnicos->idTipoProveedor == '1') {
        $menuTecnicos->idTipoProveedor = "Insumos";
    } else if ($menuTecnicos->idTipoProveedor == '2') {
        $menuTecnicos->idTipoProveedor = "Servicios";
    } elseif ($menuTecnicos->idTipoProveedor == '3') {
        $menuTecnicos->idTipoProveedor = "Transportistas";
    } else {
        $menuTecnicos->idTipoProveedor = " ";
    };

    $arrayR[] = $menuTecnicos;
}

echo json_encode($arrayR);
?>