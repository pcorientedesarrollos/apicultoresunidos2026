<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$json = file_get_contents("php://input");

try {
    $con->beginTransaction();
    if (!$json) {
        throw new Exception('No se recibieron los parámetros');
    } else {
        $datos = json_decode($json);
        $info = $datos->valor;
        $datosEncabezado = $info[0];
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
    }

    $sqlEncabezado = "INSERT INTO $encabezado (fecha, idProveedor, idMotivo, cantidadTotal, importeTotal, tipoCliente)
                VALUES (:fecha, :idProveedor, :idMotivo, :cantidadTotal, :importeTotal, :tipoCliente)";
    $insertarEncabezado = $con->prepare($sqlEncabezado);
    $insertarEncabezado->bindParam(':fecha', $info[0]->fecha);
    $insertarEncabezado->bindParam(':idProveedor', $info[0]->idProveedor);
    $insertarEncabezado->bindParam(':idMotivo', $info[0]->idMotivo);
    $insertarEncabezado->bindParam(':cantidadTotal', $info[0]->cantidadTotal);
    $insertarEncabezado->bindParam(':importeTotal', $info[0]->importeTotal);
    $insertarEncabezado->bindParam(':tipoCliente', $info[0]->tipoCliente);

    $insertarEncabezado->execute();
    $idEntradaMP = $con->lastInsertid();

    foreach ($info[1] as $detalle) {
        $sql = "INSERT INTO $tbl_detalle (idEntradaMateria, cantidad, descripcion, precioUnitario, importe, idMovimiento, movimiento, idSubcuenta, subcuenta, idConcepto, concepto) 
                VALUES (:idEntradaMateria, :cantidad, :descripcion, :precioUnitario, :importe, :idMovimiento, :movimiento, :idSubcuenta, :subcuenta, :idConcepto, :concepto)";
        $insertarDetalle = $con->prepare($sql);
        $insertarDetalle->bindParam(':idEntradaMateria', $idEntradaMP);
        // $insertarDetalle->bindParam(':tipo', $detalle->tipo);
        $insertarDetalle->bindParam(':cantidad', $detalle->cantidad);
        $insertarDetalle->bindParam(':descripcion', $detalle->descripcion);
        $insertarDetalle->bindParam(':precioUnitario', $detalle->precioUnitario);
        $insertarDetalle->bindParam(':importe', $detalle->importe);

        // nuevas cuentas!
        $insertarDetalle->bindParam(':idMovimiento', $detalle->idMovimiento);
        $insertarDetalle->bindParam(':movimiento', $detalle->movimiento);
        $insertarDetalle->bindParam(':idSubcuenta', $detalle->idSubcuenta);
        $insertarDetalle->bindParam(':subcuenta', $detalle->subcuenta);
        $insertarDetalle->bindParam(':idConcepto', $detalle->idConcepto);
        $insertarDetalle->bindParam(':concepto', $detalle->concepto);

        $insertarDetalle->execute();
        if ($insertarDetalle == false) {
            throw new Exception($con->errorInfo());
        }
    }

    $con->commit();
    echo json_encode(['error' => false, 'message' => 'Se han guardado los datos', 'swal' => 'success']);
} catch (Exception $e) {
    $con->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage() . '. Line ' . $e->getLine(), 'swal' => 'error']);
    exit();
}
