<?php

include_once '../../recoleccion/php/jefesDeCompras/totalComprasReales.php';

$resultado = obtenerDatosJefesDeCompras();
if ($_GET['miel'] == '1') {
    $nombre_reporte = 'Compras acumuladas miel 100% pura de abeja';
} else if ($_GET['miel'] == '2') {
    $nombre_reporte = 'Compras acumuladas miel 100% orgánica';
}

header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename = Compras acumuladas.xls");
header("Prafma: no-cache");
header("Expires:0");

//  Imprimir el formato del excel
echo '<table>
    <thead>
        <tr style="font-size:18px">
        <th colspan=1></th>
            <th colspan=5>
            ' . utf8_decode('Apicultores Unidos de la Península S.A. de C.V.') . '
            </th>
        </tr>
        <tr style="font-size:16px">
        <th colspan=1></th>
            <th colspan=5>
                ' . utf8_decode($nombre_reporte) . '
            </th>
        </tr>
        <tr><th></th></tr>
        <tr>
            <th colspan=6></th>
            <th style="text-align:right">
            <p style="font-size:12px">' . utf8_decode('Código: RCO-CA-01') . '<p>
            </th>
        </tr>
    </thead>
</table>';


echo '<table>
	<tr>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td>
                <p style="font-size:22px; font-weight:bold"> <b>' . utf8_decode('Código:') . 'RCO-PA-04</b> </p>
                </td>
	</tr>
</table>';

if (isset($_GET['acumulado'])) {
    echo '<table>
    <thead>
        <tr>
        <th style="background-color: #ffe558; text-transform: none; font-weight: 700; text-align: center; font-size: 15px;"></th>
            <th style="background-color: #ffe558; text-transform: none; font-weight: 700; text-align: center; font-size: 15px;">' . utf8_decode('Proyección anual (kg)') . '</th>
            <th style="background-color: #ffe558; text-transform: none; font-weight: 700; text-align: center; font-size: 15px;">' . utf8_decode('% Total') . '</th>
            <th style="background-color: #ffe558; text-transform: none; font-weight: 700; text-align: center; font-size: 15px;">' . utf8_decode('Compras realizadas (kg)') . '</th>
            <th style="background-color: #ffe558; text-transform: none; font-weight: 700; text-align: center; font-size: 15px;">' . utf8_decode('% Avance') . '</th>       
        </tr>
    </thead>
    <tbody>';
    foreach ($resultado['compradores'] as $comprador) {
        echo '<tr>
        <td style="text-transform: none; font-weight: 400; font-size: 15px;">' . utf8_decode($comprador['nombre']) . '</td>
        <td style="text-transform: none; font-weight: 400; text-align: center; font-size: 15px;">' . utf8_decode(number_format($comprador['meta'], 2, '.', ',')) . '</td>
        <td style="text-transform: none; font-weight: 400; text-align: center; font-size: 15px;">' . utf8_decode(number_format($comprador['totalPorCiento'], 2, '.', ',')) . '' . "%" . '</td>
        <td style="text-transform: none; font-weight: 400; text-align: center; font-size: 15px;">' . utf8_decode(number_format($comprador['totalReal'], 2, '.', ',')) . '</td>
        <td style="text-transform: none; font-weight: 400; text-align: center; font-size: 15px;">' . utf8_decode(number_format($comprador['avance'], 2, '.', ',')) . '' . "%" . '</td>    
    </tr>';
    }
    echo '
    <tr>
        <td style="text-transform: none; font-weight: 400; font-size: 15px;"><b>' . utf8_decode('Total') . '</b></td>    
        <td style="text-transform: none; font-weight: 400; text-align: center; font-size: 15px;"><b>' . utf8_decode(number_format($resultado['sumaKgsMetas'], 2, '.', ',')) . '</b></td>
        <td style="text-transform: none; font-weight: 400; text-align: center; font-size: 15px;"><b>' . utf8_decode(number_format($resultado['sumaTotalPorCiento'], 2, '.', ',')) . '' . "%" . '</b></td>
        <td style="text-transform: none; font-weight: 400; text-align: center; font-size: 15px;"><b>' . utf8_decode(number_format($resultado['sumaKgsCompraReal'], 2, '.', ',')) . '</b></td>
        <td style="text-transform: none; font-weight: 400; text-align: center; font-size: 15px;"><b>' . utf8_decode(number_format($resultado['sumaTotalAvance'], 2, '.', ',')) . '' . "%" . '</b></td>
    </tr>
</tbody>
</table>
<br>
<table>
    <thead>
        <tr>
            <th style="background-color: #ffe558; text-transform: none; font-weight: 700; text-align: center; font-size: 15px;" colspan="6">' . utf8_decode('Proyección vs acumulado de
            kilos comprados') . '</th>
       </tr>
       <tr>
            <th style="background-color: #ffe558; text-transform: none; font-weight: 700; text-align: center; font-size: 15px;"></th>
            <th style="background-color: #ffe558; text-transform: none; font-weight: 700; text-align: center; font-size: 15px;">' . utf8_decode('Meta de compras (kg)') . '</th>
            <th style="background-color: #ffe558; text-transform: none; font-weight: 700; text-align: center; font-size: 15px;">' . utf8_decode('Acumulado (kg)') . '</th>
            <th style="background-color: #ffe558; text-transform: none; font-weight: 700; text-align: center; font-size: 15px;">' . utf8_decode('Faltante') . '</th>
            <th style="background-color: #ffe558; text-transform: none; font-weight: 700; text-align: center; font-size: 15px;">' . utf8_decode('% Avance') . '</th>
            <th style="background-color: #ffe558; text-transform: none; font-weight: 700; text-align: center; font-size: 15px;">' . utf8_decode('% Acum. Compra') . '</th>     
        </tr>
    </thead>';
    foreach ($resultado['compradores'] as $comprador) {
        echo '<tbody>
        <tr>
            <td style="text-transform: none; font-weight: 400; font-size: 15px;"><b>' . utf8_decode($comprador['nombre']) . '</b></td>
            <td style="text-transform: none; font-weight: 400; text-align: center; font-size: 15px;"><b>' . utf8_decode(number_format($comprador['meta'], 2, '.', ',')) . '</b></td>
            <td style="text-transform: none; font-weight: 400; text-align: center; font-size: 15px;"><b>' . utf8_decode(number_format($comprador['totalReal'], 2, '.', ',')) . '</b></td>
            <td style="text-transform: none; font-weight: 400; text-align: center; font-size: 15px;"><b>' . utf8_decode(number_format($comprador['faltante'], 2, '.', ',')) . '</b></td>
            <td style="text-transform: none; font-weight: 400; text-align: center; font-size: 15px;"><b>' . utf8_decode(number_format($comprador['avance'], 2, '.', ',')) . '' . "%" . '</b></td>    
            <td style="text-transform: none; font-weight: 400; text-align: center; font-size: 15px;"><b>' . utf8_decode(number_format($comprador['acumuladoCompra'], 2, '.', ',')) . '' . "%" . '</b></td>    
        </tr>';
        foreach ($comprador['zonas'] as $zona) {
            echo '<tr>
            <td style="text-transform: none; font-weight: 400; font-size: 15px;">' . utf8_decode($zona['zona']) . '</td>
            <td style="text-transform: none; font-weight: 400; text-align: center; font-size: 15px;">' . utf8_decode(number_format($zona['metaZona'], 2, '.', ',')) . '</td>
            <td style="text-transform: none; font-weight: 400; text-align: center; font-size: 15px;">' . utf8_decode(number_format($zona['real'], 2, '.', ',')) . '</td>
            <td style="text-transform: none; font-weight: 400; text-align: center; font-size: 15px;">' . utf8_decode(number_format($zona['faltanteZona'], 2, '.', ',')) . '</td>
            <td style="text-transform: none; font-weight: 400; text-align: center; font-size: 15px;">' . utf8_decode(number_format($zona['avanceZona'], 2, '.', ',')) . '' . "%" . '</td>    
            <td style="text-transform: none; font-weight: 400; text-align: center; font-size: 15px;"></td>    
        </tr>';
        }
        echo '</tbody>';
    }
    echo '
    <tr>
        <td style="text-transform: none; font-weight: 400; font-size: 15px;"><b>' . utf8_decode('Total') . '</b></td>    
        <td style="text-transform: none; font-weight: 400; text-align: center; font-size: 15px;"><b>' . utf8_decode(number_format($resultado['sumaKgsMetas'], 2, '.', ',')) . '</b></td>
        <td style="text-transform: none; font-weight: 400; text-align: center; font-size: 15px;"><b>' . utf8_decode(number_format($resultado['sumaKgsCompraReal'], 2, '.', ',')) . '</b></td>        
        <td style="text-transform: none; font-weight: 400; text-align: center; font-size: 15px;"><b>' . utf8_decode(number_format($resultado['totalFaltante'], 2, '.', ',')) . '</b></td>
        <td style="text-transform: none; font-weight: 400; text-align: center; font-size: 15px;"><b>' . utf8_decode(number_format($resultado['sumaTotalAvance'], 2, '.', ',')) . '' . "%" . '</b></td>
        <td style="text-transform: none; font-weight: 400; text-align: center; font-size: 15px;"><b>' . utf8_decode(number_format($resultado['sumaTotalPorCiento'], 2, '.', ',')) . '' . "%" . '</b></td>  
    </tr>
</table>
';
} else {
    foreach ($resultado['compradores'] as $comprador) {
        echo '<table>
        <thead>
            <tr>       
                <th style="text-align: left; background-color: #ffe558; text-transform: none; font-weight: 700; font-size: 15px; width: 50%" colspan="2"> ' . utf8_decode($comprador['nombre']) . '</th>
                <th style="background-color: #ffe558; text-transform: none; font-weight: 700; text-align: center; font-size: 15px; width: 50%"> ' . utf8_decode('Compra total (kg)') . '</th>
            </tr>
        </thead>
        <tbody>';
        foreach ($comprador['zonas'] as $zona) {
            echo '<tr>
            <td style="text-transform: none; font-weight: 400; font-size: 15px; width: 25%">' . utf8_decode($zona['zona']) . '</td>
            <td style="text-transform: none; font-weight: 400; font-size: 15px; width: 25%">' . utf8_decode($zona['referencia']) . '</td>
            <td style="text-transform: none; font-weight: 400; text-align: center; font-size: 15px; width: 50%">' . utf8_decode(number_format($zona['real'], 2, '.', ',')) . '</td>
            </tr>';
        }
        echo '</tbody>
        </table>';
    }

    echo '<br>
    <table>
        <thead>
            <tr>
                <th colspan="3" style="background-color: #ffe558; text-transform: none; font-weight: 700; font-size: 15px;">' . utf8_decode('Resumen de compras') . '</th>
            </tr>
            <tr>
                <td colspan="2"></td>
                <td style="text-transform: none; font-weight: 400; text-align: center; font-size: 15px;"><b>' . utf8_decode('Kilogramos') . '</b></td>
            </tr>
        </thead>
        <tbody>';
    foreach ($resultado['compradores'] as $comprador) {
        echo '<tr>
            <td style="text-transform: none; font-weight: 400; font-size: 15px;" colspan="2">' . utf8_decode($comprador['nombre']) . '</td>
            <td style="text-transform: none; font-weight: 400; text-align: center; font-size: 15px;">' . utf8_decode(number_format($comprador['totalReal'], 2, '.', ',')) . '</td>
        </tr>
        ';
    }
    echo '
        <tr>
        <td style="text-transform: none; font-weight: 400; font-size: 15px;" colspan="2">' . utf8_decode('Total de kilos') . '</td>
            <td style="text-align: center">
                <b>
                ' . utf8_decode(number_format($resultado['sumaKgsCompraReal'], 2, '.', ',')) . '
               </b>
            </td>
        </tr>
    </tbody>
    </table>
    <br>';
}
