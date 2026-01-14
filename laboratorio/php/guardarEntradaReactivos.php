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
        $proveedor = $datos->proveedor;
        $conceptos = $datos->conceptos;
        $fechaEntrada = $datos->fecha;
        $totalCompra = $datos->total;
    }

    if (isset($datos->idEntrada)) {
        $sql = "UPDATE reactivosentradaencabezado SET fecha = :fecha, idProveedor = :idProveedor, total = :total WHERE idEntrada = :idEntrada";
        $insertarEncabezado = $con->prepare($sql);
        $insertarEncabezado->bindParam(':fecha', $fechaEntrada);
        $insertarEncabezado->bindParam(':idProveedor', $proveedor->id);
        $insertarEncabezado->bindParam(':total', $totalCompra);
        $insertarEncabezado->bindParam(':idEntrada', $datos->idEntrada);
    } else {
        $sql = "INSERT INTO reactivosentradaencabezado (fecha, idProveedor, total)
                VALUES (:fecha, :idProveedor, :total)";
        $insertarEncabezado = $con->prepare($sql);
        $insertarEncabezado->bindParam(':fecha', $fechaEntrada);
        $insertarEncabezado->bindParam(':idProveedor', $proveedor->id);
        $insertarEncabezado->bindParam(':total', $totalCompra);
    }
    $insertarEncabezado->execute();

    if ($insertarEncabezado == false) {
        throw new Exception($con->errorInfo());
    }
    if (isset($datos->idEntrada)) {
        $idEntrada = $datos->idEntrada;
        $sql = "DELETE FROM reactivosentradadetalle WHERE idEntrada = :idEntrada";
        $deleteConcepts = $con->prepare($sql);
        $deleteConcepts->bindParam(':idEntrada', $idEntrada);
        $deleteConcepts->execute();
        if ($deleteConcepts == false) {
            throw new Exception($con->errorInfo());
        }
    } else {
        $idEntrada = $con->lastInsertid();
    }

    foreach ($conceptos as $concepto) {

        // $idReactivo = $concepto->idReactivo;

        // $idMovimiento = $concepto->idMovimiento;
        // $nombre_movimiento = $concepto->movimiento;

        // // B) SUBCUENTA

        // $idSubcuenta = $concepto->idSubcuenta;
        // $nombre_subcuenta = $concepto->subcuenta;

        // // C) CONCEPTO
        // $idConcepto = $concepto->idConcepto;
        // $nombre_concepto = $concepto->concepto;


        /**27/12/28 se elimino el campo unidad de la consulta para insertar y se agregaron 6 nuevos campos*/
        // $sql = "INSERT INTO reactivosentradadetalle (idEntrada, cantidad, costoUnitario, importe, idMovimiento, movimiento, idSubcuenta, subcuenta, idConcepto, concepto)
        //         VALUES (:idEntrada, :cantidad, :costoUnitario, :importe, :idMovimiento, :movimiento, :idSubcuenta, :subcuenta, :idConcepto, :concepto)";
        $sql = "INSERT INTO reactivosentradadetalle (idEntrada, cantidad, costoUnitario, importe, idReactivo, reactivo)
        VALUES (:idEntrada, :cantidad, :costoUnitario, :importe, :idReactivo, :reactivo)";
        $insertarDetalle = $con->prepare($sql);
        $insertarDetalle->bindParam(':idEntrada', $idEntrada);
        $insertarDetalle->bindParam(':cantidad', $concepto->cantidad);
        $insertarDetalle->bindParam(':costoUnitario', $concepto->costoUnitario);
        $insertarDetalle->bindParam(':importe', $concepto->importe);
        $insertarDetalle->bindParam(':idReactivo', $concepto->idReactivo);
        $insertarDetalle->bindParam(':reactivo', $concepto->reactivo);
        // Nuevos campos
        // $insertarDetalle->bindParam(':idMovimiento', $idMovimiento);
        // $insertarDetalle->bindParam(':movimiento', $nombre_movimiento);
        // $insertarDetalle->bindParam(':idSubcuenta', $idSubcuenta);
        // $insertarDetalle->bindParam(':subcuenta', $nombre_subcuenta);
        // $insertarDetalle->bindParam(':idConcepto', $idConcepto);
        // $insertarDetalle->bindParam(':concepto', $nombre_concepto);

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
