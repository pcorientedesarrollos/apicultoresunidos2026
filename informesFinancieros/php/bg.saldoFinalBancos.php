<?php

/**
 * Calcula el saldo de cada banco por mes y devuselve un arreglo coon cada banco 
 * y una propiedad que es el total de todos los bancos
 */

function obtenerSaldosBancos($mes = false, $meses = false)
{

    global $con;
    $resultado_bancos = array(
        'bancos' => array(),
        'saldosInicialesPorMes' => array(),
        'saldosFinalesPorMes' => array(),
        'saldoFinal' => 0
    );
    
    /* Consultar los meses del calendario para su uso */

    if ($mes && $meses) {
        $sqlMeses = $con->prepare("SELECT idMes, mes FROM meses WHERE idMes BETWEEN '" . $mes . "' AND '" . $meses . "' ORDER BY idMes");
    } else if ($mes) {
        $sqlMeses = $con->prepare("SELECT idMes, mes FROM meses WHERE idMes = '" . $mes . "' ORDER BY idMes");
    } else {
        $sqlMeses = $con->prepare("SELECT idMes, mes FROM meses ORDER BY idMes");
    }

    $sqlMeses->execute();
    comprobarEjecucionPdo($sqlMeses);
    $resultado_bancos['meses'] = $sqlMeses->fetchAll(PDO::FETCH_ASSOC);

    // Primero consultamos en la base de datos todos los bancos que hay, haciendo ciertos filtros
    // Para iterar

    $consultaBancosSistema = $con->prepare("SELECT b.idBanco, b.banco, cb.idCuenta, cb.numDeCuenta
    FROM bancos b
    LEFT JOIN cuentasbancarias cb ON cb.idBanco = b.idBanco
    WHERE idCuenta IS NOT NULL AND numDeCuenta IS NOT NULL AND numDeCuenta != 0");
    $consultaBancosSistema->execute();
    comprobarEjecucionPdo($consultaBancosSistema);
    $listaBancos = $consultaBancosSistema->fetchAll(PDO::FETCH_ASSOC);

    // Iteramos los bancos para obtener sus saldos por mes

    foreach ($listaBancos as $banco) {
        $nuevoBanco = new stdClass();
        $nuevoBanco->nombre = $banco['banco'] . ' ' . $banco['numDeCuenta'];
        $nuevoBanco->saldosIniciales = array();
        $nuevoBanco->saldosFinales = array();
        $nuevoBanco->ultimoSaldo = 0;

        // Iteramos cada mes del banco

        foreach ($resultado_bancos['meses'] as $indiceMensual => $mes) {

            // Iniciar el saldo por mes
            $resultado_bancos['saldosInicialesPorMes'][$indiceMensual] = isset($resultado_bancos['saldosInicialesPorMes'][$indiceMensual])
                ? $resultado_bancos['saldosInicialesPorMes'][$indiceMensual]
                : 0;
            $resultado_bancos['saldosFinalesPorMes'][$indiceMensual] = isset($resultado_bancos['saldosFinalesPorMes'][$indiceMensual])
                ? $resultado_bancos['saldosFinalesPorMes'][$indiceMensual]
                : 0;

            // Iniciar el saldo inicial y final del banco en el mes actual del foreach
            $nuevoBanco->saldosIniciales[$indiceMensual] = isset($nuevoBanco->saldosIniciales[$indiceMensual])
                ? $nuevoBanco->saldosIniciales[$indiceMensual]
                : 0;
            $nuevoBanco->saldosFinales[$indiceMensual] = isset($nuevoBanco->saldosFinales[$indiceMensual])
                ? $nuevoBanco->saldosFinales[$indiceMensual]
                : 0;

            $sqlSaldo = $con->prepare("SELECT COALESCE(SUM(cantidad),0) AS saldoIngresos,
                                    (SELECT COALESCE(SUM(cantidad),0) FROM auxiliardebancos WHERE ingresoEgreso = 1 AND idCuenta = :idCuenta AND 
                                    SUBSTR(fecha FROM 6 FOR 2) = " . $mes['idMes'] . ") AS saldoEgresos,
                                    (SELECT cantidad FROM auxiliardebancos WHERE tipoDePersona = 0 AND ingresoEgreso = 0 AND idCuenta = :idCuenta AND 
                                    SUBSTR(fecha FROM 6 FOR 2) = " . $mes['idMes'] . ") AS saldoInicial
                                    FROM auxiliardebancos WHERE tipoDePersona != 0 AND ingresoEgreso = 0 AND idCuenta = :idCuenta AND 
                                    SUBSTR(fecha FROM 6 FOR 2) = " . $mes['idMes'] . "");
            $sqlSaldo->bindParam(':idCuenta', $banco['idCuenta']);

            $sqlSaldo->execute();
            comprobarEjecucionPdo($sqlSaldo);

            $saldo = $sqlSaldo->fetch(PDO::FETCH_ASSOC);
            $saldoActual = $saldo['saldoInicial'] + $saldo['saldoIngresos'] - $saldo['saldoEgresos'];
            $nuevoBanco->saldosIniciales[$indiceMensual] = floatval($saldo['saldoInicial']);
            $resultado_bancos['saldosInicialesPorMes'][$indiceMensual] += floatval($saldo['saldoInicial']);
            $nuevoBanco->saldosFinales[$indiceMensual] = $saldoActual;
            $resultado_bancos['saldosFinalesPorMes'][$indiceMensual] += $saldoActual;
            // $resultado_bancos['saldoFinal'] += $saldoActual;

            if ($saldo['saldoInicial'] > 0 || $saldo['saldoIngresos'] > 0) {
                $nuevoBanco->ultimoSaldo = $saldoActual;
            }
        }

        $resultado_bancos['saldoFinal'] += $nuevoBanco->ultimoSaldo;
        array_push($resultado_bancos['bancos'], $nuevoBanco);
    }

    return $resultado_bancos;
}
