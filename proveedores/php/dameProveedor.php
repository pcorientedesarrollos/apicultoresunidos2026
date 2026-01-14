<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$conexion = $pdo->conectar();
$idProveedor = $_GET["id"];


//$sql = "SELECT * FROM proveedor pr "
//        . "INNER JOIN datosfiscales df on pr.idDatosFiscales = df.idDatosFiscales "
//        . "LEFT JOIN direccion d on d.idDireccion = pr.idDireccion "
//        . "LEFT JOIN localidades l ON l.idlocalidad = d.idLocalidad"
//        . " WHERE idProveedor = '$idProveedor' ";
$sql = "SELECT * FROM proveedor pr 
LEFT JOIN datosfiscales df on pr.idDatosFiscales = df.idDatosFiscales 
LEFT JOIN direccion d on d.idDireccion = pr.idDireccion 
LEFT JOIN localidades l ON l.idlocalidad = d.idLocalidad
LEFT JOIN estados em On em.idEstado = d.idEstado
WHERE pr.idProveedor = :idProveedor";

$datos = $conexion->prepare($sql);
$datos->bindParam(':idProveedor', $idProveedor);
$datos->execute();

if ($datos == false) {
    echo mysql_error();
} else {
//    $sqlEliminarContactos = "DELETE FROM contacto WHERE contacto = ''";
//    $datosEliminarContactos = mysql_query($sqlEliminarContactos);
//    $array = array();
//    $cont = 0;


    while ($rs = $datos->fetch()) {
        $proveedor = new stdClass();
        $proveedor->id = $rs["idProveedor"];
        $proveedor->nombre = $rs["nombre"];
        $proveedor->idComprador = $rs["idComprador"];
        $proveedor->idDireccion = $rs["idDireccion"];
        $proveedor->empresa = $rs["empresa"];     
        $proveedor->latitud = $rs["latitud"];        
        $proveedor->longitud = $rs["longitud"];        
        $proveedor->calle = $rs["calle"];
        $proveedor->cruzamientos = $rs["cruzamientos"];
        $proveedor->numeroInterior = $rs["numeroInterior"];
        $proveedor->numeroExterior = $rs["numeroExterior"];
        $proveedor->estado = $rs["estado"];
        $proveedor->idEstado = $rs["idEstado"];
        $proveedor->idlocalidad = $rs["idlocalidad"];
        $proveedor->localidad = $rs["localidad"];
        $proveedor->cp = $rs["codigoPostal"];
        $proveedor->idSagarpa = $rs["idSagarpa"];
        $proveedor->tipo = $rs["tipo"];
        $proveedor->idDatosFiscales = $rs["idDatosFiscales"];
        $proveedor->razonSocial = $rs["razonSocial"];
        $proveedor->curp = $rs["curp"];
        $proveedor->rfc = $rs["rfc"];
        $proveedor->tipoDeMiel = $rs["tipoDeMiel"];        
        $proveedor->telefonos = array();
        $proveedor->contactos = array();
        $proveedor->correos = array();
        $proveedor->colonia = $rs["colonia"];
        $proveedor->direccionCompleta = $rs['direccionCompleta'];

        $sqlTelefonos = "SELECT * FROM telefonos WHERE idContacto =:idProveedor";
        $datosTelefonos = $conexion->prepare($sqlTelefonos);
        $datosTelefonos->bindParam(':idProveedor', $idProveedor);
        $datosTelefonos->execute();


        while ($rsTel = $datosTelefonos->fetch()) {
            $telefonos = new stdClass();
            $telefonos->id = $rsTel["idTelefono"];
            $telefonos->telefono = $rsTel["telefono"];
            $proveedor->telefonos[] = $telefonos;
        }

        $sqlContactos = "SELECT * FROM contacto WHERE idContacto =:idProveedor";

        $datosContactos = $conexion->prepare($sqlContactos);
        $datosContactos->bindParam(':idProveedor', $idProveedor);
        $datosContactos->execute();


        while ($rsCont = $datosContactos->fetch()) {
            $contacto = new stdClass();
            $contacto->idContacto = $rsCont["idContacto"];
            $contacto->id = $rsCont["id"];
            $contacto->nombre = $rsCont["contacto"];
            $proveedor->contactos[] = $contacto;
        }

        $sqlCorreos = "SELECT * FROM correos WHERE idContacto =:idProveedor";
        $datosCorreos = $conexion->prepare($sqlCorreos);
        $datosCorreos->bindParam(':idProveedor', $idProveedor);
        $datosCorreos->execute();

        while ($rsCorreo = $datosCorreos->fetch()) {
            $correo = new stdClass();
            $correo->id = $rsCorreo["id"];
            $correo->email = $rsCorreo["correo"];
            $correo->idContacto = $rsCorreo["idContacto"];
            $proveedor->correos[] = $correo;
        }
//        $array[$cont] = $proveedor;
//        $cont++;
    }

    echo json_encode($proveedor);
}