<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

if (isset($_GET['idEntradaMateria'])) {

    $idEntradaMateria = $_GET['idEntradaMateria'];
    if (isset($_GET['tipoDeMiel'])) {
        switch ($_GET['tipoDeMiel']) {
            case '1':
                $encabezado = 'materiaprimaencabezadoentradas';
                $tbl_detalle = 'materiaprimadetalleentradas';
                break;
            case '2':
                $encabezado = 'materiaprimaencabezadoentradas_organico';
                $tbl_detalle = 'materiaprimadetalleentradas_organico';
                break;
            case '7':
                $encabezado = 'materiaprimaencabezadoentradas_naranjo';
                $tbl_detalle = 'materiaprimadetalleentradas_naranjo';
                break;
            default:
                throw new Exception('Tipo de miel inválido');
                break;
        }
    }

    $query = "SELECT me.*, c.condicionSalida AS condicion
    FROM $encabezado me
    LEFT JOIN condicionesdesalidas c ON c.idCondicionSalida = me.idMotivo
    WHERE me.idEntradaMateria = :idEntradaMateria ";
    $datos = $con->prepare($query);
    $datos->bindParam(':idEntradaMateria', $idEntradaMateria);
    $datos->execute();
    if ($datos == false) {
        throw new Exception($con->errorInfo());
    } else {
        $encabezadoEntrada = new stdClass();
        while ($rs = $datos->fetch()) {
            $encabezadoEntrada->idEntradaMateria = $rs["idEntradaMateria"];
            $encabezadoEntrada->fecha = $rs["fecha"];
            $encabezadoEntrada->idProveedor = $rs["idProveedor"];
            $encabezadoEntrada->condicion = $rs["condicion"];
            $encabezadoEntrada->idMotivo = $rs["idMotivo"];
            $encabezadoEntrada->cantidadTotal = $rs["cantidadTotal"];
            $encabezadoEntrada->importeTotal = $rs["importeTotal"];
            $encabezadoEntrada->tipoCliente = $rs["tipoCliente"];
            $encabezadoEntrada->entradasDetallePush = array();

            if ($encabezadoEntrada->tipoCliente == '1') {
                $sqlNombre = "SELECT nombre AS proveedor FROM proveedor WHERE idProveedor = $encabezadoEntrada->idProveedor";
                $datoNombre = $con->prepare($sqlNombre);
                $datoNombre->execute();
                while ($row = $datoNombre->fetch()) {
                    $encabezadoEntrada->proveedor = $row["proveedor"];
                }
            } else if ($encabezadoEntrada->tipoCliente == '3') {
                $sqlNombre = "SELECT nombreProveedor AS proveedor FROM proveedoresmantto WHERE idProveedorMantto = $encabezadoEntrada->idProveedor";
                $datoNombre = $con->prepare($sqlNombre);
                $datoNombre->execute();
                while ($row = $datoNombre->fetch()) {
                    $encabezadoEntrada->proveedor = $row["proveedor"];
                }
            }
            else if ($encabezadoEntrada->tipoCliente == '6') {
                $sqlNombre = "SELECT nombre AS proveedor FROM clientes WHERE idCliente = $encabezadoEntrada->idProveedor";
                $datoNombre = $con->prepare($sqlNombre);
                $datoNombre->execute();
                while ($row = $datoNombre->fetch()) {
                    $encabezadoEntrada->proveedor = $row["proveedor"];
                }
            }

            $sqlDetalle = "SELECT mpd.idDetalleEntrada, mpd.idEntradaMateria, mpd.tipo,
             mpd.cantidad, mpd.descripcion, mpd.precioUnitario, mpd.importe, mpd.idMovimiento, mpd.movimiento, mpd.idSubcuenta, mpd.subcuenta, mpd.idConcepto, mpd.concepto
            FROM $tbl_detalle mpd
            WHERE mpd.idEntradaMateria = :idEntradaMateria";
            $datosDetalle = $con->prepare($sqlDetalle);
            $datosDetalle->bindParam(':idEntradaMateria', $idEntradaMateria);
            $datosDetalle->execute();

            $encabezadoEntrada->entradasDetallePush = $datosDetalle->fetchAll(PDO::FETCH_ASSOC);

            $sqlTotales = "SELECT SUM(importe) as sumaImporteTotal,
            (SELECT SUM(cantidad) FROM $tbl_detalle WHERE idEntradaMateria ='$idEntradaMateria') as sumaCantidadTotal
            FROM $tbl_detalle WHERE idEntradaMateria = :idEntradaMateria";
            $datosTotales = $con->prepare($sqlTotales);
            $datosTotales->bindParam(':idEntradaMateria', $idEntradaMateria);
            $datosTotales->execute();
            if ($datosTotales == false) {
                throw new Exception($con->errorInfo());
            } else {
                while ($resTotal = $datosTotales->fetch()) {
                    $encabezadoEntrada->sumaCantidadTotal = $resTotal["sumaCantidadTotal"];
                    $encabezadoEntrada->sumaImporteTotal = $resTotal["sumaImporteTotal"];
                }
            }
        }
    }
    echo json_encode($encabezadoEntrada);
} else if (isset($_GET['idDetalleEntrada'])) {

    if (isset($_GET['tipoDeMiel'])) {
        switch ($_GET['tipoDeMiel']) {
            case '1':
                $encabezado = 'materiaprimaencabezadoentradas';
                $tbl_detalle = 'materiaprimadetalleentradas';
                break;
            case '2':
                $encabezado = 'materiaprimaencabezadoentradas_organico';
                $tbl_detalle = 'materiaprimadetalleentradas_organico';
                break;
            case '7':
                $encabezado = 'materiaprimaencabezadoentradas_naranjo';
                $tbl_detalle = 'materiaprimadetalleentradas_naranjo';
                break;
            default:
                throw new Exception('Tipo de miel inválido');
                break;
        }
    }

    $sqlDetalle = "SELECT mpd.idDetalleEntrada, mpd.idEntradaMateria, mpd.tipo,
    mpd.cantidad, mpd.descripcion, mpd.precioUnitario, mpd.importe, mpd.idMovimiento, mpd.movimiento, mpd.idSubcuenta, mpd.subcuenta, mpd.idConcepto, mpd.concepto
   FROM $tbl_detalle mpd
   WHERE mpd.idDetalleEntrada = :idDetalleEntrada";
    $datosDetalle = $con->prepare($sqlDetalle);
    $datosDetalle->bindParam(':idDetalleEntrada', $_GET['idDetalleEntrada']);
    $datosDetalle->execute();

    $entradasDetallePush = $datosDetalle->fetch(PDO::FETCH_ASSOC);

    echo json_encode($entradasDetallePush);
}
