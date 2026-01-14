<?php

function generarXlsAcumuladoDeGastos($mes = false, $meses = false, $esXls = false, $resultado = false)
{

    $xls = ['xls' => ''];

    $xls['xls'] .= '<div class="table-responsive no-padding">';
    $xls['xls'] .= '<table class="table table-bordered table-condensed">
        <thead>
            <tr>
                <th style="text-transform: none; font-weight: 600; text-align: center; font-size: 15px; color:BLACK"
                colspan="14">DESGLOSE DE GASTOS AUP</th>
            </tr>
            <tr>
                <th style="width: 25em;">Concepto</th>';
    foreach ($resultado['meses'] as $mes) {
        $nombreMes = $mes["mes"];
        $xls['xls'] .= '<th rowspan = "1" style="width: 6em">' . utf8_decode($nombreMes) . '</th>';
    }
    $xls['xls'] .= '
            <th> ACUMULADO</th>
            </tr>           
        </thead>
        <tbody>';
    foreach ($resultado['cuentas'] as $cuenta) {
        $nombreCuenta = $cuenta->cuenta;
        $acumuladoPorCuenta = $cuenta->acumuladoPorCuenta;
        $xls['xls'] .= '<tr>
                <td style="width: 25em;">' . strtoupper($nombreCuenta) . '</td>';
        foreach ($cuenta->sumaPorMes as $indice => $sumaCuenta) {
            $xls['xls'] .= '<td style="text-align: right; width: 6em">$' . number_format($sumaCuenta, 2, '.', ',') . '</td>';
        }
        $xls['xls'] .= '
        <td style="text-align: right;">$' . number_format($acumuladoPorCuenta, 2, '.', ',') . ' </td>
        </tr>';
    }
    $xls['xls'] .= '
        <tr>
            <td style="text-align: right; width: 25em">
                <B>TOTAL</B>
            </td>';
    foreach ($resultado['sumaTotal'] as $indice => $suma) {
        $xls['xls'] .= ' <td rowspan="1" style="text-align: right; width: 6em"><b> $' . number_format($suma, 2, '.', ',') . ' </b></td>';
    }
    $xls['xls'] .= '
            <td style="text-align: right;"><b>$' . number_format($resultado['totalAcumulado'], 2, '.', ',') . '</b> </td>                   
        </tr>
        </tbody>
        </table>';
    $xls['xls'] .= '</div>
        <br>
        <h4 style="text-align: center">
        <b>' . strtoupper('INTEGRACION DETALLADA DE LOS COSTOS INCURRIDOS EN EL EJERCICIO') . '</b>
        </h4>';

    foreach ($resultado['cuentas'] as $cuenta) {
        $nombreCuenta = $cuenta->cuenta;
        $acumuladoPorCuenta = $cuenta->acumuladoPorCuenta;
        $xls['xls'] .= '<div class="table-responsive no-padding">
         <table class="table-bordered table-condensed">
        <thead>
            <tr>
                <th  style=" text-transform: none; text-align: center; font-weight: 600; font-size: 15px; color:BLACK"
                colspan="14">' . strtoupper($nombreCuenta) . '</th>
            </tr>
            <tr>
                <th style="width: 25em;">CONCEPTO</th>';
        foreach ($resultado['meses'] as $mes) {
            $nombreMes = $mes["mes"];
            $xls['xls'] .= '<th style="width: 6em">' . strtoupper($nombreMes) . '</th>';
        }
        $xls['xls'] .= '
            <th> ACUMULADO</th>
            </tr>          
        </thead>
        <tbody>';
        foreach ($cuenta->gastosAup as $subcuenta) {
            $nombreSubcuenta = $subcuenta->nombre;
            $total = $subcuenta->total;
            $xls['xls'] .= '<tr>
                    <td style="width: 25em;">' . strtoupper($nombreSubcuenta) . '</td>';
            foreach ($subcuenta->totales as $indice => $totalPorMes) {
                $xls['xls'] .= '<td style="text-align: right; width: 6em">$' . number_format($totalPorMes, 2, '.', ',') . '</td>';
            }
            $xls['xls'] .= '
                    <td style="text-align: right;">$
                    ' . number_format($total, 2, '.', ',') . '
                    </td>
                </tr>';
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
     </div>
     <br>';

    }

    $xls['xls'] .= '<div class="table-responsive no-padding">';
    $xls['xls'] .= '<table class="table table-bordered table-condensed"">
    <thead>
            <tr>
                <th style="width: 25em; text-transform: none; font-weight: 600; text-align: center; color:BLACK"
                rowspan="2">TOTAL COSTOS INCURRIDOS EN EL EJERCICIO</th>';
    foreach ($resultado['meses'] as $mes) {
        $nombreMes = $mes["mes"];
        $xls['xls'] .= '<th rowspan = "1" style="width: 6em">' . utf8_decode($nombreMes) . '</th>';
    }
    $xls['xls'] .= '
                <th rowspan="1">ACUMULADO</th>
            </tr>
            <tr>';
    foreach ($resultado['sumaTotal'] as $indice => $suma) {
        $xls['xls'] .= ' <th rowspan="1" style="text-align: right; background-color: white"> $' . number_format($suma, 2, '.', ',') . ' </th>';
    }
    $xls['xls'] .= '
                <td style="text-align: right; background-color: white">
                    <b>$' . number_format($resultado['totalAcumulado'], 2, '.', ',') . '</b>
                </td>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>';

    $xls['xls'] .= '</div>';

    if ($esXls) {
        $xls['xls'] .= '<div class="row">';

        $xls['xls'] .= '<div class="col-xs-12 col-md-6">';

        $xls['xls'] .= '<p>__________________________</p>';
        $xls['xls'] .= '<p>C.P. Ibis Janitzio Banderas Couoh</p>';
        $xls['xls'] .= '<p>Gerente General</p>';

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
