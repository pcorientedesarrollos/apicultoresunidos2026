<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO(); $con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$json = file_get_contents("php://input");

try {
    if(!$json) {
        throw new Exception('No se recibieron datos');
    } else {
        $datos = json_decode($json);

        $info = $datos->valor;
        $idDatosFiscales = 0;
        $idDireccion = 0;
        $idProveedor = 0;   
    }

    $con->beginTransaction();

    $sqlDatosFiscales = $con->prepare("INSERT INTO datosfiscales (razonSocial, rfc, curp) VALUES (:razonSocial, :rfc, :curp)");
    $sqlDatosFiscales->bindParam(':razonSocial', $info->razonSocial);
    $sqlDatosFiscales->bindParam(':rfc', $info->rfc);
    $sqlDatosFiscales->bindParam(':curp', $info->curp);
    $sqlDatosFiscales->execute();

    if($sqlDatosFiscales == FALSE) {
        throw new Exception($con->errorInfo());
    }
    $idDatosFiscales = $con->lastInsertId();

    $sqlDireccion = $con->prepare("INSERT INTO direccion (direccionCompleta, idEstado, idlocalidad, codigoPostal, colonia) VALUES (:direccionCompleta, :idEstado, :idlocalidad, :cp, :colonia)");
    $sqlDireccion->bindParam(':direccionCompleta', $info->direccionCompleta);
    $sqlDireccion->bindParam(':idEstado', $info->idEstado);
    $sqlDireccion->bindParam(':idlocalidad', $info->idlocalidad);
    $sqlDireccion->bindParam(':cp', $info->cp);
    $sqlDireccion->bindParam(':colonia', $info->colonia);
    $sqlDireccion->execute();
    if($sqlDireccion == FALSE) {
        throw new Exception($con->errorInfo());
    }
    $idDireccion = $con->lastInsertId();

    // Si no viene el tipo de miel, (que no debe pasar si se verifica),
    // le podrá por defecto 3
    $info->tipoDeMiel = isset($info->tipoDeMiel) ? $info->tipoDeMiel : '3';

    $sqlProveedor = $con->prepare("INSERT INTO proveedor(tipo, nombre, idDatosFiscales, idDireccion, idSagarpa, tipoDeMiel) VALUES (:tipo, :nombre, :idDatosFiscales, :idDireccion, :idSagarpa, :tipoDeMiel)");
    $sqlProveedor->bindParam(':tipo', $info->tipo);
    $sqlProveedor->bindParam(':nombre', $info->nombre);
    $sqlProveedor->bindParam(':idDatosFiscales', $idDatosFiscales);
    $sqlProveedor->bindParam(':idDireccion', $idDireccion);
    $sqlProveedor->bindParam(':idSagarpa', $info->idSagarpa);
    $sqlProveedor->bindParam(':tipoDeMiel', $info->tipoDeMiel);
    $sqlProveedor->execute();

    if($sqlProveedor == FALSE) {
        throw new Exception($con->errorInfo());
    }

    $idProveedor = $con->lastInsertId();


    $sqlTelefono = $con->prepare("INSERT INTO telefonos (idTipo ,idContacto,telefono, extencion) VALUES ('1', :idProveedor, :telefono, '0')");
    $sqlTelefono->bindParam(':idProveedor', $idProveedor);
    $sqlTelefono->bindParam(':telefono', $info->telefono);
    $sqlTelefono->execute();

    $con->commit();
    echo json_encode(['encabezado'=> 'Hecho', 'mensaje'=>'Nuevo proveedor disponible', 'tipo'=>'success']);
} catch (Exception $e){
    $con->rollBack();
    echo json_encode(['encabezado'=> 'Error', 'mensaje'=>$e->getMessage(), 'tipo'=>'error']);
}