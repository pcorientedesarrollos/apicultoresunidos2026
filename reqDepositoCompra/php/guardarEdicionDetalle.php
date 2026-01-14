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

    $con->beginTransaction();

    $sqlInsertarEncabezado = "UPDATE requisicionencabezado SET totalTambores = :totalTambores, totalKilos = :totalKilos, importeTotal = :importeTotal WHERE idRequisicion = :idRequisicion";
    $queryInsertarEncabezado = $con->prepare($sqlInsertarEncabezado);
    $queryInsertarEncabezado->bindParam(':idRequisicion', $info[0]->idRequisicion);
    $queryInsertarEncabezado->bindParam(':totalTambores', $info[0]->totalTambores);
    $queryInsertarEncabezado->bindParam(':totalKilos', $info[0]->totalKilos);
    $queryInsertarEncabezado->bindParam(':importeTotal', $info[0]->importeTotal);
    $queryInsertarEncabezado->execute();
    if (!$queryInsertarEncabezado) {
        throw new Exception($con->errorInfo());
    }

    $sqlInsertaDetalle = "INSERT INTO requisiciondetalle (idRequisicion, idComprador, idProveedor, noTambores, peso, precio, importe, banco, observaciones, idTipoDeMiel, saldoActual)
        VALUES (:idRequisicion, :idComprador, :idProveedor, :noTambores, :peso, :precio, :importe, :banco, :observaciones, :idTipoDeMiel, :saldoActual)";
    $queryInsertaDetalle = $con->prepare($sqlInsertaDetalle);
    $queryInsertaDetalle->bindParam(':idRequisicion', $info[0]->idRequisicion);
    $queryInsertaDetalle->bindParam(':idComprador', $info[1]->idComprador);
    $queryInsertaDetalle->bindParam(':idProveedor', $info[1]->idProveedor);
    $queryInsertaDetalle->bindParam(':noTambores', $info[1]->noTambores);
    $queryInsertaDetalle->bindParam(':peso', $info[1]->peso);
    $queryInsertaDetalle->bindParam(':precio', $info[1]->precio);
    $queryInsertaDetalle->bindParam(':importe', $info[1]->importe);
    $queryInsertaDetalle->bindParam(':banco', $info[1]->banco);
    $queryInsertaDetalle->bindParam(':observaciones', $info[1]->observaciones);
    $queryInsertaDetalle->bindParam(':idTipoDeMiel', $info[1]->idTipoDeMiel);
    $queryInsertaDetalle->bindParam(':saldoActual', $info[1]->saldoDeudor);
    $queryInsertaDetalle->execute();
    if (!$queryInsertaDetalle) {
        throw new Exception($con->errorInfo());
    }

    $con->commit();
    echo json_encode(['error' => false, 'message' => 'Nuevo pedido guardado']);
} catch (Exception $e) {
    $con->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
