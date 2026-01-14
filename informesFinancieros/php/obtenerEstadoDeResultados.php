<?php
// include_once '../../DAOConeccion/conePDO.php';
// $pdo = new conePDO();
// $con = $pdo->conectar();

// Inicializar el arreglo

$resultado = array(
    'meses' => array(),
    'actividades_operacion' => array(
        'cobranza_clientes' => array(),
        'total_cobranza' => array()
    ),
    'pago_proveedores' => array(
        'conceptos' => array(),
        'total_pago_proveedores' => array()
    ),
    'gastos_operacion' => array(
        'cuentas' => array(
            'subcuentas' => array()
        ),
        'total_gastos_operacion' => array()
    ),
    'centros_acopio' => array(
        'total_mensual' => array()
    ),
    'compras_totales' => array(
        'total_mensual' => array()
    ),
    'compras_totales_organico' => array(
        'total_mensual' => array()
    ),
    'compras_totales_cera' => array(
        'total_mensual' => array()
    ),
    'compras_totales_cera_organico' => array(
        'total_mensual' => array()
    ),
    'compras_totales_apicolas' => array(
        'total_mensual' => array()
    ),
    'total_pago_bienes_servicios' => 0
);

function obtenerInformeEstadoDeResultados($mes = false, $meses = false, $xls = false)
{
    global $con;
    global $resultado;

    $cobranzaAClientes = obtenerCobranzaClientes($mes, $meses, $con);
    $resultado['meses'] = $cobranzaAClientes['meses'];
    $resultado['actividades_operacion'] = $cobranzaAClientes['actividades_operacion'];

    // /* Consultar los meses del calendario para su uso */

    if ($mes) {
        if ($meses) {
            $sqlMeses = $con->prepare("SELECT idMes, mes FROM meses WHERE idMes >= '" . $mes . "' AND idMes <= '" . $meses . "' ORDER BY idMes");
        } else {
            $sqlMeses = $con->prepare("SELECT idMes, mes FROM meses WHERE idMes = '" . $mes . "' ORDER BY idMes");
        }
    } else {
        // $sqlMeses = $con->prepare("SELECT idMes, mes FROM meses WHERE idMes <= MONTH(CURRENT_DATE()) ORDER BY idMes");
        $sqlMeses = $con->prepare("SELECT idMes, mes FROM meses ORDER BY idMes");    
    }

    $sqlMeses->execute();
    comprobarEjecucionPdo($sqlMeses);
    $resultado['meses'] = $sqlMeses->fetchAll(PDO::FETCH_ASSOC);

    $sqlCuentas = $con->prepare("SELECT c.idCuentaConcepto, UPPER(c.cuenta) AS cuenta 
                          FROM cuentas c 
                          LEFT JOIN relacioncuentainformes r ON r.idCuentaConcepto = c.idCuentaConcepto
                          WHERE r.idInforme = '3' ORDER BY ingresoEgreso ASC");
    $sqlCuentas->execute();
    comprobarEjecucionPdo($sqlCuentas);
    $listaCuentas = $sqlCuentas->fetchAll(PDO::FETCH_ASSOC);

    foreach ($listaCuentas as $dato) {
        $nueva_cuenta = new stdClass();
        $nueva_cuenta->idCuentaConcepto = $dato['idCuentaConcepto'];
        $nueva_cuenta->cuenta = $dato['cuenta'];
        $nueva_cuenta->gastosAup = array();
        $nueva_cuenta->sumaPorMes = array();

        $nueva_cuenta->acumuladoPorCuenta = 0;

        $sqlSubcuentas = $con->prepare("SELECT idSubcuenta, UPPER(subcuenta) as subcuenta FROM subcuentas WHERE idCuentaConcepto = :idCuenta");
        $sqlSubcuentas->bindParam(':idCuenta', $nueva_cuenta->idCuentaConcepto);
        $sqlSubcuentas->execute();
        comprobarEjecucionPdo($sqlSubcuentas);
        $listaSubcuentas = $sqlSubcuentas->fetchAll(PDO::FETCH_ASSOC);

        foreach ($listaSubcuentas as $subcuenta) {
            $nueva_subcuenta = new stdClass();
            $nueva_subcuenta->subSubconceptos = array();
            $nueva_subcuenta->nombre = $subcuenta['subcuenta'];
            $nueva_subcuenta->idSubcuenta = $subcuenta['idSubcuenta'];
            $nueva_subcuenta->totales = array();
            $nueva_subcuenta->total = 0;
            foreach ($resultado['meses'] as $indice => $mesIterando) {
                $nueva_cuenta->sumaPorMes[$indice] = isset($nueva_cuenta->sumaPorMes[$indice]) ? $nueva_cuenta->sumaPorMes[$indice] : 0;
                $resultado['actividades_operacion']['total_otros_ingresos'][$indice] = isset($resultado['actividades_operacion']['total_otros_ingresos'][$indice])
                    ? $resultado['actividades_operacion']['total_otros_ingresos'][$indice]
                    : 0;
                $sqlTotales = $con->prepare("SELECT SUM(total) AS total, mes
                                                FROM(SELECT SUM(cantidad) AS total, SUBSTR(fecha FROM 6 FOR 2 ) AS mes
                                                FROM auxiliardebancos WHERE tipoMovimiento = :idCuentaConcepto AND idSubcuenta = :idSubcuenta
                                                AND SUBSTR(fecha FROM 6 FOR 2) = CONVERT(:idMes, UNSIGNED INTEGER) AND (tipoDePersona != 6 or (tipoDePersona = 6 AND nombreDe NOT IN (SELECT idCliente FROM clientes WHERE flujoEfectivo = 1)))
                                                GROUP BY idSubcuenta
                                                UNION
                                                SELECT SUM(d.cantidad) AS total, SUBSTR(e.fecha FROM 6 FOR 2 ) AS mes
                                                FROM cajachicadetalle d 
                                                LEFT JOIN cajachica e ON e.idCajaChica = d.idCajaChica
                                                WHERE d.idMovimiento = :idCuentaConcepto AND d.idConcepto = :idSubcuenta
                                                AND SUBSTR(fecha FROM 6 FOR 2) = CONVERT(:idMes, UNSIGNED INTEGER) AND (e.tipoDeCliente != 6 or (e.tipoDeCliente = 6 AND e.nombre NOT IN (SELECT idCliente FROM clientes WHERE flujoEfectivo = 1)))
                                                GROUP BY d.idConcepto) AS caja");
                $sqlTotales->bindParam(':idCuentaConcepto', $nueva_cuenta->idCuentaConcepto);
                $sqlTotales->bindParam(':idSubcuenta', $subcuenta['idSubcuenta']);
                $sqlTotales->bindParam(':idMes', $mesIterando['idMes']);
                $sqlTotales->execute();
                $resultado_subcuenta_mes = $sqlTotales->fetch(PDO::FETCH_ASSOC);
                $nueva_cuenta->sumaPorMes[$indice] += $resultado_subcuenta_mes['total'];
                $nueva_subcuenta->total += $resultado_subcuenta_mes['total'];
                // $resultado['sumaTotal'][$indice] += $resultado_subcuenta_mes['total'];
                $resultado['actividades_operacion']['total_otros_ingresos'][$indice] += $resultado_subcuenta_mes['total'];
                array_push($nueva_subcuenta->totales, $resultado_subcuenta_mes['total']);
            }

            $sqlSubSubcuentas = $con->prepare("SELECT idSubSubcuenta, subSubcuenta FROM subsubcuentas WHERE idSubcuenta = :idSubcuenta");
            $sqlSubSubcuentas->bindParam(':idSubcuenta', $subcuenta['idSubcuenta']);
            $sqlSubSubcuentas->execute();
            $listaSubSubcuentas = $sqlSubSubcuentas->fetchAll(PDO::FETCH_ASSOC);

            if (count($listaSubSubcuentas) > 0) {

                foreach ($listaSubSubcuentas as $subSubcuenta) {
                    $nueva_subSubcuenta = new stdClass();
                    $nueva_subSubcuenta->subSubcuenta = $subSubcuenta['subSubcuenta'];
                    $nueva_subSubcuenta->idSubSubcuenta = $subSubcuenta['idSubSubcuenta'];
                    $nueva_subSubcuenta->sumaPorMesSubSub = array();
                    $nueva_subSubcuenta->totalesSub = array();
                    $nueva_subSubcuenta->totalSub = 0;
                    foreach ($resultado['meses'] as $indice => $mesIterando) {
                        $nueva_subSubcuenta->sumaPorMesSubSub[$indice] = isset($nueva_subSubcuenta->sumaPorMesSubSub[$indice]) ? $nueva_subSubcuenta->sumaPorMesSubSub[$indice] : 0;
                        $sqlTotalesSub = $con->prepare("SELECT SUM(total) AS total, mes
                                                    FROM(SELECT SUM(cantidad) AS total, SUBSTR(fecha FROM 6 FOR 2 ) AS mes
                                                    FROM auxiliardebancos WHERE idSubcuenta = :idSubcuenta AND idSubsubcuenta = :idSubSubcuenta
                                                    AND SUBSTR(fecha FROM 6 FOR 2) = CONVERT(:idMes, UNSIGNED INTEGER) AND (tipoDePersona != 6 or (tipoDePersona = 6 AND nombreDe NOT IN (SELECT idCliente FROM clientes WHERE flujoEfectivo = 1)))
                                                    GROUP BY idSubsubcuenta
                                                    UNION
                                                    SELECT SUM(d.cantidad) AS total, SUBSTR(e.fecha FROM 6 FOR 2 ) AS mes
                                                    FROM cajachicadetalle d 
                                                    LEFT JOIN cajachica e ON e.idCajaChica = d.idCajaChica
                                                    WHERE d.idConcepto = :idSubcuenta AND idSubConcepto = :idSubSubcuenta
                                                    AND SUBSTR(fecha FROM 6 FOR 2) = CONVERT(:idMes, UNSIGNED INTEGER) AND (e.tipoDeCliente != 6 or (e.tipoDeCliente = 6 AND e.nombre NOT IN (SELECT idCliente FROM clientes WHERE flujoEfectivo = 1)))
                                                    GROUP BY d.idSubConcepto) AS caja");
                        $sqlTotalesSub->bindParam(':idSubSubcuenta', $subSubcuenta['idSubSubcuenta']);
                        $sqlTotalesSub->bindParam(':idSubcuenta', $nueva_subcuenta->idSubcuenta);
                        $sqlTotalesSub->bindParam(':idMes', $mesIterando['idMes']);
                        $sqlTotalesSub->execute();
                        $resultado_subcuenta_mesSub = $sqlTotalesSub->fetch(PDO::FETCH_ASSOC);
                        $nueva_subSubcuenta->sumaPorMesSubSub[$indice] += $resultado_subcuenta_mesSub['total'];
                        $nueva_subSubcuenta->totalSub += $resultado_subcuenta_mesSub['total'];
                        array_push($nueva_subSubcuenta->totalesSub, $resultado_subcuenta_mesSub['total']);
                    }
                    array_push($nueva_subcuenta->subSubconceptos, $nueva_subSubcuenta);
                }

            }

            array_push($nueva_cuenta->gastosAup, $nueva_subcuenta);

        }

        foreach ($nueva_cuenta->sumaPorMes as $suma) {
            $nueva_cuenta->acumuladoPorCuenta += $suma;
        }
        // $resultado['totalAcumulado'] += $nueva_cuenta->acumuladoPorCuenta;
        array_push($resultado['gastos_operacion']['cuentas'], $nueva_cuenta);

    }

    $total_acumulado = 0;

    foreach ($resultado['meses'] as $indice => $mesIterando) {
        $total_todos_meses = 0;
        $resultado['centros_acopio']['total_mensual'][$indice] = isset($resultado['centros_acopio']['total_mensual'][$indice]) ? $resultado['centros_acopio']['total_mensual'][$indice] : 0;
        $sqlGastosAcopio = $con->prepare("SELECT SUM(cantidad) AS total FROM gastosrealizados WHERE idMes = :idMes");
        $sqlGastosAcopio->bindParam(':idMes', $mesIterando['idMes']);
        $sqlGastosAcopio->execute();
        $resultado_Acopio = $sqlGastosAcopio->fetch(PDO::FETCH_ASSOC);
        $total_todos_meses += $resultado_Acopio['total'];
        $resultado['centros_acopio']['total_mensual'][$indice] += $resultado_Acopio['total'];
        $total_acumulado += $total_todos_meses;
    }

    array_push($resultado['centros_acopio']['total_mensual'], $total_acumulado);

    // Salidas del inventario convencional
    $resultadoInventario = realizarFuncionesInventarioMiel('1');
    // Salidas del inventario orgánico
    $resultadoInventario_organico = realizarFuncionesInventarioMiel('2');

    foreach ($resultado['meses'] as $indice => $mesIterando) {
        // Convencional
        if (isset($resultadoInventario['comprasDeMielPorMeses']['datos'][$indice])) {
            $resultado['compras_totales']['total_mensual'][$indice] = $resultadoInventario['comprasDeMielPorMeses']['datos'][$indice]['totalImporteSalida'];
        }
        // Organinco
        if (isset($resultadoInventario_organico['comprasDeMielPorMeses']['datos'][$indice])) {
            $resultado['compras_totales_organico']['total_mensual'][$indice] = $resultadoInventario_organico['comprasDeMielPorMeses']['datos'][$indice]['totalImporteSalida'];
        }
    }
    $total_acumulado1 = 0;
    foreach ($resultado['compras_totales']['total_mensual'] as $total) {
        $total_todos_meses = 0;
        $total_todos_meses += $total;
        $total_acumulado1 += $total_todos_meses;
    }
    $total_acumulado2 = 0;
    foreach ($resultado['compras_totales_organico']['total_mensual'] as $total) {
        $total_todos_meses = 0;
        $total_todos_meses += $total;
        $total_acumulado2 += $total_todos_meses;
    }

    array_push($resultado['compras_totales']['total_mensual'], $total_acumulado1);
    array_push($resultado['compras_totales_organico']['total_mensual'], $total_acumulado2);


    /*COMPRAS TOTALES CERA*/
    $salidas_cera_meses = array_fill(0, 12, 0);
    $salidas_cera_meses_organico = array_fill(0, 12, 0);
    // Salidas del inventario convencional
    $info = new stdClass();
    if ($mes && $meses) {
        for ($i = $mes; $i <= $meses; $i++) {
            $info->opcion = '2';
            $info->mes = $i;
            $info->tipoCera = '1';
            $resultadoInventario_cera = realizarFuncionesInventarioCera($info);
            $salidas_cera_meses[($i - 1)] = $resultadoInventario_cera['encabezado']['totalSalidas'];
            $info->tipoCera = '2';
            $resultadoInventario_cera_organico = realizarFuncionesInventarioCera($info);
            $salidas_cera_meses_organico[($i - 1)] = $resultadoInventario_cera_organico['encabezado']['totalSalidas'];
        }
    } else if ($mes) {
        $info->opcion = '2';
        $info->mes = $mes;
        $info->tipoCera = '1';
        $resultadoInventario_cera = realizarFuncionesInventarioCera($info);
        $salidas_cera_meses[($mes - 1)] = $resultadoInventario_cera['encabezado']['totalSalidas'];
        $info->tipoCera = '2';
        $resultadoInventario_cera_organico = realizarFuncionesInventarioCera($info);
        $salidas_cera_meses_organico[($mes - 1)] = $resultadoInventario_cera_organico['encabezado']['totalSalidas'];
    } else {
        for ($i = 1; $i <= 12; $i++) {
            $info->opcion = '2';
            $info->mes = $i;
            $info->tipoCera = '1';
            $resultadoInventario_cera = realizarFuncionesInventarioCera($info);
            $salidas_cera_meses[($i - 1)] = $resultadoInventario_cera['encabezado']['totalSalidas'];
            $info->tipoCera = '2';
            $resultadoInventario_cera_organico = realizarFuncionesInventarioCera($info);
            $salidas_cera_meses_organico[($i - 1)] = $resultadoInventario_cera_organico['encabezado']['totalSalidas'];
        }
    }

    foreach ($resultado['meses'] as $indice => $mesIterando) {
        if (isset($salidas_cera_meses[$indice])) {
            $resultado['compras_totales_cera']['total_mensual'][$indice] = $salidas_cera_meses[$indice];
        }
        if (isset($salidas_cera_meses_organico[$indice])) {
            $resultado['compras_totales_cera_organico']['total_mensual'][$indice] = $salidas_cera_meses_organico[$indice];
        }
    }
    $total_acumulado_cera = 0;
    foreach ($resultado['compras_totales_cera']['total_mensual'] as $total) {
        $total_todos_meses = 0;
        $total_todos_meses += $total;
        $total_acumulado_cera += $total_todos_meses;
    }
    $total_acumulado_cera_organico = 0;
    foreach ($resultado['compras_totales_cera_organico']['total_mensual'] as $total) {
        $total_todos_meses = 0;
        $total_todos_meses += $total;
        $total_acumulado_cera_organico += $total_todos_meses;
    }
    array_push($resultado['compras_totales_cera']['total_mensual'], $total_acumulado_cera);
    array_push($resultado['compras_totales_cera_organico']['total_mensual'], $total_acumulado_cera_organico);

        /*COMPRAS TOTALES PRODUCTOS APICOLAS*/
    $salidas_apicola_meses = array_fill(0, 12, 0);
    $datosApicola = new stdClass();
    if ($mes && $meses) {
        for ($i = $mes; $i <= $meses; $i++) {
            $datosApicola->opcion = '2';
            $datosApicola->mes = $i;
            $resultadoInventario_apicola = realizarFuncionesInventarioApicola($datosApicola);
            $salidas_apicola_meses[($i - 1)] = $resultadoInventario_apicola['totalImporteSalida'];
        }
    } else if ($mes) {
        $datosApicola->opcion = '2';
        $datosApicola->mes = $mes;
        $resultadoInventario_apicola = realizarFuncionesInventarioApicola($datosApicola);
        $salidas_apicola_meses[($mes - 1)] = $resultadoInventario_apicola['totalImporteSalida'];
    } else {
        for ($i = 1; $i <= 12; $i++) {
            $datosApicola->opcion = '2';
            $datosApicola->mes = $i;
            $resultadoInventario_apicola = realizarFuncionesInventarioApicola($datosApicola);
            $salidas_apicola_meses[($i - 1)] = $resultadoInventario_apicola['totalImporteSalida'];
        }
    }

    foreach ($resultado['meses'] as $indice => $mesIterando) {
        if (isset($salidas_apicola_meses[$indice])) {
            $resultado['compras_totales_apicolas']['total_mensual'][$indice] = $salidas_apicola_meses[$indice];
        }
    }
    $total_acumulado_apicola = 0;
    foreach ($resultado['compras_totales_apicolas']['total_mensual'] as $total) {
        $total_todos_meses = 0;
        $total_todos_meses += $total;
        $total_acumulado_apicola += $total_todos_meses;
    }
    array_push($resultado['compras_totales_apicolas']['total_mensual'], $total_acumulado_apicola);


    return $resultado;

}