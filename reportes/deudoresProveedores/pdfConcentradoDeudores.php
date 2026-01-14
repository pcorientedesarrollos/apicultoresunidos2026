<?php

require('../../fpdf/FPDF/fpdf.php');
include_once '../../deudoresProveedores/php/estadoProveedor.php';
date_default_timezone_set('America/Merida');
$fecha = date('d/m/Y');
class PDF extends FPDF
{
    var $startPage = 23;
    var $footerPoint = 15;

    function Header()
    {
        $fecha = $GLOBALS['fecha'];
        $this->Image('../img/LOGO.png', 10, 10, 25);
        $this->setXY(0, 3);
        $this->setFont('Arial', 'B', 11);
        $this->cell($this->getPageWidth(), 4, 'OAXACA MIEL S.A. DE C.V.', 0, 15, 'C', 0);
        $this->setFont('Arial', '', 9);
        $this->setX(0);
        $this->cell($this->getPageWidth(), 4, ' ', 0, 1, 'C', 0);
        $this->setX(0);
        $this->cell($this->getPageWidth(), 4, utf8_decode(''), 0, 1, 'C', 0);
        $this->setX(0);
        $this->cell($this->getPageWidth(), 4, utf8_decode(''), 0, 1, 'C', 0);
        $this->setX(0);
        $this->cell($this->getPageWidth(), 4, 'Tel: (999) 9.88.09.90', 0, 1, 'C', 0);
        $this->setX(0);
        $this->cell($this->getPageWidth(), 4, 'ESTADO DE CUENTA - ' . $fecha, 0, 0, 'C', 0);
        $this->Ln(10);
    }

    function Footer()
    {
        $this->SetY(-$this->footerPoint);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 20, utf8_decode('Página ' . $this->PageNo() . ' de {nb}'), 0, 0, 'C');
    }
}

function addLine($y, $detalle, $pdf, $porc_cantidad, $porc_texto, $medidas)
{
    $heigthPerRow = 0;

    $pdf->MultiCell($porc_cantidad, $medidas['alto_fila_sm'], utf8_decode($detalle['fecha']), 0, 'C', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($pdf->startPage + $porc_cantidad, $y);

    $pdf->MultiCell($porc_texto, $medidas['alto_fila_sm'], utf8_decode($detalle['descripcion']), 0, 'L', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($pdf->startPage + $porc_cantidad + $porc_texto, $y);

    $pdf->MultiCell($porc_texto, $medidas['alto_fila_sm'], utf8_decode(strtoupper($detalle['cheque'])) . ' ' . utf8_decode(strtoupper($detalle['deBanco'])) . ' ' . utf8_decode($detalle['deCuenta']), 0, 'L', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($pdf->startPage + $porc_cantidad + $porc_texto * 2, $y);

    $pdf->MultiCell($porc_cantidad, $medidas['alto_fila_sm'], utf8_decode(number_format(floatval($detalle['banco']), 2, '.', ',')), 0, 'R', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($pdf->startPage + $porc_cantidad * 2 + $porc_texto * 2, $y);

    $pdf->MultiCell($porc_cantidad, $medidas['alto_fila_sm'], utf8_decode(number_format(floatval($detalle['caja']), 2, '.', ',')), 0, 'R', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($pdf->startPage + $porc_cantidad * 3 + $porc_texto * 2, $y);

    $pdf->MultiCell($porc_cantidad, $medidas['alto_fila_sm'], utf8_decode(number_format(floatval($detalle['ceraApicola']), 2, '.', ',')), 0, 'R', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($pdf->startPage + $porc_cantidad * 4 + $porc_texto * 2, $y);

    $pdf->MultiCell($porc_cantidad, $medidas['alto_fila_sm'], utf8_decode(number_format(floatval($detalle['retenciones']), 2, '.', ',')), 0, 'R', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($pdf->startPage + $porc_cantidad * 5 + $porc_texto * 2, $y);

    $pdf->MultiCell($porc_cantidad, $medidas['alto_fila_sm'], utf8_decode(number_format(intval($detalle['entrada']), 0, '.', ',')), 0, 'R', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($pdf->startPage + $porc_cantidad * 6 + $porc_texto * 2, $y);

    $pdf->MultiCell($porc_cantidad, $medidas['alto_fila_sm'], utf8_decode(number_format(floatval($detalle['kilosNeto']), 2, '.', ',')), 0, 'R', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($pdf->startPage + $porc_cantidad * 7 + $porc_texto * 2, $y);

    $pdf->MultiCell($porc_cantidad, $medidas['alto_fila_sm'], utf8_decode(number_format(floatval($detalle['precioPromedio']), 2, '.', ',')), 0, 'R', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($pdf->startPage + $porc_cantidad * 8 + $porc_texto * 2, $y);

    $pdf->MultiCell($porc_cantidad, $medidas['alto_fila_sm'], utf8_decode(number_format(floatval($detalle['importe']), 2, '.', ',')), 0, 'R', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($pdf->startPage + $porc_cantidad * 9 + $porc_texto * 2, $y);

    $pdf->MultiCell($porc_cantidad, $medidas['alto_fila_sm'], utf8_decode(number_format(floatval($detalle['gastos']), 2, '.', ',')), 0, 'R', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($pdf->startPage + $porc_cantidad * 10 + $porc_texto * 2, $y);
    
    $pdf->MultiCell($porc_cantidad, $medidas['alto_fila_sm'], utf8_decode(number_format(floatval($detalle['desconocido']), 2, '.', ',')), 0, 'R', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($pdf->startPage + $porc_cantidad * 11 + $porc_texto * 2, $y);

    $pdf->MultiCell($porc_cantidad, $medidas['alto_fila_sm'], utf8_decode(number_format(floatval($detalle['saldo']), 2, '.', ',')), 0, 'R', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;

    // Cuadros para las celdas
    // $pdf->setXY($pdf->startPage, $y);
    // $pdf->cell($porc_cantidad, $heigthPerRow, '', 'LR', 'C', 0);
    // $pdf->cell($porc_texto, $heigthPerRow, '', 'R', 'C', 0);
    // $pdf->cell($porc_texto, $heigthPerRow, '', 'R', 'C', 0);
    // $pdf->cell($porc_cantidad, $heigthPerRow, '', 'R', 'C', 0);
    // $pdf->cell($porc_cantidad, $heigthPerRow, '', 'R', 'C', 0);
    // $pdf->cell($porc_cantidad, $heigthPerRow, '', 'R', 'C', 0);
    // $pdf->cell($porc_cantidad, $heigthPerRow, '', 'R', 'C', 0);
    // $pdf->cell($porc_cantidad, $heigthPerRow, '', 'R', 'C', 0);
    // $pdf->cell($porc_cantidad, $heigthPerRow, '', 'R', 'C', 0);
    // $pdf->cell($porc_cantidad, $heigthPerRow, '', 'R', 'C', 0);
    // $pdf->cell($porc_cantidad, $heigthPerRow, '', 'R', 'C', 0);
    // $pdf->cell($porc_cantidad, $heigthPerRow, '', 'R', 'C', 0);
    // $pdf->cell($porc_cantidad, $heigthPerRow, '', 'R', 'C', 0);
    // $pdf->cell($porc_cantidad, $heigthPerRow, '', 'R', 'C', 0);

    $pdf->Line($pdf->startPage, $y + $heigthPerRow, $pdf->getPageWidth() - $pdf->startPage, $y + $heigthPerRow);
    return $heigthPerRow;
};

function cambiar_fondo($pdf, $t = 0)
{
    switch ($t) {
        case 1:
            $pdf->SetFillColor(255, 229, 88);
            $pdf->SetTextColor(0, 0, 0);
            break;
        default:
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetTextColor(0, 0, 0);
            break;
    }
}

function outputPdf($idProveedor)
{
    global $con;
    $pdf = new PDF('L', 'mm', 'A4');
    $pdf->SetTitle('ESTADO DE CUENTA');
    $pdf->AliasNbPages();
    // $pdf->SetAutoPageBreak(FALSE);
    $pdf->AddPage();

    $proveedor = obtenerEstadoDeCuentaProveedor($idProveedor);
    $medidas = array(
        'alto_fila' => 5,
        'alto_fila_sm' => 4,
        'ancho_disponible' => $pdf->getPageWidth() - $pdf->startPage * 2
    );

    $pdf->setX($pdf->startPage);
    $pdf->setFont('Arial', '', 10);
    $pdf->cell(0, 5, utf8_decode(strtoupper('Proveedor: ' . $proveedor['nombre'])), 0, 2, 'L', 0);
    $pdf->setXY($pdf->startPage, $pdf->getY());

    // Construir la tabla
    cambiar_fondo($pdf, 1);
    $pdf->setFont('Arial', '', 8);
    $porc_textos = $medidas['ancho_disponible'] * .30;
    $porc_texto = $porc_textos / 2;
    $porc_cantidades = $medidas['ancho_disponible'] - $porc_textos;
    $porc_cantidad = $porc_cantidades / 12;

    $pdf->cell($porc_cantidad, $medidas['alto_fila'] * 2, utf8_decode('FECHA'), 1, 0, 'C', 1);
    $pdf->cell($porc_texto, $medidas['alto_fila'] * 2, utf8_decode('CONCEPTO'), 'LTR', 0, 'C', 1);
    $pdf->cell($porc_texto, $medidas['alto_fila'] * 2, utf8_decode('REFERENCIA'), 'LTR', 0, 'C', 1);
    $pdf->cell($porc_cantidad * 4, $medidas['alto_fila'], utf8_decode('INGRESO O EGRESO'), 1, 0, 'C', 1);
    $pdf->cell($porc_cantidad * 4, $medidas['alto_fila'], utf8_decode('MIEL'), 1, 0, 'C', 1);
    $pdf->cell($porc_cantidad, $medidas['alto_fila'] * 2, utf8_decode('GASTOS'), 1, 0, 'C', 1);
    // $pdf->cell($porc_cantidad, $medidas['alto_fila'] * 2, utf8_decode('ISR'), 1, 0, 'C', 1);
    $pdf->cell($porc_cantidad, $medidas['alto_fila'] * 2, utf8_decode('OTROS'), 1, 0, 'C', 1);
    $pdf->cell($porc_cantidad, $medidas['alto_fila'], utf8_decode('SALDO'), 'LTR', 1, 'C', 1);

    $pdf->setX($pdf->startPage);
    $pdf->cell($porc_cantidad, $medidas['alto_fila'], '', 0, 0, 'C', 0);
    $pdf->cell($porc_texto, $medidas['alto_fila'], '', 'B', 0, 'C', 0);
    $pdf->cell($porc_texto, $medidas['alto_fila'], '', 'B', 0, 'C', 0);
    $pdf->cell($porc_cantidad, $medidas['alto_fila'], utf8_decode('Banco'), 1, 0, 'C', 1);
    $pdf->cell($porc_cantidad, $medidas['alto_fila'], utf8_decode('Efectivo'), 1, 0, 'C', 1);
    $pdf->cell($porc_cantidad, $medidas['alto_fila'], utf8_decode('Prod:C-A-B '), 1, 0, 'C', 1);
    $pdf->cell($porc_cantidad, $medidas['alto_fila'], utf8_decode('ISR Ret'), 1, 0, 'C', 1);
    $pdf->cell($porc_cantidad, $medidas['alto_fila'], utf8_decode('Entrada'), 1, 0, 'C', 1);
    $pdf->cell($porc_cantidad, $medidas['alto_fila'], utf8_decode('KG'), 1, 0, 'C', 1);
    $pdf->cell($porc_cantidad, $medidas['alto_fila'], utf8_decode('Precio'), 1, 0, 'C', 1);
    $pdf->cell($porc_cantidad, $medidas['alto_fila'], utf8_decode('Importe'), 1, 0, 'C', 1);
    $pdf->cell($porc_cantidad, $medidas['alto_fila'], '', 0, 0, 'C', 0);
    $pdf->cell($porc_cantidad, $medidas['alto_fila'], '', 0, 0, 'C', 0);
    // $pdf->cell($porc_cantidad, $medidas['alto_fila'], '', 0, 0, 'C', 0);
    $pdf->cell($porc_cantidad, $medidas['alto_fila'], utf8_decode('Final'), 'LBR', 1, 'C', 1);

    // Saldo inicial
    $y = $pdf->getY();
    $pdf->setFont('Arial', 'B', 7);
    $pdf->setXY($pdf->startPage, $y);
    //  $pdf->cell($porc_cantidad, $medidas['alto_fila_sm'], '', 'LRB', 'C', 0);
     $pdf->cell($porc_texto, $medidas['alto_fila_sm'], 'Saldo inicial', 'RB', 0, 'L', 0);
     $pdf->cell($porc_texto, $medidas['alto_fila_sm'], '', 'RB', 0, 'C', 0);
     $pdf->cell($porc_cantidad, $medidas['alto_fila_sm'], '', 'RB', 0, 'R', 0);
     $pdf->cell($porc_cantidad, $medidas['alto_fila_sm'], '', 'RB', 0, 'R', 0);
     $pdf->cell($porc_cantidad, $medidas['alto_fila_sm'], '', 'RB', 0, 'R', 0);
     $pdf->cell($porc_cantidad, $medidas['alto_fila_sm'], '', 'RB', 0, 'R', 0);
     $pdf->cell($porc_cantidad, $medidas['alto_fila_sm'], '', 'RB', 0, 'C', 0);
     $pdf->cell($porc_cantidad, $medidas['alto_fila_sm'], '', 'RB', 0, 'C', 0);
     $pdf->cell($porc_cantidad, $medidas['alto_fila_sm'], '', 'RB', 0, 'C', 0);
     $pdf->cell($porc_cantidad, $medidas['alto_fila_sm'], '', 'RB', 0, 'R', 0);
    $pdf->cell($porc_cantidad, $medidas['alto_fila_sm'], '', 'RB', 0, 'R', 0);
     $pdf->cell($porc_cantidad, $medidas['alto_fila_sm'], '', 'RB', 0, 'R', 0);
     $pdf->cell($porc_cantidad, $medidas['alto_fila_sm'], '', 'RB', 0, 'R', 0);

     $pdf->cell($porc_cantidad, $medidas['alto_fila_sm'], utf8_decode(number_format($proveedor['saldoInicial'], 2, '.', ',')), 'RB', 1, 'R', 0);

    // Datos de la tabla
    cambiar_fondo($pdf);
    $pdf->setFont('Arial', '', 6);
    $y = $pdf->getY();
    $pdf->setXY($pdf->startPage, $y);

    foreach ($proveedor['arrayDeudores'] as $detalle) {
        $detalle['fecha'] = date_format(DateTime::createFromFormat('Y-m-d', $detalle['fecha']), 'd/m/Y');
        $pdf->setXY($pdf->startPage, $y);
        $size = addLine($y, $detalle, $pdf, $porc_cantidad, $porc_texto, $medidas);
        $y += $size;
        if ($y > ($pdf->GetPageHeight() - $pdf->footerPoint - 20)) {
            $pdf->addPage();
            $y = 40;
        }
    };

    // Totales
    $pdf->setFont('Arial', 'B', 7);
    $pdf->setXY($pdf->startPage, $y);
    
    // Todas las celdas en una línea (0 en lugar de 1 al final para evitar salto)
    $pdf->cell($porc_cantidad, $medidas['alto_fila_sm'], '', 'LRB', 0, 'C', 0);
    $pdf->cell($porc_texto, $medidas['alto_fila_sm'], '', 'RB', 0, 'C', 0);
    $pdf->cell($porc_texto, $medidas['alto_fila_sm'], '', 'RB', 0, 'C', 0);
    $pdf->cell($porc_cantidad, $medidas['alto_fila_sm'], number_format(floatval($proveedor['totalBancos']), 2, '.', ','), 'RB', 0, 'R', 0);
    $pdf->cell($porc_cantidad, $medidas['alto_fila_sm'], number_format(floatval($proveedor['totalCaja']), 2, '.', ','), 'RB', 0, 'R', 0);
    $pdf->cell($porc_cantidad, $medidas['alto_fila_sm'], number_format(floatval($proveedor['totalCeraApicola']), 2, '.', ','), 'RB', 0, 'R', 0);
    $pdf->cell($porc_cantidad, $medidas['alto_fila_sm'], number_format(floatval($proveedor['totalRetenciones']), 2, '.', ','), 'RB', 0, 'R', 0);
    $pdf->cell($porc_cantidad, $medidas['alto_fila_sm'], '', 'RB', 0, 'C', 0);
    $pdf->cell($porc_cantidad, $medidas['alto_fila_sm'], number_format(floatval($proveedor['totalKilos']), 2, '.', ','), 'RB', 0, 'R', 0);
    $pdf->cell($porc_cantidad, $medidas['alto_fila_sm'], number_format(floatval($proveedor['totalPrecio']), 2, '.', ','), 'RB', 0, 'R', 0);
    $pdf->cell($porc_cantidad, $medidas['alto_fila_sm'], number_format(floatval($proveedor['totalImporte']), 2, '.', ','), 'RB', 0, 'R', 0);
    $pdf->cell($porc_cantidad, $medidas['alto_fila_sm'], number_format(floatval($proveedor['totalGastos']), 2, '.', ','), 'RB', 0, 'R', 0);
    $pdf->cell($porc_cantidad, $medidas['alto_fila_sm'], '', 'RB', 0, 'C', 0);
    $pdf->cell($porc_cantidad, $medidas['alto_fila_sm'], number_format(floatval($proveedor['totalSaldo']), 2, '.', ','), 'RB', 1, 'R', 0);

    $pdf->Output('I', strtoupper($proveedor['nombre']) . '_ESTADO DE CUENTA.pdf', true);
}

function outputError($message)
{
    $pdf = new FPDF('P', 'mm', 'A4');
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->cell(500, 5, utf8_decode($message), 0, 2, 'L', 0);
    $pdf->Output('I', $message . '.pdf', true);
}

try {
    if (!isset($_GET['idProveedor'])) {
        throw new Exception('No se especificó el proveedor');
    }
    outputPdf($_GET['idProveedor']);
} catch (Exception $e) {
    outputError($e->getMessage());
    exit();
}
