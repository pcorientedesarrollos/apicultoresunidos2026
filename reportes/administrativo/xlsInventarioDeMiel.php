<?php

include_once('../../inventarios/php/obtenerInventarioMiel.php');

try {
    // Si no recibe el parámetro 'miel' se le asigna por defecto convencional
    $tipoMiel = isset($_GET['miel']) ? $_GET['miel'] : '1';

    if (!isset($_GET['opcion'])) {
        throw new Exception('No se recibieron parámetros');
    } else {
        $filtro = $_GET['filtro'];
        if ($_GET['opcion'] == '1') {
            $_tipoReporte = 1;
        } else if ($_GET['opcion'] == '2') {
            if (!isset($_GET['mes'])) {
                throw new Exception('No se recibieron parámetros');
            } else {
                $_tipoReporte = 2;
                $idMes = $_GET['mes'];
            }
        } else if ($_GET['opcion'] == '3') {
            if (!isset($_GET['fechaUno']) && !isset($_GET['fechaDos'])) {
                throw new Exception('No se recibieron parámetros');
            } else {
                $_tipoReporte = 3;
                $fechaUno = $_GET['fechaUno'];
                $fechaDos = $_GET['fechaDos'];
            }
        }
    }

    header('Content-type: application/vnd.ms-excel');
    header("Content-Disposition: attachment; filename = Inventario de miel.xls");
    header("Prafma: no-cache");
    header("Expires:0");

    switch ($_tipoReporte) {
        case 1:
            $mostrar_promedio_meses = true;
            $result = calcularInventarioMensual(false, $filtro, false, false, true, false, false, $tipoMiel);
            $idUltimoMes = getUltimoMes($tipoMiel);
            $compras_de_miel_por_meses = precio_promedio_por_meses($idUltimoMes, $filtro, $tipoMiel);
            break;
        case 2:
            $mostrar_promedio_meses = true;
            if ($idMes > 1) {
                $datos_del_mes_pasado = calcularInventarioMensual($idMes - 1, $filtro, true, false, false, false, false, $tipoMiel);
                $result = calcularInventarioMensual($idMes, $filtro, false, $datos_del_mes_pasado, false, false, false, $tipoMiel);
            } else {
                $result = calcularInventarioMensual($idMes, $filtro, false, false, false, false, false, $tipoMiel);
            }
            $compras_de_miel_por_meses = precio_promedio_por_meses($idMes, $filtro, $tipoMiel);
            break;
        case 3:
            $mostrar_promedio_meses = true;
            $mostrar_promedio_periodo = true;
            $result = calcularInventarioMensual(false, $filtro, false, false, false, $fechaUno, $fechaDos, $tipoMiel);
            $idUltimoMes = getUltimoMes($tipoMiel);
            $compras_de_miel_por_meses = precio_promedio_por_meses($idUltimoMes, $filtro, $tipoMiel);
            $compras_de_miel_por_periodo = precio_promedio_por_periodo($fechaUno, $fechaDos, $filtro, $tipoMiel);
            break;
        default:
            throw new Exception('No se recibieron los parámetros correctos');
            break;
    }

    echo '<table style="font-size: 10px">
    <thead>
        <tr style="font-size:18px">
            <th colspan=12>
                Apicultores Unidos de la Peninsula S.A. de C.V.
            </th>
        </tr>
        <tr style="font-size:16px">
            <th colspan=12>
                CONTROL DE INVENTARIO DE COMPRAS MIEL
            </th>
        </tr>
        <tr>
            <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="2">Fecha</th>
            <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="2">Folio Interno</th>
            <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="2">Control</th>
            <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="2"></th>
            <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="2">Nombre del apicultor</th>
            <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="1" colspan="2">Kilogramos</th>
            <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="2">Existencia (kg.)</th>
            <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="2">Precio por kg.</th>
            <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="1" colspan="2">Importe</th>
            <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="2">Importe acumulado</th>
        </tr>
        <tr>
            <th style="text-transform: none; font-weight: 600;">Entradas</th>
            <th style="text-transform: none; font-weight: 600;">Salidas</th>
            <th style="text-transform: none; font-weight: 600;">Entradas</th>
            <th style="text-transform: none; font-weight: 600;">Salidas</th>
        </tr>
    </thead>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td style="text-align: right">' . number_format($result['existenciaPasada'], 2, '.', ',') . '</td>
        <td></td>
        <td></td>
        <td></td>
        <td style="text-align: right">' . number_format($result['importeAcumuladoPasado'], 2, '.', ',') . '</td>
    </tr>';

    foreach ($result['inventarioMiel'] as $inventario) {
        echo '<tr>
                <td style="text-align: left">' . $inventario['fecha'] . '</td>
                <td style="text-align: left">' . $inventario['folioInterno'] . '</td>
                <td style="text-align: left">' . utf8_decode($inventario['clasificacion']) . '</td>
                <td style="text-align: left">' . $inventario['folio'] . '</td>
                <td style="text-align: left">' . utf8_decode($inventario['nombre']) . '</td>
                <td style="text-align: right">' . number_format($inventario['entrada'], 2, '.', ',') . '</td>
                <td style="text-align: right">' . number_format($inventario['salida'], 2, '.', ',') . '</td>
                <td style="text-align: right">' . number_format($inventario['existencia'], 2, '.', ',') . '</td>
                <td style="text-align: right"> $ ' . number_format($inventario['precio'], 2, '.', ',') . '</td>
                <td style="text-align: right"> $ ' . number_format($inventario['totalImporte'], 2, '.', ',') . '</td>
                <td style="text-align: right"> $ ' . number_format($inventario['totalSalida'], 2, '.', ',') . '</td>
                <td style="text-align: right"> $ ' . number_format($inventario['importeAcumulado'], 2, '.', ',') . '</td>
            </tr>';
    }
    echo '<tr style="font-size:16px">
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td style="text-align: right"><b>Total Acumulado</b></td>
            <td style="text-align: right">' . number_format($result['encabezado']['totalEntradas'], 2, '.', ',') . '</td>
            <td style="text-align: right">' . number_format($result['encabezado']['totalSalidas'], 2, '.', ',') . '</td>
            <td style="text-align: right">' . number_format($result['encabezado']['totalInventario'], 2, '.', ',') . '</td>
            <td style="text-align: right"> $ ' . number_format($result['encabezado']['promedioPrecio'], 2, '.', ',') . '</td>
            <td style="text-align: right"> $ ' . number_format($result['encabezado']['totalImporteEntrada'], 2, '.', ',') . '</td>
            <td style="text-align: right"> $ ' . number_format($result['encabezado']['totalImporteSalida'], 2, '.', ',') . '</td>
            <td style="text-align: right"> $ ' . number_format($result['encabezado']['totalImportesAcumulados'], 2, '.', ',') . '</td>
        </tr>
    </table>';

    echo '<table>
    <tr>
        <td></td>
    </tr>
    <tr>
        <td></td>
    </tr>
    <tr>
        <td></td>
    </tr>
    </table>';

    echo '<table style="font-size: 10px">
        <thead>
            <tr>
                <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" colspan=5><b>Resumen por Empresas</b></th>
            </tr>
            <tr>
                <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;">Empresa</th>
                <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;">Localidad</th>
                <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;">Kg</th>
                <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;">Precio Promedio</th>
                <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;">Importe</th>
                <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;">% Compra</th>
            </tr>
        </thead>';

    foreach ($result['resumenPorEmpresas']['empresas'] as $empresa) {
        echo '<tr>
        <td style="text-align: left">' . utf8_decode($empresa['nombre']) . '</td>
        <td style="text-align: left">' . utf8_decode($empresa['localidad']) . '</td>
        <td style="text-align: right">' . number_format($empresa['neto'], 2, '.', ',') . '</td>
        <td style="text-align: right"> $ ' . number_format($empresa['precioPromedio'], 2, '.', ',') . '</td>
        <td style="text-align: right"> $ ' . number_format($empresa['importe'], 2, '.', ',') . '</td>
        <td style="text-align: right">' . number_format($empresa['porcentajeCompra'], 2, '.', ',') . '%</td>
    </tr>';
    };

    echo '<tr style="font-size:15px">
        <th style="text-align: center" colspan="2">
            <b>TOTAL GENERAL</b>
        </th>
        <th style="text-align: right">' . number_format($result['resumenPorEmpresas']['totalKg'], 2, '.', ',') . '</th>
        <th style="text-align: right"> $ ' . number_format($result['resumenPorEmpresas']['precioPromedio'], 2, '.', ',') . '</th>
        <th style="text-align: right"> $ ' . number_format($result['resumenPorEmpresas']['totalImporte'], 2, '.', ',') . '</th>
        <th style="text-align: center">' . number_format($result['resumenPorEmpresas']['totalPorcentaje'], 2, '.', ',') . '%</th>
    </tr>
    </table>';

    echo '<table>
    <tr>
        <td></td>
    </tr>
    <tr>
        <td></td>
    </tr>
    <tr>
        <td></td>
    </tr>
    </table>';

    if ($mostrar_promedio_meses) {
        echo '<table style="font-size: 10px">
        <thead>
            <tr>
                <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" colspan=5><b> Precio promedio por meses </b></th>
            </tr>
            <tr>
                <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;">Periodo</th>
                <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;">Kg</th>
                <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;">Precio promedio</th>
                <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;">Importe</th>
                <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;">% Compra</th>
            </tr>
        </thead>';

        foreach ($compras_de_miel_por_meses['datos'] as $key => $mes) {
            echo '<tr>
            <td style="text-align: left">' . $mes['mes'] . '</td>
            <td style="text-align: right">' . number_format($mes['totalEntradas'], 2, '.', ',') . '</td>
            <td style="text-align: right"> $ ' . number_format($mes['promedioPrecio'], 2, '.', ',') . '</td>
            <td style="text-align: right">' . number_format($mes['totalImporteEntrada'], 2, '.', ',') . '</td>
            <td style="text-align: right">' . $mes['porcentajeCompra'] . ' %</td>
        </tr>';
        }

        echo '<tr style="font-size:15px">
            <th style="text-align: left">
                <b>TEMPORADA</b>
            </th>
            <th style="text-align: right">' . number_format($compras_de_miel_por_meses['resumenCompra']['totalKgEntrada'], 2, '.', ',') . '</th>
            <th style="text-align: right"> $ ' . number_format($compras_de_miel_por_meses['resumenCompra']['precioPromedio'], 2, '.', ',') . '</th>
            <th style="text-align: right"> $ ' . number_format($compras_de_miel_por_meses['resumenCompra']['totalImporteEntrada'], 2, '.', ',') . '</th>
            <th style="text-align: right">' . $compras_de_miel_por_meses['resumenCompra']['pCompra'] . ' %</th>
        </tr>
    </table>';
    }

    if ($mostrar_promedio_periodo) {
        echo '<table style="font-size: 10px">
        <thead>
            <tr>
                <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" colspan=5><b> Precio promedio por periodo </b></th>
            </tr>
            <tr>
                <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;">Periodo</th>
                <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;">Kg</th>
                <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;">Precio promedio</th>
                <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;">Importe</th>
                <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;">% Compra</th>
            </tr>
        </thead>';

        echo '<tr style="font-size:15px">
            <th style="text-align: left">
                ' . $compras_de_miel_por_periodo['resumenCompra']['periodo'] . '
            </th>
            <th style="text-align: right">' . number_format($compras_de_miel_por_periodo['resumenCompra']['totalKg'], 2, '.', ',') . '</th>
            <th style="text-align: right"> $ ' . number_format($compras_de_miel_por_periodo['resumenCompra']['precioPromedio'], 2, '.', ',') . '</th>
            <th style="text-align: right"> $ ' . number_format($compras_de_miel_por_periodo['resumenCompra']['totalImporte'], 2, '.', ',') . '</th>
            <th style="text-align: right">' . $compras_de_miel_por_periodo['resumenCompra']['pCompra'] . ' %</th>
        </tr>
    </table>';
    }
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
    exit();
}
