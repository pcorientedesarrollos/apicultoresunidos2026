<?php

date_default_timezone_set("America/Merida");

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();


$postdata = file_get_contents('php://input');

if ($postdata) {
    $post = json_decode($postdata);
    $cuentas = $post->datos; // Arreglo de las cuentas bancarias
    $idMes = $post->idMes; // Mes que cierra

    $_fecha = [];
    $_anio = date('Y');

    $_fecha[0] = $idMes == 12 ? $_anio + 1 : $_anio;
    $_fecha[1] = $idMes == 12 ? '01' : $idMes + 1;
    $_fecha[2] = '01';

    $fecha = join('-', $_fecha);
    $idMes = explode("-", $fecha)[1];
    $hora = date("00:00:00");
    $referencia = 'SALDO INICIAL DE LA CUENTA';
    $nombreDe = '';
    $descripcion = 'SALDO INICIAL';
    $tipoDepositoCompra = '';
    $tiposMovimiento = 0;
    $ingresoEgreso = 0;
    

    try {
        $_numberOfLoops = 0;
        $con->beginTransaction();
        foreach ($cuentas as $cuenta) {
            $_numberOfLoops++;
            $idBanco = $cuenta->idBanco;
            $idCuenta = $cuenta->idCuenta;
            $cantidad = $cuenta->saldoInicial;
            $insertarSaldoInicialCuenta = $con->prepare("INSERT INTO auxiliardebancos (idBanco, idCuenta, fecha, idMes, hora, referencia, tipoDePersona, nombreDe, descripcion, movimiento, tipoDepositoCompra, cantidad, tipoMovimiento, ingresoEgreso) 
            VALUES (:idBanco, :idCuenta, :fecha, :idMes, :hora, :referencia, '0', :nombreDe, :descripcion, 'INICIO DE MES', :tipoDepositoCompra, :cantidad, :tipoMovimiento, :ingresoEgreso)");
            $insertarSaldoInicialCuenta->bindParam(':idBanco', $idBanco);
            $insertarSaldoInicialCuenta->bindParam(':idCuenta', $idCuenta);
            $insertarSaldoInicialCuenta->bindParam(':fecha', $fecha);
            $insertarSaldoInicialCuenta->bindParam(':idMes', $idMes);
            $insertarSaldoInicialCuenta->bindParam(':hora', $hora);
            $insertarSaldoInicialCuenta->bindParam(':referencia', $referencia);
            $insertarSaldoInicialCuenta->bindParam(':nombreDe', $nombreDe);
            $insertarSaldoInicialCuenta->bindParam(':descripcion', $descripcion);
            $insertarSaldoInicialCuenta->bindParam(':tipoDepositoCompra', $tipoDepositoCompra);
            $insertarSaldoInicialCuenta->bindParam(':cantidad', $cantidad);
            $insertarSaldoInicialCuenta->bindParam(':tipoMovimiento', $tiposMovimiento);
            $insertarSaldoInicialCuenta->bindParam(':ingresoEgreso', $ingresoEgreso);
            $insertarSaldoInicialCuenta->execute();
            if ($insertarSaldoInicialCuenta->rowCount() != 1) {
                throw new Exception( 'Uno o más cuentas no han podido inicializarse' );
            } else {
                if ($_numberOfLoops == count($cuentas)) {
                    $con->commit();
                    echo json_encode( [ 'error'=>false, 'message'=>'Se ha guardado los saldos iniciales del siguiente mes', 'swal'=>'success' ] );
                }
            }
        }
    } catch (Exception $e) {
        $con->rollBack();
        echo json_encode( [ 'error'=>true, 'message'=>$e->getMessage() . '. On line ' . $e->getLine(), 'swal'=>'error' ] );
    }
} else {
    echo json_encode( [ 'error'=>true, 'message'=>'No se recibieron los parámetros correctos', 'swal'=>'error' ] );
}
