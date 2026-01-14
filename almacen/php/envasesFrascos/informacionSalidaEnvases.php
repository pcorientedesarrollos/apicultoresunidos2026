<?php

include_once '../../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

if (isset($_GET['idSalidaEnvases'])) {

    $idSalidaEnvases = $_GET['idSalidaEnvases'];

    $query = "SELECT me.*
    FROM envasesfrascosencabezadosalidas me
    WHERE me.idSalidaEnvases = :idSalidaEnvases ";
    $datos = $con->prepare($query);
    $datos->bindParam(':idSalidaEnvases', $idSalidaEnvases);
    $datos->execute();
    if ($datos == false) {
        throw new Exception($con->errorInfo());
    } else {
        $encabezadoSalida = new stdClass();
        while ($rs = $datos->fetch()) {
            $encabezadoSalida->idSalidaEnvases = $rs["idSalidaEnvases"];
            $encabezadoSalida->fecha = $rs["fecha"];
            $encabezadoSalida->idProveedor = $rs["idProveedor"];
            $encabezadoSalida->cantidadTotal = $rs["cantidadTotal"];
            $encabezadoSalida->importeTotal = $rs["importeTotal"];
            $encabezadoSalida->tipoCliente = $rs["tipoCliente"];
            $encabezadoSalida->salidasDetallePush = array();

            if ($encabezadoSalida->tipoCliente == '1') {
                $sqlNombre = "SELECT nombre AS proveedor FROM proveedor WHERE idProveedor = $encabezadoSalida->idProveedor";
                $datoNombre = $con->prepare($sqlNombre);
                $datoNombre->execute();
                while ($row = $datoNombre->fetch()) {
                    $encabezadoSalida->proveedor = $row["proveedor"];
                }
            } else if ($encabezadoSalida->tipoCliente == '3') {
                $sqlNombre = "SELECT nombreProveedor AS proveedor FROM proveedoresmantto WHERE idProveedorMantto = $encabezadoSalida->idProveedor";
                $datoNombre = $con->prepare($sqlNombre);
                $datoNombre->execute();
                while ($row = $datoNombre->fetch()) {
                    $encabezadoSalida->proveedor = $row["proveedor"];
                }
            } else {
                $encabezadoSalida->proveedor = "";
            }

            $sqlDetalle = "SELECT mpd.idDetalleSalida, mpd.idSalidaEnvases, mpd.tipo, mpd.cantidad, mpd.descripcion, mpd.precioUnitario, mpd.importe, mpd.idMovimiento, mpd.movimiento, mpd.idSubcuenta, mpd.subcuenta, mpd.idConcepto, mpd.concepto
            FROM envasesfrascosdetallesalidas mpd
            WHERE mpd.idSalidaEnvases = :idSalidaEnvases";
            $datosDetalle = $con->prepare($sqlDetalle);
            $datosDetalle->bindParam(':idSalidaEnvases', $idSalidaEnvases);
            $datosDetalle->execute();

            $encabezadoSalida->salidasDetallePush = $datosDetalle->fetchAll(PDO::FETCH_ASSOC);

            $sqlTotales = "SELECT SUM(importe) as sumaImporteTotal,
            (SELECT SUM(cantidad)FROM envasesfrascosdetallesalidas WHERE idSalidaEnvases ='$idSalidaEnvases') as sumaCantidadTotal
            FROM envasesfrascosdetallesalidas WHERE idSalidaEnvases = :idSalidaEnvases";
            $datosTotales = $con->prepare($sqlTotales);
            $datosTotales->bindParam(':idSalidaEnvases', $idSalidaEnvases);
            $datosTotales->execute();
            if ($datosTotales == false) {
                throw new Exception($con->errorInfo());
            } else {
                while ($resTotal = $datosTotales->fetch()) {
                    $encabezadoSalida->sumaCantidadTotal = $resTotal["sumaCantidadTotal"];
                    $encabezadoSalida->sumaImporteTotal = $resTotal["sumaImporteTotal"];
                }
            }
        }
    }
    echo json_encode($encabezadoSalida);
} else if (isset($_GET['idDetalleSalida'])) {

    $sqlDetalle = "SELECT mpd.idDetalleSalida, mpd.idSalidaEnvases, mpd.tipo,
        mpd.cantidad, mpd.descripcion, mpd.precioUnitario, mpd.importe, mpd.idMovimiento, mpd.movimiento, mpd.idSubcuenta, mpd.subcuenta, mpd.idConcepto, mpd.concepto
       FROM envasesfrascosdetallesalidas mpd
       WHERE mpd.idDetalleSalida = :idDetalleSalida";
    $datosDetalle = $con->prepare($sqlDetalle);
    $datosDetalle->bindParam(':idDetalleSalida', $_GET['idDetalleSalida']);
    $datosDetalle->execute();

    $salidasDetallePush = $datosDetalle->fetch(PDO::FETCH_ASSOC);

    echo json_encode($salidasDetallePush);
}
