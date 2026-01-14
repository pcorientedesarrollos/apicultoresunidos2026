<?php

function documentoUno($reporte)
{
    // Acumulado
    header('Content-type: application/vnd.ms-excel');
    header("Content-Disposition: attachment; filename=Reporte de auditoría.xls");
    header("Pragma: no-cache");
    header("Expires:0");
    echo '<table>
            <tr>
                <td style="text-align:center; font-size:22px; font-weight:bold" colspan="8">' . utf8_decode('Apicultores Unidos de la Peninsula S.A. de C.V.') . '</td>
            </tr>
            <tr>
                <td style="text-align:center; font-size:16px; font-weight:bold" colspan="8">' . utf8_decode('RESULTADOS DE AUDITORÍA') . '</td>
            </tr>
            <tr>
                <td style="text-align:center; font-size:16px; font-weight:bold" colspan="8">' . utf8_decode('Planta Mérida, Yucatán') . '</td>
            </tr>
            <tr>
                
            </tr>
        </table>';
    echo '<table class="table" border=1>
                <tr>
                    <th colspan="3" style="text-align:center">' . utf8_decode('Resultados de la auditoría') . '</th>
                </tr>
                <tr>
                    <th>Concepto</th>
                    <th>Cantidad</th>
                    <th>Neto</th>

                </tr>
                <tr>
                    <td>' . utf8_decode('Tambores escaneados') . '</td>
                    <td style="text-align:right">' . number_format($reporte['ttEscaneados'], 2, '.', ',') . '</td>
                    <td style="text-align:right">' . number_format(floatval($reporte['totalEscaneado']), 2, '.', ',') . '</td>
                </tr>
                <tr>
                    <td>' . utf8_decode('Tambores no escaneados') . '</td>
                    <td style="text-align:right">' . number_format($reporte['ttNoEscaneados'], 2, '.', ',') . '</td>
                    <td style="text-align:right">' . number_format(floatval($reporte['totalNoEscaneado']), 2, '.', ',') . '</td>
                </tr>
                <tr>
                    <td>' . utf8_decode('Compras de miel') . '</td>
                    <td style="text-align:right"></td>
                    <td style="text-align:right">' . number_format($reporte['comprasMiel'], 2, '.', ',') . '</td>
                </tr>
            </table>';

    echo '<table>
        <tr>
        <th></th>
        <th></th>
        <th></th>
        <th></th>

        <th> ' . utf8_decode('Código: RAL-IVF-01 - REVISIÓN: 01') . '</th>
        </tr>
        </table>';

    echo '<table border=1>
        <thead>
            <tr style="height: 35px">
                <th class="bgm-blue" style="text-transform: none; font-weight: 600"></th>
                <th class="bgm-blue" style="text-transform: none; font-weight: 600">ZONA</th>
                <th class="bgm-blue" style="text-transform: none; font-weight: 600">TIPO DE MIEL</th>
                <th class="bgm-blue" style="text-transform: none; font-weight: 600">ESCANEADOS</th>
                <th class="bgm-blue" style="text-transform: none; font-weight: 600">NO ESCANEADOS</th>
                <th class="bgm-blue" style="text-transform: none; font-weight: 600">NETO ESCANEADO</th>
            </tr>
        </thead>
        <tbody>';
    foreach ($reporte['zonas'] as $key => $zona) {
        $_style = $zona['tambEscaneados'] == 0 ? 'background: #ffcccc;background-color: #ffcccc;' : '';
        echo '<tr style="' . $_style . '">
                <td>' . intval($key + 1) . '</td>
                <td>' . utf8_decode($zona['zona']) . '</td>
                <td>' . utf8_decode($zona['tipoDeMiel']) . '</td>
                <td style="text-align:center">' . number_format($zona['tambEscaneados'], 0, '.', ',') . '</td>
                <td style="text-align:center">' . number_format($zona['tambores_faltantes'], 0, '.', ',') . '</td>
                <td style="text-align:center">' . number_format($zona['netoEscaneado'], 2, '.', ',') . '</td>
            </tr>';
    }
    echo '</tbody>
    </table>';
}

function documentoDos($reporte)
{
    // Por zonas
    header('Content-type: application/vnd.ms-excel');
    header("Content-Disposition: attachment; filename=Reporte de auditoría.xls");
    header("Pragma: no-cache");
    header("Expires:0");
    echo '<table>
        <tr>
            <td style="text-align:center; font-size:22px; font-weight:bold" colspan="8">' . utf8_decode('Apicultores Unidos de la Peninsula S.A. de C.V.') . '</td>
        </tr>
        <tr>
            <td style="text-align:center; font-size:16px; font-weight:bold" colspan="8">' . utf8_decode('INVENTARIO FÍSICO ALMACÉN') . '</td>
        </tr>
        <tr>
            <td style="text-align:center; font-size:16px; font-weight:bold" colspan="8">' . utf8_decode('Planta Mérida, Yucatán') . '</td>
        </tr>
        <tr>
        <td style="text-align:center; font-size:16px; font-weight:black" colspan="8">' . utf8_decode('Código: RAL-IVF-01 - REVISIÓN: 01') . '</td>
        </tr>
    </table>';

    echo '<table border=1>
            <thead>
                <tr>';

    foreach ($reporte['zonas'] as $zona) {
        echo '<th colspan="2">Zona</th>
            <th>' . $zona['zona'] . '</th>
            <th></th>';
    }
    echo '</tr>
    <tr>';

    foreach ($reporte['zonas'] as $zona) {
        echo '<th>#</th>
            <th>Folio</th>
            <th>Peso neto</th>
            <th></th>';
    }

    echo '</tr>
        </thead>';

    $maximo_tambores = 0;
    foreach ($reporte['zonas'] as $indexZona => $zona) {
        if (count($zona['desglose']['tambores_escaneados']) > $maximo_tambores) {
            $maximo_tambores = count($zona['desglose']['tambores_escaneados']);
        }
    }

    echo '<tbody>';

    for ($i = 0; $i < $maximo_tambores; $i++) {
        $fila = '';
        foreach ($reporte['zonas'] as $index => $zona) {
            if (array_key_exists($i, $zona['desglose']['tambores_escaneados'])) {
                $tambor = $zona['desglose']['tambores_escaneados'][$i];
                $columnas = '<td>' . intval($i + 1) . '</td><td>' . $tambor['idAlmacen'] . '</td><td>' . number_format($tambor['neto'], 2, '.', ',') . '</td><td></td>';
            } else {
                $columnas = '<td></td><td></td><td></td><td></td>';
            }
            $fila .= $columnas;
        }
        echo '<tr>' . $fila . '</tr>';
    }

    $gran_total = 0;

    echo '<tr>';
    foreach ($reporte['zonas'] as $zona) {
        $netoZona = 0;
        foreach ($zona['desglose']['tambores_escaneados'] as $tamborEnZona) {
            $netoZona += floatval($tamborEnZona['neto']);
        }
        $gran_total += $netoZona;
        echo '<th colspan="2"><b>Total neto</b></th>
            <th>' . utf8_decode(number_format($netoZona, 2, '.', ',')) . '</th>
            <th></th>';
    }
    echo '</tr>';
    echo '<tr></tr>';

    echo '<tr>';
    foreach ($reporte['zonas'] as $zona) {
        echo '<th colspan="2">Revision Conteo</th>
            <th>__________</th>
            <th></th>';
    }
    echo '</tr>';
    echo '<tr>';
    foreach ($reporte['zonas'] as $zona) {
        echo '<th colspan="2">Supervisa Contero</th>
            <th>__________</th>
            <th></th>';
    }
    echo '</tr>';

    echo '<tr></tr>';

    echo '<tr>
        <td></td>
        <td>TOTAL</td>
        <td>' . utf8_decode(number_format($gran_total, 2, '.', ',')) . '</td>
    </tr>';

    echo '</tbody></table>';
}

function documentoTres($reporte)
{
    // Tambores no escaneados
    $tambores = $reporte['tambores_no_escaneados'];
    header('Content-type: application/vnd.ms-excel');
    header("Content-Disposition: attachment; filename=TAMBORES_NO_ESCANEADOS.xls");
    header("Pragma: no-cache");
    header("Expires:0");

    echo '<table>
            <tr>
                <td style="text-align:center; font-size:22px; font-weight:bold" colspan="6">' . utf8_decode('Apicultores Unidos de la Peninsula S.A. de C.V.') . '</td>
            </tr>
            <tr>
                <td style="text-align:center; font-size:16px; font-weight:bold" colspan="6">' . utf8_decode('TAMBORES NO ESCANEADOS DURANTE LA AUDITORÍA') . '</td>
            </tr>
            <tr>
                <td style="text-align:center; font-size:16px; font-weight:bold" colspan="6">' . utf8_decode('Planta Mérida, Yucatán') . '</td>
            </tr>
            <tr>
                
            </tr>
        </table>';


    echo '<table>
        <tr>
        <th></th>
        <th></th>
        <th></th>
        <th></th>

        <th> ' . utf8_decode('Código: RAL-IVF-01 - REVISIÓN: 01') . '</th>
        </tr>
        </table>';

    echo '<table border=1>
        <thead>
            <tr style="height: 35px">
                <th class="bgm-blue" style="text-transform: none; font-weight: 600"></th>
                <th class="bgm-blue" style="text-transform: none; font-weight: 600">FOLIO</th>
                <th class="bgm-blue" style="text-transform: none; font-weight: 600">TIPO DE MIEL</th>
                <th class="bgm-blue" style="text-transform: none; font-weight: 600">NETO</th>
                <th class="bgm-blue" style="text-transform: none; font-weight: 600">FECHA ENTRADA</th>
                <th class="bgm-blue" style="text-transform: none; font-weight: 600">ESTADO DEL TAMBOR</th>
            </tr>
        </thead>
        <tbody>';

    $total_neto = 0;
    foreach ($tambores as $key => $tambor) {
        echo '<tr>
                <td>' . intval($key + 1) . '</td>
                <td style="text-align:left">' . utf8_decode($tambor['idAlmacen']) . '</td>
                <td style="text-align:left">' . utf8_decode($tambor['tipoDeMiel']) . '</td>
                <td style="text-align:right">' . number_format($tambor['neto'], 2, '.', ',') . '</td>
                <td style="text-align:left">' . $tambor['fecha'] . '</td>
                <td style="text-align:left">' . utf8_decode($tambor['estado_tambor']) . '</td>
            </tr>';
        $total_neto += floatval($tambor['neto']);
    }
    echo '</tbody>
    </table>';

    echo '<table>
        <tr></tr>
        <tr></tr>
        </table>';

    echo '<table class="table" border=1>
                <tr>
                    <th style="text-align:center">' . utf8_decode('Total neto') . '</th>
                </tr>
                <tr>
                    <th>' . number_format($total_neto, 2, '.', ',') .  '</th>
                </tr>
            </table>';
}

function documentoError()
{
    $nombre_archivo = 'Documento.xls';
    // header('Content-type: application/vnd.ms-excel');
    // header("Content-Disposition: attachment; filename=$nombre_archivo");
    // header("Pragma: no-cache");
    // header("Expires:0");
}

if (isset($_GET['idAud']) && isset($_GET['tipoReporte'])) {
    include_once '../../almacen/php/dameInformacionAuditoria.php';
    $tipoReporte = $_GET['tipoReporte'];
    $reporte = obtenerInformacionAuditoria($_GET['idAud'], $tipoReporte);

    if ($tipoReporte == '1') {
        documentoUno($reporte);
    } elseif ($tipoReporte == '2') {
        documentoDos($reporte);
    } elseif ($tipoReporte == '3') {
        documentoTres($reporte);
    } else {
        documentoError();
    }
} else {
    documentoError();
}
