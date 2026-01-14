<?php
// include_once '../../DAOConeccion/conePDO.php';
// include_once './bg.saldoFinalBancos.php';
// $pdo = new conePDO();
// $con = $pdo->conectar();

// // Inicializar el arreglo

// Obtiene el saldo de el campo solicitado
function valorDelCampo($campo)
{
    global $con;
    $valor = 0;
    $querySeleccionaValor = $con->prepare("SELECT valor FROM valoresbalancegeneral WHERE idCampo = :campo;");
    $querySeleccionaValor->bindParam(':campo', $campo);
    $querySeleccionaValor->execute();
    if ($querySeleccionaValor == false) {
        throw new Exception();
    }

    $resultado = $querySeleccionaValor->fetch(PDO::FETCH_ASSOC);

    if ($resultado['valor']) {
        $valor = $resultado['valor'];
    }

    return $valor;
}

/**
 * Comprueba que no exista errores
 * en la ejecución de la consulta
 */

function obtenerBalanceGeneral($mes = false, $meses = false, $xls = false)
{
    global $con;
    $resultado = array(
        'activo' => array(
            'circulante' => array(
                'conceptos' => array(),
                'total' => 0
            ),
            'activos' => array(
                'clasificaciones' => array(),
                'total' => 0
            ),
            'diferido' => array(
                'conceptos' => array(),
                'total' => 0
            ),
            'total_activo' => 0
        ),
        'pasivo' => array(
            'corto_plazo' => array(
                'conceptos' => array(),
                'total' => 0
            ),
            'largo_plazo' => array(
                'conceptos' => array(),
                'total' => 0
            ),
            'total_pasivo' => 0
        ),
        'capital' => array(
            'conceptos' => array(),
            'total_capital' => 0
        ),
        'pasivo_capital' => 0,
        'prueba' => array()
    );

    // Circulante

    // Efectivo y bancos
    $resultado_bancos = obtenerSaldosBancos($mes, $meses);
    $resultado_cajachica = obtenerSaldosCajaChica($mes, $meses);

    $totalEfectivoYBancos = $resultado_bancos['saldoFinal'] + $resultado_cajachica['saldoFinal'];

    $nuevo_concepto_circulante = new stdClass();
    $nuevo_concepto_circulante->nombre = 'EFECTIVO Y BANCOS';
    $nuevo_concepto_circulante->total = $totalEfectivoYBancos;
    $resultado['activo']['circulante']['total'] += $nuevo_concepto_circulante->total;
    array_push($resultado['activo']['circulante']['conceptos'], $nuevo_concepto_circulante);

    // Cuentas por cobrar
    $nuevo_concepto_circulante = new stdClass();
    $nuevo_concepto_circulante->nombre = 'CUENTAS POR COBRAR';

    $nuevo_concepto_circulante->editable = 'true';
    $nuevo_concepto_circulante->id = 'cuentasPorCobrar';
    $nuevo_concepto_circulante->class = 'editables';

    $nuevo_concepto_circulante->total = valorDelCampo($nuevo_concepto_circulante->id);
    $resultado['activo']['circulante']['total'] += $nuevo_concepto_circulante->total;
    array_push($resultado['activo']['circulante']['conceptos'], $nuevo_concepto_circulante);

    // Nuevo*: Deudores
    $nuevo_concepto_circulante = new stdClass();
    $nuevo_concepto_circulante->nombre = 'DEUDORES';

    $nuevo_concepto_circulante->editable = 'true';
    $nuevo_concepto_circulante->id = 'deudores';
    $nuevo_concepto_circulante->class = 'editables';

    $nuevo_concepto_circulante->total = valorDelCampo($nuevo_concepto_circulante->id);
    $resultado['activo']['circulante']['total'] += $nuevo_concepto_circulante->total;
    array_push($resultado['activo']['circulante']['conceptos'], $nuevo_concepto_circulante);

    // anticipo por compras y gts
    $saldoDeudores = obtenerSaldosDeudores($mes, $meses);

    $nuevo_concepto_circulante = new stdClass();
    $nuevo_concepto_circulante->nombre = 'ANTICIPO POR COMPRAS Y GTS';
    if ($saldoDeudores['conciliacion'] > 0) {
        $nuevo_concepto_circulante->total = abs($saldoDeudores['conciliacion']);
    } elseif ($saldoDeudores['conciliacion'] <= 0) {
        $nuevo_concepto_circulante->total = 0;
    }
    $resultado['activo']['circulante']['total'] += $nuevo_concepto_circulante->total;
    array_push($resultado['activo']['circulante']['conceptos'], $nuevo_concepto_circulante);

    // Obtener los resultados de los inventarios

    $resultados_inventarios = obtenerSaldosInventarios();

    // inventarios miel convencional
    $nuevo_concepto_circulante = new stdClass();
    $nuevo_concepto_circulante->nombre = 'INVENTARIOS MIEL CONVENCIONAL';
    $nuevo_concepto_circulante->total = $resultados_inventarios['almacen'];
    $resultado['activo']['circulante']['total'] += $nuevo_concepto_circulante->total;
    array_push($resultado['activo']['circulante']['conceptos'], $nuevo_concepto_circulante);

    // inventarios miel organico
    $nuevo_concepto_circulante = new stdClass();
    $nuevo_concepto_circulante->nombre = 'INVENTARIOS MIEL ORGÁNICA';
    $nuevo_concepto_circulante->total = $resultados_inventarios['almacen_organico'];
    $resultado['activo']['circulante']['total'] += $nuevo_concepto_circulante->total;
    array_push($resultado['activo']['circulante']['conceptos'], $nuevo_concepto_circulante);

    // Inventarios de cera convencional y organica

    $nuevo_concepto_circulante = new stdClass();
    $nuevo_concepto_circulante->nombre = 'INVENTARIO CERA CONVENCIONAL';
    $nuevo_concepto_circulante->total = $resultados_inventarios['cera'];
    $resultado['activo']['circulante']['total'] += $nuevo_concepto_circulante->total;
    array_push($resultado['activo']['circulante']['conceptos'], $nuevo_concepto_circulante);

    $nuevo_concepto_circulante = new stdClass();
    $nuevo_concepto_circulante->nombre = 'INVENTARIO CERA ORGÁNICA';
    $nuevo_concepto_circulante->total = $resultados_inventarios['cera_organico'];
    $resultado['activo']['circulante']['total'] += $nuevo_concepto_circulante->total;
    array_push($resultado['activo']['circulante']['conceptos'], $nuevo_concepto_circulante);

    // otros inventarios
    $nuevo_concepto_circulante = new stdClass();
    $nuevo_concepto_circulante->nombre = 'OTROS INVENTARIOS';
    $nuevo_concepto_circulante->total = $resultados_inventarios['otros'];
    $resultado['activo']['circulante']['total'] += $nuevo_concepto_circulante->total;
    array_push($resultado['activo']['circulante']['conceptos'], $nuevo_concepto_circulante);

    // impuestos a favor
    $nuevo_concepto_circulante = new stdClass();
    $nuevo_concepto_circulante->nombre = 'IMPUESTOS A FAVOR';
    $nuevo_concepto_circulante->editable = 'true';
    $nuevo_concepto_circulante->id = 'impuestosAFavor';
    $nuevo_concepto_circulante->class = 'editables';
    $nuevo_concepto_circulante->total = valorDelCampo($nuevo_concepto_circulante->id);
    $resultado['activo']['circulante']['total'] += $nuevo_concepto_circulante->total;
    array_push($resultado['activo']['circulante']['conceptos'], $nuevo_concepto_circulante);


    // Fijos

    // Obtener primero las clasificaciones de los activos

    $sqlClasificaciones = $con->prepare("SELECT idClasificacion, clasificacion FROM clasificaciones");
    $sqlClasificaciones->execute();
    comprobarEjecucionPdo($sqlClasificaciones);

    $clasificaciones_activos = $sqlClasificaciones->fetchAll(PDO::FETCH_ASSOC);

    // Iteramos las clasificaciones, por cada una debemos consultar la suma de los activos que le pertenecen

    foreach ($clasificaciones_activos as $clasificacion) {
        $nueva_clasificacion = new stdClass();
        $nueva_clasificacion->nombre = $clasificacion['clasificacion'];
        $nueva_clasificacion->total = 0;

        $consultaTotalClasificacion = $con->prepare("SELECT SUM(costo) as total FROM equipos WHERE idClasificacion = :idClasificacion");
        $consultaTotalClasificacion->bindParam(':idClasificacion', $clasificacion['idClasificacion']);
        $consultaTotalClasificacion->execute();
        comprobarEjecucionPdo($consultaTotalClasificacion);
        $total_clasificacion = $consultaTotalClasificacion->fetch(PDO::FETCH_ASSOC);
        $nueva_clasificacion->total = intval($total_clasificacion['total']);
        if ($nueva_clasificacion->total && $nueva_clasificacion->total > 0) {
            array_push($resultado['activo']['activos']['clasificaciones'], $nueva_clasificacion);
            $resultado['activo']['activos']['total'] += $nueva_clasificacion->total;
        }
    }



    // Calcular los impuestos pagados en caja chica y en bancos

    // ya no se va a tomar de las cuentas fijas, se deja en cero otra vez (No está en el sistema)
    // $sqlImpuestos = $con->prepare("SELECT SUM(total) as total FROM (
    //     SELECT SUM(cantidad) as total FROM `cajachicadetalle` WHERE idMovimiento = 3 AND idConcepto = 15
    //     UNION
    //     SELECT SUM(cantidad) as total FROM `auxiliardebancos` WHERE ingresoEgreso = 1 AND idSubcuenta = 15) resultados");

    // $sqlImpuestos->execute();
    // comprobarEjecucionPdo($sqlImpuestos);
    // $resultado_impuestos = $sqlImpuestos->fetch(PDO::FETCH_ASSOC);
    
    // Crear un nuevo concepto de activo diferido para mandarlo al arreglo
    $nuevo_diferido = new stdClass();
    $nuevo_diferido->nombre = 'IMPUESTO ESTATAL Y FEDERAL';
    $nuevo_diferido->editable = 'true';
    $nuevo_diferido->id = 'impuestoEstatalFederal';
    $nuevo_diferido->class = 'editables';
    $nuevo_diferido->total = valorDelCampo($nuevo_diferido->id);
    $resultado['activo']['diferido']['total'] += $nuevo_diferido->total;
    array_push($resultado['activo']['diferido']['conceptos'], $nuevo_diferido);

    $nuevo_diferido = new stdClass();
    $nuevo_diferido->nombre = 'DEPÓSITOS EN GARANTÍA';
    $nuevo_diferido->editable = 'true';
    $nuevo_diferido->id = 'depositosEnGarantia';
    $nuevo_diferido->class = 'editables';
    $nuevo_diferido->total = valorDelCampo($nuevo_diferido->id);
    $resultado['activo']['diferido']['total'] += $nuevo_diferido->total;
    array_push($resultado['activo']['diferido']['conceptos'], $nuevo_diferido);



    // P A S I V O S

    // Pasivo a corto plazo
    // 1.Acreedores diversos
    $nuevo_concepto_pasivo = new stdClass();
    $nuevo_concepto_pasivo->nombre = 'ACREEDORES DIVERSOS';
    $nuevo_concepto_pasivo->editable = 'true';
    $nuevo_concepto_pasivo->id = 'acreedoresDiversos';
    $nuevo_concepto_pasivo->class = 'editables';
    $nuevo_concepto_pasivo->total = valorDelCampo($nuevo_concepto_pasivo->id);

    $resultado['pasivo']['corto_plazo']['total'] += $nuevo_concepto_pasivo->total;
    array_push($resultado['pasivo']['corto_plazo']['conceptos'], $nuevo_concepto_pasivo);

    // 2. Acreedores bancarios
    $nuevo_concepto_pasivo = new stdClass();
    $nuevo_concepto_pasivo->nombre = 'ACREEDORES BANCARIOS';
    $nuevo_concepto_pasivo->editable = 'true';
    $nuevo_concepto_pasivo->id = 'acreedoresBancarios';
    $nuevo_concepto_pasivo->class = 'editables';
    $nuevo_concepto_pasivo->total = valorDelCampo($nuevo_concepto_pasivo->id);

    // Calcular el total de los acreedores bancarios (EGRESO)
    // Dijo Ibis que no está en el sistema, se deja en ceros
    // $sqlPasivoAcreedoresBancarios = $con->prepare("SELECT SUM(cantidad) AS total
    // FROM auxiliardebancos
    // WHERE tipoDePersona = 9 AND ingresoEgreso = 1");
    // $sqlPasivoAcreedoresBancarios->execute();
    // comprobarEjecucionPdo($sqlPasivoAcreedoresBancarios);
    // $resultadoAcreedoresBancarios = $sqlPasivoAcreedoresBancarios->fetch(PDO::FETCH_ASSOC);
    // $nuevo_concepto_pasivo->total = $resultadoAcreedoresBancarios['total'];

    $resultado['pasivo']['corto_plazo']['total'] += $nuevo_concepto_pasivo->total;
    array_push($resultado['pasivo']['corto_plazo']['conceptos'], $nuevo_concepto_pasivo);

    // 3. Apicultores
    $nuevo_concepto_pasivo = new stdClass();
    $nuevo_concepto_pasivo->nombre = 'APICULTORES';

    if ($saldoDeudores['conciliacion'] < 0) {
        $nuevo_concepto_pasivo->total = abs($saldoDeudores['conciliacion']);
    } elseif ($saldoDeudores['conciliacion'] >= 0) {
        $nuevo_concepto_pasivo->total = 0;
    }

    $resultado['pasivo']['corto_plazo']['total'] += $nuevo_concepto_pasivo->total;
    array_push($resultado['pasivo']['corto_plazo']['conceptos'], $nuevo_concepto_pasivo);

    // 4. Impuestos por pagar
    $nuevo_concepto_pasivo = new stdClass();
    $nuevo_concepto_pasivo->nombre = 'IMPUESTOS POR PAGAR';
    $nuevo_concepto_pasivo->editable = 'true';
    $nuevo_concepto_pasivo->id = 'impuestosPorPagar';
    $nuevo_concepto_pasivo->class = 'editables';
    $nuevo_concepto_pasivo->total = valorDelCampo($nuevo_concepto_pasivo->id);

    $resultado['pasivo']['corto_plazo']['total'] += $nuevo_concepto_pasivo->total;
    array_push($resultado['pasivo']['corto_plazo']['conceptos'], $nuevo_concepto_pasivo);

    // 5. IVA POR TRASLADAR
    $nuevo_concepto_pasivo = new stdClass();
    $nuevo_concepto_pasivo->nombre = 'IVA POR TRASLADAR';
    $nuevo_concepto_pasivo->editable = 'true';
    $nuevo_concepto_pasivo->id = 'ivaPorTrasladar';
    $nuevo_concepto_pasivo->class = 'editables';
    $nuevo_concepto_pasivo->total = valorDelCampo($nuevo_concepto_pasivo->id);

    $resultado['pasivo']['corto_plazo']['total'] += $nuevo_concepto_pasivo->total;
    array_push($resultado['pasivo']['corto_plazo']['conceptos'], $nuevo_concepto_pasivo);


    // PASIVO A LARGO PLAZO

        // 1. Acreedores bancarios
    $nuevo_concepto_pasivo = new stdClass();
    $nuevo_concepto_pasivo->nombre = 'ACREEDORES BANCARIOS';
    $nuevo_concepto_pasivo->editable = 'true';
    $nuevo_concepto_pasivo->id = 'acreedoresBancariosLargoPlazo';
    $nuevo_concepto_pasivo->class = 'editables';
    $nuevo_concepto_pasivo->total = valorDelCampo($nuevo_concepto_pasivo->id);

    $resultado['pasivo']['largo_plazo']['total'] += $nuevo_concepto_pasivo->total;
    array_push($resultado['pasivo']['largo_plazo']['conceptos'], $nuevo_concepto_pasivo);

    // CAPITAL CONTABLE

    // Capital social
    $nuevo_concepto_capital = new stdClass();
    $nuevo_concepto_capital->nombre = 'CAPITAL SOCIAL';
    $nuevo_concepto_capital->editable = 'true';
    $nuevo_concepto_capital->id = 'capitalSocial';
    $nuevo_concepto_capital->class = 'editables';
    $nuevo_concepto_capital->total = valorDelCampo($nuevo_concepto_capital->id);

    $resultado['capital']['total_capital'] += $nuevo_concepto_capital->total;
    array_push($resultado['capital']['conceptos'], $nuevo_concepto_capital);

    // Resultado del ejercicio 
    $nuevo_concepto_capital = new stdClass();
    $nuevo_concepto_capital->nombre = 'RESULTADO DE EJERS. ANTS.';
    $nuevo_concepto_capital->editable = 'true';
    $nuevo_concepto_capital->id = 'resultadoEjercicioAnts';
    $nuevo_concepto_capital->class = 'editables';
    $nuevo_concepto_capital->total = valorDelCampo($nuevo_concepto_capital->id);

    $resultado['capital']['total_capital'] += $nuevo_concepto_capital->total;
    array_push($resultado['capital']['conceptos'], $nuevo_concepto_capital);

    // Utilidad del ejercicio
    $nuevo_concepto_capital = new stdClass();
    $nuevo_concepto_capital->nombre = 'UTILIDAD DEL EJERCICIO';
    $nuevo_concepto_capital->editable = 'true';
    $nuevo_concepto_capital->id = 'utilidadDelEjercicio';
    $nuevo_concepto_capital->class = 'editables';
    $nuevo_concepto_capital->total = valorDelCampo($nuevo_concepto_capital->id);

    $resultado['capital']['total_capital'] += $nuevo_concepto_capital->total;
    array_push($resultado['capital']['conceptos'], $nuevo_concepto_capital);

    /**
     * C A L C U L A R  L O S  T O T A L E S   D E   A C T I V O S,
     * P A S I V O S   Y  C A P I T A L  C O N T A B L E
     */

    //  Total activos
    $resultado['activo']['total_activo'] =
        $resultado['activo']['circulante']['total']
        + $resultado['activo']['activos']['total']
        + $resultado['activo']['diferido']['total'];

    // Total pasivos

    $resultado['pasivo']['total_pasivo'] =
        $resultado['pasivo']['corto_plazo']['total']
        + $resultado['pasivo']['largo_plazo']['total'];

    
    // Total capital contable se calcula en el momento ya que no tiene
    // varios subconceptos como activos y pasivos

    // CALCULAR LA SUMA DE PASIVOS CON ACTIVOS

    $resultado['pasivo_capital'] = $resultado['pasivo']['total_pasivo'] + $resultado['capital']['total_capital'];

    return $resultado;

}

// if (!isset($_GET['descargar'])) {
//     try {
//         $balance_general = obtenerBalanceGeneral();
//         echo json_encode($balance_general);
//     } catch (Exception $e) {
//         echo json_encode(['error' => true, 'message' => $e->getMessage() . '. ' . $e->getLine()]);
//     }
// }