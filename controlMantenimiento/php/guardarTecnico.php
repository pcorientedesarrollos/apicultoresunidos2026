<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$miInfo = file_get_contents("php://input");
$info = json_decode($miInfo);
$fechaRegistro = date("Y-m-d");

if (isset($_GET['idProveedorMantto'])) {
    $sqlUp = "UPDATE proveedoresmantto SET idArea = :idArea, fechaRegistro = :fechaRegistro, nombreProveedor = :nombreProveedor, nombreContacto = :nombreContacto, idTipoProveedor = :idTipoProveedor, insumo = :insumo, domicilio = :domicilio, telefono = :telefono, correo = :correo, web = :web, estatus = :estatus, credito = :credito WHERE idProveedorMantto = :idProveedorMantto";
    $datos = $con->prepare($sqlUp);
    $datos->bindParam(':idArea', $info->idArea);
    $datos->bindParam(':fechaRegistro', $fechaRegistro);
    $datos->bindParam(':nombreProveedor', $info->nombreProveedor);
    $datos->bindParam(':nombreContacto', $info->nombreContacto);
    $datos->bindParam(':idTipoProveedor', $info->idTipoProveedor);
    $datos->bindParam(':insumo', $info->insumo);
    $datos->bindParam(':domicilio', $info->domicilio);
    $datos->bindParam(':telefono', $info->telefono);
    $datos->bindParam(':correo', $info->correo);
    $datos->bindParam(':web', $info->web);
    $datos->bindParam(':estatus', $info->estatus);
    $datos->bindParam(':credito', $info->credito);
    $datos->bindParam(':idProveedorMantto', $info->idProveedorMantto);
    $datos->execute();
} else {
    $sql = "INSERT INTO proveedoresmantto (idArea, fechaRegistro, nombreProveedor, nombreContacto, idTipoProveedor, insumo, domicilio, telefono, correo, web, estatus, credito) VALUES (:idArea, :fechaRegistro, :nombreProveedor, :nombreContacto, :idTipoProveedor, :insumo, :domicilio, :telefono, :correo, :web, :estatus, :credito)";
    $dato = $con->prepare($sql);
    $dato->bindParam(':idArea', $info->idArea);
    $dato->bindParam(':fechaRegistro', $fechaRegistro);
    $dato->bindParam(':nombreProveedor', $info->nombreProveedor);
    $dato->bindParam(':nombreContacto', $info->nombreContacto);
    $dato->bindParam(':idTipoProveedor', $info->idTipoProveedor);
    $dato->bindParam(':insumo', $info->insumo);
    $dato->bindParam(':domicilio', $info->domicilio);
    $dato->bindParam(':telefono', $info->telefono);
    $dato->bindParam(':correo', $info->correo);
    $dato->bindParam(':web', $info->web);
    $dato->bindParam(':estatus', $info->estatus);
    $dato->bindParam(':credito', $info->credito);
    $dato->execute();
    if ($dato == false) {
        echo mysql_error();
    } else {
        echo 'Nuevo técnico agregado';
    }
}
?>