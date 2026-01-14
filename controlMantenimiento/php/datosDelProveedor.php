<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$idProveedorMantto = $_GET['idProveedorMantto'];

$sql = "SELECT idProveedorMantto, domicilio, telefono, web, correo, nombreContacto
        FROM proveedoresmantto
        WHERE idProveedorMantto = :idProveedorMantto";
$datos = $con->prepare($sql);
$datos->bindParam(':idProveedorMantto', $idProveedorMantto);
$datos->execute();

if ($datos == false) {
    echo mysql_error();
} else {
    while ($rs = $datos->fetch()) {
        $datosProveedor = new stdClass();
        $datosProveedor->idProveedorMantto = $rs["idProveedorMantto"];
        $datosProveedor->domicilio = $rs["domicilio"];
        $datosProveedor->telefono = $rs["telefono"];
        $datosProveedor->web = $rs["web"];
        $datosProveedor->correo = $rs["correo"];
        $datosProveedor->nombreContacto = $rs["nombreContacto"];
    }
    echo json_encode($datosProveedor);
}
?>