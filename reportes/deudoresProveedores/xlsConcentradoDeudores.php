<?php
include_once '../../deudoresProveedores/php/deudoresProveedores.php';
include_once '../../controlAdministrativo/php/traeMes.php';
$fecha = date('d/m/Y');



$reporte = obtenerDeudoresProveedores();

$nombre_reporte = 'Concentrado';
$fecha = $GLOBALS['fecha'];
$tipo_reporte = strtoupper($_GET['parametro']);

header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=Concentrado.xls");
header("Pragma: no-cache");
header("Expires:0");

$totalSaldoDeudor = 0;
$totalSaldoProveedor = 0;
$conciliacion = 0;
$cantidadTotalBancos = 0;
$cantidadTotalEfectivo = 0;
$cantidadTotalIngresosCeraApicola = 0;
$cantidadTotalKilosMiel = 0;
$cantidadTotalImporteMiel = 0;
$cantidadTotalPrecioPromedi = 0;
$cantidadTotalEgresosCeraApicola = 0;
$cantidadTotalGastos = 0;
$cantidadTotalRetenciones = 0;
$cantidadTotalDevolucionesEfectivo = 0;
$cantidadTotalDevolucionesCeraApicola = 0;
$cantidadTotalVentasCeraApicola = 0;
$cantidadTotalGranTotal = 0;
$cantidadTotalPrecioPromedio = 0;

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
            </tr>';
if ($tipo_reporte == 'TODOS') {
    echo '
            <tr style="text-align: center;">
            <td colspan="10"><b>CONCENTRADO DE TODOS LOS PROVEEDORES -  ' . utf8_decode(strtoupper($fecha)) . '</b></td>
        </tr>
            </table>';
} else {
    echo '
            <tr style="text-align: center;">
            <td colspan="10"><b>CONCENTRADO DE PROVEEDORES ' . utf8_decode(strtoupper($tipo_reporte)) . ' - ' . utf8_decode(strtoupper($fecha)) . '</b></td>

        </tr>
            </table>';
}
if (isset($_GET['idMes'])) {
    $mes_reporte = obtenerMes($_GET['idMes']);
    echo '
            <table>
            <thead>
            <tr>
            <th></th>
            <th></th>
            <th></th>
            <th>            <h4>
            ' . utf8_decode(strtoupper($mes_reporte['mes'])) . '
            </h4>
            </th>
            </tr>
            </thead>
            </table>

            ';
}


echo '
        <br>
        <br>
        ';

echo '
    
    <div class="table-responsive no-padding">
                                <table class="table table-bordered table-condensed">
                                    <thead>
                                        <tr>
                                            <th rowspan="2"></th>
                                            <th rowspan="2">Id Sagarpa</th>
                                            <th rowspan="2">Localidad</th>
                                            <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;"
                                                rowspan="2">Deudor/Proveedor</th>
                                            <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;"
                                                rowspan="1">Proveedores</th>
                                            <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 15px;"
                                                rowspan="1">Deudores</th>
                                        </tr>
                                        <tr>
                                            <th style="text-transform: none; font-weight: 600; text-align: center;">Miel
                                                por pagar</th>
                                            <th style="text-transform: none; font-weight: 600; text-align: center;">
                                                Anticipos entregados</th>
                                        </tr>
                                    </thead>';


foreach ($reporte as $key => $proveedor) {


    $proveedor['index'] = $key + 1;

    if (!isset($proveedor['totalSaldo'])) {
        $proveedor['totalSaldo'] = floatval($proveedor['cantidad']);
    }

    if (isset($proveedor['totalSaldo'])) {

        if ($proveedor['totalSaldo'] === 0) {
            $proveedor['saldoDeudor'] = 0;
            $proveedor['saldoProveedor'] = 0;
        }
        // elseif ($proveedor['cantidad'] === 0) {
        //     $proveedor['saldoDeudor'] = 0;
        //     $proveedor['saldoProveedor'] = 0;
        // }
        elseif ($proveedor['totalSaldo'] > 0) {
            $proveedor['saldoDeudor'] = floatval($proveedor['totalSaldo']);
            $proveedor['saldoProveedor'] = 0;
            $totalSaldoDeudor += floatval($proveedor['saldoDeudor']);
        } elseif ($proveedor['totalSaldo'] < 0) {
            $proveedor['saldoProveedor'] = floatval($proveedor['totalSaldo']);
            $proveedor['saldoDeudor'] = 0;
            $totalSaldoProveedor += floatval($proveedor['saldoProveedor']);
        }
        //  elseif ($proveedor['cantidad'] > 0) {
        //     $proveedor['saldoProveedor'] = 0;
        //     $proveedor['saldoDeudor'] = floatval($proveedor['cantidad']);
        //     $totalSaldoDeudor += floatval($proveedor['saldoDeudor']);
        // } elseif ($proveedor['cantidad'] < 0) {
        //     $proveedor['saldoProveedor'] = floatval($proveedor['cantidad']);
        //     $proveedor['saldoDeudor'] = 0;
        //     $totalSaldoProveedor += floatval($proveedor['saldoProveedor']);
        // } 
        else {
            $proveedor['saldoDeudor'] = 0;
            $proveedor['saldoProveedor'] = 0;
        }


        $conciliacion = $totalSaldoDeudor + $totalSaldoProveedor;

        if (!isset($proveedor['totalBancos'])) {
            $proveedor['totalBancos'] = 0;
        }
        if (!isset($proveedor['totalCaja'])) {
            $proveedor['totalCaja'] = 0;
        }
        if (!isset($proveedor['totalDevolucionesCeraApicola'])) {
            $proveedor['totalDevolucionesCeraApicola'] = 0;
        }
        if (!isset($proveedor['totalDevolucionesMiel'])) {
            $proveedor['totalDevolucionesMiel'] = 0;
        }
        if (!isset($proveedor['totalEgresosCeraApicola'])) {
            $proveedor['totalEgresosCeraApicola'] = 0;
        }
        if (!isset($proveedor['totalGastos'])) {
            $proveedor['totalGastos'] = 0;
        }
        if (!isset($proveedor['totalRetenciones'])) {
            $proveedor['totalRetenciones'] = 0;
        }
        if (!isset($proveedor['totalImporte'])) {
            $proveedor['totalImporte'] = 0;
        }
        if (!isset($proveedor['totalIngresosCeraApicola'])) {
            $proveedor['totalIngresosCeraApicola'] = 0;
        }
        if (!isset($proveedor['totalKilos'])) {
            $proveedor['totalKilos'] = 0;
        }
        if (!isset($proveedor['totalPrecio'])) {
            $proveedor['totalPrecio'] = 0;
        }
        if (!isset($proveedor['totalVentasCeraApicola'])) {
            $proveedor['totalVentasCeraApicola'] = 0;
        }
        if (!isset($proveedor['totalSaldo'])) {
            $proveedor['totalSaldo'] = floatval($proveedor['cantidad']);
        }
        $cantidadTotalBancos += floatval($proveedor['totalBancos']);
        $cantidadTotalEfectivo += floatval($proveedor['totalCaja']);
        $cantidadTotalIngresosCeraApicola += floatval($proveedor['totalIngresosCeraApicola']);
        $cantidadTotalKilosMiel += floatval($proveedor['totalKilos']);
        $cantidadTotalImporteMiel += floatval($proveedor['totalImporte']);
        if ($cantidadTotalKilosMiel > 0) {
            $cantidadTotalPrecioPromedio = $cantidadTotalImporteMiel / $cantidadTotalKilosMiel;
        }
        $cantidadTotalEgresosCeraApicola += floatval($proveedor['totalEgresosCeraApicola']);
        $cantidadTotalGastos += floatval($proveedor['totalGastos']);
        $cantidadTotalRetenciones += floatval($proveedor['totalRetenciones']);
        $cantidadTotalDevolucionesEfectivo += floatval($proveedor['totalDevolucionesMiel']);
        $cantidadTotalDevolucionesCeraApicola += floatval($proveedor['totalDevolucionesCeraApicola']);
        $cantidadTotalVentasCeraApicola += floatval($proveedor['totalVentasCeraApicola']);
        $cantidadTotalGranTotal += floatval($proveedor['totalSaldo']);
    } else {
        $proveedor['saldoDeudor'] = 0;
        $proveedor['saldoProveedor'] = 0;
    }

    if ($proveedor['saldoProveedor'] == '0' && $proveedor['saldoDeudor'] == '0' && isset($_GET['noLiquidados'])) {
        // saltarse los que ya están liquidados
        continue;
    }

    armarTabla($proveedor);
};

function armarTabla($proveedor)
{
    echo '
        <tbody>
        <tr style="font-size:14px">
            <td style="text-align: center">' . utf8_decode($proveedor['index']) . '</td>
            <td style="text-align: center">' . utf8_decode($proveedor['idSagarpa']) . '</td>
            <td style="text-align: center"> ' . utf8_decode($proveedor['localidad']) . '</td>
            <td style="text-align: center">' . utf8_decode($proveedor['nombre']) . '</td>
            <td style="text-align: center"> ' . utf8_decode($proveedor['saldoProveedor']) . ' </td>
            <td style="text-align: center"> ' . utf8_decode($proveedor['saldoDeudor']) . ' </td>
        </tr>
        </tbody>
        ';
}




echo '
    
                </table>
                </div>
    ';


echo '
    <br>
    <br>
    ';

echo '
    <div class="row">
    <div class="col-lg-5 pull-right">
        <div class="table-responsive no-padding">
            <table class="table table-bordered table-condensed" style="font-size: 10px;">
                <thead>
                    <tr>
                        <th style="text-align: center; font-weight: 700; font-size: 15px;"
                            colspan="3">Saldo actual</th>
                    </tr>
                    <tr>
                        <th style="text-align: center; font-weight: 700; font-size: 15px;">
                            Miel por pagar</th>
                        <th style="text-align: center; font-weight: 700; font-size: 15px;">
                            Anticipos entregados</th>
                        <th style="text-align: center; font-weight: 700; font-size: 15px;">
                            Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td
                            style="text-align: right; font-weight: 600; font-size: 14px; color:red">
                            ' . utf8_decode($totalSaldoProveedor) . '</td>
                        <td style="text-align: right; font-weight: 600; font-size: 14px;">
                        ' . utf8_decode($totalSaldoDeudor) . '</td>
                        <td style="text-align: right; font-weight: 600; font-size: 14px;">
                        ' . utf8_decode($conciliacion) . '</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
    ';

echo '
    <br>
    <br>
    ';

echo '
    <div class="table-responsive no-padding">
    <table class="table table-bordered table-condensed" style="font-size: 10px">
        <thead>
            <tr>
                <th style="font-weight: 600; text-align: center; font-size: 15px;"
                    colspan="13">TOTALES</th>
            </tr>
            <tr>
                <th style="font-weight: 600; text-align: center; font-size: 15px; background-color: lightgreen "
                    colspan="4">INGRESOS</th>
                <th style="font-weight: 600; text-align: center; font-size: 15px; background-color: lightpink"
                    colspan="8">EGRESOS</th>
                <th style="font-weight: 600; text-align: center; font-size: 15px;">Saldo
                </th>
            </tr>
            <tr>
                <th style=" background-color: lightgreen">Bancos</th>
                <th style=" background-color: lightgreen">Efectivo</th>
                <th style=" background-color: lightgreen"> ' . utf8_decode('Cera/Prod. Apícolas') . '</th>
                <th style=" background-color: lightgreen">ISR Ret</th>

                <th style=" background-color: lightpink">Kilos Miel</th>
                <th style=" background-color: lightpink">Precio promedio</th>
                <th style=" background-color: lightpink">Importe Miel</th>
                <th style=" background-color: lightpink"> ' . utf8_decode('Importe Cera/Prod. Apícolas') . '</th>
                <th style=" background-color: lightpink">Gastos</th>
                <th style=" background-color: lightpink">Devoluciones Efectivo</th>
                <th style=" background-color: lightpink"> ' . utf8_decode('Devoluciones Cera/Prod. Apícolas') . '
                </th>
                <th style=" background-color: lightpink"> ' . utf8_decode('Pago Cera/Prod. Apícolas') . '</th>
                <th>Final</th>
            </tr>
        </thead>

        ';
echo '
        <tbody>
            <tr style="font-size:14px">
                <td style="text-align: right">' . utf8_decode($cantidadTotalBancos) . '</td>
                <td style="text-align: right">' . utf8_decode($cantidadTotalEfectivo) . '</td>
                <td style="text-align: right"> ' . utf8_decode($cantidadTotalIngresosCeraApicola) . ' </td>
                <td style="text-align: right"> ' . utf8_decode($cantidadTotalRetenciones) . ' </td>
                
                <td style="text-align: right"> ' . utf8_decode($cantidadTotalKilosMiel) . ' </td>
                <td style="text-align: right"> ' . utf8_decode($cantidadTotalPrecioPromedio) . '
                </td>
                <td style="text-align: right"> ' . utf8_decode($cantidadTotalImporteMiel) . ' </td>
                <td style="text-align: right"> ' . utf8_decode($cantidadTotalEgresosCeraApicola) . '
                </td>
                <td style="text-align: right"> ' . utf8_decode($cantidadTotalGastos) . ' </td>
                <td style="text-align: right">
                ' . utf8_decode($cantidadTotalDevolucionesEfectivo) . '</td>
                <td style="text-align: right">
                ' . utf8_decode($cantidadTotalDevolucionesCeraApicola) . '</td>
                <td style="text-align: right">' . utf8_decode($cantidadTotalVentasCeraApicola) . '
                </td>
                <td style="text-align: right"> ' . utf8_decode($cantidadTotalGranTotal) . ' </td>
            </tr>
        </tbody>
        </table>
        </div>
    ';
