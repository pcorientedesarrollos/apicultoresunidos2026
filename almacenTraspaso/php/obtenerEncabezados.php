<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

try {

    $arrayObtenido = file_get_contents('php://input');
    $info = json_decode($arrayObtenido);

    switch ($info[0]) {
        case '1':
            $encabezado = 'almacenencabezadotraspaso';
            $detalle = 'almacentraspaso';
            break;
        case '2':
            $encabezado = 'almacenencabezadotraspaso_organico';
            $detalle = 'almacentraspaso_organico';
            break;
    }

    if (isset($_GET['mes'])) {
        $mes = $_GET['mes'];
    }

    $sql = "SELECT *, count(alm.idAlmacen) registros, sum(alm.neto) kgs
    FROM $encabezado al 
    LEFT JOIN proveedor pr ON pr.idProveedor = al.idProveedor
    LEFT JOIN $detalle alm ON al.idAlmacen = alm.idAlmacenEncabezado
    LEFT JOIN direccion dr ON pr.idDireccion = dr.idDireccion
    LEFT JOIN localidades lo ON lo.idlocalidad = dr.idlocalidad";

    if ($info[2] == '1') {
        if (isset($_GET['mes'])) {
            $sql .= " WHERE al.idAlmacen = alm.idAlmacenEncabezado AND SUBSTR(fecha FROM 6 FOR 2) = " . $mes;
        } else {
            $sql .= " WHERE al.idAlmacen = alm.idAlmacenEncabezado";
        }
    } else if ($info[2] == '2') {
        if (isset($_GET['mes'])) {
            $sql .= " WHERE al.idAlmacen NOT IN (SELECT alm.idAlmacenEncabezado FROM $detalle alm) AND SUBSTR(fecha FROM 6 FOR 2) = " . $mes;
        } else {
            $sql .= " WHERE al.idAlmacen NOT IN (SELECT alm.idAlmacenEncabezado FROM $detalle alm)";
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
        $almacen->folio = $rs["folio"];
        $almacen->totalCompra = $rs["totalCompra"];
        $almacen->localidad = $rs["localidad"];
        $almacen->kgs = $rs['kgs'];

        $slq = "SELECT count(al.autorizado),
        (SELECT count(la.idAlmacen) FROM laboratorio_traspaso la WHERE la.entradaNo = :idAlmacenEncabezado AND la.tipoDeMiel = :miel) as muestras
        FROM $detalle al
        WHERE al.autorizado = 1 and al.idAlmacenEncabezado = :idAlmacenEncabezado";
        $datosAutorizados = $con->prepare($slq);
        $datosAutorizados->bindParam(':idAlmacenEncabezado', $rs[0]);
        $datosAutorizados->bindParam(':miel', $info[0]);
        $datosAutorizados->execute();
        if ($datosAutorizados == false) {
            throw new Exception($con->errorInfo());
        }
        while ($rsAutorizado = $datosAutorizados->fetch()) {
            $almacen->datosAutorizados = $rsAutorizado[0];
            $almacen->muestras = $rsAutorizado[1];
        }

        array_push($array, $almacen);
    }

    echo json_encode(['error' => false, 'resultado' => $array]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
