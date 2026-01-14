<?php

include_once '../../inventarios/php/obtenerInventarioMielDisponible.php';

try {

    if (isset($_GET['idTipoDeMiel'])) {

        if(isset($_GET['laboratorio'])) {
            $laboratorio = TRUE;
            $columnas = 20;
        } else {
            $laboratorio = FALSE;
            $columnas = 10;
        }

        if (isset($_GET['idZona'])) {
            $reporte = obtenerInventarioMielDisponible($_GET['idTipoDeMiel'], $_GET['idZona'], $laboratorio);
        } else {
            $reporte = obtenerInventarioMielDisponible($_GET['idTipoDeMiel'], FALSE, $laboratorio);
        }

        $nombre_archivo = 'Inventario de miel disponible.xls';
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
                ' . utf8_decode('ENTRADA') . '
            </th>
            <th>
                ' . utf8_decode('FECHA') . '
            </th>
            <th>
                ' . utf8_decode('PROVEEDOR') . '
            </th>
            <th>
                ' . utf8_decode('ID SAGARPA') . '
            </th>
            <th>
                ' . utf8_decode('LOCALIDAD') . '
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
        if($laboratorio) {
            echo '<th>
                    ' . utf8_decode('Porcentaje') . '
                </th>
                <th>
                    ' . utf8_decode('Sf') . '
                </th>
                <th>
                    ' . utf8_decode('St') . '
                </th>
                <th>
                    ' . utf8_decode('C13') . '
                </th>
                <th>
                    ' . utf8_decode('HMF') . '
                </th>
                <th>
                    ' . utf8_decode('Color') . '
                </th>
                <th>
                    ' . utf8_decode('tt') . '
                </th>
                <th>
                    ' . utf8_decode('Micro') . '
                </th>
                <th>
                    ' . utf8_decode('Floración') . '
                </th>
                <th>
                    ' . utf8_decode('Resultado final') . '
                </th>';
        }
        echo '</thead>
        <tbody>';
    foreach($reporte['registros'] as $tambor) {

        $tambor['fecha'] = DateTime::createFromFormat('Y-m-d', $tambor['fecha']);
        echo'<tr>
                <td style="text-align:left;">' . utf8_decode($tambor['folio']) . '</td>
                <td style="text-align:left;">' . utf8_decode($tambor['entrada']) . '</td>
                <td style="text-align:left;">' . utf8_decode(date_format($tambor['fecha'], 'd/m/Y')) . '</td>
                <td style="text-align:left;">' . utf8_decode($tambor['Proveedor']) . '</td>
                <td style="text-align:left;">' . utf8_decode($tambor['idSagarpa']) . '</td>
                <td style="text-align:left;">' . utf8_decode($tambor['localidad']) . '</td>
                <td style="text-align:right;">' . utf8_decode(number_format($tambor['bruto'], 2, '.', ',')) . '</td>
                <td style="text-align:right;">' . utf8_decode(number_format($tambor['tara'], 2, '.', ',')) . '</td>
                <td style="text-align:right;">' . utf8_decode(number_format($tambor['neto'], 2, '.', ',')) . '</td>
                <td style="text-align:left;">' . utf8_decode($tambor['zona']) . '</td>';
        if($laboratorio) {
            if($tambor['laboratorio']['micro'] == '1') {
                $microbiologia = 'Ausencia';
            } else if($tambor['laboratorio']['micro'] == '2') {
                $microbiologia = 'Presencia';
            } else {
                $microbiologia = '';
            }
            
            echo'<td style="text-align:left;">' . utf8_decode($tambor['laboratorio']['porcentaje']) . '</td>
                <td style="text-align:left;">' . utf8_decode($tambor['laboratorio']['sf']) . '</td>
                <td style="text-align:left;">' . utf8_decode($tambor['laboratorio']['st']) . '</td>
                <td style="text-align:left;">' . utf8_decode($tambor['laboratorio']['c13']) . '</td>
                <td style="text-align:left;">' . utf8_decode($tambor['laboratorio']['hmf']) . '</td>
                <td style="text-align:left;">' . utf8_decode($tambor['laboratorio']['color']) . '</td>
                <td style="text-align:right;">' . utf8_decode($tambor['laboratorio']['tt']) . '</td>
                <td style="text-align:right;">' . utf8_decode($microbiologia) . '</td>
                <td style="text-align:right;">' . utf8_decode($tambor['laboratorio']['floracion']) . '</td>
                <td style="text-align:left;">' . utf8_decode($tambor['laboratorio']['resultado']) . '</td>';
        }

        echo '</tr>';
    };

    echo    '<tr>
            <td></td>
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
