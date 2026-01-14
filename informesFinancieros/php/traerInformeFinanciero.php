<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();
$postdata = file_get_contents('php://input');

// Insertamos el PHP que nos va a traer la estrucutura
include_once './informesFinancieros/obtenerCobranzaClientes.php';
include_once './informesFinancieros/flujoDeEfectivo.php';
include_once './informesFinancieros/balanceGeneral.php';
include_once './informesFinancieros/estadoDeResultados.php';
include_once './informesFinancieros/acumuladoDeGastos.php';
include_once './obtenerFlujoEfectivo.php';
include_once './obtenerBalanceGeneral.php';
include_once './obtenerEstadoDeResultados.php';
include_once './bg.saldoFinalBancos.php';
include_once './bg.saldosInventarios.php';
include_once './bg.saldoFinalCajaChica.php';
include_once './bg.saldoDeudores.php';

include_once '../../controlAdministrativo/php/inventarioComprasMiel.php';
include_once '../../controlAdministrativo/php/inventarioCera.php';
include_once '../../controlAdministrativo/php/inventarioProductosApicola.php';

// 1. Revisar parámetros: Acumulado, por mes, por meses, id del informe

// 1.a Si es acumulado, enviar por medio de un parámetro GET la palabra "acumulado"
// 1.b Si es por mes, enviar por medio de un parámetro GET la palabra "mes"
// 1.c Si es por rango de meses, enviar por medio de un parámetro GET la palabra "meses"

// En cualquiera de los tres casos, SIEMPRE se deberá recibir por medio de POST, un arreglo:
// El primer índice del arreglo, SIEMPRE corresponderá al id del informe que se esté solicitando
// Si el informe es por mes, se ocupará el segundo índice
// Y si el informe corresponde a rango de meses, se ocupará hasta el tercer íncide

// 2. Revisar si quiere descargar los datos o descargar un .xls
// 2.a Si requiere descargar un .xls, enviar por medio de un parámetro de tipo GET la palabra "xls"
// 2.b Si requiere descargar solo los datos, no se envia ningún dato EXTRA por medio de GET

// = = = OPCIONAL = = =

// Cuando se quiere descargar un xls, lo primero es hacer una petición HTTP REQUEST al archivo,
// para verificar que no exista algún error:
// Las respuestas para las peticiones HTTP REQUEST tendrán la siguiente estructura:
// { error: boolean, message: string, data(en caso de vista):object }

function agregarEncabezadoExcel($mes = false, $meses = false, $nombreInforme)
{

    $titulo_fecha = '';


    if ($mes && $meses) {

        $fecha_del_mes_1 = explode('-', date('d-m-Y'));
        $fecha_del_mes_1[0] = '01';
        $fecha_del_mes_1[1] = $mes;
        $fecha_del_mes_1 = join('-', $fecha_del_mes_1);

        $fecha_del_mes_2 = explode('-', date('d-m-Y'));
        $fecha_del_mes_2[1] = $meses;
        $fecha_del_mes_2 = join('-', $fecha_del_mes_2);

        $ultimo_dia_mes_dos = date("t-m-Y", strtotime($fecha_del_mes_2));
        $titulo_fecha = $nombreInforme . ' del ' . $fecha_del_mes_1 . ' al ' . $ultimo_dia_mes_dos;

    } else if ($mes) {

        $fecha_del_mes = explode('-', date('d-m-Y'));
        $fecha_del_mes[1] = $mes;
        $fecha_del_mes = join('-', $fecha_del_mes);
        $ultimo_dia = date("t-m-Y", strtotime($fecha_del_mes));
        $titulo_fecha = $nombreInforme . ' al ' . $ultimo_dia;

    } else {

        $fecha_hoy = date('d-m-Y');
        $titulo_fecha = $nombreInforme . ' al ' . $fecha_hoy;

    }

    return $encabezado = '<table>
        <tr style="text-align: center;">
            <td><b>OAXACA MIEL S.A. DE C.V.</b></td>
        </tr>

        <tr style="text-align: center;">
            <td><b> </b></td>
        </tr>

        <tr style="text-align: center;">
            <td><b>' . strtoupper('Carretera Merida - Cancun Km 7.5 S/N, CP 97370') . '</b></td>
        </tr>

        <tr style="text-align: center;">
            <td><b>' . strtoupper('Colonia San Pedro Noh Pat, Kanasin, Yucatan') . '</b></td>
        </tr>

        <tr style="text-align: center;">
            <td><b>Tel: (999) 9.88.09.90</b></td>
        </tr>
        <tr style="text-align: center;">
            <td><b>' . strtoupper($nombreInforme) . '</b></td>
        </tr>
        <tr style="text-align: center;">
            <td><b>' . strtoupper($titulo_fecha) . '</b></td>
        </tr>

        <tr></tr>
        <tr></tr>
    </table>';
}

function verificarInformeFinanciero($id)
{
    global $con;
    $return = false;
    $sql = $con->prepare("SELECT idInforme FROM informesfinancieros WHERE idInforme = '" . $id . "'");
    $sql->execute();

    if (!$sql) {
        throw new Exception($con->errorInfo());
    } else {
        $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);
        // Verificar si trajo algún dato
        if (count($resultado) == 1) {
            $return = true;
        } else {
            throw new Exception('El tipo de informe solicitado no es válido');
        }
    }

    return $return;
}

function obtenerAcumulado($idInforme, $mes = false, $meses = false, $xls = false)
{
    global $con;

    $resultado = array(
        'meses' => array(),
        'cuentas' => array(),
        'sumaTotal' => array(),
        'totalDeIngresos' => array(),
        'totalDeEgresos' => array(),
        'totalAcumulado' => 0,
        'acumuladoIngresos' => 0,
        'acumuladoEgresos' => 0,
    );
    if ($mes && $meses) {
        $sqlMeses = $con->prepare("SELECT idMes, mes FROM meses WHERE idMes BETWEEN '" . $mes . "' AND '" . $meses . "' ORDER BY idMes");
    } else if ($mes) {
        $sqlMeses = $con->prepare("SELECT idMes, mes FROM meses WHERE idMes = '" . $mes . "' ORDER BY idMes");
    } else {
        // $sqlMeses = $con->prepare("SELECT idMes, mes FROM meses WHERE idMes <= MONTH(CURRENT_DATE()) ORDER BY idMes");
        $sqlMeses = $con->prepare("SELECT idMes, mes FROM meses ORDER BY idMes");    
    }

    $sqlMeses->execute();
    $resultado['meses'] = $sqlMeses->fetchAll(PDO::FETCH_ASSOC);

    $sqlCuentas = $con->prepare("SELECT c.idCuentaConcepto, UPPER(c.cuenta) AS cuenta, ingresoEgreso 
                          FROM cuentas c 
                          LEFT JOIN relacioncuentainformes r ON r.idCuentaConcepto = c.idCuentaConcepto
                          WHERE r.idInforme = '" . $idInforme . "' ORDER BY r.orden ASC");
    $sqlCuentas->execute();
    $listaCuentas = $sqlCuentas->fetchAll(PDO::FETCH_ASSOC);
    foreach ($listaCuentas as $dato) {
        $nueva_cuenta = new stdClass();
        $nueva_cuenta->idCuentaConcepto = $dato['idCuentaConcepto'];
        $nueva_cuenta->cuenta = $dato['cuenta'];
        $nueva_cuenta->tipo = $dato['ingresoEgreso'];
        $nueva_cuenta->gastosAup = array();
        $nueva_cuenta->sumaPorMes = array();
        $nueva_cuenta->acumuladoPorCuenta = 0;
        $nueva_cuenta->acumuladoEgreso = 0;
        $nueva_cuenta->acumuladoIngreso = 0;

        $sqlSubcuentas = $con->prepare("SELECT idSubcuenta, UPPER(subcuenta) as subcuenta FROM subcuentas WHERE idCuentaConcepto = :idCuenta");
        $sqlSubcuentas->bindParam(':idCuenta', $nueva_cuenta->idCuentaConcepto);
        $sqlSubcuentas->execute();
        $listaSubcuentas = $sqlSubcuentas->fetchAll(PDO::FETCH_ASSOC);

        foreach ($listaSubcuentas as $subcuenta) {
            $nueva_subcuenta = new stdClass();
            $nueva_subcuenta->idSubcuenta = $subcuenta['idSubcuenta'];
            $nueva_subcuenta->nombre = $subcuenta['subcuenta'];
            $nueva_subcuenta->totales = array();
            $nueva_subcuenta->total = 0;
            $nueva_subcuenta->esEncabezado = true;
            array_push($nueva_cuenta->gastosAup, $nueva_subcuenta);
            foreach ($resultado['meses'] as $indice => $mesIterando) {
                $nueva_cuenta->sumaPorMes[$indice] = isset($nueva_cuenta->sumaPorMes[$indice]) ? $nueva_cuenta->sumaPorMes[$indice] : 0;
                $resultado['sumaTotal'][$indice] = isset($resultado['sumaTotal'][$indice]) ? $resultado['sumaTotal'][$indice] : 0;
                $resultado['totalDeIngresos'][$indice] = isset($resultado['totalDeIngresos'][$indice]) ? $resultado['totalDeIngresos'][$indice] : 0;
                $resultado['totalDeEgresos'][$indice] = isset($resultado['totalDeEgresos'][$indice]) ? $resultado['totalDeEgresos'][$indice] : 0;
                $sqlTotales = $con->prepare("SELECT SUM(total) AS total, mes
                FROM(SELECT total, mes
                FROM(SELECT SUM(cantidad) AS total, SUBSTR(fecha FROM 6 FOR 2 ) AS mes
                FROM auxiliardebancos WHERE tipoMovimiento = :idCuentaConcepto AND idSubcuenta = :idSubcuenta
                AND SUBSTR(fecha FROM 6 FOR 2) = CONVERT(:idMes, UNSIGNED INTEGER) AND (tipoDePersona != 6 or (tipoDePersona = 6 AND nombreDe NOT IN (SELECT idCliente FROM clientes WHERE flujoEfectivo = 1)))
                ) AS bancos
                UNION
                SELECT SUM(cantidad) AS total, mes
                FROM (SELECT CASE WHEN d.cantidad IS NOT NULL THEN d.cantidad ELSE importe END AS cantidad,  
                SUBSTR(e.fecha FROM 6 FOR 2) AS mes
                FROM cajachicadetalle d 
                LEFT JOIN cajachica e ON e.idCajaChica = d.idCajaChica
                WHERE d.idMovimiento = :idCuentaConcepto AND d.idConcepto = :idSubcuenta
                AND SUBSTR(fecha FROM 6 FOR 2) = CONVERT(:idMes, UNSIGNED INTEGER) AND (e.tipoDeCliente != 6 or (e.tipoDeCliente = 6 AND e.nombre NOT IN (SELECT idCliente FROM clientes WHERE flujoEfectivo = 1)))
                ) AS caja) AS todo");
                $sqlTotales->bindParam(':idCuentaConcepto', $nueva_cuenta->idCuentaConcepto);
                $sqlTotales->bindParam(':idSubcuenta', $subcuenta['idSubcuenta']);
                $sqlTotales->bindParam(':idMes', $mesIterando['idMes']);
                $sqlTotales->execute();
                $resultado_subcuenta_mes = $sqlTotales->fetch(PDO::FETCH_ASSOC);
                $nueva_cuenta->sumaPorMes[$indice] += $resultado_subcuenta_mes['total'];
                $nueva_subcuenta->total += $resultado_subcuenta_mes['total'];
                $resultado['sumaTotal'][$indice] += $resultado_subcuenta_mes['total'];
                if ($nueva_cuenta->tipo == '0') {
                    $resultado['totalDeIngresos'][$indice] += $resultado_subcuenta_mes['total'];
                } else if ($nueva_cuenta->tipo == '1') {
                    $resultado['totalDeEgresos'][$indice] += $resultado_subcuenta_mes['total'];
                }
                array_push($nueva_subcuenta->totales, $resultado_subcuenta_mes['total']);
            }

            $sqlSubSubcuentas = $con->prepare("SELECT idSubSubcuenta, subSubcuenta FROM subsubcuentas WHERE idSubcuenta = :idSubcuenta");
            $sqlSubSubcuentas->bindParam(':idSubcuenta', $subcuenta['idSubcuenta']);
            $sqlSubSubcuentas->execute();
            $listaSubSubcuentas = $sqlSubSubcuentas->fetchAll(PDO::FETCH_ASSOC);

            if (count($listaSubSubcuentas) > 0) {
                foreach ($listaSubSubcuentas as $subSubcuenta) {
                    $nueva_subSubcuenta = new stdClass();
                    $nueva_subSubcuenta->nombre = $subSubcuenta['subSubcuenta'];
                    $nueva_subSubcuenta->totales = array();
                    $nueva_subSubcuenta->total = 0;
                    foreach ($resultado['meses'] as $indice => $mesIterando) {
                        $sqlTotalesSub = $con->prepare("SELECT SUM(total) AS total, mes
                        FROM(SELECT total, mes
                        FROM(SELECT SUM(cantidad) AS total, SUBSTR(fecha FROM 6 FOR 2 ) AS mes
                        FROM auxiliardebancos WHERE idSubcuenta = :idSubcuenta AND idSubsubcuenta = :idSubSubcuenta
                        AND SUBSTR(fecha FROM 6 FOR 2) = CONVERT(:idMes, UNSIGNED INTEGER) AND (tipoDePersona != 6 or (tipoDePersona = 6 AND nombreDe NOT IN (SELECT idCliente FROM clientes WHERE flujoEfectivo = 1)))
                        GROUP BY idSubsubcuenta) AS bancos
                        UNION
                        SELECT SUM(cantidad) AS total, mes
                        FROM (SELECT CASE WHEN d.cantidad IS NOT NULL THEN d.cantidad ELSE importe END AS cantidad,  
                        SUBSTR(e.fecha FROM 6 FOR 2) AS mes
                        FROM cajachicadetalle d 
                        LEFT JOIN cajachica e ON e.idCajaChica = d.idCajaChica
                        WHERE d.idConcepto = :idSubcuenta AND idSubConcepto = :idSubSubcuenta
                        AND SUBSTR(fecha FROM 6 FOR 2) = CONVERT(:idMes, UNSIGNED INTEGER) AND (e.tipoDeCliente != 6 or (e.tipoDeCliente = 6 AND e.nombre NOT IN (SELECT idCliente FROM clientes WHERE flujoEfectivo = 1)))
                        ) AS caja) AS todo");
                        $sqlTotalesSub->bindParam(':idSubSubcuenta', $subSubcuenta['idSubSubcuenta']);
                        $sqlTotalesSub->bindParam(':idSubcuenta', $nueva_subcuenta->idSubcuenta);
                        $sqlTotalesSub->bindParam(':idMes', $mesIterando['idMes']);
                        $sqlTotalesSub->execute();
                        $resultado_subcuenta_mesSub = $sqlTotalesSub->fetch(PDO::FETCH_ASSOC);
                        $nueva_subSubcuenta->total += $resultado_subcuenta_mesSub['total'];
                        array_push($nueva_subSubcuenta->totales, $resultado_subcuenta_mesSub['total']);
                    }
                    array_push($nueva_cuenta->gastosAup, $nueva_subSubcuenta);
                }
            } else {
                $nueva_subcuenta->esEncabezado = false;
            }
        }

        foreach ($nueva_cuenta->sumaPorMes as $suma) {
            $nueva_cuenta->acumuladoPorCuenta += $suma;
            if ($nueva_cuenta->tipo == '1') {
                $nueva_cuenta->acumuladoEgreso += $suma;
            } else if ($nueva_cuenta->tipo == '0') {
                $nueva_cuenta->acumuladoIngreso += $suma;
            }
        }
        $resultado['totalAcumulado'] += $nueva_cuenta->acumuladoPorCuenta;
        $resultado['acumuladoIngresos'] += $nueva_cuenta->acumuladoIngreso;
        $resultado['acumuladoEgresos'] += $nueva_cuenta->acumuladoEgreso;
        array_push($resultado['cuentas'], $nueva_cuenta);

    }

    // Hacemos un switch del id del informe para saber a que función llamar
    switch ($idInforme) {

        // Aquí en otros case deberían ir los otros informes
        case '1':
            $estructura = generarXlsAcumuladoDeGastos($mes, $meses, $xls, $resultado);
            if ($xls) {
                $resultado['informeFinancieroEstructura'] = agregarEncabezadoExcel($mes, $meses, 'Acumulado de gastos');
            } else {
                $resultado['informeFinancieroEstructura'] = '';
            }
            $resultado['informeFinancieroEstructura'] .= utf8_encode($estructura['xls']);
            break;
        case '2':
            // Ejemplo con flujo de efectivo
            // Llamaríamos al php que nos trae la estructura (la misma que se puede usar para el excel)
            // y la asigamos a una variable, este es un ejemplo:
            $estructura = generarXlsFlujoEfectivo($mes, $meses, $xls, $resultado['cuentas']);
            // Lo añadimos al arreglo del resultado
            // Hay que codificar a utf8 para que retorne la respuesta
            if ($xls) {
                $resultado['informeFinancieroEstructura'] = agregarEncabezadoExcel($mes, $meses, 'Flujo de efectivo');
            } else {
                $resultado['informeFinancieroEstructura'] = '';
            }
            $resultado['informeFinancieroEstructura'] .= utf8_encode($estructura);
            break;
        case '3': // Estado de Resultados
            // $resultado['prueba'] = realizarFuncionesInventarioMiel('2');
            $estructura = generarXlsEstadoDeResultados($mes, $meses, $xls, $resultado['cuentas']);
            $total_cobranza = $estructura['cobranza'];
            $ultimo_indice = 0;
            foreach ($resultado['totalDeIngresos'] as $indice => $valor) {
                $ultimo_indice = $indice;
                $resultado['totalDeIngresos'][$indice] = $resultado['totalDeIngresos'][$indice] + $total_cobranza[$indice];
            }
            if (count($total_cobranza) > 0) {
                $resultado['acumuladoIngresos'] += $total_cobranza[($ultimo_indice + 1)];
            }
            if ($xls) {
                $resultado['informeFinancieroEstructura'] = agregarEncabezadoExcel($mes, $meses, 'Estado de resultados');
            } else {
                $resultado['informeFinancieroEstructura'] = '';
            }
            $resultado['informeFinancieroEstructura'] .= utf8_encode($estructura['xls']);
            break;
        case '4': // Informe: Balance general
            $estructura = generarXlsBalanceGeneral($mes, $meses, $xls, $resultado['cuentas']);
            if ($xls) {
                $resultado['informeFinancieroEstructura'] = agregarEncabezadoExcel($mes, $meses, 'Balance general');
            } else {
                $resultado['informeFinancieroEstructura'] = '';
            }
            $resultado['informeFinancieroEstructura'] .= utf8_encode($estructura);
            break;
        default:
            $resultado['informeFinancieroEstructura'] = '<hr>';
            break;
    }

    if ($xls) {
        // if (!isset($_SESSION)) {
            session_start();
        // }
        $_SESSION['informeFinanciero'] = $resultado['informeFinancieroEstructura'];
        session_write_close();

        echo json_encode(['xls' => true]);
    } else {
        echo json_encode($resultado);
    }

}

$descargar_xls = isset($_GET['xls']) ? true : false;
try {
    if (!$postdata) {
        throw new Exception('No se recibió los parámetros esperados');
    } else {
        $post = json_decode($postdata);
    }

    // Aquí, verificar que el valor enviado para el informe financiero exista en la tabla de informes financieros

    $verificacion_informe = verificarInformeFinanciero($post[0]);
    if (!$verificacion_informe) {
        throw new Exception('El tipo de informe solicitado no es válido');
    }

    if (isset($_GET['acumulado'])) {

        if (count($post) == 1) {
            obtenerAcumulado($post[0], false, false, $descargar_xls);
        } else {
            throw new Exception('No se recibió los parámetros esperados');
        }


    } else if (isset($_GET['mes'])) {

        if (count($post) == 2) {
            obtenerAcumulado($post[0], $post[1], false, $descargar_xls);
        } else {
            throw new Exception('No se recibió los parámetros esperados');
        }

    } else if (isset($_GET['meses'])) {

        if (count($post) == 3) {
            obtenerAcumulado($post[0], $post[1], $post[2], $descargar_xls);
        } else {
            throw new Exception('No se recibió los parámetros esperados');
        }

    } else {
        throw new Exception('No se recibió los parámetros esperados');
    }

} catch (Exception $e) {
    echo $e->getMessage();
}