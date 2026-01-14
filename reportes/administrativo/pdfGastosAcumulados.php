<?php

include_once '../../fpdf/FPDF/fpdf.php';
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$conexion = $pdo->conectar();

$pdf = new FPDF('L', 'mm', 'A4');
$pdf->AddPage();


function fondoVerde()
{
    global $pdf;
    $pdf->SetFillColor(137, 172, 118);
    $pdf->SetTextColor(0, 0, 0);
}

function fondoBlanco()
{
    global $pdf;
    $pdf->SetFillColor(255, 255, 255);
    $pdf->SetTextColor(0, 0, 0);
}

// DEFINIR VARIABLES PARA LAS DIMENSIONES DEL DOCUMENTO

$variables = new stdClass();
$variables->anchoLogo = 20;
$variables->margenes = 10;


$variables->anchoPagina = $pdf->GetPageWidth();
$variables->anchoDocumento = $variables->anchoPagina - ($variables->margenes * 2);
$variables->xInicial = $variables->margenes;
$variables->xFinal = $variables->anchoPagina - $variables->margenes;

//Comenzar la tabla después del encabezado, en el ancho del logo
$tabla = [
    'inicioTablaConceptos' => $variables->anchoLogo + 15, //10 de maren y 5 de sepaacion
    'encabezado' => 5,
    'datos' => 5
];

#ENCABEZADO

fondoVerde();
$pdf->SetFont('Arial', 'B', 14);
$pdf->Image('../img/imgMovimientoCajaChica.png', 10, 10, $variables->anchoLogo);
$pdf->setXY($variables->margenes, 10);
$pdf->Cell($variables->anchoDocumento, 6, utf8_decode('Apicultores Unidos de la Peninsula S.A. de C.V.'), 0, 1, 'C', 0);

# MARCOS

fondoBlanco();
$pdf->setXY($variables->xInicial, $tabla['inicioTablaConceptos']);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell($variables->anchoDocumento, 5, utf8_decode('INTEGRACION COSTOS INCURRIDOS EN EL EJERCICIO'), 0, 1, 'L', 0);
$pdf->cell($variables->anchoDocumento, $tabla['encabezado'], '', 1, 0, 'L', 0);


$y = $pdf->getY() + 5;
// $pdf->setXY()



$pdf->Output();
