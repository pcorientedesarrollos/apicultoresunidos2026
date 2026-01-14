<?php

include_once '../../../DAOConeccion/conePDO.php';
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
        $conceptos = $datos->conceptos;
    }
    if (isset($datos->idSolicitudCompra)) {
        $sql = "UPDATE solicitudcompra_encabezado SET fecha = :fecha, departamento = :departamento, solicita = :solicita WHERE idSolicitudCompra = :idSolicitudCompra";
        $insertarEncabezado = $con->prepare($sql);
        $insertarEncabezado->bindParam(':fecha', $datos->fecha);
        $insertarEncabezado->bindParam(':departamento', $datos->departamento);
        $insertarEncabezado->bindParam(':solicita', $datos->solicita);
        $insertarEncabezado->bindParam(':idSolicitudCompra', $datos->idSolicitudCompra);
    } else {
        $sql = "INSERT INTO solicitudcompra_encabezado (fecha, departamento, solicita)
                VALUES (:fecha, :departamento, :solicita)";
        $insertarEncabezado = $con->prepare($sql);
        $insertarEncabezado->bindParam(':fecha', $datos->fecha);
        $insertarEncabezado->bindParam(':departamento', $datos->departamento);
        $insertarEncabezado->bindParam(':solicita', $datos->solicita);
    }
    $insertarEncabezado->execute();

    if ($insertarEncabezado == false) {
        // throw new Exception($con->errorInfo());
        // https://www.php.net/manual/es/class.errorexception.php
        throw new ErrorException($mensaje, 0, $severidad, $fichero, $línea);
    }
    if (isset($datos->idSolicitudCompra)) {
        $idSolicitudCompra = $datos->idSolicitudCompra;
        $sql = "DELETE FROM solicitudcompra_conceptos WHERE idSolicitudCompra = :idSolicitudCompra";
        $deleteConcepts = $con->prepare($sql);
        $deleteConcepts->bindParam(':idSolicitudCompra', $idSolicitudCompra);
        $deleteConcepts->execute();
        if ($deleteConcepts == false) {
            // throw new Exception($con->errorInfo());
            throw new ErrorException($mensaje, 0, $severidad, $fichero, $línea);
        }
    } else {
        $idSolicitudCompra = $con->lastInsertid();
    }

    foreach ($conceptos as $concepto) {

        // Ya no vamos a insertar unidad
        // movimiento y concepto son opcionales en la vista, subcuenta si viene siempre
        // hay que sacar el nombre de todos 

        // A) MOVIMIENTO
        $idMovimiento = $concepto->idMovimiento;
        $nombre_movimiento = $concepto->movimiento;

        // B) SUBCUENTA

        $idSubcuenta = $concepto->idSubcuenta;
        $nombre_subcuenta = $concepto->subcuenta;

        // C) CONCEPTO
        $idConcepto = $concepto->idConcepto;
        $nombre_concepto = $concepto->concepto;

        $sql = "INSERT INTO solicitudcompra_conceptos (idSolicitudCompra, cantidad, descripcion, idMovimiento, movimiento, idSubcuenta, subcuenta, idConcepto, concepto, idProveedor, costo, estado)
                VALUES (:idSolicitudCompra, :cantidad, :descripcion, :idMovimiento, :movimiento, :idSubcuenta, :subcuenta, :idConcepto, :concepto, :idProveedor, :costo, '0')";
        $insertarDetalle = $con->prepare($sql);
        $insertarDetalle->bindParam(':idSolicitudCompra', $idSolicitudCompra);
        $insertarDetalle->bindParam(':cantidad', $concepto->cantidad);
        $insertarDetalle->bindParam(':descripcion', $concepto->descripcion);
        $insertarDetalle->bindParam(':idMovimiento', $idMovimiento);
        $insertarDetalle->bindParam(':movimiento', $nombre_movimiento);
        $insertarDetalle->bindParam(':idSubcuenta', $idSubcuenta);
        $insertarDetalle->bindParam(':subcuenta', $nombre_subcuenta);
        $insertarDetalle->bindParam(':idConcepto', $idConcepto);
        $insertarDetalle->bindParam(':concepto', $nombre_concepto);
        $insertarDetalle->bindParam(':idProveedor', $concepto->idProveedor);
        $insertarDetalle->bindParam(':costo', $concepto->costo);
        $insertarDetalle->execute();
        if ($insertarDetalle == false) {
            throw new ErrorException($mensaje, 0, $severidad, $fichero, $línea);
            // throw new Exception($con->errorInfo());            
        }
    }

    $con->commit();
    echo json_encode(['error' => false, 'message' => 'Se han guardado los datos', 'swal' => 'success']);
} catch (Exception $e) {
    $con->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage() . '. Line ' . $e->getLine(), 'swal' => 'error']);
    exit();
}
