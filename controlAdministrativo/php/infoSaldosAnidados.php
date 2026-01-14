<?php
date_default_timezone_set("America/Merida");

function main($con, $idCuenta, $post)
{
    $seleccionaSaldo = $con->prepare("SELECT cantidad, ingresoEgreso FROM auxiliardebancos WHERE idCuenta = :idCuenta AND idMes = $post->idMes");
    $seleccionaSaldo->bindParam(':idCuenta', $idCuenta);
    $seleccionaSaldo->execute();
    $saldoCuenta = new stdClass();
    $ultimoSaldoEnMes = 0;
    if ($seleccionaSaldo->rowCount() >= 1) {
        foreach ($seleccionaSaldo->fetchAll(PDO::FETCH_ASSOC) as $i) {
            if ($i['ingresoEgreso'] == 0) {
                $ultimoSaldoEnMes += $i['cantidad'];
            } else {
                $ultimoSaldoEnMes -= $i['cantidad'];
            }
        }
        $saldoCuenta->ultimoSaldo = $ultimoSaldoEnMes;
    } else {
        $saldoCuenta->ultimoSaldo = 0.00;
        $consultaUltimoSaldoDeLaCuenta = $con->prepare("SELECT idMes FROM auxiliardebancos WHERE idCuenta = :idCuenta AND idMes < $post->idMes");
        $consultaUltimoSaldoDeLaCuenta->bindParam(':idCuenta', $idCuenta);
        $consultaUltimoSaldoDeLaCuenta->execute();

        if ($consultaUltimoSaldoDeLaCuenta->rowCount() >= 1) {
            $arregloDeMeses = array();
            foreach ($consultaUltimoSaldoDeLaCuenta->fetchAll(PDO::FETCH_ASSOC) as $movimiento) {
                array_push($arregloDeMeses, $movimiento['idMes']);
            }
            $ultimoMesSaldo = max($arregloDeMeses);

            $seleccionaSaldo = $con->prepare("SELECT cantidad, ingresoEgreso FROM auxiliardebancos WHERE idCuenta = :idCuenta AND idMes = $ultimoMesSaldo");
            $seleccionaSaldo->bindParam(':idCuenta', $idCuenta);
            $seleccionaSaldo->execute();
            $saldoCuenta = new stdClass();
            $ultimoSaldoEnMes = 0;
            foreach ($seleccionaSaldo->fetchAll(PDO::FETCH_ASSOC) as $i) {
                if ($i['ingresoEgreso'] == 0) {
                    $ultimoSaldoEnMes += $i['cantidad'];
                } else {
                    $ultimoSaldoEnMes -= $i['cantidad'];
                }
            }

        //     $hora = date("H:i:s");
        //     $idBanco = $post->idBanco;
        //     $fecha = $post->fecha;
        //     $idMes = $post->idMes;
        //     $idCuenta = $idCuenta;
        //     $referencia = 'CIERRE DE MES';
        //     $nombreDe = '';
        //     $descripcion = 'TRASPASO DE SALDO A SIGUIENTE MES';
        //     $tipoDepositoCompra = '';
            $cantidad = $ultimoSaldoEnMes;
        //     $tiposMovimiento = 0;
        //     $ingresoEgreso = 0;            

        //     $insertarSaldoInicialCuenta = $con->prepare("INSERT INTO auxiliardebancos (idBanco, idCuenta, fecha, idMes, hora, referencia, nombreDe, descripcion, tipoDepositoCompra, cantidad, tipoMovimiento, ingresoEgreso) VALUES (:idBanco, :idCuenta, :fecha, :idMes, :hora, :referencia, :nombreDe, :descripcion, :tipoDepositoCompra, :cantidad, :tipoMovimiento, :ingresoEgreso)");
        //     $insertarSaldoInicialCuenta->bindParam(':idBanco', $idBanco);
        //     $insertarSaldoInicialCuenta->bindParam(':idCuenta', $idCuenta);
        //     $insertarSaldoInicialCuenta->bindParam(':fecha', $fecha);
        //     $insertarSaldoInicialCuenta->bindParam(':idMes', $idMes);
        //     $insertarSaldoInicialCuenta->bindParam(':hora', $hora);
        //     $insertarSaldoInicialCuenta->bindParam(':referencia', $referencia);
        //     $insertarSaldoInicialCuenta->bindParam(':nombreDe', $nombreDe);
        //     $insertarSaldoInicialCuenta->bindParam(':descripcion', $descripcion);
        //     $insertarSaldoInicialCuenta->bindParam(':tipoDepositoCompra', $tipoDepositoCompra);
        //     $insertarSaldoInicialCuenta->bindParam(':cantidad', $cantidad);
        //     $insertarSaldoInicialCuenta->bindParam(':tipoMovimiento', $tiposMovimiento);
        //     $insertarSaldoInicialCuenta->bindParam(':ingresoEgreso', $ingresoEgreso);            
        //     $insertarSaldoInicialCuenta->execute();
        //     ###################################################################################3

            
        //     $idBanco = $post->idBanco;
        //     $idCuenta = $idCuenta;
        //     $fecha = $post->fecha;
        //     $idMes =$post->idMes;
        //     $hora = date("H:i:s");
        //     $referencia = 'SALDO INICIAL DE LA CUENTA';
        //     $nombreDe = '';
        //     $descripcion = 'SALDO INCIAL';
        //     $tipoDepositoCompra = '';
        //     $cantidad = $ultimoSaldoEnMes;
        //     $tiposMovimiento = 0;
        //     $ingresoEgreso = 0;
            
        //     $insertarSaldoInicialCuenta = $con->prepare("INSERT INTO auxiliardebancos (idBanco, idCuenta, fecha, idMes, hora, referencia, nombreDe, descripcion, tipoDepositoCompra, cantidad, tipoMovimiento, ingresoEgreso) 
        //     VALUES (:idBanco, :idCuenta, :fecha, :idMes, :hora, :referencia, :nombreDe, :descripcion, :tipoDepositoCompra, :cantidad, :tipoMovimiento, :ingresoEgreso)");
        //     $insertarSaldoInicialCuenta->bindParam(':idBanco', $idBanco);
        //     $insertarSaldoInicialCuenta->bindParam(':idCuenta', $idCuenta);
        //     $insertarSaldoInicialCuenta->bindParam(':fecha', $fecha);
        //     $insertarSaldoInicialCuenta->bindParam(':idMes', $idMes);
        //     $insertarSaldoInicialCuenta->bindParam(':hora', $hora);
        //     $insertarSaldoInicialCuenta->bindParam(':referencia', $referencia);
        //     $insertarSaldoInicialCuenta->bindParam(':nombreDe', $nombreDe);
        //     $insertarSaldoInicialCuenta->bindParam(':descripcion', $descripcion);
        //     $insertarSaldoInicialCuenta->bindParam(':tipoDepositoCompra', $tipoDepositoCompra);
        //     $insertarSaldoInicialCuenta->bindParam(':cantidad', $cantidad);
        //     $insertarSaldoInicialCuenta->bindParam(':tipoMovimiento', $tiposMovimiento);
        //     $insertarSaldoInicialCuenta->bindParam(':ingresoEgreso', $ingresoEgreso);
            
        //     $insertarSaldoInicialCuenta->execute();
        //     if ($insertarSaldoInicialCuenta->rowCount() == 1) {
            $saldoCuenta->ultimoSaldo = $cantidad;
        //     }
        } else {
            $saldoCuenta->ultimoSaldo = 0.00;
        }
    }
    
    # JSON-encode the response
    echo $json_response = json_encode($saldoCuenta);
};


if (isset($_GET['idCuenta'])) {
    include_once '../../DAOConeccion/conePDO.php';
    $pdo = new conePDO();
    $con = $pdo->conectar();
    $post = json_decode(file_get_contents('php://input'));
    main($con, $_GET['idCuenta'], $post);
} else {
    exit();
}
