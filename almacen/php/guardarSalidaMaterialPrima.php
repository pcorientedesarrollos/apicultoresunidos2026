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
        $datosDetalle = $info[1];
    }

    $sqlEncabezado = "INSERT INTO materiaprimaencabezadosalidas (fecha, idProveedor, idMotivo, cantidadTotal, importeTotal, tipoCliente)
                VALUES (:fecha, :idProveedor, :idMotivo, :cantidadTotal, :importeTotal, :tipoCliente)";
    $insertarEncabezado = $con->prepare($sqlEncabezado);
    $insertarEncabezado->bindParam(':fecha', $datosEncabezado->fecha);
    $insertarEncabezado->bindParam(':idProveedor', $datosEncabezado->idProveedor);
    $insertarEncabezado->bindParam(':idMotivo', $datosEncabezado->idMotivo);
    $insertarEncabezado->bindParam(':cantidadTotal', $datosEncabezado->cantidadTotal);
    $insertarEncabezado->bindParam(':importeTotal', $datosEncabezado->importeTotal);
    $insertarEncabezado->bindParam(':tipoCliente', $datosEncabezado->tipoCliente);

    $insertarEncabezado->execute();
    $idSalidaMateria = $con->lastInsertid();

    foreach ($datosDetalle as $detalle) {
        /**28/12/2018 ya no se va a insertar tipo, se insertan las nuevas cuentas */
        $sql = "INSERT INTO materiaprimadetallesalidas (idSalidaMateria, cantidad, descripcion, precioUnitario, importe, idMovimiento, movimiento, idSubcuenta, subcuenta, idConcepto, concepto) 
                VALUES (:idSalidaMateria, :cantidad, :descripcion, :precioUnitario, :importe, :idMovimiento, :movimiento, :idSubcuenta, :subcuenta, :idConcepto, :concepto)";
        $insertarDetalle = $con->prepare($sql);
        $insertarDetalle->bindParam(':idSalidaMateria', $idSalidaMateria);
        // $insertarDetalle->bindParam(':tipo', $detalle->tipo);
        $insertarDetalle->bindParam(':cantidad', $detalle->cantidad);
        $insertarDetalle->bindParam(':descripcion', $detalle->descripcion);
        $insertarDetalle->bindParam(':precioUnitario', $detalle->precioUnitario);
        $insertarDetalle->bindParam(':importe', $detalle->importe);

        // Nuevas cuetas
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

