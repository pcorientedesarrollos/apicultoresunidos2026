<?php

// Calcula el saldo final de caja chica por mes y acumulado

function obtenerSaldosCajaChica($mes = false, $meses = false)
{
    global $con;
    $resultado_cajachica = array(
        'conceptos' => array(),
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
    $resultado_cajachica['meses'] = $sqlMeses->fetchAll(PDO::FETCH_ASSOC);

    // Creamos un objeto para el concepto, para manejarlo como el array de los bancos y los informes
    $nuevoConcepto = new stdClass();
    $nuevoConcepto->nombre = 'CAJA CHICA';
    $nuevoConcepto->saldosIniciales = array();
    $nuevoConcepto->saldosFinales = array();

    foreach ($resultado_cajachica['meses'] as $indiceMensual => $mes) {

        $resultado_cajachica['saldosInicialesPorMes'][$indiceMensual] = isset($resultado_cajachica['saldosInicialesPorMes'][$indiceMensual])
            ? $resultado_cajachica['saldosInicialesPorMes'][$indiceMensual]
            : 0;
        $resultado_cajachica['saldosFinalesPorMes'][$indiceMensual] = isset($resultado_cajachica['saldosFinalesPorMes'][$indiceMensual])
            ? $resultado_cajachica['saldosFinalesPorMes'][$indiceMensual]
            : 0;

            // Iniciar el saldo inicial y final del banco en el mes actual del foreach
        $nuevoConcepto->saldosIniciales[$indiceMensual] = isset($nuevoConcepto->saldosIniciales[$indiceMensual])
            ? $nuevoConcepto->saldosIniciales[$indiceMensual]
            : 0;
        $nuevoConcepto->saldosFinales[$indiceMensual] = isset($nuevoConcepto->saldosFinales[$indiceMensual])
            ? $nuevoConcepto->saldosFinales[$indiceMensual]
            : 0;

        $sqlSaldo = $con->prepare("SELECT COALESCE(SUM(total),0) AS saldoIngresos,
            (SELECT COALESCE(SUM(total),0) FROM cajachica WHERE tipo = 1 AND SUBSTR(fecha FROM 6 FOR 2) = " . $mes['idMes'] . ") AS saldoEgresos,
            (SELECT COALESCE(SUM(total), 0) FROM cajachica WHERE tipoDeCliente IS NULL AND tipo = 0 AND nombre IS NULL AND SUBSTR(fecha FROM 6 FOR 2) = " . $mes['idMes'] . ") AS saldoInicial
            FROM cajachica WHERE tipoDeCliente != 0 AND tipo = 0 AND SUBSTR(fecha FROM 6 FOR 2) = " . $mes['idMes'] . "");

        $sqlSaldo->execute();
        comprobarEjecucionPdo($sqlSaldo);

        $saldo = $sqlSaldo->fetch(PDO::FETCH_ASSOC);
        $saldoActual = $saldo['saldoInicial'] + $saldo['saldoIngresos'] - $saldo['saldoEgresos'];
        $nuevoConcepto->saldosIniciales[$indiceMensual] = floatVal($saldo['saldoInicial']);
        $resultado_cajachica['saldosInicialesPorMes'][$indiceMensual] += floatVal($saldo['saldoInicial']);
        $nuevoConcepto->saldosFinales[$indiceMensual] = $saldoActual;
        $resultado_cajachica['saldosFinalesPorMes'][$indiceMensual] += $saldoActual;

        if ($saldo['saldoInicial'] > 0 || $saldo['saldoIngresos'] > 0) {
            $resultado_cajachica['saldoFinal'] = $saldoActual;
        }
    }

    array_push($resultado_cajachica['conceptos'], $nuevoConcepto);

    return $resultado_cajachica;
}
