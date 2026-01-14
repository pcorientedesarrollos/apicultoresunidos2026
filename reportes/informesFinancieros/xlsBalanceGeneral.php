<?php
include_once '../../informesFinancieros/php/obtenerFlujoEfectivo.php';

if (isset($_GET['descargar'])) {

    $reporte = obtenerInformeFlujoEfectivo();

    $nombre_reporte = 'Flujo de Efectivo AUP';

    header('Content-type: application/vnd.ms-excel');
    header("Content-Disposition: attachment; filename=$nombre_reporte.xls");
    header("Pragma: no-cache");
    header("Expires:0");


    // Encabezado del reporte

    echo '<table>
        <tr style="text-align: center;">
            <td colspan="10"><b>OAXACA MIEL S.A. DE C.V.</b></td>
        </tr>

        <tr style="text-align: center;">
            <td colspan="10"><b> </b></td>
        </tr>

        <tr style="text-align: center;">
            <td colspan="10"><b>' . utf8_decode('') . '</b></td>
        </tr>

        <tr style="text-align: center;">
            <td colspan="10"><b>' . utf8_decode('') . '</b></td>
        </tr>

        <tr style="text-align: center;">
            <td colspan="10"><b>Tel: (999) 9.88.09.90</b></td>
        </tr>
        <tr style="text-align: center;">
            <td colspan="10"><b>Flujo de efectivo</b></td>
        </tr>
        <tr></tr>
        <tr></tr>
        <tr></tr>
    </table>';
    

    // Imprimir la tabla

    echo '<table>';

    // Encabezado de la tabla

    echo '<thead>
            <tr>
                <th class="text-center"> CONCEPTO </th>';

    foreach ($reporte['meses'] as $mes) {
        echo '<th class="text-center">' . strtoupper($mes['mes']) . '</th>';
    }

    echo '<th class="text-center"> ACUMULADO </th>
            </tr>
        </thead>';


    // tbody
    echo '<tbody>';

    // Cobranza a clientes


    echo ' <tr>
    <td class="text-center">
        <b> Cobranza a clientes </b>
    </td>';

    foreach ($reporte['actividades_operacion']['total_cobranza'] as $valor) {
        echo '<td class="text-right">
        <b>$' . number_format($valor, 2, '.', ',') . '</b>
    </td>';
    }

    echo '</tr>';

    foreach ($reporte['actividades_operacion']['cobranza_clientes'] as $cliente) {
        echo '<tr>';
        echo '<td class="text-left">' . strtoupper(utf8_decode($cliente->nombre)) . '</td>';

        foreach ($cliente->totalesPorMes as $totalPorMes) {
            echo '<td class="text-right">$' . number_format($totalPorMes, 2, '.', ',') . '</td>';
        }
        echo '</tr>';
    }

    // Finaliza cobranza a clientes


    // Otros ingresos


    echo '<tr>
    <td class="text-center">
        <b> Otros ingresos </b>
    </td>';

    foreach ($reporte['actividades_operacion']['total_otros_ingresos'] as $valor) {
        echo ' <td class="text-right">
            <b>$' . number_format($valor, 2, '.', ',') . '</b>
        </td>';
    }

    echo '</tr>';

    foreach ($reporte['actividades_operacion']['otros_ingresos'] as $otro) {
        echo '<tr>';
        echo '<td class="text-left">' . strtoupper(utf8_decode($otro->concepto)) . '</td>';

        foreach ($otro->totalesPorMes as $totalPorMes) {
            echo '<td class="text-right">$' . number_format($totalPorMes, 2, '.', ',') . '</td>';
        }
        echo '</tr>';
    }

    // Finaliza otros ingresos


    // Total ingresos

    echo ' <tr>
    <td class="text-center">
        <b> **Total ingresos </b>
    </td>';

    foreach ($reporte['actividades_operacion']['total_ingresos'] as $t) {
        echo '<td class="text-right">
            <b>$' . number_format($t, 2, '.', ',') . '</b>
        </td>';
    }

    echo '</tr>';

    // Finaliza total ingresos


    // Pago a proveedores Mat. Prima Directa

    echo '<tr>
            <td class="text-center">
                <b> Pago a proveedores Mat. Prima Directa </b>
            </td>
        </tr>';
    foreach ($reporte['pago_proveedores']['conceptos'] as $c) {
        echo '<tr>';
        echo '<td class="text-left">' . strtoupper(utf8_decode($c->concepto)) . '</td>';

        foreach ($c->totalesPorMes as $totalPorMes) {
            echo '<td class="text-right">$' . number_format($totalPorMes, 2, '.', ',') . '</td>';
        }

        echo '</tr>';
    }                        

    // Finaliza Pago a proveedores Mat. Prima Directa


    // Total pago proveedores

    echo '<tr>
            <td class="text-center">
                <b> Compras Netas Materia Prima </b>
            </td>';

    foreach ($reporte['pago_proveedores']['total_pago_proveedores'] as $total) {
        echo '<td class="text-right">
        <b>$' . number_format($total, 2, '.', ',') . '</b>
        </td>';
    }

    echo '</tr>';


    // Finaliza total pago proveedores


    // Gastos de operación

    echo '<tr>
            <td class="text-center">
                <b>' . utf8_decode('Gastos de operación') . '</b>
            </td>
        </tr>';

    foreach ($reporte['gastos_operacion']['cuentas'] as $cuenta) {
        echo '<tr>';
        echo '<td class="text-left">' . strtoupper(utf8_decode($cuenta->nombre)) . '</td>';
        foreach ($cuenta->totalesPorMes as $totalPorMes) {
            echo ' <td class="text-right">$' . number_format($totalPorMes, 2, '.', ',') . '</td>';
        }
        echo '</tr>';
    }

    // Finaliza gastos de operación

    echo '</tbody>';
    echo '</table>';



    // Final de la tabla




} else {
    $nombre_archivo = 'Documento.xls';
    header('Content-type: application/vnd.ms-excel');
    header("Content-Disposition: attachment; filename=$nombre_archivo");
    header("Pragma: no-cache");
    header("Expires:0");
}