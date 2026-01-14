<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$postdata = file_get_contents('php://input');

try {

    // La edición del movimiento debe contemplar los siguientes puntos:

    // a) Edición de un movimiento de auxiliar de bancos 
    // b) Edición de un movimiento entre cuentas
    //     1) Editar los dos movimientos generados, obteniendo el id del movimiento relacionado
    //     2) Edición de los bancos
    // c) Edición de un movimiento de póliza de cheque (Similar a movimiento de auxiliar de bancos)
    // d) Edición de un movimiento que modifica otro registro en caja chica
    // e) Edicion de un movimiento que es un saldo inicial de la cuenta

    $con->beginTransaction();

    if (!$postdata) {
        throw new Exception('No se recibieron los datos');
    }
    $datos = json_decode($postdata);

    if ($datos->tipoDePersona == '0' && $datos->nombreDe == '0') {
        // Este es un saldo inicial

        $query = $con->prepare("UPDATE auxiliardebancos SET cantidad = :cantidad WHERE idAuxiliar = :idAuxiliar");
        $query->bindParam(':cantidad', $datos->cantidad);
        $query->bindParam(':idAuxiliar', $datos->idAuxiliar);
        $query->execute();

    }

    if ($datos->tipoDePersona != '0'
        && $datos->nombreDe != '0'
        && $datos->tipoMovimiento != '0'
        && $datos->idSubcuenta
        && $datos->idSubcuenta != '0') {
            // Este es un auxiliar


        // Seleccionar el nombres de las cuentas de los catálogos para actualizar 


        $sqlCuenta = $con->prepare("SELECT cuenta FROM cuentas WHERE idCuentaConcepto = :cuenta");
        $sqlCuenta->bindParam(':cuenta', $datos->tipoMovimiento);
        $sqlCuenta->execute();

        if (!$sqlCuenta) {
            throw new Exception($con->errorInfo());
        }

        $resultadoCuenta = $sqlCuenta->fetch(PDO::FETCH_ASSOC);

        $sqlSubcuenta = $con->prepare("SELECT subcuenta FROM subcuentas WHERE idSubcuenta = :subcuenta");
        $sqlSubcuenta->bindParam(':subcuenta', $datos->idSubcuenta);
        $sqlSubcuenta->execute();

        if (!$sqlSubcuenta) {
            throw new Exception($con->errorInfo());
        }

        $resultadoSubcuenta = $sqlSubcuenta->fetch(PDO::FETCH_ASSOC);

        $sqlSubsubcuenta = $con->prepare("SELECT subSubcuenta FROM subsubcuentas WHERE idSubSubcuenta = :subsubcuenta");
        $sqlSubsubcuenta->bindParam(':subsubcuenta', $datos->idSubsubcuenta);
        $sqlSubsubcuenta->execute();

        if (!$sqlSubsubcuenta) {
            throw new Exception($con->errorInfo());
        }

        $resultadoSubsubcuenta = $sqlSubsubcuenta->fetch(PDO::FETCH_ASSOC);


        // Actualizar el registro

        $query = $con->prepare("UPDATE auxiliardebancos
        SET fecha = :fecha, idMes = :idMes, hora = :hora,
        tipoDePersona = :tipoDePersona, nombreDe = :nombreDe, concepto = :concepto,
        descripcion = :descripcion, movimiento = :movimiento, cantidad = :cantidad,
        tipoMovimiento = :tipoMovimiento, idSubcuenta = :idSubcuenta, idSubsubcuenta = :idSubsubcuenta, subsubcuenta = :subsubcuenta, kg = :kg
        WHERE idAuxiliar = :idAuxiliar");
        $query->bindParam(':fecha', $datos->fecha);
        $query->bindParam(':idMes', $datos->idMes);
        $query->bindParam(':hora', $datos->hora);
        $query->bindParam(':tipoDePersona', $datos->tipoDePersona);
        $query->bindParam(':nombreDe', $datos->nombreDe);
        $query->bindParam(':concepto', $resultadoSubcuenta['subcuenta']);
        $query->bindParam(':descripcion', $datos->descripcion);
        $query->bindParam(':movimiento', $resultadoCuenta['cuenta']);
        $query->bindParam(':cantidad', $datos->cantidad);
        $query->bindParam(':tipoMovimiento', $datos->tipoMovimiento);
        $query->bindParam(':idSubcuenta', $datos->idSubcuenta);
        $query->bindParam(':idSubsubcuenta', $datos->idSubsubcuenta);
        $query->bindParam(':subsubcuenta', $resultadoSubsubcuenta['subSubcuenta']);
        $query->bindParam(':kg', $datos->kg);
        $query->bindParam(':idAuxiliar', $datos->idAuxiliar);
        $query->execute();


    }

    if ($datos->tipoDePersona != '0' && $datos->nombreDe != '0'
        && $datos->idTransferencia
        && $datos->idTransferencia == '1') {
        // Este es un movimiento de transferencia

        $query = $con->prepare("UPDATE auxiliardebancos
        SET fecha = :fecha, idMes = :idMes, hora = :hora,
        tipoDePersona = :tipoDePersona, nombreDe = :nombreDe,
        descripcion = :descripcion, cantidad = :cantidad
        WHERE idAuxiliar = :idAuxiliar");
        $query->bindParam(':fecha', $datos->fecha);
        $query->bindParam(':idMes', $datos->idMes);
        $query->bindParam(':hora', $datos->hora);
        $query->bindParam(':tipoDePersona', $datos->tipoDePersona);
        $query->bindParam(':nombreDe', $datos->nombreDe);
        $query->bindParam(':descripcion', $datos->descripcion);
        $query->bindParam(':cantidad', $datos->cantidad);
        $query->bindParam(':idAuxiliar', $datos->idAuxiliar);
        $query->execute();


        // Debe modificar la información del banco del movimiento relacionado
        if ($datos->ingresoEgreso == '1') {
            // El movimiento es un egreso, puede modificar el banco al cual se envió el dinero
            $sqlSeleccionaIdAuxiliarRelacionado = "SELECT ingreso as idAuxiliar FROM relaciondemovimientos WHERE egreso = :valor";




            $idMovimientoRelacionado = $con->prepare($sqlSeleccionaIdAuxiliarRelacionado);
            $idMovimientoRelacionado->bindParam(':valor', $datos->idAuxiliar);
            $idMovimientoRelacionado->execute();

            if ($idMovimientoRelacionado == false) {
                throw new Exception($con->errorInfo());
            }

            $resultadoMovimientosRelacionados = $idMovimientoRelacionado->fetch(PDO::FETCH_ASSOC);

            $query = $con->prepare("UPDATE auxiliardebancos
            SET fecha = :fecha, idMes = :idMes, hora = :hora, idBanco = :idBanco, idCuenta = :idCuenta,
            tipoDePersona = :tipoDePersona, nombreDe = :nombreDe,
            cantidad = :cantidad
            WHERE idAuxiliar = :idAuxiliar");
            $query->bindParam(':fecha', $datos->fecha);
            $query->bindParam(':idMes', $datos->idMes);
            $query->bindParam(':hora', $datos->hora);
            $query->bindParam(':idBanco', $datos->idBancoTransferencia);
            $query->bindParam(':idCuenta', $datos->idCuentaTransferencia);

            $query->bindParam(':tipoDePersona', $datos->tipoDePersona);
            $query->bindParam(':nombreDe', $datos->nombreDe);
            $query->bindParam(':cantidad', $datos->cantidad);

            $query->bindParam(':idAuxiliar', $resultadoMovimientosRelacionados['idAuxiliar']);

            $query->execute();
        


            // Seleccionar el nombre del banco y la subcuenta para modificar el descripción
            // DEL MOVIMIENTO ACTUAL

            $sqlNombreCuentaBanco = $con->prepare("SELECT banco, numDeCuenta
            FROM `bancos` b, `cuentasbancarias` c
            WHERE b.idBanco = :idBanco AND c.idCuenta = :idCuenta");
            $sqlNombreCuentaBanco->bindParam(':idBanco', $datos->idBancoTransferencia);
            $sqlNombreCuentaBanco->bindParam(':idCuenta', $datos->idCuentaTransferencia);
            $sqlNombreCuentaBanco->execute();


            if ($sqlNombreCuentaBanco == false) {
                throw new Exception($con->errorInfo());
            }

            $resultadoNombreBanco = $sqlNombreCuentaBanco->fetch(PDO::FETCH_ASSOC);

            $nueva_descripcion_movimiento = "TRASPASO ENTRE CUENTAS A " . $resultadoNombreBanco['banco'] . " " . $resultadoNombreBanco['numDeCuenta'];

            $query = $con->prepare("UPDATE auxiliardebancos
            SET descripcion = :descripcion
            WHERE idAuxiliar = :idAuxiliar");
            $query->bindParam(':descripcion', $nueva_descripcion_movimiento);
            $query->bindParam(':idAuxiliar', $datos->idAuxiliar);
            $query->execute();
        }
    }

    if ($datos->tipoDePersona != '0' && $datos->nombreDe != '0'
        && $datos->idPolizaCheque
        && $datos->idPolizaCheque != '0') {
        // Este es una póliza de cheque

        $query = $con->prepare("UPDATE auxiliardebancos
        SET fecha = :fecha, idMes = :idMes, hora = :hora,
        tipoDePersona = :tipoDePersona, nombreDe = :nombreDe,
        descripcion = :descripcion, cantidad = :cantidad
        WHERE idAuxiliar = :idAuxiliar");
        $query->bindParam(':fecha', $datos->fecha);
        $query->bindParam(':idMes', $datos->idMes);
        $query->bindParam(':hora', $datos->hora);
        $query->bindParam(':tipoDePersona', $datos->tipoDePersona);
        $query->bindParam(':nombreDe', $datos->nombreDe);
        $query->bindParam(':descripcion', $datos->descripcion);
        $query->bindParam(':cantidad', $datos->cantidad);
        $query->bindParam(':idAuxiliar', $datos->idAuxiliar);
        $query->execute();

        if ($query == false) {
            throw new Exception($con->errorInfo());
        }
    }




    // Ahora verifica si en la tabla de relaciones modifica caja chica

    // Si tiene el id hay que modificarlo


    // Verificar en la tabla relaciondemovimientos

    // Sql que obtiene los id de caja chica y poliza en caso que tenga movimientos relacionados

    $sqlMovimientosRelacionados = $con->prepare("SELECT poliza, cajaChica
    FROM relaciondemovimientos WHERE idMovimiento = :idMovimiento;");
    $sqlMovimientosRelacionados->bindParam(':idMovimiento', $datos->idAuxiliar);
    $sqlMovimientosRelacionados->execute();

    if ($sqlMovimientosRelacionados == false) {
        throw new Exception($con->errorInfo());
    }

    $resultadoMovimientosRelacionados = $sqlMovimientosRelacionados->fetch(PDO::FETCH_ASSOC);


    // a) Si afecta movimiento de caja chica

    if ($resultadoMovimientosRelacionados['cajaChica']) {
        // Tenemos que editar el movimiento de caja chica, encabezado y detalle
        $sqlEdicionCajaChica = $con->prepare("UPDATE cajachica
        SET fecha = :fecha, idMes = :idMes, hora = :hora,
        tipoDeCliente = :tipoDeCliente, nombre = :nombre, total = :total
        WHERE idCajaChica = :idCajaChica;
        UPDATE cajachicadetalle SET importe = :total, descripcion = :descripcion, concepto = :concepto
        WHERE idCajaChica = :idCajaChica;");

        $sqlEdicionCajaChica->bindParam(':fecha', $datos->fecha);
        $sqlEdicionCajaChica->bindParam(':idMes', $datos->idMes);
        $sqlEdicionCajaChica->bindParam(':hora', $datos->hora);
        $sqlEdicionCajaChica->bindParam(':tipoDeCliente', $datos->tipoDePersona);
        $sqlEdicionCajaChica->bindParam(':nombre', $datos->nombreDe);
        $sqlEdicionCajaChica->bindParam(':total', $datos->cantidad);
        $sqlEdicionCajaChica->bindParam(':descripcion', $datos->descripcion);
        $sqlEdicionCajaChica->bindParam(':concepto', $datos->concepto);
        $sqlEdicionCajaChica->bindParam(':idCajaChica', $resultadoMovimientosRelacionados['cajaChica']);

        $sqlEdicionCajaChica->execute();

        if (!$sqlEdicionCajaChica) {
            throw new Exception($con->errorInfo());
        }

        $sqlEdicionCajaChica->closeCursor();
    }

    // B) Si afecta a poliza de chque (movimiento tipo 7)

    if ($resultadoMovimientosRelacionados['poliza']) {
        // Tenemos que editar el movimiento de poliza, en la tabla polizacheque

        $sqlEdicionPoliza = $con->prepare("UPDATE polizacheque SET fecha = :fecha,
        hora = :hora, tipoPersona = :tipoPersona, persona = :persona,
        cantidad = :cantidad WHERE idPolizaCheque = :idPolizaCheque");

        $sqlEdicionPoliza->bindParam(':fecha', $datos->fecha);
        $sqlEdicionPoliza->bindParam(':hora', $datos->hora);
        $sqlEdicionPoliza->bindParam(':tipoPersona', $datos->tipoDePersona);
        $sqlEdicionPoliza->bindParam(':persona', $datos->nombreDe);
        $sqlEdicionPoliza->bindParam(':cantidad', $datos->cantidad);
        $sqlEdicionPoliza->bindParam(':idPolizaCheque', $resultadoMovimientosRelacionados['poliza']);

        $sqlEdicionPoliza->execute();

        if (!$sqlEdicionPoliza) {
            throw new Exception($con->errorInfo());
        }

        $sqlEdicionPoliza->closeCursor();

    }
    $con->commit();
    echo json_encode(['error' => false, 'message' => 'Se ha editado el registro.']);
} catch (Exception $e) {
    $con->rollBack();
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
