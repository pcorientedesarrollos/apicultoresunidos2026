<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
date_default_timezone_set("America/Merida");

$post = file_get_contents('php://input');
if ($post) {
    try {
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $con->beginTransaction();

        $datos = json_decode($post);

        $_replaceSigns = ['$', ',', 'M', 'X','N', 'm', 'x', 'n', ' '];
        $datos->saldo = str_replace($_replaceSigns, '', $datos->saldo);

        $tipo = 0;
        $hora = date("H:i:s");
        $dato = $con->prepare("INSERT INTO cajachica (fecha, idMes, hora, tipo, total) VALUES (:fecha, :idMes, :hora, :tipo, :total)");
        $dato->bindParam(':fecha', $datos->fecha);
        $dato->bindParam(':idMes', $datos->mes);
        $dato->bindParam(':hora', $hora);
        $dato->bindParam(':tipo', $tipo);
        $dato->bindParam(':total', $datos->saldo);
        $dato->execute();

        if ($dato->rowCount() < 1) {
            throw new Exception('No se ha podido insertar el encabezado');
        }

        $idCajaChica = $con->lastInsertId();

        $movimiento = 'Saldo Inicial';
        $idMovimiento = 0;
        $descripcion = 'Saldo inicial para caja chica';

        $detalle = $con->prepare("INSERT INTO cajachicadetalle (idCajaChica,  descripcion, movimiento, idMovimiento, importe) VALUES (:idCajaChica, :descripcion, :movimiento, :idMovimiento, :importe)");
        $detalle->bindParam(':idCajaChica', $idCajaChica);
        $detalle->bindParam(':descripcion', $descripcion);
        $detalle->bindParam(':movimiento', $movimiento);
        $detalle->bindParam(':idMovimiento', $idMovimiento);
        $detalle->bindParam(':importe', $datos->saldo);
        $detalle->execute();
        $con->commit();
        
        $_message = 'Saldo inicial: ' . '$' . number_format($datos->saldo, 2, '.', ',');
        echo json_encode(['error'=>false, 'message'=>$_message, 'swal'=>'success']);
    } catch (Exception $e) {
        $con->rollBack();
        $_message = 'Exception captured: ' . $e->getMessage() . ' on line ' . $e->getLine() . '. Código: ' . $e->getCode();
        echo json_encode(['error'=>true, 'message'=>$_message, 'swal'=>'error']);


        exit();
    }
} else {
    exit();
}
