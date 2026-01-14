<?php

function comprobarEjecucionPdo($sql)
{
    /**
     * Comprueba que no exista errores
     * en la ejecución de la consulta
     */

    global $con;
    if ($sql == false) {
        throw new Exception($con->errorInfo());
    }
}

function obtenerInformeFlujoEfectivo($mes = false, $meses = false, $xls = false)
{

    global $con;
    $resultado = array(
        'meses' => array(), 'actividades_operacion' => array(), 'saldosCajaChica' => array(), 'saldosBancos' => array(),
        'compras' => array()
    );

    $cobranzaAClientes = obtenerCobranzaClientes($mes, $meses, $con);
    $resultado['meses'] = $cobranzaAClientes['meses'];
    $resultado['actividades_operacion'] = $cobranzaAClientes['actividades_operacion'];

    // Saldos iniciales y finales caja y bancos

    $resultado['saldosCajaChica'] = obtenerSaldosCajaChica($mes, $meses);
    $resultado['saldosBancos'] = obtenerSaldosBancos($mes, $meses);

    return $resultado;

}
