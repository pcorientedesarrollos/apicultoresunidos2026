<?php
include_once '../../almacen/php/dameInformacionAuditoriaPZona.php';

if (isset($_GET['idZonaAuditoria']) && isset($_GET['idTipoDeMiel']) && isset($_GET['idAuditoria']) && isset($_GET['tipoReporte'])) {
    $reporte = obtenerDetalleZona($_GET['idZonaAuditoria'], $_GET['idTipoDeMiel'], $_GET['idAuditoria'], $_GET['tipoReporte']);
    $tipoReporte = $_GET['tipoReporte'];
    switch ($_GET['tipoReporte']) {
        case '1':
            $nombre_reporte = 'Tambores que se mantienen en la zona';
            break;
        case '2':
            $nombre_reporte = 'Tambores que cambiaron a esta zona';
            break;
        case '3':
            $nombre_reporte = utf8_decode('Tambores no escaneados en la zona. (Se muestran únicamente los tambos con fecha de entrada menor o igual a fecha de programación)');
            break;
        case '4':
            $nombre_reporte = 'Todos los tambores de la zona';
            break;
    }

    // $nombre_reporte .= '.xls';
    $reporte['fecha'] = DateTime::createFromFormat('Y-m-d', $reporte['fecha']);

    header('Content-type: application/vnd.ms-excel');
    header("Content-Disposition: attachment; filename=Reporte de auditoría.xls");
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
            </tr></table> <br>';

    echo '<table style="border-collapse: collapse" border=1>
            <tr>
                <td colspan="2" style="text-align:center">Resultados';
    if ($tipoReporte == '4') {
        echo ' (Folios escaneados)';
    }
    echo '    </td>        </tr>
            <tr>
                <td>Bruto</td>
                <td style="text-align:right">' . utf8_decode(number_format($reporte['encabezado']['bruto'], 2, '.', ',')) . '</td>
            </tr>
            <tr>
                <td>Tara</td>
                <td style="text-align:right">' . utf8_decode(number_format($reporte['encabezado']['tara'], 2, '.', ',')) . '</td>
            </tr>
            <tr>
                <td>Neto:</td>
                <td style="text-align:right">' . utf8_decode(number_format($reporte['encabezado']['neto'], 2, '.', ',')) . '</td>
            </tr>
        </table> <br>';

    if ($tipoReporte == '4') {
        echo '<table style="border-collapse: collapse" border=1>
            <tr>
                <td colspan="2" style="text-align:center">Resultados (Folios disponibles no escaneados)</td>
            </tr>
            <tr>
                <td>Bruto</td>
                <td style="text-align:right">' . utf8_decode(number_format($reporte['encabezadoNoEscaneado']['bruto'], 2, '.', ',')) . '</td>
            </tr>
            <tr>
                <td>Tara</td>
                <td style="text-align:right">' . utf8_decode(number_format($reporte['encabezadoNoEscaneado']['tara'], 2, '.', ',')) . '</td>
            </tr>
            <tr>
                <td>Neto:</td>
                <td style="text-align:right">' . utf8_decode(number_format($reporte['encabezadoNoEscaneado']['neto'], 2, '.', ',')) . '</td>
            </tr>
        </table>';
    }

    echo '<table>
            <tr></tr>
            <tr>
                <td><b>' . $nombre_reporte . '</b></td>
            </tr>
        </table> <br>';
    if ($tipoReporte == '4') {
        echo '<b>' . utf8_decode('Folios escaneados') . '</b>';
        echo '<br> <br>';
    }
    echo '<table border=1 style="border-collapse: collapse">
            <thead>
                <th>
                    ' . utf8_decode('FOLIO TAMBOR') . '
                </th>
                <th>
                    ' . utf8_decode('BRUTO') . '
                </th>
                <th>
                    ' . utf8_decode('TARA') . '
                </th>
                <th>
                    ' . utf8_decode('NETO') . '
                </th>
                <th>
                ' . utf8_decode('NUEVA ZONA') . '
                </th>
                <th>
                    ' . utf8_decode('FECHA ENTRADA') . '
                </th>
                <th>
                    ' . utf8_decode('TIPO DE MIEL') . '
                </th>
                <th>
                    ' . utf8_decode('ANTERIOR ZONA') . '
                </th>               
                <th>
                    ' . utf8_decode('ESTADO DEL TAMBOR') . '
                </th>
                <th>
                    ' . utf8_decode('USUARIO QUE ESCANEA') . '
                </th>
                <th>
                    ' . utf8_decode('FECHA Y HORA DE ESCANEO') . '
                </th>
            </thead>
            <tbody>';
    foreach ($reporte['tambores'] as $tambor) {
        echo    '<tr>
                    <td style="text-align:center;">' . utf8_decode($tambor['folio']) . '</td>
                    <td style="text-align:right;">' . utf8_decode(number_format($tambor['bruto'], 2, '.', ',')) . '</td>
                    <td style="text-align:right;">' . utf8_decode(number_format($tambor['tara'], 2, '.', ',')) . '</td>
                    <td style="text-align:right;">' . utf8_decode(number_format($tambor['neto'], 2, '.', ',')) . '</td>
                    <td style="text-align:center;">' . utf8_decode($tambor['zonaDespues']) . '</td>                  
                    <td style="text-align:center;">' . utf8_decode($tambor['fecha']) . '</td>
                    <td style="text-align:center;">' . utf8_decode($tambor['tipoDeMiel']) . '</td>
                    <td style="text-align:center;">' . utf8_decode($tambor['zonaAntes']) . '</td>
                     <td style="text-align:right;">' . utf8_decode($tambor['estado_tambor']) . ' ' . utf8_decode($tambor['idLote']) . '</td>
                    <td style="text-align:center;">' . utf8_decode(strtoupper($tambor['usuario'])) . '</td>
                    <td style="text-align:center;">' . utf8_decode($tambor['hora']) . '</td>

                </tr>';
    };
    echo    '</tbody>
        </table> <br><br>';
    if ($tipoReporte == '4') {
        echo '<b>' . utf8_decode('Folios no escaneados') . '</b>';
        echo '<br> <br>';
        echo '<table border=1 style="border-collapse: collapse">
        <thead>
            <th>
                ' . utf8_decode('FOLIO TAMBOR') . '
            </th>
            <th>
                ' . utf8_decode('BRUTO') . '
            </th>
            <th>
                ' . utf8_decode('TARA') . '
            </th>
            <th>
                ' . utf8_decode('NETO') . '
            </th>
            <th>
            ' . utf8_decode('NUEVA ZONA') . '
            </th>
            <th>
                ' . utf8_decode('FECHA ENTRADA') . '
            </th>
            <th>
                ' . utf8_decode('TIPO DE MIEL') . '
            </th>
            <th>
                ' . utf8_decode('ANTERIOR ZONA') . '
            </th>               
            <th>
                ' . utf8_decode('ESTADO DEL TAMBOR') . '
            </th>
            <th>
                ' . utf8_decode('USUARIO QUE ESCANEA') . '
            </th>
            <th>
                ' . utf8_decode('FECHA Y HORA DE ESCANEO') . '
            </th>
        </thead>
        <tbody>';
        foreach ($reporte['tambores_noEscaneados'] as $tambor1) {
            echo    '<tr>
            <td style="text-align:center;">' . utf8_decode($tambor1['folio']) . '</td>
            <td style="text-align:right;">' . utf8_decode(number_format($tambor1['bruto'], 2, '.', ',')) . '</td>
            <td style="text-align:right;">' . utf8_decode(number_format($tambor1['tara'], 2, '.', ',')) . '</td>
            <td style="text-align:right;">' . utf8_decode(number_format($tambor1['neto'], 2, '.', ',')) . '</td>
            <td style="text-align:center;">' . utf8_decode($tambor1['zonaDespues']) . '</td>                  
            <td style="text-align:center;">' . utf8_decode($tambor1['fecha']) . '</td>
            <td style="text-align:center;">' . utf8_decode($tambor1['tipoDeMiel']) . '</td>
            <td style="text-align:center;">' . utf8_decode($tambor1['zonaAntes']) . '</td>
             <td style="text-align:right;">' . utf8_decode($tambor1['estado_tambor']) . ' ' . utf8_decode($tambor1['idLote']) . '</td>
            <td style="text-align:center;">' . utf8_decode(strtoupper($tambor1['usuario'])) . '</td>
            <td style="text-align:center;">' . utf8_decode($tambor1['hora']) . '</td>

        </tr>';
        };
        echo    '</tbody>';
    }
} else {
    $nombre_archivo = ' Documento . xls ';

    header(' Content - type: application / vnd . ms - excel');
    header("Content-Disposition: attachment; filename=$nombre_archivo");
    header("Pragma: no-cache");
    header("Expires:0");
}
