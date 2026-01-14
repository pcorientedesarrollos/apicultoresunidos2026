<?php

include_once '../../DAOConeccion/conePDO.php';
$datos = json_decode(file_get_contents("php://input"));
$con = new conePDO();
$cn = $con->conectar();

date_default_timezone_set('America/Merida');

function validarDatos($datos)
{
    if (!isset($datos->nombreProveedor)) {
        return false;
    }
    return true;
}

function main($datos)
{
    global $cn;
    try {
        $cn->beginTransaction();

        $datos->nombreProveedor = isset($datos->nombreProveedor) ? $datos->nombreProveedor : '';
        $datos->domicilio = isset($datos->domicilio) ? $datos->domicilio : '';
        $datos->telefono = isset($datos->telefono) ? $datos->telefono : '';
        $fechaRegistro = date('Y-m-d');

        $insertar = $cn->prepare("INSERT INTO clientes (fechaRegistro, nombre, domicilio, telefono) VALUES (:fechaRegistro, :nombre, :domicilio, :telefono)");
        $insertar->bindParam(':fechaRegistro', $fechaRegistro);
        $insertar->bindParam(':nombre', $datos->nombreProveedor);
        $insertar->bindParam(':domicilio', $datos->domicilio);
        $insertar->bindParam(':telefono', $datos->telefono);
        $insertar->execute();
        if ($insertar->rowCount() == 1) {
            echo json_encode(['error'=>false, 'message'=>'Se ha registrado un nuevo cliente', 'swal'=>'success']);
            $cn->commit();
        } else {
            throw new Exception('No se ha registrado');
        }
    } catch (Exception $e) {
        $cn->rollBack();
        echo json_encode(['error'=>true, 'message'=>$e->getMessage(), 'swal'=>'error']);
    }
}

if ($datos) {
    if (validarDatos($datos)) {
        main($datos);
    } else {
        echo json_encode(['error'=>true, 'message'=>'Hacen falta argumentos', 'swal'=>'error']);
    }
} else {
    echo 'Must send data';
    exit();
}
