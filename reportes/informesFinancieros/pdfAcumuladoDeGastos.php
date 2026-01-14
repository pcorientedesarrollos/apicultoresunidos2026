<?php
require('../../fpdf/FPDF/fpdf.php');
include_once '../../DAOConeccion/conePDO.php';
include_once '../../informesFinancieros/php/acumuladoDeGastos.php';

$pdo = new conePDO();
$con = $pdo->conectar();

function outputPdf()
{
    global $con;
    $datos = obtenerAcumulado();
    $pdf = new FPDF('L', 'mm', 'A4');
    $pdf->AddPage();
    $titulo_del_documento = 'ACUMULADO DE GASTOS';
    $pdf->Ln();
    $titulo_mes = strtoupper($datos['meses'][count($datos['meses']) - 1]['mes']);
    $titulo_mes .= ' ';
    $titulo_mes .= date("Y");
    $titulo_meses = strtoupper($datos['meses'][count($datos['meses']) - 1]['mes']);
    $titulo_meses .= ' - ';
    $titulo_meses = strtoupper($datos['meses'][count($datos['meses']) - 1]['mes']);  
    $titulo_meses .= ' ';  
    $titulo_meses .= date("Y");

    $pageWidth = $pdf->getPageWidth() - 5;
    $pageHeight = $pdf->getPageHeight();
    $lateralMargins = 10;
    $GLOBALS['littleRow'] = 3;
    $GLOBALS['twelve'] = $pageWidth / 12;
    $GLOBALS['sixth'] = $pageWidth / 6;
    $GLOBALS['cuarter'] = $pageWidth / 4;
    $GLOBALS['middle'] = $pageWidth / 2;
    $GLOBALS['rowHeight'] = 5;
    $GLOBALS['initialX'] = 10;
    $GLOBALS['logoW'] = 22;
    $GLOBALS['heigth'] = $pageWidth / 8;

    $GLOBALSY['initialY'] = 25;
    $GLOBALSY['nameRowY'] = 37;
    $GLOBALSY['dataRowY'] = 45;
    $GLOBALSY['dateRowY'] = 20;
    $GLOBALSY['signRowY'] = $pageHeight / 2 - 40;
    $GLOBALSY['logoY'] = 10;
    $GLOBALSY['documentTitleY'] = 10;
    $GLOBALSY['totalY'] = 45 + $GLOBALS['rowHeight'] * 10;
    $GLOBALSY['initialX'] = $lateralMargins;

    $pdf->SetFillColor(137, 172, 118);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 9);

    $pdf->Image('../img/imgMovimientoCajaChica.png', $GLOBALS['initialX'], $GLOBALSY['logoY'], $GLOBALS['logoW']);

    $pdf->setXY($GLOBALS['initialX'] + 85, $GLOBALSY['logoY']);
    $pdf->cell(100, 4, utf8_decode('OAXACA MIEL S.A. DE C.V.'), 0, 2, 'C', 0);
    $pdf->cell(100, 4, utf8_decode(' '), 0, 2, 'C', 0);
    $pdf->cell(100, 4, utf8_decode(''), 0, 2, 'C', 0);
    $pdf->cell(100, 4, utf8_decode(''), 0, 2, 'C', 0);
    $pdf->cell(100, 4, utf8_decode(''), 0, 2, 'C', 0);
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->cell(100, 4, utf8_decode($titulo_del_documento), 0, 2, 'C', 0);
    if(isset($_GET['mes']) && isset($_GET['mes1'])){
        $pdf->cell(100, 4, utf8_decode($titulo_meses), 0, 2, 'C', 0);
    } else if(isset($_GET['mes'])){
        $pdf->cell(100, 4, utf8_decode($titulo_mes), 0, 2, 'C', 0);
    }else{
        $pdf->cell(100, 4, utf8_decode(date("Y")), 0, 2, 'C', 0);
    }
    $pdf->cell(100, 17, utf8_decode('DESGLOSE DE GASTOS AUP'), 0, 2, 'C', 0);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('Arial', 'B', 8);

    $x = $GLOBALS['initialX'];
    $y = $GLOBALSY['dataRowY'];
    $pdf->setXY($x, $y + 8);
    $heigthPerRow = 0;

    /**
     * Espacio que tendrá la columna para los nombres de los
     * conceptos, el resto del espacio dependerá de este valor
     **/

    $concepto = 60;

    $remainder = $pageWidth - $concepto - $GLOBALS['initialX'];
    
    $cada_fila = $remainder / (count($datos['meses']) + 1);
    $pdf->cell($concepto, $GLOBALS['rowHeight'], utf8_decode('CONCEPTO'), 1, 0, 'C', 1);
    foreach ($datos['meses'] as $mes) {
        $pdf->cell($cada_fila, $GLOBALS['rowHeight'], strtoupper(utf8_decode($mes['mes'])), 1, 0, 'C', 1);
    }
    $pdf->cell($cada_fila, $GLOBALS['rowHeight'], utf8_decode('ACUMULADO'), 1, 1, 'C', 1);

    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetFont('Arial', '', 7);
    foreach ($datos['cuentas'] as $cuenta) {
        $pdf->cell($concepto, $GLOBALS['rowHeight'], strtoupper(utf8_decode($cuenta->cuenta)), 1, 0, 'L', 0);
        foreach ($cuenta->sumaPorMes as $sumaCuenta) {
            $pdf->cell($cada_fila, $GLOBALS['rowHeight'], utf8_decode('$ ' . number_format($sumaCuenta, 2, '.', ',')), 1, 0, 'R', 0);
        }
        $pdf->cell($cada_fila, $GLOBALS['rowHeight'], utf8_decode('$ ' . number_format($cuenta->acumuladoPorCuenta, 2, '.', ',')), 1, 0, 'R', 0);
        $pdf->Ln();
    }
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->cell($concepto, $GLOBALS['rowHeight'], utf8_decode('TOTAL'), 1, 0, 'R', 0);
    foreach ($datos['sumaTotal'] as $suma) {
        $pdf->cell($cada_fila, $GLOBALS['rowHeight'], utf8_decode('$ ' . number_format($suma, 2, '.', ',')), 1, 0, 'R', 0);
    }
    $pdf->cell($cada_fila, $GLOBALS['rowHeight'], utf8_decode('$ ' . number_format($datos['totalAcumulado'], 2, '.', ',')), 1, 0, 'R', 0);
    $pdf->Ln(15);
    
    $pdf->setXY(95, 100);
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->cell(100, 1, utf8_decode('INTEGRACIÓN DETALLADA DE LOS COSTOS INCURRIDOS EN EL EJERCICIO'), 0, 2, 'C', 0);
    $pdf->Ln();

    foreach ($datos['cuentas'] as $cuenta) {
        if($cuenta->acumuladoPorCuenta > 0){
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->cell(100, 10, utf8_decode($cuenta->cuenta), 0, 2, 'L', 0);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->cell($concepto, $GLOBALS['rowHeight'], utf8_decode('CONCEPTO'), 1, 0, 'C', 1);
            foreach ($datos['meses'] as $mes) {
                $pdf->cell($cada_fila, $GLOBALS['rowHeight'], strtoupper(utf8_decode($mes['mes'])), 1, 0, 'C', 1);
            }
            $pdf->cell($cada_fila, $GLOBALS['rowHeight'], utf8_decode('ACUMULADO'), 1, 1, 'C', 1);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('Arial', '', 7);
            foreach ($cuenta->gastosAup as $subcuenta) {
                if($subcuenta->total > 0){
                    $y_antes_multicell = $pdf->getY();
                    $x_antes_multicell = $pdf->getX();
                    if ($y_antes_multicell >= 182) {
                        $pdf->AddPage();
                        $pdf->setY(20);
                        $y_antes_multicell = 20;
                    }
                    $pdf->MultiCell($concepto, $GLOBALS['rowHeight'], strtoupper(utf8_decode($subcuenta->nombre)), 1, 'L', 0);
                    $tamano_multicell = $pdf->getY() - $y_antes_multicell;
                    $pdf->setXY($x_antes_multicell + $concepto, $y_antes_multicell);
                    foreach($subcuenta->totales as $totalPorMes){
                        $pdf->cell($cada_fila, $tamano_multicell, utf8_decode('$ ' . number_format($totalPorMes, 2, '.', ',')), 1, 0, 'R', 0);
                    }
                    $pdf->cell($cada_fila, $tamano_multicell, utf8_decode('$ ' . number_format($subcuenta->total, 2, '.', ',')), 1, 0, 'R', 0);
                    $pdf->Ln();
                }
           
            }
            $pdf->SetFont('Arial', 'B', 7);
            $pdf->cell($concepto, $GLOBALS['rowHeight'], utf8_decode('TOTAL'), 1, 0, 'R', 0);
            foreach ($cuenta->sumaPorMes as $totalSubcuenta) {
                $pdf->cell($cada_fila, $GLOBALS['rowHeight'], utf8_decode('$ ' . number_format($totalSubcuenta, 2, '.', ',')), 1, 0, 'R', 0);
            }
            $pdf->cell($cada_fila, $GLOBALS['rowHeight'], utf8_decode('$ ' . number_format($cuenta->acumuladoPorCuenta, 2, '.', ',')), 1, 0, 'R', 0);
            $pdf->Ln(10);
        }

    }
    $pdf->Ln(10);

    //*TOTAL DE COSTOS INCURIDOS*
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('Arial', 'B', 8);
    $y_antes_multicell = $pdf->getY();
    $x_antes_multicell = $pdf->getX();
    if ($y_antes_multicell >= 182) {
        $pdf->AddPage();
        $pdf->setY(20);
        $y_antes_multicell = 20;
    }
    $pdf->MultiCell($concepto, $GLOBALS['rowHeight'], utf8_decode('TOTAL COSTOS INCURRIDOS EN EL EJERCICIO'), 1, 'C', 1);
    $tamano_multicell = $pdf->getY() - $y_antes_multicell;
    $pdf->setXY($x_antes_multicell + $concepto, $y_antes_multicell);
   
    foreach ($datos['meses'] as $mes) {
        $pdf->cell($cada_fila, $tamano_multicell / 2, strtoupper(utf8_decode($mes['mes'])), 1, 0, 'C', 1);
    }
    $pdf->cell($cada_fila, $GLOBALS['rowHeight'], utf8_decode('ACUMULADO'), 1, 1, 'C', 1);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->cell($concepto, $GLOBALS['rowHeight'], '', 0, 0, 'C', 0);
    foreach ($datos['sumaTotal'] as $suma) {
        $pdf->cell($cada_fila, $GLOBALS['rowHeight'], utf8_decode('$ ' . number_format($suma, 2, '.', ',')), 1, 0, 'R', 0);
    }
    $pdf->cell($cada_fila, $GLOBALS['rowHeight'], utf8_decode('$ ' . number_format($datos['totalAcumulado'], 2, '.', ',')), 1, 0, 'R', 0);
   
    $pdf->Output();
}

if (isset($_GET['acumulado'])) {
    outputPdf();
} else {
    exit();
}
