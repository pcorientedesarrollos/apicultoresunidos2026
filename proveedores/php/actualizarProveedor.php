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
    $sqlDatosFiscales = $con->prepare("UPDATE datosfiscales set razonSocial = :razonSocial, rfc = :rfc, curp = :curp WHERE idDatosFiscales = :idDatosFiscales");
    $sqlDatosFiscales->bindParam(':razonSocial', $info[0]->razonSocial);
    $sqlDatosFiscales->bindParam(':rfc', $info[0]->rfc);
    $sqlDatosFiscales->bindParam(':curp', $info[0]->curp);
    $sqlDatosFiscales->bindParam(':idDatosFiscales', $info[0]->idDatosFiscales);
    $sqlDatosFiscales->execute();

    if ($sqlDatosFiscales == false) {
        throw new Exception($con->errorInfo());
    }

    $sqlDireccion = $con->prepare("UPDATE direccion set direccionCompleta = :direccionCompleta, idEstado = :idEstado, idlocalidad = :idlocalidad, codigoPostal = :cp, colonia = :colonia WHERE idDireccion = :idDireccion");
    $sqlDireccion->bindParam(':direccionCompleta', $info[0]->direccionCompleta);
    $sqlDireccion->bindParam(':idEstado', $info[0]->idEstado);
    $sqlDireccion->bindParam(':idlocalidad', $info[0]->idlocalidad);
    $sqlDireccion->bindParam(':cp', $info[0]->cp);
    $sqlDireccion->bindParam(':colonia', $info[0]->colonia);
    $sqlDireccion->bindParam(':idDireccion', $info[0]->idDireccion);
    $sqlDireccion->execute();
    if ($sqlDireccion == false) {
        throw new Exception($con->errorInfo());
    }
    $sqlProveedor = $con->prepare("UPDATE proveedor set tipo = :tipo, nombre = :nombre, idComprador = :idComprador, idSagarpa = :idSagarpa, tipoDeMiel = :tipoDeMiel, empresa = :empresa, latitud = :latitud, longitud = :longitud WHERE idProveedor = :id");
    $sqlProveedor->bindParam(':tipo', $info[0]->tipo);
    $sqlProveedor->bindParam(':nombre', $info[0]->nombre);
    $sqlProveedor->bindParam(':idComprador', $info[0]->idComprador);
    $sqlProveedor->bindParam(':idSagarpa', $info[0]->idSagarpa);
    $sqlProveedor->bindParam(':tipoDeMiel', $info[0]->tipoDeMiel);
    $sqlProveedor->bindParam(':empresa', $info[0]->empresa);
    $sqlProveedor->bindParam(':latitud', $info[0]->latitud);
    $sqlProveedor->bindParam(':longitud', $info[0]->longitud);
    $sqlProveedor->bindParam(':id', $info[0]->id);
    $sqlProveedor->execute();

    if ($sqlProveedor == false) {
        throw new Exception($con->errorInfo());
    }

    foreach ($info[1] as $telefono) {
        if ($telefono->id > 0) {
            $sqlTelefono = $con->prepare("UPDATE telefonos SET telefono = :telefono WHERE idTelefono = :idTelefono");
            $sqlTelefono->bindParam(':telefono', $telefono->telefono);
            $sqlTelefono->bindParam(':idTelefono', $telefono->id);
            $sqlTelefono->execute();
        } else {
            $sqlTelefono = $con->prepare("INSERT INTO telefonos (idTipo, idContacto, telefono, extencion) VALUES ('1', :idProveedor, :telefono, '0')");
            $sqlTelefono->bindParam(':idProveedor', $info[0]->id);
            $sqlTelefono->bindParam(':telefono', $telefono->telefono);
            $sqlTelefono->execute();
        }
    }

    foreach ($info[2] as $contacto) {

        if ($contacto->id > 0) {
            $sqlContacto = $con->prepare("UPDATE contacto set contacto = :nombre WHERE id = :id");
            $sqlContacto->bindParam(':nombre', $contacto->nombre);
            $sqlContacto->bindParam(':id', $contacto->id);
            $sqlContacto->execute();
        } else {
            $sqlContacto = $con->prepare("INSERT INTO contacto (idTipo, idContacto, contacto) VALUES ('1', :idProveedor, :nombre)");
            $sqlContacto->bindParam(':idProveedor', $info[0]->id);
            $sqlContacto->bindParam(':nombre', $contacto->nombre);
            $sqlContacto->execute();
        }

    }

    foreach ($info[3] as $correo) {

        if ($correo->id > 0) {
            $sqlCorreo = $con->prepare("UPDATE correos set correo = :email WHERE id = :idProveedor");
            $sqlCorreo->bindParam(':email', $correo->email);
            $sqlCorreo->bindParam(':idProveedor', $correo->id);
            $sqlCorreo->execute();
        } else {
            $sqlCorreo = $con->prepare("INSERT INTO correos (idTipo, idContacto, correo) VALUES ('1', :idProveedor, :email)");
            $sqlCorreo->bindParam(':idProveedor', $info[0]->id);
            $sqlCorreo->bindParam(':email', $correo->email);
            $sqlCorreo->execute();
        }

    }

    $con->commit();
    echo json_encode(['encabezado' => 'Hecho', 'mensaje' => 'Se ha actualizado la información', 'tipo' => 'success']);
} catch (Exception $e) {
    $con->rollBack();
    echo json_encode(['encabezado' => 'Error', 'mensaje' => $e->getMessage() . '.' . $e->getLine(), 'tipo' => 'error']);
}