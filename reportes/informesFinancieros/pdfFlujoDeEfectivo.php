<?php
require('../../fpdf/FPDF/fpdf.php');
include_once '../../DAOConeccion/conePDO.php';
include_once '../../informesFinancieros/php/obtenerFlujoEfectivo.php';

$pdo = new conePDO();
$con = $pdo->conectar();

function outputPdf()
{
    global $con;
    $datos = $reporte = obtenerInformeFlujoEfectivo();
    $pdf = new FPDF('L', 'mm', 'A4');
    $pdf->AddPage();
    $titulo_del_documento = 'FLUJO DE EFECTIVO AL MES DE ';
    $titulo_del_documento .= strtoupper($datos['meses'][count($datos['meses']) - 1]['mes']);

    // Si no llega cierta propiedad, mostrar un PDF con algún mensaje
    // if (!isset($datos['encabezado']['idCajaChica'])) {
    //     $pdf->SetFont('Arial', 'B', 9);
    //     $pdf->cell(500, 5, utf8_decode('No se ha podido traer la información'), 0, 2, 'L', 0);
    //     $pdf->cell(500, 5, utf8_decode($datos['error']), 0, 2, 'L', 0);
    //     $pdf->Output();
    //     exit();
    // }

    // $pdf->SetAutoPageBreak(false);
    $pageWidth = $pdf->getPageWidth() - 5;
    $pageHeight = $pdf->getPageHeight();

    $GLOBALS['littleRow'] = 3;
    $GLOBALS['twelve'] = $pageWidth / 12;
    $GLOBALS['sixth'] = $pageWidth / 6;
    $GLOBALS['cuarter'] = $pageWidth / 4;
    $GLOBALS['middle'] = $pageWidth / 2;
    $GLOBALS['rowHeight'] = 5;
    $GLOBALS['initialX'] = 10;
    $GLOBALS['logoW'] = 22;

    $GLOBALSY = [];
    $GLOBALSY['initialY'] = 25;
    $GLOBALSY['nameRowY'] = 37;
    $GLOBALSY['dataRowY'] = 45;
    $GLOBALSY['dateRowY'] = 20;
    $GLOBALSY['signRowY'] = $pageHeight / 2 - 40;
    $GLOBALSY['logoY'] = 10;
    $GLOBALSY['documentTitleY'] = 10;
    $GLOBALSY['totalY'] = 45 + $GLOBALS['rowHeight'] * 10;

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
    $pdf->SetFont('Arial', '', 9);


    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('Arial', 'B', 9);

    $x = $GLOBALS['initialX'];
    $y = $GLOBALSY['dataRowY'];
    $pdf->setXY($x, $y);

    /**
     * Espacio que tendrá la columna para los nombres de los
     * conceptos, el resto del espacio dependerá de este valor
     */

    $concepto = 60;

    $remainder = $pageWidth - $concepto - $GLOBALS['initialX'];
    
    // Separamos un espacio para cada mes más un espacio para el acumulado
    $cada_fila = $remainder / (count($datos['meses']) + 1);

    $pdf->cell($concepto, $GLOBALS['rowHeight'], utf8_decode('Concepto'), 1, 0, 'C', 1);
    foreach ($datos['meses'] as $mes) {
        $pdf->cell($cada_fila, $GLOBALS['rowHeight'], utf8_decode($mes['mes']), 1, 0, 'C', 1);
    }
    $pdf->cell($cada_fila, $GLOBALS['rowHeight'], utf8_decode('Acumulado'), 1, 1, 'C', 1);

    // *ACTIVIDADES DE OPERACIÓN*

    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->cell($concepto, $GLOBALS['rowHeight'], utf8_decode('* ACTIVIDADES DE OPERACIÓN *'), 1, 2, 'L', 0);

    $pdf->cell($concepto, $GLOBALS['rowHeight'], utf8_decode('Cobranza a clientes'), 1, 0, 'L', 0);
    foreach ($datos['actividades_operacion']['total_cobranza'] as $total) {
        $pdf->cell($cada_fila, $GLOBALS['rowHeight'], utf8_decode('$ ' . number_format($total, 2, '.', ',')), 1, 0, 'R', 0);
    }
    $pdf->Ln();

    // Viene la lista de los clientes
    $pdf->SetFont('Arial', '', 7.5);
    foreach ($datos['actividades_operacion']['cobranza_clientes'] as $cliente) {
        $pdf->cell($concepto, $GLOBALS['rowHeight'], utf8_decode($cliente->nombre), 1, 0, 'L', 0);
        foreach ($cliente->totalesPorMes as $total) {
            $pdf->cell($cada_fila, $GLOBALS['rowHeight'], utf8_decode('$ ' . number_format($total, 2, '.', ',')), 1, 0, 'R', 0);
        }
        $pdf->Ln();
    }

    // Otros ingresos

    $pdf->SetFont('Arial', 'B', 8);
    $pdf->cell($concepto, $GLOBALS['rowHeight'], utf8_decode('Otros ingresos'), 1, 0, 'L', 0);
    foreach ($datos['actividades_operacion']['total_otros_ingresos'] as $total) {
        $pdf->cell($cada_fila, $GLOBALS['rowHeight'], utf8_decode('$ ' . number_format($total, 2, '.', ',')), 1, 0, 'R', 0);
    }
    $pdf->Ln();

        // Viene la lista de otros ingresos
    $pdf->SetFont('Arial', '', 7.5);
    foreach ($datos['actividades_operacion']['otros_ingresos'] as $otro_ingreso) {
        $pdf->cell($concepto, $GLOBALS['rowHeight'], utf8_decode($otro_ingreso->concepto), 1, 0, 'L', 0);
        foreach ($otro_ingreso->totalesPorMes as $total) {
            $pdf->cell($cada_fila, $GLOBALS['rowHeight'], utf8_decode('$ ' . number_format($total, 2, '.', ',')), 1, 0, 'R', 0);
        }
        $pdf->Ln();
    }

    // Total de los ingresos

    $pdf->SetFont('Arial', 'B', 8);
    $pdf->cell($concepto, $GLOBALS['rowHeight'], utf8_decode('*** Total ingresos'), 1, 0, 'L', 0);
    foreach ($datos['actividades_operacion']['total_ingresos'] as $total) {
        $pdf->cell($cada_fila, $GLOBALS['rowHeight'], utf8_decode('$ ' . number_format($total, 2, '.', ',')), 1, 0, 'R', 0);
    }
    $pdf->Ln();

    // Pago a proveedores

    $pdf->cell($concepto, $GLOBALS['rowHeight'], utf8_decode('Pago a proveedores'), 1, 2, 'L', 0);
    $pdf->cell($concepto, $GLOBALS['rowHeight'], utf8_decode('Mat. Prima Directa'), 1, 2, 'L', 0);

    $pdf->SetFont('Arial', '', 7.5);
    foreach ($datos['pago_proveedores']['conceptos'] as $concepto_proveedores) {
        $pdf->cell($concepto, $GLOBALS['rowHeight'], utf8_decode($concepto_proveedores->concepto), 1, 0, 'L', 0);
        foreach ($concepto_proveedores->totalesPorMes as $total) {
            $pdf->cell($cada_fila, $GLOBALS['rowHeight'], utf8_decode('$ ' . number_format($total, 2, '.', ',')), 1, 0, 'R', 0);
        }
        $pdf->Ln();
    }

    // Total pago a proveedores

    $pdf->SetFont('Arial', 'B', 8);
    $pdf->cell($concepto, $GLOBALS['rowHeight'], utf8_decode('Compras Netas Materia Prima'), 1, 0, 'L', 0);
    foreach ($datos['pago_proveedores']['total_pago_proveedores'] as $total) {
        $pdf->cell($cada_fila, $GLOBALS['rowHeight'], utf8_decode('$ ' . number_format($total, 2, '.', ',')), 1, 0, 'R', 0);
    }
    $pdf->Ln();

    // Gastos de operación
    $pdf->cell($concepto, $GLOBALS['rowHeight'], utf8_decode('Gastos de operación'), 1, 2, 'L', 0);

    // Lista de cuentas con sus subcuentas

    foreach ($datos['gastos_operacion']['cuentas'] as $cuenta) {
        if (isset($cuenta->esEncabezado) && $cuenta->esEncabezado) {
            $pdf->SetFont('Arial', 'B', 8);
        } else {
            $pdf->SetFont('Arial', '', 7.5);
        }
        $pdf->cell($concepto, $GLOBALS['rowHeight'], utf8_decode($cuenta->nombre), 1, 0, 'L', 0);
        foreach ($cuenta->totalesPorMes as $total) {
            $pdf->cell($cada_fila, $GLOBALS['rowHeight'], utf8_decode('$ ' . number_format($total, 2, '.', ',')), 1, 0, 'R', 0);
        }
        $pdf->Ln();
    }

    $pdf->Output();
}

if (isset($_GET['descargar'])) {
    outputPdf();
} else {
    exit();
}
