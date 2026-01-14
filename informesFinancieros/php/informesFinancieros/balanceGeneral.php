<?php

function generarXlsBalanceGeneral($mes = false, $meses = false, $esXls = false, $cuentas = false)
{
    $reporte = obtenerBalanceGeneral($mes, $meses, $esXls);
    $xls = '';

    if ((intval($reporte['activo']['total_activo']) != intval($reporte['pasivo_capital'])) && !$esXls) {
        $xls .= '<p class="text-danger"> <i class="fa fa-exclamation-triangle"></i> El reporte "Balance General" no finaliza correctamente al final de los saldos</p><br>';
    } else {
        if (!$esXls) {
            $xls .= '<p class="text-success"> <i class="fa fa-check"></i> El reporte "Balance General" finaliza correctamente</p><br>';
        }
    }


    $xls .= '<div class="row">';
            // <!-- Activos -->

    $xls .= '<div class="col-xs-12 col-md-6">
                <div class="table-responsive">
                    <table class="table table-bordered table-condensed">
                        <thead>
                            <tr>
                                <th colspan="2" class="text-center">Activos</th>
                            </tr>
                            <tr>
                                <th class="text-center">' . strtoupper('Descripcion') . '</th>
                                <th class="text-center">Saldo al final del mes</th>
                            </tr>
                        </thead>
                        <tbody>';
                            // <!-- Circulante -->
    $xls .= '<tr>
                <td colspan="2" class="text-center">
                    <p><b>CIRCULANTE</b></p>
                </td>
            </tr>';
    foreach ($reporte['activo']['circulante']['conceptos'] as $circulante) {
        $extraClase = isset($circulante->class) ? $circulante->class : 'sinExtraClase';
        $editable = isset($circulante->editable) ? $circulante->editable : 'false';
        $trid = isset($circulante->id) ? $circulante->id : '';
        $xls .= '<tr>
                    <td>
                        <p>' . strtoupper($circulante->nombre) . '</p>
                    </td>
                    <td class="text-right ' . $extraClase . '" contenteditable="' . $editable . '" id="' . $trid . '">
                        <p>$' . number_format($circulante->total, 2, '.', ',') . '</p>
                    </td>
                </tr>';
    }


    $xls .= '<tr>
                <td>
                    <p><b>Total Activo CIRCULANTE</b></p>
                </td>
                <td class="text-right">
                    <p><b>$' . number_format($reporte['activo']['circulante']['total'], 2, '.', ',') . '</b></p>
                </td>
            </tr>';


    // <!-- Fijo -->
    $xls .= '<tr>
                <td>
                    <p><b>FIJO</b></p>
                </td>
                <td></td>
            </tr>';

    foreach ($reporte['activo']['activos']['clasificaciones'] as $clasificacion) {
        $xls .= '<tr>
                    <td>
                        <p>' . strtoupper($clasificacion->nombre) . '</p>
                    </td>
                    <td class="text-right">
                        <p>$' . number_format($clasificacion->total, 2, '.', ',') . '</p>
                    </td>
                </tr>';
    }

    $xls .= '<tr>
                <td>
                    <p><b>Total Activo FIJO</b></p>
                </td>
                <td class="text-right">
                    <p><b>$' . number_format($reporte['activo']['activos']['total'], 2, '.', ',') . '</b></p>
                </td>
            </tr>';

                            // <!-- Diferido -->
    $xls .= '<tr>
                <td>
                    <p><b>DIFERIDO</b></p>
                </td>
                <td></td>
            </tr>';

    foreach ($reporte['activo']['diferido']['conceptos'] as $diferido) {
        $extraClase = isset($diferido->class) ? $diferido->class : 'sinExtraClase';
        $editable = isset($diferido->editable) ? $diferido->editable : 'false';
        $trid = isset($diferido->id) ? $diferido->id : '';
        $xls .= '<tr>
                    <td>
                        <p>' . strtoupper($diferido->nombre) . '</p>
                    </td>
                    <td class="text-right ' . $extraClase . '" contenteditable="' . $editable . '" id="' . $trid . '">
                        <p>$' . number_format($diferido->total, 2, '.', ',') . '</p>
                    </td>
                </tr>';
    }

    $xls .= '<tr>
                <td>
                    <p><b>Total Activo DIFERIDO</b></p>
                </td>
                <td class="text-right">
                    <p><b>$' . number_format($reporte['activo']['diferido']['total'], 2, '.', ',') . '</b></p>
                </td>
            </tr>';

                            // <!-- TOTAL ACTIVO (se pasó a la tabla de abajo)-->
    $xls .= '</tbody>
    </table>
</div>
</div>';

            // <!-- Pasivos -->

    $xls .= '<div class="col-xs-12 col-md-6">
                <div class="table-responsive">
                    <table class="table table-bordered table-condensed">
                        <thead>
                            <tr>
                                <th colspan="2" class="text-center">Pasivos</th>
                            </tr>
                            <tr>
                                <th class="text-center">' . strtoupper('Descripcion') . '</th>
                                <th class="text-center">Saldo al final del mes</th>
                            </tr>
                        </thead>
                        <tbody>';
                            // <!-- pasivo a corto plazo -->
    $xls .= '<tr>
                <td colspan="2" class="text-center">
                    <p><b>Pasivo a corto plazo</b></p>
                </td>
            </tr>';

    foreach ($reporte['pasivo']['corto_plazo']['conceptos'] as $concepto) {
        $extraClase = isset($concepto->class) ? $concepto->class : 'sinExtraClase';
        $editable = isset($concepto->editable) ? $concepto->editable : 'false';
        $trid = isset($concepto->id) ? $concepto->id : '';
        $xls .= '<tr ng-repeat="concepto in balanceGeneral.resultado.pasivo.corto_plazo.conceptos">
                    <td>
                        <p>' . strtoupper($concepto->nombre) . '</p>
                    </td>
                    <td class="text-right ' . $extraClase . '" contenteditable="' . $editable . '" id="' . $trid . '">
                        <p>$' . number_format($concepto->total, 2, '.', ',') . '</p>
                    </td>
                </tr>';
    }


    $xls .= '<tr>
                <td>
                    <p><b>Total Pasivo a corto plazo</b></p>
                </td>
                <td class="text-right">
                    <p><b>$' . number_format($reporte['pasivo']['corto_plazo']['total'], 2, '.', ',') . '</b></p>
                </td>
            </tr>';

                            // <!-- pasivo a largo plazo -->
    $xls .= '<tr>
                <td>
                    <p><b>Pasivo a largo plazo</b></p>
                </td>
                <td></td>
            </tr>';

    foreach ($reporte['pasivo']['largo_plazo']['conceptos'] as $concepto) {
        $extraClase = isset($concepto->class) ? $concepto->class : 'sinExtraClase';
        $editable = isset($concepto->editable) ? $concepto->editable : 'false';
        $trid = isset($concepto->id) ? $concepto->id : '';
        $xls .= '<tr>
                    <td>
                        <p>' . strtoupper($concepto->nombre) . '</p>
                    </td>
                    <td class="text-right ' . $extraClase . '" contenteditable="' . $editable . '" id="' . $trid . '">
                        <p>$' . number_format($concepto->total, 2, '.', ',') . '</p>
                    </td>
                </tr>';
    }

    $xls .= '<tr>
                <td>
                    <p><b>Total Pasivo a largo plazo</b></p>
                </td>
                <td class="text-right">
                    <p><b>$' . number_format($reporte['pasivo']['largo_plazo']['total'], 2, '.', ',') . '</b></p>
                </td>
            </tr>';

                            // <!-- Total pasivo -->
    $xls .= '<tr>
                <td>
                    <p><b>TOTAL PASIVO</b></p>
                </td>
                <td class="text-right">
                    <p><b>$' . number_format($reporte['pasivo']['total_pasivo'], 2, '.', ',') . '</b></p>
                </td>
            </tr>';

    // <!-- capital contable -->
    $xls .= '<tr><td colspan="2"><p>&nbsp;</p></td></tr>';
    $xls .= '<tr>
                <td colspan="2" class="text-center">
                    <p><b>CAPITAL CONTABLE</b></p>
                </td>
            </tr>';

    foreach ($reporte['capital']['conceptos'] as $concepto) {
        $extraClase = isset($concepto->class) ? $concepto->class : 'sinExtraClase';
        $editable = isset($concepto->editable) ? $concepto->editable : 'false';
        $trid = isset($concepto->id) ? $concepto->id : '';
        $xls .= '<tr>
                    <td>
                        <p>' . strtoupper($concepto->nombre) . '</p>
                    </td>
                    <td class="text-right ' . $extraClase . '" contenteditable="' . $editable . '" id="' . $trid . '">
                        <p>$' . number_format($concepto->total, 2, '.', ',') . '</p>
                    </td>
                </tr>';
    }


    $xls .= '<tr>
                <td>
                    <p><b>Total CAPITAL CONTABLE</b></p>
                </td>
                <td class="text-right">
                    <p><b>$' . number_format($reporte['capital']['total_capital'], 2, '.', ',') . '</b></p>
                </td>
            </tr>';

    // <!-- TOTAL PASIVO + CAPITAL (SE Pasó a la tabla de abajo) -->
    $xls .= '</tbody>
            </table>
        </div>

    </div>
</div>';

    $xls .= '<div class="row">
        <div class="col-xs-12 col-md-6">';

    $xls .= '<div class="table-responsive">
            <table class="table table-bordered table-condensed">';
    $xls .= '<tr>
            <td>
                <p><b>TOTAL ACTIVO</b></p>
            </td>
            <td class="text-right">
                <p><b>$' . number_format($reporte['activo']['total_activo'], 2, '.', ',') . '</b></p>
            </td>
        </tr>';
    $xls .= '</table></div>'; // Final tabla

    $xls .= '</div>';  // Columna 1

    $xls .= '<div class="col-xs-12 col-md-6">';

    $xls .= '<div class="table-responsive">
            <table class="table table-bordered table-condensed">';
    $xls .= '<tr>
            <td>
                <p><b> TOTAL PASIVO + CAPITAL</b></p>
            </td>
            <td class="text-right">
                <p><b>$' . number_format($reporte['pasivo_capital'], 2, '.', ',') . '</b></p>
            </td>
        </tr>';
    $xls .= '</table></div>'; // Final tabla

    $xls .= '</div>'; // Columna 2

    $xls .= '</div>'; // Row

    if ($esXls) {
        $xls .= '<div class="row">';

        $xls .= '<p>BAJO PROTESTA DE DECIR VERDAD MANIFIESTO QUE LAS CIFRAS CONTENIDAS EN ESTE ESTADO FINANCIERO SON VERACEZ Y CONTIENEN TODA LA INFORMACION
        REFERENTE A LA SITUACION FINANCIERA Y/O RESULTADOS DE LA EMPRESA Y AFIRMO QUE SOY LEGALMENTE RESPONSABLE DE LA AUTENTICIDAD Y VERACIDAD DE LAS
        MISMAS Y ASI MISMO ASUMO CUALQUIER RESPONSABILIDAD DERIVADA DE CUALQUIER DECLARACION EN FALSO SOBRE LAS MISMAS. </p>';

        $xls .= '<div class="col-xs-12 col-md-6">';

        $xls .= '<p>__________________________</p>';
        $xls .= '<p>CP Rigoberto Serrano Silva</p>';
        $xls .= '<p>Representante Legal</p>';
        $xls .= '<p>Apicultores Unidos de la Peninsula, S.A.  de C.V.</p>';

        $xls .= '</div>';

        $xls .= '<div class="col-xs-12 col-md-6">';

        $xls .= '<p>__________________________</p>';
        $xls .= '<p>CP David Porfirio Santos Redondo</p>';
        $xls .= '<p>Contador General</p>';

        $xls .= '</div>';

        $xls .= '</div>';
    }

    return $xls;
}