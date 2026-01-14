<?php

function obtenerCobranzaClientes($mes = false, $meses = false, $con)
{
    $resultadoCobranza = array(
        'meses' => array(),
        'actividades_operacion' => array(
            'cobranza_clientes' => array(),
            'total_cobranza' => array()
        )
    );

    if ($mes) {
        if ($meses) {
            $sqlMeses = $con->prepare("SELECT idMes, mes FROM meses WHERE idMes >= '" . $mes . "' AND idMes <= '" . $meses . "' ORDER BY idMes");
        } else {
            $sqlMeses = $con->prepare("SELECT idMes, mes FROM meses WHERE idMes = '" . $mes . "' ORDER BY idMes");
        }

    } else {
        $sqlMeses = $con->prepare("SELECT idMes, mes FROM meses ORDER BY idMes");
    }

    $sqlMeses->execute();
    if (!$sqlMeses) {
        throw new Exception($con->errorInfo());
    }
    $resultadoCobranza['meses'] = $sqlMeses->fetchAll(PDO::FETCH_ASSOC);


    /* Actividades de Operación */
    /* Cobranza a clientes */

    /**
     * Llamar al catálogo de clientes
     * Y por cada uno, ejecutar la consulta
     * Almacenar
     */

    $sqlSeleccionaClientes = $con->prepare("SELECT idCliente, nombre FROM clientes WHERE flujoEfectivo = 1 AND estado = 1 ORDER BY nombre");
    $sqlSeleccionaClientes->execute();
    if (!$sqlSeleccionaClientes) {
        throw new Exception($con->errorInfo());
    }
    $listaClientes = $sqlSeleccionaClientes->fetchAll(PDO::FETCH_ASSOC);

    $total_acumulado = 0;

    foreach ($listaClientes as $cliente) {

        $nuevo_cliente = new stdClass();
        $nuevo_cliente->nombre = $cliente['nombre'];
        $nuevo_cliente->totalesPorMes = array();

        $tiene_movimientos = false; /* Condición que  evalúa si el cliente tiene movimientos*/
        $total_todos_meses = 0;
        foreach ($resultadoCobranza['meses'] as $indiceMensual => $mes) {

            /* Iniciar el total de cobranza por mes (Si no existe) */
            $resultadoCobranza['actividades_operacion']['total_cobranza'][$indiceMensual] = isset($resultadoCobranza['actividades_operacion']['total_cobranza'][$indiceMensual])
                ? $resultadoCobranza['actividades_operacion']['total_cobranza'][$indiceMensual]
                : 0;

            // $sqlSeleccionaTotalCliente = $con->prepare("SELECT SUM(cantidad) as total FROM auxiliardebancos
            // WHERE nombreDe = CONVERT(:nombreDe, UNSIGNED INTEGER) AND tipoDePersona = 6
            // AND ingresoEgreso = 0 AND SUBSTR(fecha FROM 6 FOR 2) = CONVERT(:idMes, UNSIGNED INTEGER)");
            $sqlSeleccionaTotalCliente = $con->prepare("SELECT SUM(cantidad) as total FROM
            (SELECT SUM(cantidad) as cantidad FROM auxiliardebancos
            WHERE nombreDe = CONVERT(:nombreDe, UNSIGNED INTEGER) AND tipoDePersona = 6
            AND ingresoEgreso = 0 AND SUBSTR(fecha FROM 6 FOR 2) = CONVERT(:idMes, UNSIGNED INTEGER)
            UNION
            SELECT SUM(total) FROM cajachica WHERE tipoDeCliente = 6 AND tipo = 0 AND nombre = :nombreDe AND SUBSTR(fecha FROM 6 FOR 2) = CONVERT(:idMes, UNSIGNED INTEGER)) tablas");
            $sqlSeleccionaTotalCliente->bindParam(':nombreDe', $cliente['idCliente']);
            $sqlSeleccionaTotalCliente->bindParam(':idMes', $mes['idMes']);
            $sqlSeleccionaTotalCliente->execute();
            if (!$sqlSeleccionaTotalCliente) {
                throw new Exception();
            }
            $resultado_cliente_mes = $sqlSeleccionaTotalCliente->fetch(PDO::FETCH_ASSOC);

            if ($resultado_cliente_mes['total'] && $resultado_cliente_mes['total'] > 0) {
                // Si el cliente tuvo movimientos con monto mayor a 0, cuenta como movimientos
                // La condición se convierte en verdadera
                // Y ya no puede ser falsa otra vez
                $tiene_movimientos = true;
                $total_todos_meses += $resultado_cliente_mes['total'];
                $resultadoCobranza['actividades_operacion']['total_cobranza'][$indiceMensual] += $resultado_cliente_mes['total'];
            } else {
                $resultado_cliente_mes['total'] = 0;
            }
            array_push($nuevo_cliente->totalesPorMes, $resultado_cliente_mes['total']);
        }

        // Se comenta la validación de que tenga movimientos, si no no los trae y queda vacío
        // (revisar si se debe descomentar las demas validaciones o no)
        // if ($tiene_movimientos) {
        array_push($nuevo_cliente->totalesPorMes, $total_todos_meses);
        $total_acumulado += $total_todos_meses;
        array_push($resultadoCobranza['actividades_operacion']['cobranza_clientes'], $nuevo_cliente);
        // }
    }

    array_push($resultadoCobranza['actividades_operacion']['total_cobranza'], $total_acumulado);


    return $resultadoCobranza;
}