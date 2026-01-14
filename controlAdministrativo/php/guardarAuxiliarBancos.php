<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$miInfo = file_get_contents("php://input");
$datos = json_decode($miInfo);
foreach ($datos as $i) {

    if (isset($i->subconcepto) && $i->subconcepto) {
        $idSubsubcuenta = $i->subconcepto->idSubSubcuenta;
        $subSubcuenta = $i->subconcepto->subSubcuenta;
    } else {
        $idSubsubcuenta = null;
        $subSubcuenta = null;
    }

    $sql = "INSERT INTO auxiliardebancos (idBanco, idCuenta, fecha, idMes, hora,
    referencia, tipoDePersona, nombreDe, concepto, idSubcuenta, descripcion,
    movimiento, tipoDepositoCompra, cantidad, tipoMovimiento, ingresoEgreso, idTransferencia, idSubsubcuenta, subsubcuenta, kg)
    VALUES (:idBanco, :idCuenta, :fecha, :idMes, :hora, :referencia, :tipoDePersona,
    :nombreDe, :concepto, :idSubcuenta, :descripcion, :movimiento, :tipoDepositoCompra,
    :cantidad, :tipoMovimiento, :ingresoEgreso, :idTransferencia, :idSubsubcuenta, :subsubcuenta, :kg)";
    $dato = $con->prepare($sql);
    $dato->bindParam(':idBanco', $i->idBanco);
    $dato->bindParam(':idCuenta', $i->idCuenta);
    $dato->bindParam(':fecha', $i->fecha);
    $dato->bindParam(':idMes', $i->idMes);
    $dato->bindParam(':hora', $i->hora);
    $dato->bindParam(':referencia', $i->referencia);
    $dato->bindParam(':tipoDePersona', $i->tipoDePersona);
    $dato->bindParam(':nombreDe', $i->nombreDe);
    $dato->bindParam(':concepto', $i->concepto);
    $dato->bindParam(':idSubcuenta', $i->idSubcuenta);
    $dato->bindParam(':descripcion', $i->descripcion);
    $dato->bindParam(':movimiento', $i->movimiento);
    $dato->bindParam(':tipoDepositoCompra', $i->tipoDepositoCompra);
    $dato->bindParam(':cantidad', $i->cantidad);
    $dato->bindParam(':tipoMovimiento', $i->tipoMovimiento->idCuentaConcepto);
    $dato->bindParam(':ingresoEgreso', $i->ingresoEgreso);
    $dato->bindParam(':idTransferencia', $i->idTransferencia);
    $dato->bindParam(':idSubsubcuenta', $idSubsubcuenta);
    $dato->bindParam(':subsubcuenta', $subSubcuenta);
    $dato->bindParam(':kg', $i->kg);
    $dato->execute();
    $idEnAuxiliarBancos = $con->lastInsertId();


    if (isset($i->guardarRelacion) && $i->guardarRelacion) {
        $_idIngreso = $idEnAuxiliarBancos;
        $_idEgreso = $idEnAuxiliarBancos - 1;
        $insertaRelacion = $con->prepare('INSERT INTO relaciondemovimientos (tipoMovimiento, ingreso, egreso, idMovimiento) VALUES ("4", :ingreso, :egreso, :idMovimiento)');
        $insertaRelacion->bindParam(':ingreso', $_idIngreso);
        $insertaRelacion->bindParam(':egreso', $_idEgreso);
        $insertaRelacion->bindParam(':idMovimiento', $idEnAuxiliarBancos);
        $insertaRelacion->execute();

    }

    // A caja chica

    if (isset($i->aCajaChica) && $i->aCajaChica) {

        // Debe guardar en :
        // encabezado
        // detalle
        // relación

        $dato = $con->prepare("INSERT INTO cajachica (fecha, idMes, hora, tipoDeCliente, nombre, tipo, total) VALUES (:fecha, :idMes, :hora, :tipoDeCliente, :nombre, '0', :total)");
        $dato->bindParam(':fecha', $i->fecha);
        $dato->bindParam(':idMes', $i->idMes);
        $dato->bindParam(':hora', $i->hora);
        $dato->bindParam(':tipoDeCliente', $i->tipoDePersona);
        $dato->bindParam(':nombre', $i->nombreDe);
        $dato->bindParam(':total', $i->cantidad);
        $dato->execute();
        $newId = $con->lastInsertId();

        $datosSub = $con->prepare("INSERT INTO cajachicadetalle (idCajaChica, concepto, descripcion, movimiento, idMovimiento, idBanco, idCuenta, kg, precio, importe)
                                    VALUES (:idCajaChica, :concepto, :descripcion, :movimiento, :idMovimiento, :idBanco, :idCuenta, '0', '0', :importe)");
        $datosSub->bindParam(':idCajaChica', $newId);
        $datosSub->bindParam(':concepto', $i->concepto);
        $datosSub->bindParam(':descripcion', $i->descripcion);
        $datosSub->bindParam(':movimiento', $i->movimiento);
        $datosSub->bindParam(':idMovimiento', $i->tipoMovimiento->idCuentaConcepto);
        $datosSub->bindParam(':idBanco', $i->idBanco);
        $datosSub->bindParam(':idCuenta', $i->idCuenta);
        $datosSub->bindParam(':importe', $i->cantidad);
        $datosSub->execute();

        $insertar = $con->prepare('INSERT INTO relaciondemovimientos (tipoMovimiento, cajaChica, idMovimiento) VALUES (:tipoMovimiento, :idCajaChica, :idMovimiento)');
        $insertar->bindParam(':tipoMovimiento', $i->tipoMovimiento->idCuentaConcepto);
        $insertar->bindParam(':idCajaChica', $newId);
        $insertar->bindParam(':idMovimiento', $idEnAuxiliarBancos);
        $insertar->execute();

    }
}
