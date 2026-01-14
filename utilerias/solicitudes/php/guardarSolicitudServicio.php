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
    if (isset($datos->idSolicitudServicio)) {
        $sql = "UPDATE solicitudservicio_encabezado SET fecha = :fecha, departamento = :departamento, solicita = :solicita WHERE idSolicitudServicio = :idSolicitudServicio";
        $insertarEncabezado = $con->prepare($sql);
        $insertarEncabezado->bindParam(':fecha', $datos->fecha);
        $insertarEncabezado->bindParam(':departamento', $datos->departamento);
        $insertarEncabezado->bindParam(':solicita', $datos->solicita);
        $insertarEncabezado->bindParam(':idSolicitudServicio', $datos->idSolicitudServicio);
    } else {
        $sql = "INSERT INTO solicitudservicio_encabezado (fecha, departamento, solicita)
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
    if (isset($datos->idSolicitudServicio)) {
        $idSolicitudServicio = $datos->idSolicitudServicio;
        $sql = "DELETE FROM solicitudservicio_conceptos WHERE idSolicitudServicio = :idSolicitudServicio";
        $deleteConcepts = $con->prepare($sql);
        $deleteConcepts->bindParam(':idSolicitudServicio', $idSolicitudServicio);
        $deleteConcepts->execute();
        if ($deleteConcepts == false) {
            // throw new Exception($con->errorInfo());
            throw new ErrorException($mensaje, 0, $severidad, $fichero, $línea);
        }
    } else {
        $idSolicitudServicio = $con->lastInsertid();
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

        // C) CONCEPTO
        $idActivo = $concepto->idActivo;
        $nombre_activo = $concepto->activo;

        $sql = "INSERT INTO solicitudservicio_conceptos (idSolicitudServicio, cantidad, descripcion, idMovimiento, movimiento, idSubcuenta, subcuenta, idConcepto, concepto, idActivo, activo, idProveedor, costo, estado)
                VALUES (:idSolicitudServicio, :cantidad, :descripcion, :idMovimiento, :movimiento, :idSubcuenta, :subcuenta, :idConcepto, :concepto, :idActivo, :activo, :idProveedor, :costo, '0')";
        $insertarDetalle = $con->prepare($sql);
        $insertarDetalle->bindParam(':idSolicitudServicio', $idSolicitudServicio);
        $insertarDetalle->bindParam(':cantidad', $concepto->cantidad);
        $insertarDetalle->bindParam(':descripcion', $concepto->descripcion);
        $insertarDetalle->bindParam(':idMovimiento', $idMovimiento);
        $insertarDetalle->bindParam(':movimiento', $nombre_movimiento);
        $insertarDetalle->bindParam(':idSubcuenta', $idSubcuenta);
        $insertarDetalle->bindParam(':subcuenta', $nombre_subcuenta);
        $insertarDetalle->bindParam(':idConcepto', $idConcepto);
        $insertarDetalle->bindParam(':concepto', $nombre_concepto);
        $insertarDetalle->bindParam(':idActivo', $idActivo);
        $insertarDetalle->bindParam(':activo', $nombre_activo);
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
