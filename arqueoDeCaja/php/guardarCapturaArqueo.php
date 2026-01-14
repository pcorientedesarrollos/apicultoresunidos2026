<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$postdata = file_get_contents('php://input');

try {

    if (!$postdata) {
        throw new Exception('No se recibieron datos');
    } else {
        $datosGuardar = json_decode($postdata);
    }

    // iniciamos una transaccion

    $con->beginTransaction();

    // Primero insertar el encabezado para obtener el ID
    if (isset($datosGuardar[0]->idSemana)) {
        $queryInsertaEncabezado = $con->prepare("UPDATE arqueo_semanas SET nombre = :nombre, fechaInicial = :fechaInicial, fechaFinal = :fechaFinal WHERE idSemana = :idSemana");
        $queryInsertaEncabezado->bindParam(':idSemana', $datosGuardar[0]->idSemana);
    } else {
        $queryInsertaEncabezado = $con->prepare("INSERT INTO arqueo_semanas (nombre, fechaInicial, fechaFinal) VALUES (:nombre, :fechaInicial, :fechaFinal)");
    }
    $queryInsertaEncabezado->bindParam(':nombre', $datosGuardar[0]->nombre);
    $queryInsertaEncabezado->bindParam(':fechaInicial', $datosGuardar[0]->fechaInicial);
    $queryInsertaEncabezado->bindParam(':fechaFinal', $datosGuardar[0]->fechaFinal);
    $queryInsertaEncabezado->execute();
    if (!$queryInsertaEncabezado) {
        throw new Exception($con->errorInfo());
    }

    // Si se inserta podemos obtener el id
    if (isset($datosGuardar[0]->idSemana)) {
        $idSemana = $datosGuardar[0]->idSemana;
    } else {
        $idSemana = $con->lastInsertId();
    }

    foreach ($datosGuardar[1] as $gastos) {
        if (isset($gastos->idGasto) && $gastos->idGasto > 0 ) {
            $queryInsertaGasto = $con->prepare("UPDATE arqueo_gastos SET concepto = :concepto, cantidad = :cantidad WHERE idGasto = :idGasto");
            $queryInsertaGasto->bindParam(':idGasto', $gastos->idGasto);
        } else {
            $queryInsertaGasto = $con->prepare("INSERT INTO arqueo_gastos (concepto, cantidad, idSemana) VALUES (:concepto, :cantidad, :idSemana)");
            $queryInsertaGasto->bindParam(':idSemana', $idSemana);
        }
        $queryInsertaGasto->bindParam(':concepto', $gastos->concepto);
        $queryInsertaGasto->bindParam(':cantidad', $gastos->cantidad);
        $queryInsertaGasto->execute();
        if (!$queryInsertaGasto) {
            throw new Exception($con->errorInfo());
        }
    }

    foreach ($datosGuardar[2] as $billetes) {
        if (isset($billetes->idBillete) && $billetes->idBillete > 0) {
            $queryInsertaBilletes = $con->prepare("UPDATE arqueo_billetesmonedas SET billeteMoneda = :billeteMoneda, numero = :numero, total = :total WHERE idBillete = :idBillete");
            $queryInsertaBilletes->bindParam(':idBillete', $billetes->idBillete);
        } else {
            $queryInsertaBilletes = $con->prepare("INSERT INTO arqueo_billetesmonedas (billeteMoneda, numero, total, idSemana) VALUES (:billeteMoneda, :numero, :total, :idSemana)");
            $queryInsertaBilletes->bindParam(':idSemana', $idSemana);
        }
        $queryInsertaBilletes->bindParam(':billeteMoneda', $billetes->billeteMoneda);
        $queryInsertaBilletes->bindParam(':numero', $billetes->numero);
        $queryInsertaBilletes->bindParam(':total', $billetes->total);
        $queryInsertaBilletes->execute();
        if (!$queryInsertaBilletes) {
            throw new Exception($con->errorInfo());
        }
    }

    // Si llega a este punto, es que no hubo algun error
    $con->commit();
    echo json_encode(['error' => false, 'message' => 'Se ha guardado un nuevo registro']);
} catch (Exception $e) {
    $con->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
