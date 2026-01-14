<?php
include_once '../../informesFinancieros/php/acumuladoDeGastos.php';

if (isset($_GET['acumulado'])) {

    $reporte = obtenerAcumulado();

    $nombre_reporte = 'Acumulado de gastos';

    header('Content-type: application/vnd.ms-excel');
    header("Content-Disposition: attachment; filename=Acumulado de gastos.xls");
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
            </tr>
            <tr style="text-align: center;">
            <td colspan="10"><b>Acumulado de gastos</b></td>
        </tr>
            </table>';

    echo '<table border=1 style="border-collapse: collapse" style="font-size: 10px">
            <thead>
                <tr>
                    <th style="width: 25em; text-transform: none; font-weight: 600; text-align: center; background-color: #FFE558; color:BLACK"
                    rowspan="2">TOTAL COSTOS INCURRIDOS EN EL EJERCICIO</th>';
                    foreach($reporte['meses'] as $mes) {    
                        $nombreMes  = $mes["mes"];  
                        echo '<th rowspan = "1" style="width: 6em">' . utf8_decode($nombreMes) . '</th>';         
                    }
                echo '
                    <th rowspan="1">ACUMULADO</th>
                </tr>
                <tr>';
                    foreach($reporte['sumaTotal'] as $indice => $suma){
                        echo' <th rowspan="1" style="text-align: right; width: 6em"> ' . number_format($suma, 2, '.', ',') . ' </th>';
                    }
                echo'
                    <td style="text-align: right; background-color: white">
                        <b>' . number_format($reporte['totalAcumulado'], 2, '.', ',') . '</b>
                    </td>
                </tr>
            </thead>
            <tbody>
            </tbody>
    </table>
    <br>
    <table border=1 style="border-collapse: collapse" style="font-size: 10px">
        <thead>
            <tr>
                <th style="text-transform: none; font-weight: 600; text-align: center; font-size: 15px; background-color: #FFE558; color:BLACK"
                >DESGLOSE DE GASTOS AUP</th>
            </tr>
            <tr>
                <th style="width: 25em;">Concepto</th>';
                foreach($reporte['meses'] as $mes) {    
                    $nombreMes = $mes["mes"];  
                    echo '<th rowspan = "1" style="width: 6em">' . utf8_decode($nombreMes) . '</th>';         
                }
            echo '
            <th> ACUMULADO</th>
            </tr>           
        </thead>
        <tbody>';
            foreach($reporte['cuentas'] as $cuenta){
                $nombreCuenta = $cuenta->cuenta;
                $acumuladoPorCuenta = $cuenta->acumuladoPorCuenta;
                echo '<tr>
                        <td style="width: 25em;">' . utf8_decode($nombreCuenta) . '</td>';
                        foreach($cuenta->sumaPorMes as $indice => $sumaCuenta){
                        echo '<td style="text-align: right; width: 6em">' . number_format($sumaCuenta, 2, '.', ',') . '</td>';
                        }
                echo '
                <td style="text-align: right;">' . number_format($acumuladoPorCuenta, 2, '.', ',') . ' </td>
                </tr>';
            } 
            echo'
                <tr>
                    <td style="text-align: right; width: 25em">
                        <B>TOTAL</B>
                    </td>';
                    foreach($reporte['sumaTotal'] as $indice => $suma){
                        echo' <td rowspan="1" style="text-align: right; width: 6em"><b> ' . number_format($suma, 2, '.', ',') . ' </b></td>';
                    }
                    echo'
                    <td style="text-align: right;"><b>' . number_format($reporte['totalAcumulado'], 2, '.', ',') . '</b> </td>                   
                </tr>
        </tbody>
     </table>
     <br>
     <p>
         <b>'.utf8_decode('INTEGRACIÓN DETALLADA DE LOS COSTOS INCURRIDOS EN EL EJERCICIO'). '</b>
     </p>
     ';
     foreach($reporte['cuentas'] as $cuenta){
        $nombreCuenta = $cuenta->cuenta;
        $acumuladoPorCuenta = $cuenta->acumuladoPorCuenta;
        echo'<table border=1 style="border-collapse: collapse" style="font-size: 10px">
        <thead>
            <tr>
                <th style=" text-transform: none; text-align: center; font-weight: 600; font-size: 15px; background-color: #FFE558; color:BLACK"
                >'. utf8_decode($nombreCuenta) . '</th>
            </tr>
            <tr>
                <th style="width: 25em;">CONCEPTO</th>';
                foreach($reporte['meses'] as $mes){
                    $nombreMes = $mes["mes"];
                    echo'<th style="width: 6em">' . utf8_decode($nombreMes) . '</th>';
                }
            echo'
            <th> ACUMULADO</th>
            </tr>          
        </thead>
        <tbody>';
            foreach($cuenta->gastosAup as $subcuenta){
                $nombreSubcuenta = $subcuenta->nombre;
                $total = $subcuenta->total;
                echo'<tr>
                    <td style="width: 25em;">' . utf8_decode($nombreSubcuenta) . '</td>';
                    foreach($subcuenta->totales as $indice => $totalPorMes){
                    echo'<td style="text-align: right; width: 6em">' . number_format($totalPorMes, 2, '.', ',') . '</td>';
                    }
                echo '
                    <td style="text-align: right;">
                    ' . number_format($total, 2, '.', ',') . '
                    </td>
                </tr>';
           }
        echo'<tr>
                <td style="text-align: right; width: 25em"><B>TOTAL</B></td>';
                foreach($cuenta->sumaPorMes as $indice => $totalSubcuenta){
                echo'<td style="text-align: right; width: 6em"><b>' . number_format($totalSubcuenta, 2, '.', ',') . '</b></td>';
                }
            echo'
                <td style="text-align: right;">
                     <b>'. number_format($acumuladoPorCuenta, 2, '.', ',').'</b>
                </td>
            </tr>
        </tbody>
     </table>
     <br>';
    }

} else {
    $nombre_archivo = 'Documento.xls';

    // header('Content-type: application/vnd.ms-excel');
    header("Content-Disposition: attachment; filename=$nombre_archivo");
    header("Pragma: no-cache");
    header("Expires:0");
}