<?php

include_once '../../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

if (isset($_GET['idEntradaEnvases'])) {

    $idEntradaEnvases = $_GET['idEntradaEnvases'];

    $query = "SELECT me.*
    FROM envasesfrascosencabezadoentradas me
    WHERE me.idEntradaEnvases = :idEntradaEnvases ";
    $datos = $con->prepare($query);
    $datos->bindParam(':idEntradaEnvases', $idEntradaEnvases);
    $datos->execute();
    if ($datos == false) {
        throw new Exception($con->errorInfo());
    } else {
        $encabezadoEntrada = new stdClass();
        while ($rs = $datos->fetch()) {
            $encabezadoEntrada->idEntradaEnvases = $rs["idEntradaEnvases"];
            $encabezadoEntrada->fecha = $rs["fecha"];
            $encabezadoEntrada->idProveedor = $rs["idProveedor"];
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

            $sqlDetalle = "SELECT mpd.idDetalleEntrada, mpd.idEntradaEnvases,
            mpd.cantidad, mpd.descripcion, mpd.precioUnitario, mpd.importe, mpd.idMovimiento, mpd.movimiento, mpd.idSubcuenta, mpd.subcuenta, mpd.idConcepto, mpd.concepto
            FROM envasesfrascosdetalleentradas mpd
            WHERE mpd.idEntradaEnvases = :idEntradaEnvases";
            $datosDetalle = $con->prepare($sqlDetalle);
            $datosDetalle->bindParam(':idEntradaEnvases', $idEntradaEnvases);
            $datosDetalle->execute();

            $encabezadoEntrada->entradasDetallePush = $datosDetalle->fetchAll(PDO::FETCH_ASSOC);

            $sqlTotales = "SELECT SUM(importe) as sumaImporteTotal,
            (SELECT SUM(cantidad) FROM envasesfrascosdetalleentradas WHERE idEntradaEnvases ='$idEntradaEnvases') as sumaCantidadTotal
            FROM envasesfrascosdetalleentradas WHERE idEntradaEnvases = :idEntradaEnvases";
            $datosTotales = $con->prepare($sqlTotales);
            $datosTotales->bindParam(':idEntradaEnvases', $idEntradaEnvases);
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

    $sqlDetalle = "SELECT mpd.idDetalleEntrada, mpd.idEntradaEnvases,
    mpd.cantidad, mpd.descripcion, mpd.precioUnitario, mpd.importe, mpd.idMovimiento, mpd.movimiento, mpd.idSubcuenta, mpd.subcuenta, mpd.idConcepto, mpd.concepto
   FROM envasesfrascosdetalleentradas mpd
   WHERE mpd.idDetalleEntrada = :idDetalleEntrada";
    $datosDetalle = $con->prepare($sqlDetalle);
    $datosDetalle->bindParam(':idDetalleEntrada', $_GET['idDetalleEntrada']);
    $datosDetalle->execute();

    $entradasDetallePush = $datosDetalle->fetch(PDO::FETCH_ASSOC);

    echo json_encode($entradasDetallePush);
}
