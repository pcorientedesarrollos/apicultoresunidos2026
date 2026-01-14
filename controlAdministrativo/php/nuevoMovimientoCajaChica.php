<?php
date_default_timezone_set('America/Merida');
$post = file_get_contents('php://input');
if ($post) {
    include_once '../../DAOConeccion/conePDO.php';
    $pdo = new conePDO();
    $dbh = $pdo->conectar();
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    try {
        $dbh->beginTransaction();

            // $_horaDeMovimiento = date("H:i:s");
        $datos = json_decode($post);
        $detalle = $datos[0];
        $encabezado = $datos[1];

        if (isset($encabezado->idCajaChica)) {
            $insertarEncabezado = $dbh->prepare("UPDATE cajachica SET fecha = :fecha, idMes = :idMes, hora = :hora, tipoDeCliente = :tipoDeCliente, nombre = :nombre, tipo = :tipo, total = :total WHERE idCajaChica = :idCajaChica");
            $insertarEncabezado->bindParam(':fecha', $encabezado->fecha);
            $insertarEncabezado->bindParam(':idMes', $encabezado->idMes);
            $insertarEncabezado->bindParam(':hora', $encabezado->hora);
            $insertarEncabezado->bindParam(':tipoDeCliente', $encabezado->tipoDeCliente);
            $insertarEncabezado->bindParam(':nombre', $encabezado->nombre);
            $insertarEncabezado->bindParam(':tipo', $encabezado->tipo);
            $insertarEncabezado->bindParam(':total', $encabezado->total);
            $insertarEncabezado->bindParam(':idCajaChica', $encabezado->idCajaChica);
            $insertarEncabezado->execute();

            $idMovimiento = $encabezado->idCajaChica;
            $insertarEncabezado->closeCursor();

            $resetearDetalle = $dbh->prepare("DELETE FROM cajachicadetalle WHERE idCajaChica = :idCajaChica");
            $resetearDetalle->bindParam(':idCajaChica', $idMovimiento);
            $resetearDetalle->execute();
        } else {
            $insertarEncabezado = $dbh->prepare("INSERT INTO cajachica (fecha, idMes, hora, tipoDeCliente, nombre, tipo, total) VALUES (:fecha, :idMes, :hora, :tipoDeCliente, :nombre, :tipo, :total)");
            $insertarEncabezado->bindParam(':fecha', $encabezado->fecha);
            $insertarEncabezado->bindParam(':idMes', $encabezado->idMes);
            $insertarEncabezado->bindParam(':hora', $encabezado->hora);
            $insertarEncabezado->bindParam(':tipoDeCliente', $encabezado->tipoDeCliente);
            $insertarEncabezado->bindParam(':nombre', $encabezado->nombre);
            $insertarEncabezado->bindParam(':tipo', $encabezado->tipo);
            $insertarEncabezado->bindParam(':total', $encabezado->total);
            $insertarEncabezado->execute();

            if ($insertarEncabezado->rowCount() != 1) {
                throw new Exception('No se ha registrado el encabezado correctamente');
            }
            $idMovimiento = $dbh->lastInsertId();
            $insertarEncabezado->closeCursor();
        }


        if ($encabezado->tipo == 0) {
            // Ingreso
            foreach ($detalle as $venta) {
                $insertarDetalle = $dbh->prepare("INSERT INTO cajachicadetalle (idCajaChica, concepto, idConcepto, idSubConcepto, descripcion, movimiento, idMovimiento, kg, precio, importe, subconcepto)
                                                    VALUES (:idCajaChica, :concepto, :idConcepto, :idSubConcepto, :descripcion, :movimiento, :idMovimiento,  :kg, :precio, :importe, :subconcepto)");
                $insertarDetalle->bindParam(':idCajaChica', $idMovimiento);
                $insertarDetalle->bindParam(':concepto', $venta->concepto);
                $insertarDetalle->bindParam(':idConcepto', $venta->idConcepto);
                $insertarDetalle->bindParam(':idSubConcepto', $venta->idSubConcepto);
                $insertarDetalle->bindParam(':descripcion', $venta->descripcion);
                $insertarDetalle->bindParam(':movimiento', $venta->movimiento);
                $insertarDetalle->bindParam(':idMovimiento', $venta->idMovimiento);
                $insertarDetalle->bindParam(':kg', $venta->kg);
                $insertarDetalle->bindParam(':precio', $venta->precio);
                $insertarDetalle->bindParam(':importe', $venta->importe);
                $insertarDetalle->bindParam(':subconcepto', $venta->subconcepto);
                $insertarDetalle->execute();
            }
        } elseif ($encabezado->tipo == 1) {
            // Egreso
            foreach ($detalle as $venta) {

                $insertarDetalle = $dbh->prepare("INSERT INTO cajachicadetalle (idCajaChica, concepto, idConcepto, idSubConcepto, subconcepto, descripcion, movimiento, idMovimiento, cantidad)
                                                VALUES (:idCajaChica,  :concepto, :idConcepto, :idSubConcepto, :subconcepto, :descripcion, :movimiento, :idMovimiento, :cantidad)");
                $insertarDetalle->bindParam(':idCajaChica', $idMovimiento);
                $insertarDetalle->bindParam(':concepto', $venta->concepto);
                $insertarDetalle->bindParam(':idConcepto', $venta->idConcepto);
                $insertarDetalle->bindParam(':idSubConcepto', $venta->idSubConcepto);
                $insertarDetalle->bindParam(':subconcepto', $venta->subconcepto);
                $insertarDetalle->bindParam(':descripcion', $venta->descripcion);
                $insertarDetalle->bindParam(':movimiento', $venta->movimiento);
                $insertarDetalle->bindParam(':idMovimiento', $venta->idMovimiento);
                $insertarDetalle->bindParam(':cantidad', $venta->cantidad);
                $insertarDetalle->execute();

                // if (isset($venta->bancoIngreso) && isset($venta->bancoCuenta)) {
                //     $insertarDetalle = $dbh->prepare("INSERT INTO cajachicadetalle (idCajaChica, concepto, idConcepto, idSubConcepto, descripcion, movimiento, idMovimiento, cantidad, idBanco, idCuenta)
                //                                 VALUES (:idCajaChica, :concepto, :idConcepto, :idSubConcepto, :descripcion, :movimiento, :idMovimiento, :cantidad, :idBanco, :idCuenta)");
                //     $insertarDetalle->bindParam(':idCajaChica', $idMovimiento);
                //     $insertarDetalle->bindParam(':concepto', $venta->concepto);
                //     $insertarDetalle->bindParam(':idConcepto', $venta->idConcepto);
                //     $insertarDetalle->bindParam(':idSubConcepto', $venta->idSubConcepto);
                //     $insertarDetalle->bindParam(':descripcion', $venta->descripcion);
                //     $insertarDetalle->bindParam(':movimiento', $venta->movimiento);
                //     $insertarDetalle->bindParam(':idMovimiento', $venta->idMovimiento);
                //     $insertarDetalle->bindParam(':cantidad', $venta->cantidad);
                //     $insertarDetalle->bindParam(':idBanco', $venta->bancoIngreso);
                //     $insertarDetalle->bindParam(':idCuenta', $venta->bancoCuenta);
                //     $insertarDetalle->execute();


                //     $referencia = 'DEPÓSITO DESDE CAJA CHICA';
                //     $idSubcuenta = 0;
                //     $tipoDepositoCompra = 'OTROS';
                //     $ingresoEgreso = 0;
                //     $auxiliarBanco = $dbh->prepare("INSERT INTO auxiliardebancos 
                //             (idBanco, idCuenta, fecha, idMes, hora, referencia, tipoDePersona, nombreDe, concepto, idSubcuenta, descripcion, movimiento, tipoDepositoCompra, cantidad, tipoMovimiento, ingresoEgreso)
                //             VALUES (:idBanco, :idCuenta, :fecha, :idMes, :hora, :referencia, :tipoDePersona, :nombreDe, :concepto, :idSubcuenta, :descripcion, :movimiento, :tdc, :cantidad, :tipoMovimiento, :ingresoEgreso)");
                //     $auxiliarBanco->bindParam(':idBanco', $venta->bancoIngreso);
                //     $auxiliarBanco->bindParam(':idCuenta', $venta->bancoCuenta);
                //     $auxiliarBanco->bindParam(':fecha', $encabezado->fecha);
                //     $auxiliarBanco->bindParam(':idMes', $encabezado->idMes);
                //     $auxiliarBanco->bindParam(':hora', $encabezado->hora);
                //     $auxiliarBanco->bindParam(':referencia', $referencia);
                //     $auxiliarBanco->bindParam(':tipoDePersona', $encabezado->tipoDeCliente);
                //     $auxiliarBanco->bindParam(':nombreDe', $encabezado->nombre);
                //     $auxiliarBanco->bindParam(':concepto', $venta->concepto);
                //     $auxiliarBanco->bindParam(':idSubcuenta', $idSubcuenta);
                //     $auxiliarBanco->bindParam(':descripcion', $venta->descripcion);
                //     $auxiliarBanco->bindParam(':movimiento', $venta->movimiento);
                //     $auxiliarBanco->bindParam(':tdc', $tipoDepositoCompra);
                //     $auxiliarBanco->bindParam(':cantidad', $venta->cantidad);
                //     $auxiliarBanco->bindParam(':tipoMovimiento', $venta->idMovimiento);
                //     $auxiliarBanco->bindParam(':ingresoEgreso', $ingresoEgreso);
                //     $auxiliarBanco->execute();


                //     $idAuxBanco = $dbh->lastInsertId();

                //     $insertar = $dbh->prepare('INSERT INTO relaciondemovimientos (tipoMovimiento, cajaChica, idMovimiento) VALUES (:tipoMovimiento, :idCajaChica, :idMovimiento)');
                //     $insertar->bindParam(':tipoMovimiento', $venta->idMovimiento);
                //     $insertar->bindParam(':idCajaChica', $idMovimiento);
                //     $insertar->bindParam(':idMovimiento', $idAuxBanco);
                //     $insertar->execute();
                // } else {
                // $insertarDetalle = $dbh->prepare("INSERT INTO cajachicadetalle (idCajaChica, concepto, idConcepto, idSubConcepto, descripcion, movimiento, idMovimiento, cantidad)
                //                                 VALUES (:idCajaChica,  :concepto, :idConcepto, :idSubConcepto, :descripcion, :movimiento, :idMovimiento, :cantidad)");
                // $insertarDetalle->bindParam(':idCajaChica', $idMovimiento);
                // $insertarDetalle->bindParam(':concepto', $venta->concepto);
                // $insertarDetalle->bindParam(':idConcepto', $venta->idConcepto);
                // $insertarDetalle->bindParam(':idSubConcepto', $venta->idSubConcepto);
                // $insertarDetalle->bindParam(':descripcion', $venta->descripcion);
                // $insertarDetalle->bindParam(':movimiento', $venta->movimiento);
                // $insertarDetalle->bindParam(':idMovimiento', $venta->idMovimiento);
                // $insertarDetalle->bindParam(':cantidad', $venta->cantidad);
                // $insertarDetalle->execute();
                // }
            }
        }
        $dbh->commit();
        $_url = 'reportes/cajaChica/pdfMovimientoCajaChica.php?idCajaChica=' . $idMovimiento;
        echo json_encode(['error' => false, 'message' => '', 'url' => $_url]);
    } catch (Exception $e) {
        $dbh->rollBack();
        echo json_encode(['error' => true, 'message' => $e->getMessage() . '. On line ' . $e->getLine()]);
    }
} else {
    exit();
}
