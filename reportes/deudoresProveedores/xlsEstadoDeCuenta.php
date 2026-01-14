<?php
include_once '../../deudoresProveedores/php/estadoProveedor.php';

if (isset($_GET['idProveedor'])) {

    $reporte = obtenerEstadoDeCuentaProveedor($_GET['idProveedor']);

    $nombre_reporte = 'Estado de cuenta';

    header('Content-type: application/vnd.ms-excel');
    header("Content-Disposition: attachment; filename=Estado de cuenta.xls");
    header("Pragma: no-cache");
    header("Expires:0");

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
            <td colspan="10"><b>Estado de cuenta</b></td>
        </tr>
            </table>';

    echo '<table border=1 style="border-collapse: collapse" style="font-size: 10px">
    <thead>
    <tr>
        <th style="text-transform: none; font-weight: 600; text-align: center; font-size: 15px; background-color: #FFE558; color:black"
            colspan="14">' . utf8_decode($reporte['nombre']) . '</th>
    </tr>
    <tr>
        <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="2">Fecha</th>
        <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="2">Concepto</th>
        <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="2">Referencia</th>
        <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="1" colspan="4">Ingreso o Egreso</th>
        <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="1" colspan="4">Miel</th>
        <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="2">Gastos</th>
        <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="2">Otros</th>
        <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" rowspan="1" colspan="1">Saldo</th>
    </tr>
    <tr>
        <th style="text-transform: none; font-weight: 600;">Bancos</th>
        <th style="text-transform: none; font-weight: 600;">Efectivo</th>
        <th style="text-transform: none; font-weight: 600;">' . utf8_decode('Cera/Prod. Apícolas') . '</th>
        <th style="text-transform: none; font-weight: 600;">ISR Ret</th>
        <th style="text-transform: none; font-weight: 600;">Entrada</th>
        <th style="text-transform: none; font-weight: 600;">Kg.</th>
        <th style="text-transform: none; font-weight: 600;">Precio</th>
        <th style="text-transform: none; font-weight: 600;">Importe</th>
        <th style="text-transform: none; font-weight: 600;">Final</th>
    </tr>
</thead>
<tbody style="font-size: 10px">';
    echo '<tr>
        <td></td>
        <td>
             <b>Saldo inicial</b>
        </td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td style="text-align: right">
             <b>' . utf8_decode(number_format($reporte['saldoInicial'], 2, '.', ',')) . '</b>
        </td>
                </tr>';
    foreach ($reporte['arrayDeudores'] as $detalle) {

        // echo json_encode ($detalle);
        echo '<tr>
                <td style="text-align:center;">' . utf8_decode($detalle['fecha']) . '</td>
                <td style="text-align:left;">' . utf8_decode(strtoupper($detalle['descripcion'])) . '</td>
                <td style="text-align: left">' . utf8_decode(strtoupper($detalle['cheque'])) . ' ' . utf8_decode(strtoupper($detalle['deBanco'])) . ' ' . utf8_decode($detalle['deCuenta']) . '</td>
                <td style="text-align:right;">' . utf8_decode(number_format(floatval($detalle['banco']), 2, '.', ',')) . '</td>
                <td style="text-align:right;">' . utf8_decode(number_format(floatval($detalle['caja']), 2, '.', ',')) . '</td>
                <td style="text-align:right;">' . utf8_decode(number_format(floatval($detalle['ceraApicola']), 2, '.', ',')) . '</td>
                <td style="text-align:right;">' . utf8_decode(number_format(floatval($detalle['retenciones']), 2, '.', ',')) . '</td>
                <td style="text-align:center;">' . utf8_decode($detalle['entrada']) . '</td>
                <td style="text-align:right;">' . utf8_decode(number_format(floatval($detalle['kilosNeto']), 2, '.', ',')) . '</td>
                <td style="text-align:right;">' . utf8_decode(number_format(floatval($detalle['precioPromedio']), 2, '.', ',')) . '</td>
                <td style="text-align:right;">' . utf8_decode(number_format(floatval($detalle['importe']), 2, '.', ',')) . '</td>
                <td style="text-align:right;">' . utf8_decode(number_format(floatval($detalle['gastos']), 2, '.', ',')) . '</td>
                <td style="text-align:right;">' . utf8_decode(number_format(floatval($detalle['desconocido']), 2, '.', ',')) . '</td>
                <td style="text-align:right;">' . utf8_decode(number_format(floatval($detalle['saldo']), 2, '.', ',')) . '</td>
                </tr>';
    };
    echo '   <tr>
                <td></td>
                <td></td>
                <td></td>
                <td style="text-align: right">                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                     
                    <b>' . utf8_decode(number_format(floatval($reporte['totalBancos']), 2, '.', ',')) . '</b></td>
                </td>
                <td style="text-align: right">
                    <b>' . utf8_decode(number_format(floatval($reporte['totalCaja']), 2, '.', ',')) . '</b>
                </td>
                <td style="text-align: right">
                    <b>' . utf8_decode(number_format(floatval($reporte['totalCeraApicola']), 2, '.', ',')) . '</b>
                </td>
                <td style="text-align: right">
                    <b>' . utf8_decode(number_format(floatval($reporte['totalRetenciones']), 2, '.', ',')) . '</b>
                </td>
                <td></td>
                <td style="text-align: right">
                    <b>' . utf8_decode(number_format(floatval($reporte['totalKilos']), 2, '.', ',')) . '</b>                
                </td>
                <td style="text-align: right">
                    <b>' . utf8_decode(number_format(floatval($reporte['totalPrecio']), 2, '.', ',')) . '</b>                
                </td>
                <td style="text-align: right">
                    <b>' . utf8_decode(number_format(floatval($reporte['totalImporte']), 2, '.', ',')) . '</b>
                </td>
                <td style="text-align: right">
                    <b>' . utf8_decode(number_format(floatval($reporte['totalGastos']), 2, '.', ',')) . '</b>
                </td>
                <td></td>
                <td style="text-align: right">
                    <b>' . utf8_decode(number_format(floatval($reporte['totalSaldo']), 2, '.', ',')) . ' </b>
                </td>
            </tr>';

    echo '</tbody>
        </table>';
} else {
    $nombre_archivo = 'Documento.xls';

    header('Content-type: application/vnd.ms-excel');
    header("Content-Disposition: attachment; filename=$nombre_archivo");
    header("Pragma: no-cache");
    header("Expires:0");
}
