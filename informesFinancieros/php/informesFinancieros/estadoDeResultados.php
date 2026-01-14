<?php

function generarXlsEstadoDeResultados($mes = false, $meses = false, $esXls = false, $cuentas = false)
{

    $reporte = obtenerInformeEstadoDeResultados($mes, $meses, $esXls);
    $xls = '';

    $xls = array('xls' => '', 'cobranza' => array());
    $xls['cobranza'] = $reporte['actividades_operacion']['total_cobranza'];

    $xls['xls'] .= '<div class="table-responsive">';
    $xls['xls'] .= '<table class="table table-bordered table-condensed">';
    $xls['xls'] .= '<thead>
                        <tr>
                            <th class="text-center"> CONCEPTO </th>';
    foreach ($reporte['meses'] as $mes) {
        $xls['xls'] .= '<th class="text-center">' . strtoupper($mes['mes']) . '</th>';
    }
    $xls['xls'] .= '<th class="text-center"> ACUMULADO </th>
                                </tr>
                            </thead>';
    $xls['xls'] .= '<tbody>';
    $xls['xls'] .= ' <tr>
                                    <td class="text-center"><b> Ingresos de clientes </b></td>';
    foreach ($reporte['actividades_operacion']['total_cobranza'] as $valor) {
        $xls['xls'] .= '<td class="text-right"><b>$' . number_format($valor, 2, '.', ',') . '</b></td>';
    }
    $xls['xls'] .= '</tr>';
    foreach ($reporte['actividades_operacion']['cobranza_clientes'] as $cliente) {
        $xls['xls'] .= '<tr>';
        $xls['xls'] .= '<td class="text-left">' . strtoupper($cliente->nombre) . '</td>';

        foreach ($cliente->totalesPorMes as $totalPorMes) {
            $xls['xls'] .= '<td class="text-right">$' . number_format($totalPorMes, 2, '.', ',') . '</td>';
        }
        $xls['xls'] .= '</tr>';
    }

    // Finaliza cobranza a clientes


    //// GASTOS CENTROS DE ACOPIO

    $xls['xls'] .= ' <tr>
    <td class="text-center">
        <b> GASTOS CENTROS DE ACOPIO </b>
    </td>';

    foreach ($reporte['centros_acopio']['total_mensual'] as $valor) {
        $xls['xls'] .= '<td class="text-right">
        <b>$' . number_format($valor, 2, '.', ',') . '</b>
    </td>';
    }

    $xls['xls'] .= '</tr>';

    //// COMPRAS TOTALES
    /* MIEL */
    $xls['xls'] .= ' <tr>
       <td class="text-center">
           <b> COMPRAS TOTALES MIEL (CONVENCIONAL) </b>
       </td>';

    foreach ($reporte['compras_totales']['total_mensual'] as $valor) {
        $xls['xls'] .= '<td class="text-right">
           <b>$' . number_format($valor, 2, '.', ',') . '</b>
       </td>';
    }

    $xls['xls'] .= '</tr>';

    $xls['xls'] .= ' <tr>
       <td class="text-center">
           <b>' . strtoupper('Compras totales MIEL (Orgánica)') . '</b>
       </td>';

    foreach ($reporte['compras_totales_organico']['total_mensual'] as $valor) {
        $xls['xls'] .= '<td class="text-right">
           <b>$' . number_format($valor, 2, '.', ',') . '</b>
       </td>';
    }

    $xls['xls'] .= '</tr>';
        
        /*CERA*/
    $xls['xls'] .= ' <tr>
    <td class="text-center">
        <b>' . strtoupper('Compras totales Cera (CONVENCIONAL)') . '</b>
    </td>';

    foreach ($reporte['compras_totales_cera']['total_mensual'] as $valor) {
        $xls['xls'] .= '<td class="text-right">
        <b>$' . number_format($valor, 2, '.', ',') . '</b>
    </td>';
    }

    $xls['xls'] .= '</tr>';

    $xls['xls'] .= ' <tr>
    <td class="text-center">
        <b>' . strtoupper('Compras totales Cera (Orgánico)') . '</b>
    </td>';

    foreach ($reporte['compras_totales_cera_organico']['total_mensual'] as $valor) {
        $xls['xls'] .= '<td class="text-right">
        <b>$' . number_format($valor, 2, '.', ',') . '</b>
    </td>';
    }

    $xls['xls'] .= '</tr>';
        
    /* PRODUCTOS APICOLAS */
    $xls['xls'] .= ' <tr>
    <td class="text-center">
        <b>' . strtoupper('Compras totales Productos Apícolas') . '</b>
    </td>';

    foreach ($reporte['compras_totales_apicolas']['total_mensual'] as $valor) {
        $xls['xls'] .= '<td class="text-right">
        <b>$' . number_format($valor, 2, '.', ',') . '</b>
    </td>';
    }

    $xls['xls'] .= '</tr>';

    foreach ($reporte['gastos_operacion']['cuentas']['subcuentas'] as $cuenta) {
        $xls['xls'] .= '<tr>';
        if (isset($cuenta->esEncabezado) && $cuenta->esEncabezado) {
            $xls['xls'] .= '<td class="text-left"> <b>' . strtoupper($cuenta->subSubcuenta) . '</b></td>';
        } else {
            $xls['xls'] .= '<td class="text-left">' . strtoupper($cuenta->subSubcuenta) . '</td>';
        }
        $xls['xls'] .= '</tr> <br>';
    }

    // Finaliza gastos de operación

    $xls['xls'] .= '</tbody>';
    $xls['xls'] .= '</table>';
    $xls['xls'] .= '</div> <br>';

    foreach ($cuentas as $cuenta) {
        $nombreCuenta = $cuenta->cuenta;
        $acumuladoPorCuenta = $cuenta->acumuladoPorCuenta;
        $xls['xls'] .= '<div class="table-responsive no-padding">
         <table class="table table-bordered table-condensed">
        <thead>
            <tr>
                <th style=" text-transform: none; text-align: center; font-weight: 600; font-size: 15px; "
                colspan="14">' . strtoupper($nombreCuenta) . '</th>
            </tr>
            <tr>
                <th style="width: 25em;">CONCEPTO</th>';
        foreach ($reporte['meses'] as $mes) {
            $nombreMes = $mes["mes"];
            $xls['xls'] .= '<th style="width: 6em">' . utf8_decode($nombreMes) . '</th>';
        }
        $xls['xls'] .= '
            <th> ACUMULADO</th>
            </tr>          
        </thead>
        <tbody>';
        foreach ($cuenta->gastosAup as $subcuenta) {
            $nombreSubcuenta = $subcuenta->nombre;
            $total = $subcuenta->total;
            $xls['xls'] .= '<tr>';
            if (isset($subcuenta->esEncabezado) && $subcuenta->esEncabezado) {
                $xls['xls'] .= '<td class="text-left"> <b>' . strtoupper($nombreSubcuenta) . '</b></td>';
            } else {
                $xls['xls'] .= '<td class="text-left">' . strtoupper($nombreSubcuenta) . '</td>';
            }
            foreach ($subcuenta->totales as $indice => $totalPorMes) {
                if (isset($subcuenta->esEncabezado) && $subcuenta->esEncabezado) {
                    $xls['xls'] .= '<td style="text-align: right; width: 6em"><b>$' . number_format($totalPorMes, 2, '.', ',') . '</b></td>';
                } else {
                    $xls['xls'] .= '<td style="text-align: right; width: 6em">$' . number_format($totalPorMes, 2, '.', ',') . '</td>';
                }
            }
            if (isset($subcuenta->esEncabezado) && $subcuenta->esEncabezado) {
                $xls['xls'] .= '
                        <td style="text-align: right;"><b>
                        $' . number_format($total, 2, '.', ',') . '
                        </b></td>
                    </tr>';
            } else {
                $xls['xls'] .= '
                        <td style="text-align: right;">
                        $' . number_format($total, 2, '.', ',') . '
                        </td>
                    </tr>';
            }
        }
        $xls['xls'] .= '<tr>
                <td style="text-align: right; width: 25em"><B>TOTAL</B></td>';
        foreach ($cuenta->sumaPorMes as $indice => $totalSubcuenta) {
            $xls['xls'] .= '<td style="text-align: right; width: 6em"><b>$' . number_format($totalSubcuenta, 2, '.', ',') . '</b></td>';
        }
        $xls['xls'] .= '
                        <td style="text-align: right;">
                            <b>$' . number_format($acumuladoPorCuenta, 2, '.', ',') . '</b>
                        </td>
                    </tr>
                </tbody>
            </table>
            <br>';
    }

    if ($esXls) {
       $xls['xls'] .= '<div class="row">';

       $xls['xls'] .= '<div class="col-xs-12 col-md-6">';

       $xls['xls'] .= '<p>__________________________</p>';
       $xls['xls'] .= '<p>C.P. Rigoberto Serrano Silva</p>';
       $xls['xls'] .= '<p>Representante Legal</p>';
       $xls['xls'] .= '<p>Apicultores Unidos de la Peninsula, S.A.  de C.V.</p>';

       $xls['xls'] .= '</div>';

       $xls['xls'] .= '<div class="col-xs-12 col-md-6">';

       $xls['xls'] .= '<p>__________________________</p>';
       $xls['xls'] .= '<p>C.P. David Porfirio Santos Redondo</p>';
       $xls['xls'] .= '<p>Contador General</p>';

       $xls['xls'] .= '</div>';

       $xls['xls'] .= '</div>';
    }

    return $xls;

}