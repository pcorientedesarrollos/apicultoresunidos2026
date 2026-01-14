<?php

include_once '../../almacen/php/traeEntradasDeMateriasPrimas.php';

if (isset($_GET['fecha1']) && isset($_GET['fecha2'])) {
    $reporte = obtenerEntradasMateriaPrima($_GET['tipoDeMiel'], $_GET['fecha1'], $_GET['fecha2']);
} else {
    $reporte = obtenerEntradasMateriaPrima($_GET['tipoDeMiel']);
}

header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=ENTRADA DE MATERIA PRIMA.xls");
header("Pragma: no-cache");
header("Expires:0");

echo '<table>
<tr>
    <td style="text-align: center;" colspan="5"><b>OAXACA MIEL S.A. DE C.V.</b></td>
</tr>

<tr>
    <td style="text-align: center;" colspan="5"><b> </b></td>
</tr>
<tr>
    <td style="text-align: center;" colspan="5">ENTRADA DE MATERIA PRIMA</td>
</tr>
<tr></tr></table>';

echo '
    <table>
    <tr>
    <th></th>
    <th></th>
    <th></th>
    <th>' . utf8_decode('Código: RAL-EE-01 - REVISIÓN: 01') . '</th>
    </tr>
    </table>
';

echo '<table border=1>
<thead>
    <tr>
        <th style="text-align: center; text-transform: none; font-weight: 600">No.</th>
        <th style="text-align: left; text-transform: none; font-weight: 600">Fecha</th>
        <th style="text-align: left; text-transform: none; font-weight: 600">Proveedor</th>
        <th style="text-align: right; text-transform: none; font-weight: 600">Cantidad</th>
        <th style="text-align: right; text-transform: none; font-weight: 600">Importe</th>
    </tr>
</thead>
<tbody>';
foreach ($reporte as $e) {
    echo '<tr>
        <td style="text-align: center">' . $e->idEntradaMateria . '</td>
        <td style="text-align: left">' . $e->fecha . '</td>
        <td style="text-align: left">' . utf8_decode(strtoupper($e->proveedor)) . '</td>
        <td style="text-align: right">' . number_format($e->cantidadTotal, 2, '.', ',') . '</td>
        <td style="text-align: right">$' . number_format($e->importeTotal, 2, '.', ',') . '</td>
    </tr>';
}
echo '</tbody >
    </table >';
