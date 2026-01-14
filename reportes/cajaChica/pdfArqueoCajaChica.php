<?php

require('../../fpdf/FPDF/fpdf.php');
date_default_timezone_set('America/Merida');
include_once '../../DAOConeccion/conePDO.php';

include_once '../../controlAdministrativo/php/nombreDePersona.php';
include_once '../../controlAdministrativo/prestamos/php/traerTotalesPrestamos.php';
include_once '../../inventarios/php/obtenerInventarioMiel.php';
include_once '../../inventarios/php/obtenerInventarioCera.php';
include_once '../../inventarios/php/obtenerInventarioApicola.php';
include_once '../../inventarios/php/obtenerInventarioMP.php';


$pdo = new conePDO();
$con = $pdo->conectar();
class PDF extends FPDF
{
    var $startPage = 15;
    var $footerPoint = 15;

    function Header()
    {
        $this->Image('../img/LOGO.png', 15, 15, 25);
        $this->setXY(0, 15);
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
        $this->Ln(10);
    }
}

function obtenerArqueo($idArqueo)
{
    global $con;

    $sqlEncabezado = "SELECT * FROM arqueo_semanas WHERE idSemana = :idArqueo";
    $queryEncabezado = $con->prepare($sqlEncabezado);
    $queryEncabezado->bindParam(':idArqueo', $idArqueo);
    $queryEncabezado->execute();
    if (!$queryEncabezado) {
        throw new Exception($con->errorInfo());
    }
    $response = $queryEncabezado->fetch(PDO::FETCH_ASSOC);
    $fechaInicial = $response['fechaInicial'];
    $fechaFinal = $response['fechaFinal'];

    $inicialEnero = 0;
    $ingresos = 0;
    $egresos = 0;
    $saldoInicial = 0;
    $ingresosSemana = 0;
    $egresosSemana = 0;
    $saldoCaja = 0;
    $totalGastos = 0;
    $saldoFinalCaja = 0;
    $totalBilletes = 0;

    $sqlInicial = "SELECT cd.importe AS inicialEnero FROM cajachicadetalle cd LEFT JOIN cajachica ce ON ce.idCajaChica = cd.idCajaChica WHERE cd.idMovimiento = 0 AND ce.idMes = 1";
    $queryInicial = $con->prepare($sqlInicial);
    $queryInicial->execute();
    $queryInicial->bindColumn('inicialEnero', $inicialEnero);
    $queryInicial->fetch(PDO::FETCH_BOUND);
    $inicialEnero = floatval($inicialEnero);

    $sqlSaldoIngresos = "SELECT SUM(cd.importe) AS ingresos FROM cajachicadetalle cd LEFT JOIN cajachica ce ON ce.idCajaChica = cd.idCajaChica WHERE ce.fecha < :fechaInicial AND cd.idMovimiento > 0";
    $querySaldoIngresos = $con->prepare($sqlSaldoIngresos);
    $querySaldoIngresos->bindParam(':fechaInicial', $fechaInicial);
    $querySaldoIngresos->execute();
    $querySaldoIngresos->bindColumn('ingresos', $ingresos);
    $querySaldoIngresos->fetch(PDO::FETCH_BOUND);
    $ingresos = floatval($ingresos);

    $sqlSaldoEgresos = "SELECT SUM(cd.cantidad) AS egresos FROM cajachicadetalle cd LEFT JOIN cajachica ce ON ce.idCajaChica = cd.idCajaChica WHERE ce.fecha < :fechaInicial";
    $querySaldoEgresos = $con->prepare($sqlSaldoEgresos);
    $querySaldoEgresos->bindParam(':fechaInicial', $fechaInicial);
    $querySaldoEgresos->execute();
    $querySaldoEgresos->bindColumn('egresos', $egresos);
    $querySaldoEgresos->fetch(PDO::FETCH_BOUND);
    $egresos = floatval($egresos);

    $saldoInicial = $inicialEnero + $ingresos - $egresos;
    $response['saldoInicial'] = $saldoInicial;

    // -----------------------------------

    $sqlTotalIngresos = "SELECT SUM(cd.importe) AS ingresosSemana FROM cajachicadetalle cd 
    LEFT JOIN cajachica ce ON ce.idCajaChica = cd.idCajaChica
    WHERE cd.idMovimiento > 0 AND ce.fecha BETWEEN :fechaInicial AND :fechaFinal";
    $queryTotalIngresos = $con->prepare($sqlTotalIngresos);
    $queryTotalIngresos->bindParam(':fechaInicial', $fechaInicial);
    $queryTotalIngresos->bindParam(':fechaFinal', $fechaFinal);
    $queryTotalIngresos->execute();
    $queryTotalIngresos->bindColumn('ingresosSemana', $ingresosSemana);
    $queryTotalIngresos->fetch(PDO::FETCH_BOUND);
    $ingresosSemana = floatval($ingresosSemana);
    $response['totalIngresos'] = $ingresosSemana;

    $sqlTotalEgresos = "SELECT SUM(cd.cantidad) AS egresosSemana FROM cajachicadetalle cd 
    LEFT JOIN cajachica ce ON ce.idCajaChica = cd.idCajaChica
    WHERE ce.fecha BETWEEN :fechaInicial AND :fechaFinal";
    $queryTotalEgresos = $con->prepare($sqlTotalEgresos);
    $queryTotalEgresos->bindParam(':fechaInicial', $fechaInicial);
    $queryTotalEgresos->bindParam(':fechaFinal', $fechaFinal);
    $queryTotalEgresos->execute();
    $queryTotalEgresos->bindColumn('egresosSemana', $egresosSemana);
    $queryTotalEgresos->fetch(PDO::FETCH_BOUND);
    $egresosSemana = floatval($egresosSemana);
    $response['totalEgresos'] = $egresosSemana;

    $saldoCaja = $saldoInicial + $ingresosSemana - $egresosSemana;
    $response['saldoCaja'] = $saldoCaja;

    //---------------------------

    $sqlGastos = "SELECT * FROM arqueo_gastos WHERE idSemana = :idArqueo";
    $queryGastos = $con->prepare($sqlGastos);
    $queryGastos->bindParam(':idArqueo', $idArqueo);
    $queryGastos->execute();
    $response['gastos'] = $queryGastos->fetchAll(PDO::FETCH_ASSOC);

    $sqlTotalGastosComprobar = "SELECT SUM(cantidad) AS totalGastos FROM arqueo_gastos
    WHERE idSemana = :idArqueo";
    $queryTotalGastosComprobar = $con->prepare($sqlTotalGastosComprobar);
    $queryTotalGastosComprobar->bindParam(':idArqueo', $idArqueo);
    $queryTotalGastosComprobar->execute();
    $queryTotalGastosComprobar->bindColumn('totalGastos', $totalGastos);
    $queryTotalGastosComprobar->fetch(PDO::FETCH_BOUND);
    $totalGastos = floatval($totalGastos);
    $response['totalGastos'] = $totalGastos;

    //------------------------------------

    $saldoFinalCaja = $saldoCaja - $totalGastos;
    $response['saldoFinalCaja'] = $saldoFinalCaja;

    //------------------------------------

    $sqlBilletes = "SELECT * FROM arqueo_billetesmonedas WHERE idSemana = :idArqueo";
    $queryBilletes = $con->prepare($sqlBilletes);
    $queryBilletes->bindParam(':idArqueo', $idArqueo);
    $queryBilletes->execute();
    $response['billetes'] = $queryBilletes->fetchAll(PDO::FETCH_ASSOC);

    $sqlTotalBilletes = "SELECT SUM(total) AS totalBilletes FROM arqueo_billetesmonedas
    WHERE idSemana = :idArqueo";
    $queryTotalBilletes = $con->prepare($sqlTotalBilletes);
    $queryTotalBilletes->bindParam(':idArqueo', $idArqueo);
    $queryTotalBilletes->execute();
    $queryTotalBilletes->bindColumn('totalBilletes', $totalBilletes);
    $queryTotalBilletes->fetch(PDO::FETCH_BOUND);
    $totalBilletes = floatval($totalBilletes);
    $response['totalBilletes'] = $totalBilletes;

    return $response;
}

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

function outputPdf($idArqueo)
{
    global $con;
    $pdf = new PDF('P', 'mm', 'A4');
    $pdf->SetTitle('ARQUEO DE CAJA CHICA');
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $arqueo = obtenerArqueo($idArqueo);
    $medidas = array(
        'alto_fila' => 5,
        'alto_fila_sm' => 4,
        'ancho_disponible' => $pdf->getPageWidth() - $pdf->startPage * 2
    );

    $pdf->setXY($pdf->startPage, $pdf->getY());
    $pdf->setFont('Arial', 'B', 10);
    $pdf->cell(0, 5, utf8_decode(strtoupper('ARQUEO DE CAJA ' . $arqueo['nombre'])), 0, 2, 'C', 0);

    $pdf->setXY($pdf->startPage, $pdf->getY());
    $pdf->setFont('Arial', 'B', 10);
    $pdf->cell(0, 5, utf8_decode(strtoupper('ARQUEO DE CAJA Y VALIDACION DE GASTOS DEL DIA ' . date('d/m/Y', strtotime($arqueo['fechaInicial'])) . ' AL ' . date('d/m/Y', strtotime($arqueo['fechaFinal'])))), 0, 2, 'C', 0);

    // Construir la tabla
    // // CARACTERISTICAS
    $pdf->setFont('Arial', 'B', 9);
    $pdf->setXY($pdf->startPage, $pdf->getY() + 4);
    cambiar_fondo($pdf, 0);
    $pdf->cell($medidas['ancho_disponible'] / 2, $medidas['alto_fila'], utf8_decode('SALDO INICIAL'), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 2, $medidas['alto_fila'], utf8_decode('$' . number_format($arqueo['saldoInicial'], 2, '.', ',')), 1, 1, 'R', 1);
    $pdf->setFont('Arial', '', 9);
    $pdf->setX($pdf->startPage);
    $pdf->cell($medidas['ancho_disponible'] / 2, $medidas['alto_fila'], utf8_decode('Total de ingresos'), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 2, $medidas['alto_fila'], utf8_decode('$' . number_format($arqueo['totalIngresos'], 2, '.', ',')), 1, 1, 'R', 1);
    $pdf->setX($pdf->startPage);
    $pdf->cell($medidas['ancho_disponible'] / 2, $medidas['alto_fila'], utf8_decode('Total de gastos'), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 2, $medidas['alto_fila'], utf8_decode('$' . number_format($arqueo['totalEgresos'], 2, '.', ',')), 1, 1, 'R', 1);
    $pdf->setFont('Arial', 'B', 9);
    $pdf->setX($pdf->startPage);
    $pdf->cell($medidas['ancho_disponible'] / 2, $medidas['alto_fila'], utf8_decode('SALDO DE CAJA'), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 2, $medidas['alto_fila'], utf8_decode('$' . number_format($arqueo['saldoCaja'], 2, '.', ',')), 1, 1, 'R', 1);
    $pdf->setX($pdf->startPage);
    $pdf->cell($medidas['ancho_disponible'], $medidas['alto_fila'], utf8_decode('GASTOS POR COMPROBAR'), 1, 1, 'L', 1);
    $pdf->setFont('Arial', '', 9);
    foreach ($arqueo['gastos'] as $gastos) {
        $pdf->setX($pdf->startPage);
        $pdf->cell($medidas['ancho_disponible'] / 2, $medidas['alto_fila'], utf8_decode($gastos['concepto']), 1, 0, 'L', 1);
        $pdf->cell($medidas['ancho_disponible'] / 2, $medidas['alto_fila'], utf8_decode('$' . number_format($gastos['cantidad'], 2, '.', ',')), 1, 1, 'R', 1);
    };
    $pdf->setFont('Arial', 'B', 9);
    $pdf->setX($pdf->startPage);
    $pdf->cell($medidas['ancho_disponible'] / 2, $medidas['alto_fila'], utf8_decode('SALDO FINAL DE CAJA'), 1, 0, 'L', 1);
    $pdf->cell($medidas['ancho_disponible'] / 2, $medidas['alto_fila'], utf8_decode('$' . number_format($arqueo['saldoFinalCaja'], 2, '.', ',')), 1, 1, 'R', 1);

    // BILLETES
    $pdf->setFont('Arial', 'B', 9);
    $pdf->setXY($pdf->startPage, $pdf->getY() + 5);
    $pdf->cell($medidas['ancho_disponible'] / 2 / 3, $medidas['alto_fila'], utf8_decode('BILLETES'), 1, 0, 'C', 1);
    $pdf->cell($medidas['ancho_disponible'] / 2 / 3, $medidas['alto_fila'], utf8_decode('NUMERO'), 1, 0, 'C', 1);
    $pdf->cell($medidas['ancho_disponible'] / 2 / 3, $medidas['alto_fila'], utf8_decode('TOTAL'), 1, 1, 'C', 1);
    $pdf->setFont('Arial', '', 9);
    foreach ($arqueo['billetes'] as $billetes) {
        $pdf->setX($pdf->startPage);
        $pdf->cell($medidas['ancho_disponible'] / 2 / 3, $medidas['alto_fila'], utf8_decode($billetes['billeteMoneda']), 1, 0, 'R', 1);
        $pdf->cell($medidas['ancho_disponible'] / 2 / 3, $medidas['alto_fila'], utf8_decode($billetes['numero']), 1, 0, 'R', 1);
        $pdf->cell($medidas['ancho_disponible'] / 2 / 3, $medidas['alto_fila'], utf8_decode('$' . number_format($billetes['total'], 2, '.', ',')), 1, 1, 'R', 1);
    }
    $pdf->setFont('Arial', 'B', 9);
    $pdf->setX($pdf->startPage);
    $pdf->cell($medidas['ancho_disponible'] / 2 / 3, $medidas['alto_fila'], utf8_decode(''), 1, 0, 'R', 1);
    $pdf->cell($medidas['ancho_disponible'] / 2 / 3, $medidas['alto_fila'], utf8_decode(''), 1, 0, 'R', 1);
    $pdf->cell($medidas['ancho_disponible'] / 2 / 3, $medidas['alto_fila'], utf8_decode('$' . number_format($arqueo['totalBilletes'], 2, '.', ',')), 1, 1, 'R', 1);

    // nombres de las personas que van a firmar

    $pdf->Ln(10);

    $pdf->Line(10 + 20, $pdf->GetY() + 20, 10 + (31.60 * 3 - 20), $pdf->GetY() + 20);
    $pdf->setXY(10, $pdf->GetY() + 20);
    $pdf->cell(95, 5, utf8_decode("C.P. David Santos Redondo"), 0, 2, 'C', 0);

    $pdf->Line(10 + 95 + 20, $pdf->GetY() - 5, 10 + (31.60 * 6 - 20), $pdf->GetY() - 5);
    $pdf->setXY(10 + 95, $pdf->GetY() - 5);
    $pdf->cell(95, 5, utf8_decode("C.P. Ibis Banderas Couoh"), 0, 2, 'C', 0);

    $pdf->Line(10 + 20, $pdf->GetY() + 20, 10 + (31.60 * 3 - 20), $pdf->GetY() + 20);
    $pdf->setXY(10, $pdf->GetY() + 20);
    $pdf->cell(95, 5, utf8_decode("Laura Sofía López Domínguez"), 0, 2, 'C', 0);

    $pdf->Output('I', 'ARQUEO DE CAJA CHICA.pdf', true);
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
    if (!isset($_GET['idArqueo'])) {
        throw new Exception('No se especificó la recolección');
    }
    outputPdf($_GET['idArqueo']);
} catch (Exception $e) {
    outputError($e->getMessage());
    exit();
}
