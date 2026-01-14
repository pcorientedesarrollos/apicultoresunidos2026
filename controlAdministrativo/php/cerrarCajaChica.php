<?php

$postdata = file_get_contents('php://input');
date_default_timezone_set('America/Merida');
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

if ($postdata) {
    $idMes = json_decode($postdata);
    $seleccionarMovimientosMensual = $con->prepare("SELECT tipo, total FROM cajachica WHERE idMEs = :idMes");
    $seleccionarMovimientosMensual->bindParam(':idMes', $idMes);
    $seleccionarMovimientosMensual->execute();

    $saldoDelMesACerrar = 0;

    foreach ($seleccionarMovimientosMensual->fetchAll() as $movimientoDelMes) {
        $saldoDelMesACerrar = $movimientoDelMes['tipo'] == 0
        ? $saldoDelMesACerrar += $movimientoDelMes['total']
        :$saldoDelMesACerrar -= $movimientoDelMes['total'];
    }

    try {
        $con->beginTransaction();
        $ultimoSaldo = $saldoDelMesACerrar;
        $tipo = 0;
        $horaDeRegistro = date("H:i:s");
        $fecha = date('Y-m-d');
        if ($idMes == 12) {
            $nuevoMes = 1;
        } else {
            $nuevoMes = $idMes + 1;
        }
        $insertarSaldoInicialCajaChica = $con->prepare("INSERT INTO cajachica (fecha, idMes, hora, tipo, total) VALUES (:fecha, :idMes, :hora, :tipo, :total)");
        $insertarSaldoInicialCajaChica->bindParam(':fecha', $fecha);
        $insertarSaldoInicialCajaChica->bindParam(':idMes', $nuevoMes);
        $insertarSaldoInicialCajaChica->bindParam(':hora', $horaDeRegistro);
        $insertarSaldoInicialCajaChica->bindParam(':tipo', $tipo);
        $insertarSaldoInicialCajaChica->bindParam(':total', $ultimoSaldo);
        $insertarSaldoInicialCajaChica->execute();

        $idCajaChica = $con->lastInsertId();
        $movimiento = 'Saldo Inicial';
        $idMovimiento = 0;
        $descripcion = 'Saldo Inicial del mes';
        $detalle = $con->prepare("INSERT INTO cajachicadetalle (idCajaChica, descripcion, movimiento, idMovimiento, importe) VALUES (:idCajaChica, :descripcion, :movimiento, :idMovimiento, :importe)");
        $detalle->bindParam(':idCajaChica', $idCajaChica);
        $detalle->bindParam(':descripcion', $descripcion);
        $detalle->bindParam(':movimiento', $movimiento );
        $detalle->bindParam(':idMovimiento', $idMovimiento);
        $detalle->bindParam(':importe', $ultimoSaldo);
        $detalle->execute();
        $con->commit();
        echo json_encode([ 'success'=>true, 'message'=>'Se ha insertado un saldo inicial', 'swal'=>'success' ]);
    } catch (Exception $e) {
        $con->rollBack();
        echo json_encode([ 'success'=>false, 'message'=>$e->getMessage() . '. On line ' . $e->getLine(), 'swal'=>'warning' ]);
    }
} else {
    echo json_encode([ 'success'=>false, 'message'=>'No se han recibido los parametros correctos', 'swal'=>'error' ]);
}
