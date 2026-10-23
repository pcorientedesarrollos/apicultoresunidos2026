<?php

include_once '../../inventarios/productosDerivados/php/obtenerInventarioProductosDerivados.php';
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename = Inventario de productos derivados.xls");
header("Prafma: no-cache");
header("Expires:0");

try {
    if (!isset($_GET['opcion'])) {
        throw new Exception('No se recibieron parámetros');
    } else {
        $opcion = $_GET['opcion'];
    }

    if ($opcion == '1') {
        $resultado = obtenerInventarioDerivado(false, true);
    } else if ($opcion == '2') {
        if (!isset($_GET['mes'])) {
            throw new Exception('No se recibieron parámetros');
        }
        $resultado = obtenerInventarioDerivado($_GET['mes'], false);
    } else if ($opcion == '3') {
        if (!isset($_GET['fechaUno']) || !isset($_GET['fechaDos'])) {
            throw new Exception('No se recibieron parámetros');
        }
        $resultado = obtenerInventarioDerivado(false, false, $_GET['fechaUno'], $_GET['fechaDos']);
    } else {
        throw new Exception('Opción no válida');
    }

    echo '<table>
        <thead>
            <tr style="font-size:18px">
                <th colspan=8>
                    Apicultores Unidos de la Peninsula S.A. de C.V.
                </th>
            </tr>
            <tr style="font-size:16px">
                <th colspan=8>
                    INVENTARIO DE PRODUCTOS DERIVADOS
                </th>
            </tr>
            <tr></tr>
            <tr>
                <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;">Producto</th>
                <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;">Saldo inicial</th>
                <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;">Entrada</th>
                <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;">Salida</th>
                <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;">Saldo</th>
                <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;">Costo de venta</th>
                <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;">Importe de compra</th>
                <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;">Importe de venta</th>
            </tr>
        </thead>';

    foreach ($resultado['productos'] as $producto) {
        $saldo = isset($producto['saldo']) ? $producto['saldo'] : $producto['existencia'];
        if ($producto['entradas'] == 0 && $producto['salidas'] == 0) {
            $saldo = $producto['existencia'];
        }
        $costoCompra = isset($producto['costoCompra']) ? $producto['costoCompra'] : 0;

        echo '<tr>
            <td style="text-align: left">' . utf8_decode($producto['subconceptoCC']) . '</td>
            <td style="text-align: right">' . utf8_decode(number_format($producto['existencia'], 2, '.', ',')) . '</td>
            <td style="text-align: right">' . utf8_decode(number_format($producto['entradas'], 2, '.', ',')) . '</td>
            <td style="text-align: right">' . utf8_decode(number_format($producto['salidas'], 2, '.', ',')) . '</td>
            <td style="text-align: right">' . utf8_decode(number_format($saldo, 2, '.', ',')) . '</td>
            <td style="text-align: right">$' . utf8_decode(number_format($producto['precioPromedio'], 2, '.', ',')) . '</td>
            <td style="text-align: right">$' . utf8_decode(number_format($costoCompra, 2, '.', ',')) . '</td>
            <td style="text-align: right">$' . utf8_decode(number_format($producto['importeSalida'], 2, '.', ',')) . '</td>
        </tr>';
    }

    echo '<tr style="font-size:12px; font-weight: 600">
            <th style="text-align: right">
                <b>Total</b>
            </th>
            <td style="text-align: right">' . utf8_decode(number_format($resultado['totalProductos'], 2, '.', ',')) . '</td>
            <td style="text-align: right">' . utf8_decode(number_format($resultado['totalEntradas'], 2, '.', ',')) . '</td>
            <td style="text-align: right">' . utf8_decode(number_format($resultado['totalSalidas'], 2, '.', ',')) . '</td>
            <td style="text-align: right">' . utf8_decode(number_format($resultado['totalSaldo'], 2, '.', ',')) . '</td>
            <td></td>
            <td></td>
            <td style="text-align: right">$' . utf8_decode(number_format($resultado['totalImporteSalida'], 2, '.', ',')) . '</td>
        </tr>
    </table>';
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
