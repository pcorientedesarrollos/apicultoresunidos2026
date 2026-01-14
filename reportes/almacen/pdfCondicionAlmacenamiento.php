<?php

require('../../fpdf/FPDF/fpdf.php');
include_once '../../clases/consultas.php';
include_once '../../DAOConeccion/conePDO.php';

$pdo = new conePDO(); $query = new consultas();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    if( !isset($_GET['idMes']) || !isset($_GET['idAlmacen'])){
        throw new Exception('No se han recibido los parámetros esperados');
    } else {
        $idMes = $_GET['idMes'];
        $idAlmacen = $_GET['idAlmacen'];
    }

    // Consulta registros

    $consulta = $query->dameCondicionAlmacenamiento($idMes, $idAlmacen);
    $consultaCondiciones = $con->prepare($consulta[1]);
    $consultaCondiciones->execute();
    if($consultaCondiciones == FALSE){
        throw new Exception($con->errorInfo());
    }

    $registrosCons = $consultaCondiciones->fetchAll(PDO::FETCH_ASSOC);
    $registros = array();
    foreach ($registrosCons as $k => $reg) {
        foreach ($reg as $key => $value) {
            if ($reg[$key] == '1') {
                $reg[$key] = '4';
            } else if ($reg[$key] == '0') {
                $reg[$key] = '';
            }
        }
        array_push($registros, $reg);
    }

    // Consulta mes

    $sql = $query->dameCondicionAlmacenamiento($idMes, $idAlmacen);
    $consultaMes = $con->prepare($sql[0]);
    $consultaMes->execute();
    if($consultaMes == FALSE){
        throw new Exception($con->errorInfo());
    }

    $mes = $consultaMes->fetch(PDO::FETCH_ASSOC);




    class PDF extends FPDF {
        function Header() {
            $this->SetFont('Arial', 'B', 15);
            $this->Cell(100, 10, utf8_decode('CONDICIONES DE ALMACENAMIENTO'), 0, 0, 'C');
            $this->Image('../../fpdf/img/logo_oaxaca.png', 180, 8, 20);
            $this->Ln(20);
        }

    }
        
    $pdf = new PDF('P', 'mm', 'A4');
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 10);
        
    $pdf->SetXY(143, 30);
    $pdf->Cell(40, 5, utf8_decode("Código: RAL-CA-01 - REVISIÓN:01"), 0, 0, 'L');
    $pdf->SetFont('Arial', '', 12);
    $pdf->SetXY(9, 25);
    $pdf->cell(80, 5, utf8_decode('ALMACEN: ' . $mes['almacen']), 0, 2, 'L');
    $pdf->cell(40, 5, utf8_decode('MES: ' . $mes['mes']), 0, 0, 'L');
        

    $pdf->SetFillColor(56,84,40);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetXY(10, 35);
    $pdf->Cell(140, 10, "Tema", 1, 0, 'C', 1);
    $pdf->setXY(150, 35);
    $pdf->Cell(10, 10, "S1", 1, 0, 'C', 1);
    $pdf->setXY(160, 35);
    $pdf->Cell(10, 10, "S2", 1, 0, 'C', 1);
    $pdf->setXY(170, 35);
    $pdf->Cell(10, 10, "S3", 1, 0, 'C', 1);
    $pdf->setXY(180, 35);
    $pdf->Cell(10, 10, "S4", 1, 0, 'C', 1);
    $pdf->setXY(190, 35);
    $pdf->Cell(10, 10, "S5", 1, 0, 'C', 1);

    $x = 10;
    $y = 45;
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 8);
    foreach ($registros as $registro) {
        $pdf->SetXY($x, $y);
        $pdf->MultiCell(140, 10, utf8_decode($registro['tema']), 1, 'L');
        $pdf->setXY(150, $y);
        $pdf->SetFont('ZapfDingbats', '', 10);
        $pdf->Cell(10, 10, $registro['semana1'], 1, 0, 'C');
        $pdf->setXY(160, $y);
        $pdf->Cell(10, 10, $registro['semana2'], 1, 0, 'C');
        $pdf->setXY(170, $y);
        $pdf->Cell(10, 10, $registro['semana3'], 1, 0, 'C');
        $pdf->setXY(180, $y);
        $pdf->Cell(10, 10, $registro['semana4'], 1, 0, 'C');
        $pdf->setXY(190, $y);
        $pdf->Cell(10, 10, $registro['semana5'], 1, 0, 'C');
        $pdf->SetFont('Arial', '', 8);
        $y = $y + 10;
        $x = 10;
    }
    $pdf->Ln(5);
    $y +=5;
    $pdf->SetXY($x, $y);
    $pdf->SetFont('ZapfDingbats', '', 10);
    $pdf->Cell(5, 5, "4", 0, 0, 'L');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(10, 5, "Cumplido", 0, 0, 'L');
    $pdf->SetTitle(strtoupper('Condiciones de almacenamiento ' . $mes['mes']));
    $pdf->Output('', strtoupper('Condiciones de almacenamiento ' . $mes['mes']) . '.pdf');

} catch (Exception $e) {
    echo $e->getMessage();
    exit();
}
