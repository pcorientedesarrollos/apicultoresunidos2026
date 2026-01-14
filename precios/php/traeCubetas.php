<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

if (isset($_GET['miel'])) {
    $miel = $_GET['miel'];
    switch ($miel) {
        case '1':
            $cubetasEncabezado = 'cubetasencabezado';
            $cubetasDetalle = 'cubetasdetalle';
            break;
        case '2':
            $cubetasEncabezado = 'cubetasencabezado_organico';
            $cubetasDetalle = 'cubetasdetalle_organico';
            break;
        case '5':
            $cubetasEncabezado = 'cubetasencabezado_mantequilla';
            $cubetasDetalle = 'cubetasdetalle_mantequilla';
            break;
        case '6':
            $cubetasEncabezado = 'cubetasencabezado_altiplano';
            $cubetasDetalle = 'cubetasdetalle_altiplano';
            break;
        case '7':
            $cubetasEncabezado = 'cubetasencabezado_naranjo';
            $cubetasDetalle = 'cubetasdetalle_naranjo';
            break;
        case '8':
            $cubetasEncabezado = 'cubetasencabezado_aguacate';
            $cubetasDetalle = 'cubetasdetalle_aguacate';
            break;
        case '9':
            $cubetasEncabezado = 'cubetasencabezado_mezquite';
            $cubetasDetalle = 'cubetasdetalle_mezquite';
            break;
    }

    $sql = "SELECT ce.idAlmacen, ce.fecha, rp.nombre, ol.localidad, ce.totalCompra, count(cd.idAlmacen) registros
    FROM $cubetasEncabezado ce 
    LEFT JOIN proveedor rp 
    ON rp.idProveedor = ce.idProveedor
    LEFT JOIN $cubetasDetalle cd 
    ON ce.idAlmacen = cd.idAlmacenEncabezado
    LEFT JOIN direccion rd 
    ON rp.idDireccion = rd.idDireccion
    LEFT JOIN localidades ol ON ol.idlocalidad = rd.idlocalidad
    WHERE ce.folioEntradaTambor = 99999";
    if (isset($_GET['mes'])) {
        $mes = $_GET['mes'];
        $sql .= " AND SUBSTR(ce.fecha FROM 6 FOR 2) = " . $mes;
    }
    $sql .= " GROUP BY ce.idAlmacen
    ORDER BY ce.fecha DESC";
    $datos = $con->prepare($sql);
    $datos->execute();

    if ($datos == false) {
        throw new Exception('No se recibieron parámetros');
    } else {
        $array = array();
        $cont = 0;
        while ($rs = $datos->fetch()) {
            $cubeta = new stdClass();
            $cubeta->fecha = $rs["fecha"];
            $cubeta->id = $rs[0];
            $cubeta->proveedor = $rs["nombre"];
            $cubeta->totalCompra = $rs["totalCompra"];
            $cubeta->localidad = $rs["localidad"];
            $cubeta->registros = $rs["registros"];


            $slqAprobados = "SELECT count(aprobado) FROM $cubetasDetalle 
            WHERE aprobado = 1 and idAlmacenEncabezado = :idAlmacenEncabezado";
            $tamboAprobado = $con->prepare($slqAprobados);
            $tamboAprobado->bindParam(':idAlmacenEncabezado', $rs[0]);
            $tamboAprobado->execute();
            if ($tamboAprobado == false) {
                throw new Exception($con->errorInfo());
            }
            while ($rsAutorizado = $tamboAprobado->fetch()) {
                $cubeta->tamboAprobado = $rsAutorizado[0];
            }

            if ($cubeta->registros == $cubeta->tamboAprobado) {
                $cubeta->estado = '1';
            } else {
                $cubeta->estado = '0';
            }


            $array[$cont] = $cubeta;
            $cont++;
        }
        echo json_encode($array);
    }
}
