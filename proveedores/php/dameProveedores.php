<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$conexion = $pdo->conectar();

if(isset($_GET['tipo'])){
    $tipo = $_GET['tipo'];
}

if(isset($_GET['estado'])){
    $estado = $_GET['estado'];
}

$str = '&#10004;';

$sql = "SELECT pr.*, CONCAT(l.localidad,', ',e.estado) AS procedencia, l.localidad, e.estado,
ac.idContrato, ai.idIne, ains.idInstalacion, aa.idApiario, aca.idCarta, ap.idPagare, c.nombre as nombreComprador,
z.zona
FROM proveedor pr 
LEFT JOIN compradores c ON c.idcomprador = pr.idComprador
LEFT JOIN datosfiscales df on pr.idDatosFiscales = df.idDatosFiscales 
LEFT JOIN direccion d on d.idDireccion = pr.idDireccion
LEFT JOIN localidades l ON l.idlocalidad = d.idLocalidad
LEFT JOIN estados e ON e.idEstado = d.idEstado
LEFT JOIN archivoscontratos ac ON ac.idProveedor = pr.idProveedor
LEFT JOIN archivosine ai ON ai.idProveedor = pr.idProveedor
LEFT JOIN archivosinstalaciones ains ON ains.idProveedor = pr.idProveedor
LEFT JOIN archivosapiarios aa ON aa.idProveedor = pr.idProveedor
LEFT JOIN archivoscartas aca ON aca.idProveedor = pr.idProveedor
LEFT JOIN archivospagares ap ON ap.idProveedor = pr.idProveedor
LEFT JOIN zonas z ON z.idzona = l.idzona";


if(isset($tipo) && isset($estado)){
    if(isset($_GET['todos'])){
        $sql .= " WHERE pr.activoInactivo = '" . $estado. "' AND (pr.tipoDeMiel = '3' OR pr.tipoDeMiel ='" . $tipo . "') AND pr.deleteProve = 0";
    }else{
        $sql .= " WHERE pr.activoInactivo = '" . $estado. "' AND pr.tipoDeMiel = '" . $tipo . "' AND pr.deleteProve = 0";
    }
}else if(isset($estado)){
    $sql .= " WHERE pr.activoInactivo = '" . $estado. "' AND pr.deleteProve = 0";
}

$sql.=" GROUP BY pr.idProveedor ORDER BY pr.nombre ASC";
$datos = $conexion->prepare($sql);
$datos->execute();

if ($datos == false) {
    echo mysql_error();
} else {
    $array = array();
    $cont = 0;
    while ($rs = $datos->fetch()) {
        $proveedor = new stdClass();
        $proveedor->id = $rs["idProveedor"];
        $proveedor->nombre = $rs["nombre"];
        $proveedor->nombreComprador = $rs["nombreComprador"];
        $proveedor->sagarpa = $rs["idSagarpa"];
        $proveedor->localidad = $rs["localidad"];
        $proveedor->estado = $rs["estado"];
        $proveedor->procedencia = $rs["procedencia"];
        $proveedor->idContrato = $rs["idContrato"];
        $proveedor->idIne = $rs["idIne"];
        $proveedor->idInstalacion = $rs["idInstalacion"];
        $proveedor->idApiario = $rs["idApiario"];
        $proveedor->idCarta = $rs["idCarta"];
        $proveedor->idPagare = $rs["idPagare"];
        $proveedor->activoInactivo = $rs["activoInactivo"];
        $proveedor->zona = $rs["zona"];

        if ($proveedor->idContrato == null) {
            $proveedor->idContrato = "";
        } else {
            $proveedor->idContrato = $str;
        }
        if ($proveedor->idIne == null) {
            $proveedor->idIne = "";
        } else {
            $proveedor->idIne = $str;
        }
        if ($proveedor->idInstalacion == null) {
            $proveedor->idInstalacion = "";
        } else {
            $proveedor->idInstalacion = $str;
        }
        if ($proveedor->idApiario == null) {
            $proveedor->idApiario = "";
        } else {
            $proveedor->idApiario = $str;
        }
        if ($proveedor->idCarta == null) {
            $proveedor->idCarta = "";
        } else {
            $proveedor->idCarta = $str;
        }
        if ($proveedor->idPagare == null) {
            $proveedor->idPagare = "";
        } else {
            $proveedor->idPagare = $str;
        }

        $proveedor->telefonos = array();

        $sqlTelefonos = "SELECT * FROM telefonos WHERE idContacto =:idProveedor";
        $datosTelefonos = $conexion->prepare($sqlTelefonos);
        $datosTelefonos->bindParam(':idProveedor', $proveedor->id);
        $datosTelefonos->execute();


        while ($rsTel = $datosTelefonos->fetch()) {
            $telefonos = new stdClass();
            $telefonos->id = $rsTel["idTelefono"];
            $telefonos->telefono = $rsTel["telefono"];
            $proveedor->telefonos[] = $telefonos;
        }
        $array[$cont] = $proveedor;
        $cont++;
    }
    // echo $sql;
    echo json_encode($array);
}