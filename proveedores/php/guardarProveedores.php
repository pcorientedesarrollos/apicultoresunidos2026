<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$json = file_get_contents("php://input");

try {
    if (!$json) {
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
    $sqlDatosFiscales->bindParam(':razonSocial', $info[0]->razonSocial);
    $sqlDatosFiscales->bindParam(':rfc', $info[0]->rfc);
    $sqlDatosFiscales->bindParam(':curp', $info[0]->curp);
    $sqlDatosFiscales->execute();

    if ($sqlDatosFiscales == false) {
        throw new Exception($con->errorInfo());
    }
    $idDatosFiscales = $con->lastInsertId();

    $sqlDireccion = $con->prepare("INSERT INTO direccion (direccionCompleta, idEstado, idlocalidad, codigoPostal, colonia) VALUES (:direccionCompleta, :idEstado, :idlocalidad, :cp, :colonia)");
    $sqlDireccion->bindParam(':direccionCompleta', $info[0]->direccionCompleta);
    $sqlDireccion->bindParam(':idEstado', $info[0]->idEstado);
    $sqlDireccion->bindParam(':idlocalidad', $info[0]->idlocalidad);
    $sqlDireccion->bindParam(':cp', $info[0]->cp);
    $sqlDireccion->bindParam(':colonia', $info[0]->colonia);
    $sqlDireccion->execute();
    if ($sqlDireccion == false) {
        throw new Exception($con->errorInfo());
    }
    $idDireccion = $con->lastInsertId();

    // Si no viene el tipo de miel, (que no debe pasar si se verifica),
    // le podrá por defecto 3
    $info[0]->tipoDeMiel = isset($info[0]->tipoDeMiel) ? $info[0]->tipoDeMiel : '3';

    $sqlProveedor = $con->prepare("INSERT INTO proveedor(tipo, nombre, idComprador, idDatosFiscales, idDireccion, idSagarpa, tipoDeMiel, empresa, latitud, longitud) VALUES (:tipo, :nombre, :idComprador, :idDatosFiscales, :idDireccion, :idSagarpa, :tipoDeMiel, :empresa, :latitud, :longitud)");
    $sqlProveedor->bindParam(':tipo', $info[0]->tipo);
    $sqlProveedor->bindParam(':nombre', $info[0]->nombre);
    $sqlProveedor->bindParam(':idComprador', $info[0]->idComprador);
    $sqlProveedor->bindParam(':idDatosFiscales', $idDatosFiscales);
    $sqlProveedor->bindParam(':idDireccion', $idDireccion);
    $sqlProveedor->bindParam(':idSagarpa', $info[0]->idSagarpa);
    $sqlProveedor->bindParam(':tipoDeMiel', $info[0]->tipoDeMiel);
    $sqlProveedor->bindParam(':empresa', $info[0]->empresa);
    $sqlProveedor->bindParam(':latitud', $info[0]->latitud);
    $sqlProveedor->bindParam(':longitud', $info[0]->longitud);
    $sqlProveedor->execute();

    if ($sqlProveedor == false) {
        throw new Exception($con->errorInfo());
    }

    $idProveedor = $con->lastInsertId();

    foreach ($info[1] as $telefono) {
        if ($telefono->telefono != "") {
            $sqlTelefono = $con->prepare("INSERT INTO telefonos (idTipo, idContacto, telefono, extencion) VALUES ('1', :idProveedor, :telefono, '0')");
            $sqlTelefono->bindParam(':idProveedor', $idProveedor);
            $sqlTelefono->bindParam(':telefono', $telefono->telefono);
            $sqlTelefono->execute();
        }
    }

    foreach ($info[2] as $contacto) {
        if ($contacto->nombre != "") {
            $sqlContacto = $con->prepare("INSERT INTO contacto (idTipo, idContacto, contacto) VALUES ('1', :idProveedor, :nombre)");
            $sqlContacto->bindParam(':idProveedor', $idProveedor);
            $sqlContacto->bindParam(':nombre', $contacto->nombre);
            $sqlContacto->execute();
        }
    }

    foreach ($info[3] as $correo) {
        if ($correo->email != "") {
            $sqlCorreo = $con->prepare("INSERT INTO correos (idTipo, idContacto, correo) VALUES ('1', :idProveedor, :email)");
            $sqlCorreo->bindParam(':idProveedor', $idProveedor);
            $sqlCorreo->bindParam(':email', $correo->email);
            $sqlCorreo->execute();
        }
    }

    $con->commit();
    echo json_encode(['encabezado' => 'Hecho', 'mensaje' => 'Nuevo proveedor disponible', 'tipo' => 'success']);
} catch (Exception $e) {
    $con->rollBack();
    echo json_encode(['encabezado' => 'Error', 'mensaje' => $e->getMessage(), 'tipo' => 'error']);
}

