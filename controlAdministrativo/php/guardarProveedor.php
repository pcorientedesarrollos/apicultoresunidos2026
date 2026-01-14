<?php

$datos = json_decode(file_get_contents("php://input"));
include_once '../../DAOConeccion/conePDO.php';
$con = new conePDO();
$cn = $con->conectar();

function validarDatos($datos)
{
    if (!isset($datos->nombreProveedor)) {
        $datos->nombreProveedor = '';
    }
    
    if (!isset($datos->domicilio)) {
        $datos->domicilio = '';
    }
    if (!isset($datos->contactoProveedor)) {
        $datos->contactoProveedor = '';
    }
    if (!isset($datos->telefono)) {
        $datos->telefono = '';
    }
    return $datos;
}

function main($datos, $fecha)
{
    global $cn;
    try {
        $cn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $cn->beginTransaction();

        $insertar = $cn->prepare("INSERT INTO proveedoresmantto (fechaRegistro, nombreProveedor, nombreContacto, domicilio, telefono) VALUES (:fechaRegistro, :nombreProveedor, :nombreContacto, :domicilio, :telefono)");
        $insertar->bindParam(':fechaRegistro', $fecha);
        $insertar->bindParam(':nombreProveedor', $datos->nombreProveedor);
        $insertar->bindParam(':nombreContacto', $datos->contactoProveedor);
        $insertar->bindParam(':domicilio', $datos->domicilio);
        $insertar->bindParam(':telefono', $datos->telefono);
        $insertar->execute();
        if ($insertar->rowCount() == 1) {
            echo json_encode(['error'=>false, 'message'=>'Se ha registrado un nuevo proveedor', 'swal'=>'success']);
            $cn->commit();
        } else {
            throw new Exeption('No se ha registrado');
        }
    } catch (Exception $e) {
        $cn->rollBack();
        echo json_encode(['error'=>true, 'message'=>$e->getMessage(), 'swal'=>'error']);
    }
}

if ($datos) {
    $datos->datos = validarDatos($datos->datos);
    main($datos->datos, $datos->fecha);
} else {
    echo 'Must send data';
    exit();
}
