<?php

include_once '../../inventarios/php/obtenerInventarioEnvasesFrascos.php';
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename = Inventario de Envases y frascos.xls");
header("Prafma: no-cache");
header("Expires:0");

try {
    if (!isset($_GET['opcion'])) {
        throw new Exception('No se recibieron parámetros');
    } else {

        $opcionMes = new stdClass();
        $opcionMes->opcion = $_GET['opcion'];

        if ($opcionMes->opcion == '2') {
            if (!isset($_GET['mes'])) {
                throw new Exception('No se recibieron parámetros');
            } else {
                $opcionMes->mes = $_GET['mes'];
            }
        }
        $acumulado = $opcionMes->opcion == '1' ? true : false;
    }
    $resultado = array();
    if ($acumulado) {
        $resultado = getInventarioEnvases(TRUE);
    } else if (isset($opcionMes->mes)) {
        $idMes = $opcionMes->mes;
        $resultado = getInventarioEnvases(FALSE, $idMes);
    }
    echo '<table>
        <thead>
            <tr style="font-size:18px">
                <th colspan=10>
                    Apicultores Unidos de la Peninsula S.A. de C.V.
                </th>
            </tr>
            <tr style="font-size:16px">
                <th colspan=10>
                    INVENTARIO DE ENVASES Y FRASCOS
                </th>
            </tr>
            <tr></tr>
            <tr></tr>
            <tr></tr>
            <tr>
                <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="2">Fecha</th>
                <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="2">Nombre</th>
                <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="2">Catalogo</th>
                <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="2">Concepto</th>
                <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="1" colspan="2">Cantidad</th>
                <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="2">Existencia (kg.)</th>
                <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="2">Precio unitario</th>
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
        <tr style="font-size:12px; font-weight: 600">
            <td style="text-align: right" colspan="6">Saldos iniciales</td>
            <td style="text-align: right">' . utf8_decode(number_format($resultado['encabezado']['existenciaAcumuladaPasada'], 2, '.', ',')) . '</td>
            <td></td>
            <td></td>
            <td></td>
            <td style="text-align: right">$' . utf8_decode(number_format($resultado['encabezado']['importeAcumuladoPasado'], 2, '.', ',')) . '</td>
        </tr>';
    foreach ($resultado['registros'] as $inventario) {
        $inventario['entrada'] = isset($inventario['entrada']) ? $inventario['entrada'] : 0;
        $inventario['salida'] = isset($inventario['salida']) ? $inventario['salida'] : 0;
        $inventario['importeEntrada'] = isset($inventario['importeEntrada']) ? $inventario['importeEntrada'] : 0;
        $inventario['importeSalida'] = isset($inventario['importeSalida']) ? $inventario['importeSalida'] : 0;
        $inventario['proveedor'] = isset($inventario['proveedor']) ? $inventario['proveedor'] : '';
        $inventario['fecha'] = DateTime::createFromFormat('Y-m-d', $inventario['fecha']);
        echo '<tr ng-repeat="inventario in registrosMP.registros">
            <td style="text-align: left">' . utf8_decode(date_format($inventario['fecha'], 'd/m/Y')) . '</td>
            <td style="text-align: left">' . utf8_decode($inventario['proveedor']) . '</td>
            <td style="text-align: left">' . utf8_decode($inventario['subcuenta']) . '</td>
            <td style="text-align: left">' . utf8_decode($inventario['concepto']) . '</td>
            <td style="text-align: right">' . utf8_decode(number_format($inventario['entrada'], 2, '.', ',')) . '</td>
            <td style="text-align: right">' . utf8_decode(number_format($inventario['salida'], 2, '.', ',')) . '</td>
            <td style="text-align: right">' . utf8_decode(number_format($inventario['existencia'], 2, '.', ',')) . '</td>
            <td style="text-align: right">$' . utf8_decode(number_format($inventario['precioUnitario'], 2, '.', ',')) . '</td>
            <td style="text-align: right">$' . utf8_decode(number_format($inventario['importeEntrada'], 2, '.', ',')) . '</td>
            <td style="text-align: right">$' . utf8_decode(number_format($inventario['importeSalida'], 2, '.', ',')) . '</td>
            <td style="text-align: right">$' . utf8_decode(number_format($inventario['importeAcumulado'], 2, '.', ',')) . '</td>
        </tr>';
    }

    echo '<tr style="font-size:12px; font-weight: 600">
            <th style="text-align: right" colspan="4">
                <b>Total</b>
            </th>
            <td style="text-align: right">' . utf8_decode(number_format($resultado['encabezado']['totalEntradas'], 2, '.', '.')) . '</td>
            <td style="text-align: right">' . utf8_decode(number_format($resultado['encabezado']['totalSalidas'], 2, '.', '.')) . '</td>
            <td style="text-align: right">' . utf8_decode(number_format($resultado['encabezado']['totalExistencia'], 2, '.', '.')) . '</td>
            <td style="text-align: right"></td>
            <td style="text-align: right">$' . utf8_decode(number_format($resultado['encabezado']['totalImporteEntradas'], 2, '.', '.')) . '</td>
            <td style="text-align: right">$' . utf8_decode(number_format($resultado['encabezado']['totalImporteSalidas'], 2, '.', '.')) . '</td>
            <td style="text-align: right">$' . utf8_decode(number_format($resultado['encabezado']['totalImporteAcumulado'], 2, '.', '.')) . '</th>
        </tr>
    </table>';
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
