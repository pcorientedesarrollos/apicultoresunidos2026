<?php

require('../fpdf/FPDF/fpdf.php');
include_once '../DAOConeccion/conePDO.php';

$pdo = new conePDO();
$con = $pdo->conectar();

function outputPdf($param) {
    $pdf = new FPDF('P', 'mm', 'A4');
    $pdf->AddPage(); 

    $initialX = 10;
    $initialY = 25;
    $pageWidth = 190;
    $pageHeight = $pdf->getPageHeight();
    $rowHeight = 5;
    $middle = $pageWidth/2;
    $third = $pageWidth/3;
    $sixth = $pageWidth/6;

    $GLOBALS['littleRow'] = 4;
    $GLOBALS['dataRowHeight'] = 25;
    $GLOBALS['signRowheight'] = 50;
    $GLOBALS['dataRowY'] = 40;
    $GLOBALS['dateRowY'] = 20;
    $GLOBALS['signRowY'] = $pageHeight / 2 - 50;

    $pdf->SetFillColor(56, 84, 40);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('Arial', 'B', 9);

    $pdf->Image('img/logoLg.png', 10, 10);
    $pdf->setXY(($sixth * 4) + $initialX, 10);
    $pdf->RoundedRect(($sixth * 4) + $initialX, 10, $sixth * 2, $rowHeight * 1.5, 2, '1234', 'DF');
    $pdf->Cell($sixth * 2, $rowHeight * 1.5, utf8_decode('CONTROL DE CAJA Y DILIGENCIA AUP'), 0, 0, 'C', 0);
    $pdf->Ln($rowHeight / 2);

    $pdf->setXY(($sixth * 4) + $initialX, $GLOBALS['dateRowY']);

    $pdf->RoundedRect(($sixth * 4) + $initialX,$GLOBALS['dateRowY'], $sixth, $rowHeight, 2, '12', 'DF');
    $pdf->Cell($sixth, $rowHeight, utf8_decode('FOLIO'), 0, 0, 'C', 0);

    $pdf->RoundedRect($initialX + ($sixth * 5),$GLOBALS['dateRowY'], $sixth, $rowHeight, 2, '12', 'DF');
    $pdf->Cell($sixth, $rowHeight, utf8_decode('FECHA'), 0, 0, 'C', 0);

    $x = $initialX;
    $y = $GLOBALS['dataRowY'];
    $pdf->setXY($x, $y);

    $pdf->cell($third, $rowHeight, utf8_decode('NOMBRE'), 1, 0, 'C', 1);
    $pdf->cell($middle, $rowHeight, utf8_decode('CONCEPTO DE GASTO Y/O MOTIVO DE LA DILIGENCIA'), 1, 0, 'C', 1);
    $pdf->cell($sixth, $rowHeight, utf8_decode('IMPORTE'), 1, 0, 'C', 1);

    $x = $initialX;
    $y = $GLOBALS['dataRowY'] + $rowHeight;
    $pdf->setXY($x, $y);

    $pdf->cell($third, $rowHeight * 6, '', 1, 0, 'L', 0);
    $pdf->cell($middle, $rowHeight * 6, '', 1, 0, 'L', 0);
    $pdf->cell($sixth, $rowHeight * 6, '', 1, 0, 'L', 0);

    #INPUTDATA

    $pdf->setFont('Arial', '', 8);
    $pdf->SetTextColor(0,0,0);
    
    $pdf->setXY($initialX + $middle, $GLOBALS['signRowY']);
    $pdf->cell($middle, $rowHeight, utf8_decode("RECIBO CANTIDAD PREVIAMENTE SOLICITADA"), 0, 2, 'C', 0);
    $pdf->Ln(25);
    $pdf->Line($initialX + $middle, $GLOBALS['signRowY'] + 25, $initialX + ($sixth * 6), $GLOBALS['signRowY'] + 25);
    $pdf->setXY($initialX + $middle, $GLOBALS['signRowY'] + 25);
    $pdf->cell($middle, $rowHeight, utf8_decode("Firma de recibido"), 0, 2, 'C', 0);

    $y = $GLOBALS['dateRowY'] + $rowHeight;
    $pdf->setXY($x + ($sixth*4), $y);
    $pdf->RoundedRect($x + ($sixth*4), $y, $sixth, $rowHeight, 2, '34', '');
    $pdf->Cell($sixth, $rowHeight, utf8_decode('1021'), 0, 0, 'C', 0);

    $pdf->RoundedRect($x + ($sixth*5), $y, $sixth, $rowHeight, 2, '34', '');
    $pdf->Cell($sixth, $rowHeight, utf8_decode('29 / 08 / 2017'), 0, 0, 'C', 0);


    $x = $initialX;
    $y = $GLOBALS['dataRowY'] + $rowHeight;
    $pdf->setXY($x, $y);

    $pdf->MultiCell($third, $rowHeight, utf8_decode('Legacy Program Analyst'), 0, 'L', 0); 
    $pdf->setXY($initialX + $third, $GLOBALS['dataRowY'] + $rowHeight);
    $pdf->MultiCell($middle, $GLOBALS['littleRow'], utf8_decode('Fuga distinctio aut facere. Delectus quia magni eveniet libero doloremque sed. Quasi asperiores officia vel asperiores sed sed repudiandae voluptatum delectus.
    Nisi nesciunt nesciunt aspernatur nesciunt dolore quod quia aut ab. Dolore cupiditate at quis pariatur voluptatem praesentium. Officia voluptate ut. Tempora minus et quia aut. Molestias ad repellendus rerum amet non dolores corrupti. Fuga non ut nesciunt.'), 0, 'L', 0);
    $pdf->setXY($initialX + $third + $middle, $GLOBALS['dataRowY'] + $rowHeight);
    $pdf->MultiCell($sixth, $rowHeight, utf8_decode('MXN $' . '231.19'), 0, 'R', 0);

    $pdf->Output(); 
}

if (isset($_GET['parametro'])) {
    outputPdf($_GET['parametro']);
} else {
    exit();
}