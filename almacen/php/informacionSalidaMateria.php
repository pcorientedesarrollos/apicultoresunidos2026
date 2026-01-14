<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

if (isset($_GET['idSalidaMateria'])) {

    $idSalidaMateria = $_GET['idSalidaMateria'];

    if (isset($_GET['tipoDeMiel'])) {
        switch ($_GET['tipoDeMiel']) {
            case '1':
                $encabezado = 'materiaprimaencabezadosalidas';
                $tbl_detalle = 'materiaprimadetallesalidas';
                break;
            case '2':
                $encabezado = 'materiaprimaencabezadosalidas_organico';
                $tbl_detalle = 'materiaprimadetallesalidas_organico';
                break;
            case '7':
                $encabezado = 'materiaprimaencabezadosalidas_naranjo';
                $tbl_detalle = 'materiaprimadetallesalidas_naranjo';
                break;
            default:
                throw new Exception('Tipo de miel inválido');
                break;
        }
    }

    $query = "SELECT me.*, c.condicionSalida AS condicion
    FROM $encabezado me
    LEFT JOIN condicionesdesalidas c ON c.idCondicionSalida = me.idMotivo
    WHERE me.idSalidaMateria = :idSalidaMateria ";
    $datos = $con->prepare($query);
    $datos->bindParam(':idSalidaMateria', $idSalidaMateria);
    $datos->execute();
    if ($datos == false) {
        throw new Exception($con->errorInfo());
    } else {
        $encabezadoSalida = new stdClass();
        while ($rs = $datos->fetch()) {
            $encabezadoSalida->idSalidaMateria = $rs["idSalidaMateria"];
            $encabezadoSalida->fecha = $rs["fecha"];
            $encabezadoSalida->idProveedor = $rs["idProveedor"];
            $encabezadoSalida->cantidadTotal = $rs["cantidadTotal"];
            $encabezadoSalida->importeTotal = $rs["importeTotal"];
            $encabezadoSalida->idMotivo = $rs["idMotivo"];
            $encabezadoSalida->condicion = $rs["condicion"];
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
            } 
            else if ($encabezadoSalida->tipoCliente == '6') {
                $sqlNombre = "SELECT nombre AS proveedor FROM clientes WHERE idCliente = $encabezadoSalida->idProveedor";
                $datoNombre = $con->prepare($sqlNombre);
                $datoNombre->execute();
                while ($row = $datoNombre->fetch()) {
                    $encabezadoSalida->proveedor = $row["proveedor"];
                }
            }else {
                $encabezadoSalida->proveedor = "";
            }

            $sqlDetalle = "SELECT mpd.idDetalleSalida, mpd.idSalidaMateria, mpd.tipo, mpd.cantidad, mpd.descripcion, mpd.precioUnitario, mpd.importe, mpd.idMovimiento, mpd.movimiento, mpd.idSubcuenta, mpd.subcuenta, mpd.idConcepto, mpd.concepto
            FROM $tbl_detalle mpd
            WHERE mpd.idSalidaMateria = :idSalidaMateria";
            $datosDetalle = $con->prepare($sqlDetalle);
            $datosDetalle->bindParam(':idSalidaMateria', $idSalidaMateria);
            $datosDetalle->execute();

            $encabezadoSalida->salidasDetallePush = $datosDetalle->fetchAll(PDO::FETCH_ASSOC);

            $sqlTotales = "SELECT SUM(importe) as sumaImporteTotal,
            (SELECT SUM(cantidad)FROM $tbl_detalle WHERE idSalidaMateria ='$idSalidaMateria') as sumaCantidadTotal
            FROM $tbl_detalle WHERE idSalidaMateria = :idSalidaMateria";
            $datosTotales = $con->prepare($sqlTotales);
            $datosTotales->bindParam(':idSalidaMateria', $idSalidaMateria);
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


    if (isset($_GET['tipoDeMiel'])) {
        switch ($_GET['tipoDeMiel']) {
            case '1':
                $encabezado = 'materiaprimaencabezadosalidas';
                $tbl_detalle = 'materiaprimadetallesalidas';
                break;
            case '2':
                $encabezado = 'materiaprimaencabezadosalidas_organico';
                $tbl_detalle = 'materiaprimadetallesalidas_organico';
                break;
            case '7':
                $encabezado = 'materiaprimaencabezadosalidas_naranjo';
                $tbl_detalle = 'materiaprimadetallesalidas_naranjo';
                break;
            default:
                throw new Exception('Tipo de miel inválido');
                break;
        }
    }

    $sqlDetalle = "SELECT mpd.idDetalleSalida, mpd.idSalidaMateria, mpd.tipo,
        mpd.cantidad, mpd.descripcion, mpd.precioUnitario, mpd.importe, mpd.idMovimiento, mpd.movimiento, mpd.idSubcuenta, mpd.subcuenta, mpd.idConcepto, mpd.concepto
       FROM $tbl_detalle mpd
       WHERE mpd.idDetalleSalida = :idDetalleSalida";
    $datosDetalle = $con->prepare($sqlDetalle);
    $datosDetalle->bindParam(':idDetalleSalida', $_GET['idDetalleSalida']);
    $datosDetalle->execute();

    $salidasDetallePush = $datosDetalle->fetch(PDO::FETCH_ASSOC);

    echo json_encode($salidasDetallePush);
}
