<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

try {

    $arrayObtenido = file_get_contents('php://input');
    $info = json_decode($arrayObtenido);

    switch ($info[0]) {
        case '1':
            $almacen_tabla = 'almacen';
            $almacenencabezado_tabla = 'almacenencabezado';
            break;
        case '2':
            $almacen_tabla = 'almacen_organico';
            $almacenencabezado_tabla = 'almacenencabezado_organico';
            break;
        case '5':
            $almacen_tabla = 'almacen_mantequilla';
            $almacenencabezado_tabla = 'almacenencabezado_mantequilla';
            break;
        case '6':
            $almacen_tabla = 'almacen_altiplano';
            $almacenencabezado_tabla = 'almacenencabezado_altiplano';
            break;
        case '7':
            $almacen_tabla = 'almacen_naranjo';
            $almacenencabezado_tabla = 'almacenencabezado_naranjo';
            break;
        case '8':
            $almacen_tabla = 'almacen_aguacate';
            $almacenencabezado_tabla = 'almacenencabezado_aguacate';
            break;
        case '9':
            $almacen_tabla = 'almacen_mezquite';
            $almacenencabezado_tabla = 'almacenencabezado_mezquite';
            break;
    }

    if (isset($_GET['mes'])) {
        $mes = $_GET['mes'];
    }

    $sql = "SELECT *, count(alm.idAlmacen) registros, sum(alm.neto) kgs, SUM(alm.diferencia) AS totalKgDiferencia
    FROM $almacenencabezado_tabla al 
    LEFT JOIN proveedor pr ON pr.idProveedor = al.idProveedor
    LEFT JOIN $almacen_tabla alm ON al.idAlmacen = alm.idAlmacenEncabezado
    LEFT JOIN direccion dr ON pr.idDireccion = dr.idDireccion
    LEFT JOIN localidades lo ON lo.idlocalidad = dr.idlocalidad
    LEFT JOIN archivospdf pdf ON pdf.idAlmacen = alm.idAlmacenEncabezado
    LEFT JOIN archivospdfpagos pago ON pago.idAlmacen = alm.idAlmacenEncabezado";

    if ($info[2] == '1') {
        if (isset($_GET['mes'])) {
            $sql .= " WHERE al.idAlmacen = alm.idAlmacenEncabezado AND SUBSTR(fecha FROM 6 FOR 2) = " . $mes;
        } else {
            $sql .= " WHERE al.idAlmacen = alm.idAlmacenEncabezado";
        }
    } else if ($info[2] == '2') {
        if (isset($_GET['mes'])) {
            $sql .= " WHERE al.idAlmacen NOT IN (SELECT alm.idAlmacenEncabezado FROM $almacen_tabla alm) AND SUBSTR(fecha FROM 6 FOR 2) = " . $mes;
        } else {
            $sql .= " WHERE al.idAlmacen NOT IN (SELECT alm.idAlmacenEncabezado FROM $almacen_tabla alm)";
        }
    } else if (isset($_GET['mes'])) {
        $sql .= " WHERE SUBSTR(fecha FROM 6 FOR 2) = " . $mes;
    }

    $sql .= " GROUP BY al.idAlmacen ORDER BY al.idAlmacen DESC";
    $datos = $con->prepare($sql);
    $datos->execute();

    if ($datos == false) {
        throw new Exception($con->errorInfo());
    }

    $array = array();
    while ($rs = $datos->fetch()) {
        $almacen = new stdClass();
        $almacen->fecha = $rs["fecha"];
        $almacen->id = $rs[0];
        $almacen->proveedor = $rs["nombre"];
        $almacen->registros = $rs["registros"];
        $almacen->archivoRelacion = $rs["archivoRelacion"];
        $almacen->archivoPago = $rs["archivoPago"];
        $almacen->folio = $rs["folio"];
        $almacen->totalCompra = $rs["totalCompra"];
        $almacen->localidad = $rs["localidad"];
        $almacen->kgs = $rs['kgs'];
        $almacen->totalKgDiferencia = $rs['totalKgDiferencia'];

        $slq = "SELECT count(autorizado) FROM $almacen_tabla 
            WHERE autorizado = 1 and idAlmacenEncabezado = :idAlmacenEncabezado";
        $datosAutorizados = $con->prepare($slq);
        $datosAutorizados->bindParam(':idAlmacenEncabezado', $rs[0]);
        $datosAutorizados->execute();
        if ($datosAutorizados == false) {
            throw new Exception($con->errorInfo());
        }
        while ($rsAutorizado = $datosAutorizados->fetch()) {
            $almacen->datosAutorizados = $rsAutorizado[0];
        }

        $slqAprobados = "SELECT count(aprobado) FROM $almacen_tabla 
            WHERE aprobado = 1 and idAlmacenEncabezado = :idAlmacenEncabezado";
        $tamboAprobado = $con->prepare($slqAprobados);
        $tamboAprobado->bindParam(':idAlmacenEncabezado', $rs[0]);
        $tamboAprobado->execute();
        if ($tamboAprobado == false) {
            throw new Exception($con->errorInfo());
        }
        while ($rsAutorizado = $tamboAprobado->fetch()) {
            $almacen->tamboAprobado = $rsAutorizado[0];
        }

        if ($almacen->registros == $almacen->tamboAprobado) {
            $almacen->estado = '1';
        } else {
            $almacen->estado = '0';
        }

        array_push($array, $almacen);
    }

    echo json_encode(['error' => false, 'resultado' => $array]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
