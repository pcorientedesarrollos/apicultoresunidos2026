<?php
$post = json_decode(file_get_contents('php://input'));

function guardarPoliza($con, $post)
{

    try {
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $con->beginTransaction();

        #Verificar folio

        $verificar = $con->prepare("SELECT idPolizaCheque FROM polizacheque WHERE folioCheque = :folio");
        $verificar->bindParam(':folio', $post->folioCheque);
        $verificar->execute();
        if ($verificar->rowCount() >= 1) {
            throw new Exception('Este folio ya se encuentra registrado');
        }

        // $concepto = ucfirst($post->concepto); //Capitalizar el concepto
        $guardar = $con->prepare("INSERT INTO polizacheque (folioCheque, fecha, hora, tipoPersona, persona, persona2, cantidad, idBanco, idCuenta, concepto) 
        VALUES (:folioCheque, :fecha, :hora, :tipoPersona, :persona, :persona2, :cantidad, :idBanco, :idCuenta, :concepto)");
        $guardar->bindParam(':fecha', $post->fecha);
        $guardar->bindParam(':hora', $post->hora);
        $guardar->bindParam(':folioCheque', $post->folioCheque);
        $guardar->bindParam(':tipoPersona', $post->persona);
        $guardar->bindParam(':persona', $post->nombre);
        $guardar->bindParam(':persona2', $post->nombre2);
        $guardar->bindParam(':cantidad', $post->cantidad);
        $guardar->bindParam(':idBanco', $post->idBanco);
        $guardar->bindParam(':idCuenta', $post->bancoCuenta);
        $guardar->bindParam(':concepto', $post->concepto->subcuenta);
        $guardar->execute();

        if ($guardar->rowCount() == 1) {
            $_url = './reportes/polizaCheque/poliza.php?idPoliza=' . $con->lastInsertId();
            $_idPoliza = $con->lastInsertId();
            
            #HACER EL DESCUENTO EN EL BANCO

            $_referencia = 'FOLIO DEL CHEQUE: ' . $post->folioCheque;
            $_descripcion = ucfirst($post->descripcion);
            // $_movimiento = 'Póliza de cheque';
            $_tipoDepositoCompra = '';
            // $_idSubcuenta = 0;
            $_idMes = explode('-', $post->fecha)[1];

            if (isset($post->subconcepto) && $post->subconcepto) {
                $idSubsubcuenta = $post->subconcepto->idSubSubcuenta;
                $subSubcuenta = $post->subconcepto->subSubcuenta;
            } else {
                $idSubsubcuenta = null;
                $subSubcuenta = null;
            }

            $sql = "INSERT INTO auxiliardebancos (idBanco, idCuenta, fecha, idMes, hora, referencia, tipoDePersona,
                nombreDe, concepto, idSubcuenta, descripcion, movimiento, tipoDepositoCompra, cantidad, tipoMovimiento,
                idPolizaCheque, ingresoEgreso, idSubsubcuenta, subsubcuenta)
                VALUES (:idBanco, :idCuenta, :fecha, :idMes, :hora, :referencia, :tipoDePersona, :nombreDe, :concepto,
                :idSubcuenta, :descripcion, :movimiento, :tipoDepositoCompra, :cantidad, :tipoMovimiento, :idPolizaCheque, :ingresoEgreso, :idSubsubcuenta, :subsubcuenta)";
            $dato = $con->prepare($sql);
            $dato->bindParam(':idBanco', $post->idBanco);
            $dato->bindParam(':idCuenta', $post->bancoCuenta);
            $dato->bindParam(':fecha', $post->fecha);
            $dato->bindParam(':idMes', $_idMes);
            $dato->bindParam(':hora', $post->hora);
            $dato->bindParam(':referencia', $_referencia);
            $dato->bindParam(':tipoDePersona', $post->persona);
            $dato->bindParam(':nombreDe', $post->nombre);
            $dato->bindParam(':concepto', $post->concepto->subcuenta);
            $dato->bindParam(':idSubcuenta', $post->concepto->idSubcuenta);
            $dato->bindParam(':descripcion', $_descripcion);
            $dato->bindParam(':movimiento', $post->tipoMovimiento->cuenta);
            $dato->bindParam(':tipoDepositoCompra', $_tipoDepositoCompra);
            $dato->bindParam(':cantidad', $post->cantidad);
            $dato->bindParam(':tipoMovimiento', $post->tipoMovimiento->idCuentaConcepto);
            $dato->bindParam(':idPolizaCheque', $_idPoliza);
            $dato->bindParam(':ingresoEgreso', $post->ingresoEgreso);
            $dato->bindParam(':idSubsubcuenta', $idSubsubcuenta);
            $dato->bindParam(':subsubcuenta', $subSubcuenta);
            $dato->execute();

            if ($dato->rowCount() == 1) {
                $idMovimientoAuxBanco = $con->lastInsertId();
                if ($post->aCajaChica) {
                    $_idMes = explode('-', $post->fecha)[1];
                    $_movimiento = 'PÓLIZA DE CHEQUE';

                    $sqlCajaChica = "INSERT INTO cajachica (fecha, idMes, hora, tipoDeCliente, nombre, tipo, total, idPolizaCheque) VALUES (:fecha, :idMes, :hora, :tipoDeCliente, :nombre, '0', :total, :idPolizaCheque)";
                    $dato = $con->prepare($sqlCajaChica);
                    $dato->bindParam(':fecha', $post->fecha);
                    $dato->bindParam(':idMes', $_idMes);
                    $dato->bindParam(':hora', $post->hora);
                    $dato->bindParam(':tipoDeCliente', $post->persona);
                    $dato->bindParam(':nombre', $post->nombre);
                    $dato->bindParam(':total', $post->cantidad);
                    $dato->bindParam(':idPolizaCheque', $_idPoliza);
                    $dato->execute();

                    if ($dato->rowCount() != 1) {
                        throw new Exception('No se ha registrado en Caja Chica');
                    }

                    $newId = $con->lastInsertId();

                    $sqlCCD = "INSERT INTO cajachicadetalle (idCajaChica, concepto, descripcion, movimiento, idMovimiento, idBanco, idCuenta, kg, precio, importe)
                    VALUES (:idCajaChica, :concepto, :descripcion, :movimiento, :idMovimiento, :idBanco, :idCuenta, '0', '0', :importe)";
                    $datosSub = $con->prepare($sqlCCD);
                    $datosSub->bindParam(':idCajaChica', $newId);
                    $datosSub->bindParam(':concepto', $_referencia);
                    $datosSub->bindParam(':descripcion', $_descripcion);
                    $datosSub->bindParam(':movimiento', $_movimiento);
                    // $datosSub->bindParam(':idMovimiento', $post->tipoMovimiento);
                    $datosSub->bindParam(':idMovimiento', $post->tipoMovimiento->idCuentaConcepto);
                    $datosSub->bindParam(':idBanco', $post->idBanco);
                    $datosSub->bindParam(':idCuenta', $post->bancoCuenta);
                    $datosSub->bindParam(':importe', $post->cantidad);
                    $datosSub->execute();

                    if ($datosSub->rowCount() != 1) {
                        throw new Exception('No se ha registrado en detalle de caja chica');
                    }

                    $insertaRelacion = $con->prepare('INSERT INTO relaciondemovimientos (tipoMovimiento, poliza, cajaChica, idMovimiento) VALUES (:tipoMovimiento, :poliza, :cajaChica, :idMovimiento)');
                    // $insertaRelacion->bindParam(':tipoMovimiento', $post->tipoMovimiento);
                    $insertaRelacion->bindParam(':tipoMovimiento', $post->tipoMovimiento->idCuentaConcepto);
                    $insertaRelacion->bindParam(':poliza', $_idPoliza);
                    $insertaRelacion->bindParam(':cajaChica', $newId);
                    $insertaRelacion->bindParam(':idMovimiento', $idMovimientoAuxBanco);
                    $insertaRelacion->execute();

                    if ($insertaRelacion->rowCount() == 1) {
                        echo json_encode(['error' => false, 'message' => 'La Póliza ha sido guardada', 'url' => $_url, 'idPoliza' => $_idPoliza]);
                        $con->commit();
                    } else {
                        throw new Exception('No se guardó la relación del movimiento');
                    }
                } else {
                    $insertaRelacion = $con->prepare('INSERT INTO relaciondemovimientos (tipoMovimiento, poliza, idMovimiento) VALUES (:tipoMovimiento, :poliza, :idMovimiento)');
                    // $insertaRelacion->bindParam(':tipoMovimiento', $post->tipoMovimiento);
                    $insertaRelacion->bindParam(':tipoMovimiento', $post->tipoMovimiento->idCuentaConcepto);
                    $insertaRelacion->bindParam(':poliza', $_idPoliza);
                    $insertaRelacion->bindParam(':idMovimiento', $idMovimientoAuxBanco);
                    $insertaRelacion->execute();

                    if ($insertaRelacion->rowCount() == 1) {
                        echo json_encode(['error' => false, 'message' => 'La Póliza ha sido guardada', 'url' => $_url, 'idPoliza' => $_idPoliza]);
                        $con->commit();
                    } else {
                        throw new Exception('No se guardó la relación del movimiento');
                    }
                }
            } else {
                throw new Exception('Error al hacer el movimiento del banco');
            }
        } else {
            throw new Exception('Error al guardar la nueva póliza');
        }
    } catch (Exception $e) {
        $con->rollBack();
        echo json_encode(['error' => true, 'message' => $e->getMessage() . '. Line ' . $e->getLine()]);
    }
};

if ($post) {
    include_once '../../../DAOConeccion/conePDO.php';
    $pdo = new conePDO();
    $con = $pdo->conectar();
    guardarPoliza($con, $post);
} else {
    exit();
}