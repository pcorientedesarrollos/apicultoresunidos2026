<?php

function generarXlsFlujoEfectivo($mes = false, $meses = false, $xls = false, $cuentas = array())
{

    $reporte = obtenerInformeFlujoEfectivo($mes, $meses, $xls);
    $xls = '';    

    // Imprimir la tabla

    $xls .= '<table class="table table-bordered table-condensed">';

    // Encabezado de la tabla

    $xls .= '<thead>
            <tr>
                <th class="text-center"> CONCEPTO </th>';

    foreach ($reporte['meses'] as $mes) {
        $xls .= '<th class="text-center">' . strtoupper($mes['mes']) . '</th>';
    }

    $xls .= '<th class="text-center"> ACUMULADO </th>
            </tr>
        </thead>';


    // tbody
    $xls .= '<tbody>';

    // Cobranza a clientes


    $xls .= ' <tr>
    <td class="text-center">
        <b> Cobranza a clientes </b>
    </td>';

    foreach ($reporte['actividades_operacion']['total_cobranza'] as $valor) {
        $xls .= '<td class="text-right">
        <b>$' . number_format($valor, 2, '.', ',') . '</b>
    </td>';
    }

    $xls .= '</tr>';

    foreach ($reporte['actividades_operacion']['cobranza_clientes'] as $cliente) {
        $xls .= '<tr>';
        $xls .= '<td class="text-left">' . strtoupper($cliente->nombre) . '</td>';

        foreach ($cliente->totalesPorMes as $totalPorMes) {
            $xls .= '<td class="text-right">$' . number_format($totalPorMes, 2, '.', ',') . '</td>';
        }
        $xls .= '</tr>';
    }

    // Finaliza cobranza a clientes

    // Saldos iniciales y finales caja y bancos

    $xls .= '<tr></tr>';

    // INICIALES

    $xls .= ' <tr>
    <td class="text-center">
        <b> Saldo inicial de caja y bancos </b>
    </td>';

    foreach ($reporte['saldosBancos']['saldosInicialesPorMes'] as $indice => $valor) {
        $totalCajaYBancosMes = $valor + $reporte['saldosCajaChica']['saldosInicialesPorMes'][$indice];
        $xls .= '<td class="text-right">
        <b>$' . number_format($totalCajaYBancosMes, 2, '.', ',') . '</b>
    </td>';
    }

    $xls .= '</tr>';

    // Saldos iniciales:
    // bancos primero
    foreach ($reporte['saldosBancos']['bancos'] as $banco) {
        $xls .= '<tr>';
        $xls .= '<td class="text-left">' . strtoupper($banco->nombre) . '</td>';

        foreach ($banco->saldosIniciales as $totalPorMes) {
            $xls .= '<td class="text-right">$' . number_format($totalPorMes, 2, '.', ',') . '</td>';
        }
        $xls .= '</tr>';
    }
    // Ahora caja chica
    foreach ($reporte['saldosCajaChica']['conceptos'] as $concepto) {
        $xls .= '<tr>';
        $xls .= '<td class="text-left">' . strtoupper($concepto->nombre) . '</td>';

        foreach ($concepto->saldosIniciales as $totalPorMes) {
            $xls .= '<td class="text-right">$' . number_format($totalPorMes, 2, '.', ',') . '</td>';
        }
        $xls .= '</tr>';
    }


    // FINALES 
    $xls .= ' <tr>
    <td class="text-center">
        <b> Saldo final de caja y bancos </b>
    </td>';

    foreach ($reporte['saldosBancos']['saldosFinalesPorMes'] as $indice => $valor) {
        $totalCajaYBancosMes = $valor + $reporte['saldosCajaChica']['saldosFinalesPorMes'][$indice];
        $xls .= '<td class="text-right">
        <b>$' . number_format($totalCajaYBancosMes, 2, '.', ',') . '</b>
    </td>';
    }

    $xls .= '</tr>';
    
    // Saldos finales:
    // bancos primero
    foreach ($reporte['saldosBancos']['bancos'] as $banco) {
        $xls .= '<tr>';
        $xls .= '<td class="text-left">' . strtoupper($banco->nombre) . '</td>';

        foreach ($banco->saldosFinales as $totalPorMes) {
            $xls .= '<td class="text-right">$' . number_format($totalPorMes, 2, '.', ',') . '</td>';
        }
        $xls .= '</tr>';
    }
    // Ahora caja chica
    foreach ($reporte['saldosCajaChica']['conceptos'] as $concepto) {
        $xls .= '<tr>';
        $xls .= '<td class="text-left">' . strtoupper($concepto->nombre) . '</td>';

        foreach ($concepto->saldosFinales as $totalPorMes) {
            $xls .= '<td class="text-right">$' . number_format($totalPorMes, 2, '.', ',') . '</td>';
        }
        $xls .= '</tr>';
    }

    // Finaliza gastos de operación

    $xls .= '</tbody>';
    $xls .= '</table>';

    foreach ($cuentas as $cuenta) {

        $xls .= '<div class="table-responsive" style="width: 100%">
            <table class="table table-bordered table-condensed">
                <thead>
                    <tr>
                        <th class="text-center" colspan="14">' . strtoupper($cuenta->cuenta) . '</th>
                    </tr>
                    <tr>
                        <th class="text-center">CONCEPTO</th>';
        foreach ($reporte['meses'] as $mes) {
            $xls .= '<th class="text-center">' . strtoupper($mes['mes']) . '</th>';
        }
        $xls .= '<th> ACUMULADO</th>
                    </tr>
                </thead>';
        $xls .= '<tbody>';
        foreach ($cuenta->gastosAup as $subcuenta) {
            $xls .= '<tr>';
                                // Nombre de la cuenta
            if (isset($subcuenta->esEncabezado) && $subcuenta->esEncabezado) {
                $xls .= '<td><b>' . strtoupper($subcuenta->nombre) . '</b></td>';
            } else {
                $xls .= '<td>' . strtoupper($subcuenta->nombre) . '</td>';
            }

                                // Resultados
            if (isset($subcuenta->esEncabezado) && $subcuenta->esEncabezado) {
                foreach ($subcuenta->totales as $totalPorMes) {
                    $xls .= '<td class="text-right"><b>$' . number_format($totalPorMes, 2, '.', ',') . '</b></td>';
                }

            } else {
                foreach ($subcuenta->totales as $totalPorMes) {
                    $xls .= '<td class="text-right">$' . number_format($totalPorMes, 2, '.', ',') . '</td>';
                }
            }

                                // $acumulado
            if (isset($subcuenta->esEncabezado) && $subcuenta->esEncabezado) {
                $xls .= '<td class="text-right"><b>$' . number_format($subcuenta->total, 2, '.', ',') . '</b></td>';
            } else {
                $xls .= '<td class="text-right">$' . number_format($subcuenta->total, 2, '.', ',') . '</td>';
            }

            $xls .= '</tr>';
        }

        $xls .= '<tr>
                    <td class="text-right">
                        <B>TOTAL</B>
                    </td>';

        foreach ($cuenta->sumaPorMes as $totalSubcuenta) {
            $xls .= '<td class="text-right">
                        <b>$' . number_format($totalSubcuenta, 2, '.', ',') . '</b>
                    </td>';
        }

        $xls .= '<td class="text-right">
                    <b>$' . number_format($cuenta->acumuladoPorCuenta, 2, '.', ',') . '</b>
                </td>';

        $xls .= '</tr>
            </tbody>
        </table>
    </div>';
    }

    return $xls;

}
