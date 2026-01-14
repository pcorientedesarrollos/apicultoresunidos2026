<?php
include_once '../../inventarios/php/obtenerInventarioMiel.php';

function realizarFuncionesInventarioMiel($tipoDeMiel = false)
{
    // Si no recibe el parámetro 'miel' se le asigna por defecto convencional
    if ($tipoDeMiel) {
        $tipoMiel = $tipoDeMiel;
    } else {
        $tipoMiel = isset($_GET['miel']) ? $_GET['miel'] : '1';
    }


    $filtro = $_GET['filtro'];

    if (isset($_GET['acumulado'])) {

        $resultado = calcularInventarioMensual(false, $filtro, false, false, true, false, false, $tipoMiel);
        $idUltimoMes = getUltimoMes($tipoMiel);
        $resultado['comprasDeMielPorMeses'] = precio_promedio_por_meses($idUltimoMes, $filtro, $tipoMiel);
        return $resultado;
    } else if (isset($_GET['idMes'])) {

        $idMes = intval($_GET['idMes']);
        if ($idMes > 1) {
            $saldoPasado = array(
                'totalInventario' => 0,
                'totalImportesAcumulados' => 0
            );
            for ($i = intval($idMes) - 1; $i > 0; $i--) {
                $EncabezadoMesPasado = calcularInventarioMensual($i, $filtro, true, false, false, false, false, $tipoMiel);
                $saldoPasado['totalInventario'] += $EncabezadoMesPasado['totalInventario'];
                $saldoPasado['totalImportesAcumulados'] += $EncabezadoMesPasado['totalImportesAcumulados'];
            }

            $datos_del_mes_solicitado = calcularInventarioMensual($idMes, $filtro, false, $saldoPasado, false, false, false, $tipoMiel);
        } else {
            // En caso de enero
            $datos_del_mes_solicitado = calcularInventarioMensual($idMes, $filtro, false, false, false, false, false, $tipoMiel);
        }

        $datos_del_mes_solicitado['comprasDeMielPorMeses'] = precio_promedio_por_meses($idMes, $filtro, $tipoMiel);
        return $datos_del_mes_solicitado;
    } else if (isset($_GET['fechaUno']) && isset($_GET['fechaDos'])) {
        $fechaUno = $_GET['fechaUno'];
        $fechaDos = $_GET['fechaDos'];
        $resultado = calcularInventarioMensual(false, $filtro, false, false, false, $fechaUno, $fechaDos, $tipoMiel);
        $idUltimoMes = getUltimoMes($tipoMiel);
        $resultado['comprasDeMielPorPeriodo'] = precio_promedio_por_periodo($fechaUno, $fechaDos, $filtro, $tipoMiel);
        $resultado['comprasDeMielPorMeses'] = precio_promedio_por_meses($idUltimoMes, $filtro, $tipoMiel);
        return $resultado;
    } else {
        throw new Exception('No se recibieron parámetros');
    }
}

try {

    if (!isset($_GET['informeFinanciero'])) {
        $result = realizarFuncionesInventarioMiel();
        echo json_encode($result);
    }
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
    exit();
}
