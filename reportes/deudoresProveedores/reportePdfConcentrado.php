<?php

require('../../fpdf/FPDF/fpdf.php');
include_once '../../deudoresProveedores/php/deudoresProveedores.php';
include_once '../../controlAdministrativo/php/traeMes.php';
date_default_timezone_set('America/Merida');
$fecha = date('d/m/Y');
class PDF extends FPDF
{
    var $startPage = 10;
    var $footerPoint = 15;

    function Header()
    {
        $fecha = $GLOBALS['fecha'];
        $tipo_reporte = strtoupper($_GET['parametro']);
        $this->Image('../img/LOGO.png', 10, 10, 25);
        $this->setXY(0, 10);
        $this->setFont('Arial', 'B', 11);
        $this->cell($this->getPageWidth(), 4, 'OAXACA MIEL S.A. DE C.V.', 0, 1, 'C', 0);
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
        if ($tipo_reporte == 'TODOS') {
            $this->cell($this->getPageWidth(), 4, 'CONCENTRADO DE TODOS LOS PROVEEDORES - ' . $fecha, 0, 2, 'C', 0);
        } else {
            $this->cell($this->getPageWidth(), 4, 'CONCENTRADO DE PROVEEDORES ' . $tipo_reporte . ' - ' . $fecha, 0, 2, 'C', 0);
        }

        if (isset($_GET['idMes'])) {
            $mes_reporte = obtenerMes($_GET['idMes']);
            $this->cell($this->getPageWidth(), 4, utf8_decode(strtoupper($mes_reporte['mes'])), 0, 0, 'C', 0);
        }
        $this->Ln(10);
    }

    function Footer()
    {
        $this->SetY(-$this->footerPoint);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 20, utf8_decode('Página ' . $this->PageNo() . ' de {nb}'), 0, 0, 'C');
    }
}

function addLine($y, $proveedor, $pdf, $medidas)
{
    $heigthPerRow = 0;

    $pdf->MultiCell($medidas['ancho_disponible'] * .04, $medidas['alto_fila_sm'], utf8_decode($proveedor['index']), 0, 'C', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($pdf->startPage + $medidas['ancho_disponible'] * .04, $y);

    $pdf->MultiCell($medidas['ancho_disponible'] * .10, $medidas['alto_fila_sm'], utf8_decode($proveedor['idSagarpa']), 0, 'L', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($pdf->startPage + $medidas['ancho_disponible'] * .14, $y);

    $pdf->MultiCell($medidas['ancho_disponible'] * .25, $medidas['alto_fila_sm'], utf8_decode(strtoupper($proveedor['localidad'])), 0, 'L', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($pdf->startPage + $medidas['ancho_disponible'] * .39, $y);

    $pdf->MultiCell($medidas['ancho_disponible'] * .30, $medidas['alto_fila_sm'], utf8_decode(strtoupper($proveedor['nombre'])), 0, 'L', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($pdf->startPage + $medidas['ancho_disponible'] * .69, $y);

    $pdf->MultiCell($medidas['ancho_disponible'] * .15, $medidas['alto_fila_sm'], utf8_decode(number_format($proveedor['saldoProveedor'], 2, '.', ',')), 0, 'R', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($pdf->startPage + $medidas['ancho_disponible'] * .84, $y);

    $pdf->MultiCell($medidas['ancho_disponible'] * .15, $medidas['alto_fila_sm'], utf8_decode(number_format($proveedor['saldoDeudor'], 2, '.', ',')), 0, 'R', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($pdf->startPage + $medidas['ancho_disponible'] * .99, $y);

    // Cuadros para las celdas

    // Dibujar línea divisoria gris
$pdf->SetDrawColor(200, 200, 200);
$x_inicio = $pdf->startPage;
$x_final = $pdf->GetPageWidth() - $pdf->startPage;
$y_linea = $pdf->GetY();
$pdf->Line($x_inicio, $y_linea, $x_final, $y_linea);
   // Establecer color blanco para bordes de celdas
$pdf->SetDrawColor(255, 255, 255);

// Cuadros para las celdas
$pdf->setXY($pdf->startPage, $y);
$pdf->cell($medidas['ancho_disponible'] * .04, $heigthPerRow, '', 0, 'C', 0);
$pdf->cell($medidas['ancho_disponible'] * .10, $heigthPerRow, '', 0, 'C', 0);
$pdf->cell($medidas['ancho_disponible'] * .25, $heigthPerRow, '', 0, 'C', 0);
$pdf->cell($medidas['ancho_disponible'] * .30, $heigthPerRow, '', 0, 'C', 0);
$pdf->cell($medidas['ancho_disponible'] * .15, $heigthPerRow, '', 0, 'C', 0);
$pdf->cell($medidas['ancho_disponible'] * .15, $heigthPerRow, '', 0, 'C', 0);



// Restaurar color original
$pdf->SetDrawColor(0, 0, 0);

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

function outputPdf()
{

    global $con;
    $pdf = new PDF('P', 'mm', 'A4');
    $pdf->SetTitle('CONCENTRADO');
    $reporte = obtenerDeudoresProveedores();
    $pdf->AliasNbPages();
    $pdf->AddPage();


    $medidas = array(
        'alto_fila' => 5,
        'alto_fila_sm' => 4,
        'ancho_disponible' => $pdf->getPageWidth() - $pdf->startPage * 2
    );

    // Construir la tabla
    cambiar_fondo($pdf, 1);
    $pdf->setFont('Arial', '', 7);
    $pdf->cell($medidas['ancho_disponible'] * .04, $medidas['alto_fila_sm'] * 2, utf8_decode(''), 1, 0, 'C', 1);
    $pdf->cell($medidas['ancho_disponible'] * .10, $medidas['alto_fila_sm'] * 2, utf8_decode('ID SAGARPA'), 1, 0, 'C', 1);
    $pdf->cell($medidas['ancho_disponible'] * .25, $medidas['alto_fila_sm'] * 2, utf8_decode('LOCALIDAD'), 1, 0, 'C', 1);
    $pdf->cell($medidas['ancho_disponible'] * .30, $medidas['alto_fila_sm'] * 2, utf8_decode('NOMBRE'), 1, 0, 'C', 1);
    $pdf->cell($medidas['ancho_disponible'] * .15, $medidas['alto_fila_sm'] * 2, utf8_decode('MIEL POR PAGAR'), 1, 0, 'C', 1);
    $pdf->cell($medidas['ancho_disponible'] * .15, $medidas['alto_fila_sm'], utf8_decode('ANTICIPOS'), 'TLR', 2, 'C', 1);

    $pdf->setX($pdf->startPage);
    $pdf->cell($medidas['ancho_disponible'] * .04, $medidas['alto_fila_sm'], utf8_decode(''), 0, 0, 'C', 0);
    $pdf->cell($medidas['ancho_disponible'] * .10, $medidas['alto_fila_sm'], utf8_decode(''), 0, 0, 'C', 0);
    $pdf->cell($medidas['ancho_disponible'] * .25, $medidas['alto_fila_sm'], utf8_decode(''), 0, 0, 'C', 0);
    $pdf->cell($medidas['ancho_disponible'] * .30, $medidas['alto_fila_sm'], utf8_decode(''), 0, 0, 'C', 0);
    $pdf->cell($medidas['ancho_disponible'] * .15, $medidas['alto_fila_sm'], utf8_decode(''), 0, 0, 'C', 0);
    $pdf->cell($medidas['ancho_disponible'] * .15, $medidas['alto_fila_sm'], utf8_decode('ENTREGADOS'), 'BLR', 2, 'C', 1);

    // Totales

    $totalSaldoDeudor = 0;
    $totalSaldoProveedor = 0;
    $conciliacion = 0;
    $cantidadTotalBancos = 0;
    $cantidadTotalEfectivo = 0;
    $cantidadTotalIngresosCeraApicola = 0;
    $cantidadTotalKilosMiel = 0;
    $cantidadTotalImporteMiel = 0;
    $cantidadTotalPrecioPromedi = 0;
    $cantidadTotalEgresosCeraApicola = 0;
    $cantidadTotalGastos = 0;
    $cantidadTotalRetenciones = 0;
    $cantidadTotalDevolucionesEfectivo = 0;
    $cantidadTotalDevolucionesCeraApicola = 0;
    $cantidadTotalVentasCeraApicola = 0;
    $cantidadTotalGranTotal = 0;
    $cantidadTotalPrecioPromedio = 0;

    // Datos de la tabla
    cambiar_fondo($pdf);
    $pdf->setFont('Arial', '', 6);
    $y = $pdf->getY();

    foreach ($reporte as $key => $proveedor) {


        $proveedor['index'] = $key + 1;

        if (!isset($proveedor['totalSaldo'])) {
            $proveedor['totalSaldo'] = floatval($proveedor['cantidad']);
        }

        if (isset($proveedor['totalSaldo'])) {

            if ($proveedor['totalSaldo'] === 0) {
                $proveedor['saldoDeudor'] = 0;
                $proveedor['saldoProveedor'] = 0;
            }
            // elseif ($proveedor['cantidad'] === 0) {
            //     $proveedor['saldoDeudor'] = 0;
            //     $proveedor['saldoProveedor'] = 0;
            // }
            elseif ($proveedor['totalSaldo'] > 0) {
                $proveedor['saldoDeudor'] = floatval($proveedor['totalSaldo']);
                $proveedor['saldoProveedor'] = 0;
                $totalSaldoDeudor += floatval($proveedor['saldoDeudor']);
            } elseif ($proveedor['totalSaldo'] < 0) {
                $proveedor['saldoProveedor'] = floatval($proveedor['totalSaldo']);
                $proveedor['saldoDeudor'] = 0;
                $totalSaldoProveedor += floatval($proveedor['saldoProveedor']);
            }
            //  elseif ($proveedor['cantidad'] > 0) {
            //     $proveedor['saldoProveedor'] = 0;
            //     $proveedor['saldoDeudor'] = floatval($proveedor['cantidad']);
            //     $totalSaldoDeudor += floatval($proveedor['saldoDeudor']);
            // } elseif ($proveedor['cantidad'] < 0) {
            //     $proveedor['saldoProveedor'] = floatval($proveedor['cantidad']);
            //     $proveedor['saldoDeudor'] = 0;
            //     $totalSaldoProveedor += floatval($proveedor['saldoProveedor']);
            // } 
            else {
                $proveedor['saldoDeudor'] = 0;
                $proveedor['saldoProveedor'] = 0;
            }


            $conciliacion = $totalSaldoDeudor + $totalSaldoProveedor;

            if (!isset($proveedor['totalBancos'])) {
                $proveedor['totalBancos'] = 0;
            }
            if (!isset($proveedor['totalCaja'])) {
                $proveedor['totalCaja'] = 0;
            }
            if (!isset($proveedor['totalDevolucionesCeraApicola'])) {
                $proveedor['totalDevolucionesCeraApicola'] = 0;
            }
            if (!isset($proveedor['totalDevolucionesMiel'])) {
                $proveedor['totalDevolucionesMiel'] = 0;
            }
            if (!isset($proveedor['totalEgresosCeraApicola'])) {
                $proveedor['totalEgresosCeraApicola'] = 0;
            }
            if (!isset($proveedor['totalGastos'])) {
                $proveedor['totalGastos'] = 0;
            }
            if (!isset($proveedor['totalRetenciones'])) {
                $proveedor['totalRetenciones'] = 0;
            }
            if (!isset($proveedor['totalImporte'])) {
                $proveedor['totalImporte'] = 0;
            }
            if (!isset($proveedor['totalIngresosCeraApicola'])) {
                $proveedor['totalIngresosCeraApicola'] = 0;
            }
            if (!isset($proveedor['totalKilos'])) {
                $proveedor['totalKilos'] = 0;
            }
            if (!isset($proveedor['totalPrecio'])) {
                $proveedor['totalPrecio'] = 0;
            }
            if (!isset($proveedor['totalVentasCeraApicola'])) {
                $proveedor['totalVentasCeraApicola'] = 0;
            }
            if (!isset($proveedor['totalSaldo'])) {
                $proveedor['totalSaldo'] = floatval($proveedor['cantidad']);
            }
            $cantidadTotalBancos += floatval($proveedor['totalBancos']);
            $cantidadTotalEfectivo += floatval($proveedor['totalCaja']);
            $cantidadTotalIngresosCeraApicola += floatval($proveedor['totalIngresosCeraApicola']);
            $cantidadTotalKilosMiel += floatval($proveedor['totalKilos']);
            $cantidadTotalImporteMiel += floatval($proveedor['totalImporte']);
            if ($cantidadTotalKilosMiel > 0) {
                $cantidadTotalPrecioPromedio = $cantidadTotalImporteMiel / $cantidadTotalKilosMiel;
            }
            $cantidadTotalEgresosCeraApicola += floatval($proveedor['totalEgresosCeraApicola']);
            $cantidadTotalGastos += floatval($proveedor['totalGastos']);
            $cantidadTotalRetenciones += floatval($proveedor['totalRetenciones']);
            $cantidadTotalDevolucionesEfectivo += floatval($proveedor['totalDevolucionesMiel']);
            $cantidadTotalDevolucionesCeraApicola += floatval($proveedor['totalDevolucionesCeraApicola']);
            $cantidadTotalVentasCeraApicola += floatval($proveedor['totalVentasCeraApicola']);
            $cantidadTotalGranTotal += floatval($proveedor['totalSaldo']);
        } else {
            $proveedor['saldoDeudor'] = 0;
            $proveedor['saldoProveedor'] = 0;
        }

        if ($proveedor['saldoProveedor'] == '0' && $proveedor['saldoDeudor'] == '0' && isset($_GET['noLiquidados'])) {
            // saltarse los que ya están liquidados
            continue;
        }

        $pdf->setXY($pdf->startPage, $y);
        $size = addLine($y, $proveedor, $pdf, $medidas);
        $y += $size;

        if ($y > ($pdf->GetPageHeight() - $pdf->footerPoint - 20) && $key != (count($reporte) - 1)) {
            $pdf->addPage();
            $y = 40;
        }
    };

    cambiar_fondo($pdf, 1);
    $pdf->setFont('Arial', '', 7);
    $pdf->setXY($pdf->startPage, $y + 10);
    $pdf->cell(150, 5, 'SALDO ACTUAL', 1, 2, 'C', 1);
    $pdf->cell(50, 5, 'Miel por pagar', 1, 0, 'C', 1);
    $pdf->cell(50, 5, 'Anticipos entregados', 1, 0, 'C', 1);
    $pdf->cell(50, 5, 'Saldo', 1, 1, 'C', 1);
    $pdf->cell(50, 5, utf8_decode(number_format($totalSaldoProveedor, 2, '.', ',')), 1, 0, 'C', 0);
    $pdf->cell(50, 5, utf8_decode(number_format($totalSaldoDeudor, 2, '.', ',')), 1, 0, 'C', 0);
    $pdf->cell(50, 5, utf8_decode(number_format($conciliacion, 2, '.', ',')), 1, 2, 'C', 0);

    $pdf->Ln(5);

    $pdf->setXY($pdf->startPage, $pdf->getY());
    $pdf->cell($medidas['ancho_disponible'], 4, 'TOTALES', 1, 2, 'C', 1);
    $pdf->setFont('Arial', '', 6);
    $pdf->cell($medidas['ancho_disponible'] / 13 * 4, 4, 'INGRESOS', 1, 0, 'C', 1);
    $pdf->cell($medidas['ancho_disponible'] / 13 * 8, 4, 'EGRESOS', 1, 0, 'C', 1);
    $pdf->cell($medidas['ancho_disponible'] / 13 * 1, 4, 'SALDO FINAL', 1, 1, 'C', 1);

    $ancho_por_columna = $medidas['ancho_disponible'] / 13;
    $pdf->cell($ancho_por_columna, 5, utf8_decode('Bancos'), 1, 0, 'C', 1);
    $pdf->cell($ancho_por_columna, 5, utf8_decode('Efectivo'), 1, 0, 'C', 1);
    $pdf->cell($ancho_por_columna, 5, utf8_decode('Cera/Apíc'), 1, 0, 'C', 1);
    $pdf->cell($ancho_por_columna, 5, utf8_decode('ISR Ret'), 1, 0, 'C', 1);

    $pdf->cell($ancho_por_columna, 5, utf8_decode('Kilos Miel'), 1, 0, 'C', 1);
    $pdf->cell($ancho_por_columna, 5, utf8_decode('Precio promedio'), 1, 0, 'C', 1);
    $pdf->cell($ancho_por_columna, 5, utf8_decode('Importe Miel'), 1, 0, 'C', 1);
    $pdf->cell($ancho_por_columna, 5, utf8_decode('Imp.Cera/Apíc'), 1, 0, 'C', 1);
    $pdf->cell($ancho_por_columna, 5, utf8_decode('Gastos'), 1, 0, 'C', 1);
    $pdf->cell($ancho_por_columna, 5, utf8_decode('Dev. Efectivo'), 1, 0, 'C', 1);
    $pdf->cell($ancho_por_columna, 5, utf8_decode('Dev.Cera/Apíc'), 1, 0, 'C', 1);
    $pdf->cell($ancho_por_columna, 5, utf8_decode('Pag.Cera/Apíc'), 1, 0, 'C', 1);
    $pdf->cell($ancho_por_columna, 5, '', 1, 1, 'C', 1);

    // Datos de la tabla
    $pdf->cell($ancho_por_columna, 5, utf8_decode(number_format($cantidadTotalBancos, 2, '.', ',')), 1, 0, 'C', 0);
    $pdf->cell($ancho_por_columna, 5, utf8_decode(number_format($cantidadTotalEfectivo, 2, '.', ',')), 1, 0, 'C', 0);
    $pdf->cell($ancho_por_columna, 5, utf8_decode(number_format($cantidadTotalIngresosCeraApicola, 2, '.', ',')), 1, 0, 'C', 0);
    $pdf->cell($ancho_por_columna, 5, utf8_decode(number_format($cantidadTotalRetenciones, 2, '.', ',')), 1, 0, 'C', 0);
    $pdf->cell($ancho_por_columna, 5, utf8_decode(number_format($cantidadTotalKilosMiel, 2, '.', ',')), 1, 0, 'C', 0);
    $pdf->cell($ancho_por_columna, 5, utf8_decode(number_format($cantidadTotalPrecioPromedio, 2, '.', ',')), 1, 0, 'C', 0);
    $pdf->cell($ancho_por_columna, 5, utf8_decode(number_format($cantidadTotalImporteMiel, 2, '.', ',')), 1, 0, 'C', 0);
    $pdf->cell($ancho_por_columna, 5, utf8_decode(number_format($cantidadTotalEgresosCeraApicola, 2, '.', ',')), 1, 0, 'C', 0);
    $pdf->cell($ancho_por_columna, 5, utf8_decode(number_format($cantidadTotalGastos, 2, '.', ',')), 1, 0, 'C', 0);
    $pdf->cell($ancho_por_columna, 5, utf8_decode(number_format($cantidadTotalDevolucionesEfectivo, 2, '.', ',')), 1, 0, 'C', 0);
    $pdf->cell($ancho_por_columna, 5, utf8_decode(number_format($cantidadTotalDevolucionesCeraApicola, 2, '.', ',')), 1, 0, 'C', 0);
    $pdf->cell($ancho_por_columna, 5, utf8_decode(number_format($cantidadTotalVentasCeraApicola, 2, '.', ',')), 1, 0, 'C', 0);
    $pdf->cell($ancho_por_columna, 5, utf8_decode(number_format($cantidadTotalGranTotal, 2, '.', ',')), 1, 0, 'C', 0);




    $pdf->Output('I', 'CONCENTRADO.pdf', true);
}

outputPdf();
