<?php
include_once '../../mensajes/Mensajes.php';
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$json = file_get_contents("php://input");

try {
    if (!$json) {
        throw new Exception($con->errorInfo());
    } else {
        $datos = json_decode($json);
        $info = $datos->valor;
    }

    $mensajes = new Mensajes();
    $datosEncabezado = $info[0];
    $fecha = date("Y-m-d");

    $con->beginTransaction();

    $sqlInsertarEncabezado = "INSERT INTO requisicionencabezado (fechaRequisicion, fechaImpresion, totalTambores, totalKilos ,importeTotal)
    VALUES (:fechaRequisicion, :fechaImpresion, :totalTambores, :totalKilos, :importeTotal)";
    $queryInsertarEncabezado = $con->prepare($sqlInsertarEncabezado);
    $queryInsertarEncabezado->bindParam(':fechaRequisicion', $info[0]->fechaRequisicion);
    $queryInsertarEncabezado->bindParam(':fechaImpresion', $fecha);
    $queryInsertarEncabezado->bindParam(':totalTambores', $info[0]->totalTambores);
    $queryInsertarEncabezado->bindParam(':totalKilos', $info[0]->totalKilos);
    $queryInsertarEncabezado->bindParam(':importeTotal', $info[0]->importeTotal);
    $queryInsertarEncabezado->execute();
    if (!$queryInsertarEncabezado) {
        throw new Exception($con->errorInfo());
    }

    // obtener el id insertado

    $idRequisicion = $con->lastInsertId();

    foreach ($info[1] as $detalle) {
        $sqlInsertaDetalle = "INSERT INTO requisiciondetalle (idRequisicion, idComprador, idProveedor, noTambores, peso, precio, importe, banco, observaciones, idTipoDeMiel, saldoActual)
        VALUES (:idRequisicion, :idComprador, :idProveedor, :noTambores, :peso, :precio, :importe, :banco, :observaciones, :idTipoDeMiel, :saldoActual)";
        $queryInsertaDetalle = $con->prepare($sqlInsertaDetalle);
        $queryInsertaDetalle->bindParam(':idRequisicion', $idRequisicion);
        $queryInsertaDetalle->bindParam(':idComprador', $detalle->idComprador);
        $queryInsertaDetalle->bindParam(':idProveedor', $detalle->idProveedor);
        $queryInsertaDetalle->bindParam(':noTambores', $detalle->noTambores);
        $queryInsertaDetalle->bindParam(':peso', $detalle->peso);
        $queryInsertaDetalle->bindParam(':precio', $detalle->precio);
        $queryInsertaDetalle->bindParam(':importe', $detalle->importe);
        $queryInsertaDetalle->bindParam(':banco', $detalle->banco);
        $queryInsertaDetalle->bindParam(':observaciones', $detalle->observaciones);
        $queryInsertaDetalle->bindParam(':idTipoDeMiel', $detalle->idTipoDeMiel);
        $queryInsertaDetalle->bindParam(':saldoActual', $detalle->saldoDeudor);
        $queryInsertaDetalle->execute();
        if (!$queryInsertaDetalle) {
            throw new Exception($con->errorInfo());
        }
    }

    $con->commit();
    echo json_encode(['error' => false, 'message' => 'Nuevo pedido guardado']);
} catch (Exception $e) {
    $con->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
