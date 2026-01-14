<?php

include_once '../../inventarios/php/obtenerInventarioMielSobranteDisponible.php';

try {

    if (isset($_GET['idTipoDeMiel'])) {

        // if(isset($_GET['laboratorio'])) {
        //     $laboratorio = TRUE;
        //     $columnas = 20;
        // } else {
        //     $laboratorio = FALSE;
            $columnas = 9;
        // }

        if (isset($_GET['idSobrante'])) {
            $reporte = obtenerInventarioMielSobranteDisponible($_GET['idTipoDeMiel'], $_GET['idSobrante']);
        } else {
            $reporte = obtenerInventarioMielSobranteDisponible($_GET['idTipoDeMiel'], FALSE);
        }

        $nombre_archivo = 'Inventario de miel sobrante disponible.xls';
        header('Content-type: application/vnd.ms-excel');
        header("Content-Disposition: attachment; filename=$nombre_archivo");
        header("Pragma: no-cache");
        header("Expires:0");

        echo '<table>
        <tr style="text-align: center;">
            <td colspan="' . $columnas . '"><b>OAXACA MIEL S.A. DE C.V.</b></td>
        </tr>
    
        <tr style="text-align: center;">
            <td colspan="' . $columnas . '"><b> </b></td>
        </tr>
    
        <tr style="text-align: center;">
            <td colspan="' . $columnas . '"><b>' . utf8_decode('') . '</b></td>
        </tr>
    
        <tr style="text-align: center;">
            <td colspan="' . $columnas . '"><b>' . utf8_decode('') . '</b></td>
        </tr>
    
        <tr style="text-align: center;">
            <td colspan="' . $columnas . '"><b>Tel: (999) 9.88.09.90</b></td>
        </tr></table>';
        echo '<table>
                <tr></tr>
                <tr></tr>
                <tr></tr>
            </table>';
    
        echo '<table border=1 style="border-collapse: collapse">
        <thead>
            <th>
                ' . utf8_decode('FOLIO') . '
            </th>
            <th>
            ' . utf8_decode('CÓDIGO') . '
            </th>
            <th>
                ' . utf8_decode('ENTRADA') . '
            </th>
            <th>
                ' . utf8_decode('FECHA') . '
            </th>
            <th>
                ' . utf8_decode('SOBRANTE') . '
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
                ' . utf8_decode('ZONA') . '
            </th>';
              echo '</thead>
        <tbody>';
    foreach($reporte['registros'] as $tambor) {

        $tambor['fecha'] = DateTime::createFromFormat('Y-m-d', $tambor['fecha']);
        echo'<tr>
                <td style="text-align:left;">' . utf8_decode($tambor['consecutivo']) . '</td>
                <td style="text-align:left;">' . utf8_decode($tambor['codigo']) . '</td>                
                <td style="text-align:left;">' . utf8_decode($tambor['consecutivoEntrada']) . '</td>
                <td style="text-align:left;">' . utf8_decode(date_format($tambor['fecha'], 'd/m/Y')) . '</td>
                <td style="text-align:left;">' . utf8_decode($tambor['sobrante']) . '</td>
                <td style="text-align:right;">' . utf8_decode(number_format($tambor['bruto'], 2, '.', ',')) . '</td>
                <td style="text-align:right;">' . utf8_decode(number_format($tambor['tara'], 2, '.', ',')) . '</td>
                <td style="text-align:right;">' . utf8_decode(number_format($tambor['neto'], 2, '.', ',')) . '</td>
                <td style="text-align:left;">' . utf8_decode($tambor['zona']) . '</td>';
      
        echo '</tr>';
    };

    echo    '<tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td style="text-align:right;">Total</td>
            <td style="text-align:right;">' . utf8_decode(number_format($reporte['encabezado']['bruto'], 2, '.', ',')) . '</td>
            <td style="text-align:right;">' . utf8_decode(number_format($reporte['encabezado']['tara'], 2, '.', ',')) . '</td>
            <td style="text-align:right;">' . utf8_decode(number_format($reporte['encabezado']['neto'], 2, '.', ',')) . '</td>
            <td></td>
        </tr>';
    
    echo    '</tbody>
    </table>';
    
    
    } else {
        throw new Exception('No se recibieron parámetros');
    }

} catch(Exception $e){
    $nombre_archivo = 'Documento.xls';

    header('Content-type: application/vnd.ms-excel');
    header("Content-Disposition: attachment; filename=$nombre_archivo");
    header("Pragma: no-cache");
    header("Expires:0");

    echo utf8_decode($e->getMessage());

}
