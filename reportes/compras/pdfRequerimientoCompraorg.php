<?php

require('../../fpdf/FPDF/fpdf.php');
date_default_timezone_set('America/Merida');
include_once '../../DAOConeccion/conePDO.php';
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

function obtenerRequerimiento($idRequerimiento)
{
    global $con;
    //OBTENER ENCABEZADO
    $sqlEncabezado = "SELECT idRequisicion, fechaRequisicion, fechaImpresion, importeTotal, totalTambores 
    FROM requisicionencabezado WHERE idRequisicion = :idRequerimiento";
    $queryEncabezado = $con->prepare($sqlEncabezado);
    $queryEncabezado->bindParam(':idRequerimiento', $idRequerimiento);
    $queryEncabezado->execute();
    if (!$queryEncabezado) {
        throw new Exception($con->errorInfo());
    }

    $resultadoRequerimiento = $queryEncabezado->fetch(PDO::FETCH_ASSOC);

    //OBTENER DETALLE
    $cobrados = 0;
    $sqlDetalle = "SELECT rd.idDetalle, p.nombre,  l.localidad, rd.idTipoDeMiel,
    rd.noTambores,rd.peso, rd.precio, rd.importe, rd.banco, rd.observaciones,
    rd.cobrado, c.nombre as nombreComprador, tm.tipoDeMiel, rd.saldoActual AS saldoDeudor
    FROM requisiciondetalle rd
    LEFT JOIN requisicionencabezado re ON rd.idRequisicion = re.idRequisicion
    LEFT JOIN compradores c ON c.idcomprador = rd.idComprador
    LEFT JOIN proveedor p ON p.idProveedor = rd.idProveedor
    LEFT JOIN direccion d ON d.idDireccion = p.idDireccion
    LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad
    LEFT JOIN tiposdemiel tm ON tm.idTipoDeMiel = rd.idTipoDeMiel
    WHERE re.idRequisicion = :idRequerimiento ORDER BY rd.idDetalle";
    $queryDetalle = $con->prepare($sqlDetalle);
    $queryDetalle->bindParam(':idRequerimiento', $idRequerimiento);
    $queryDetalle->execute();
    if (!$queryDetalle) {
        throw new Exception($con->errorInfo());
    }
    $resultadoRequerimiento['requerimientosDetalle'] = $queryDetalle->fetchAll(PDO::FETCH_ASSOC);

    foreach ($resultadoRequerimiento['requerimientosDetalle'] as $reqDetalle) {
        if ($reqDetalle['cobrado'] == '1') {
            $cobrados++;
        }
    }

    $sqlTotales = "SELECT SUM(importe) as sumaTotal,
    (SELECT SUM(noTambores)FROM requisiciondetalle WHERE idRequisicion = :idRequerimiento) as sumaTambores,
    (SELECT SUM(peso) FROM requisiciondetalle WHERE idRequisicion = :idRequerimiento) as sumaKilos
    FROM requisiciondetalle WHERE idRequisicion = :idRequerimiento";
    $datosTotales = $con->prepare($sqlTotales);
    $datosTotales->bindParam(':idRequerimiento', $idRequerimiento);
    $datosTotales->execute();
    if (!$datosTotales) {
        throw new Exception($con->errorInfo());
    }

    $resultadoTotales = $datosTotales->fetch(PDO::FETCH_ASSOC);
    $resultadoRequerimiento['importeTotal'] = $resultadoTotales["sumaTotal"];
    $resultadoRequerimiento['totalTambores'] = $resultadoTotales["sumaTambores"];
    $resultadoRequerimiento['totalKilos'] = $resultadoTotales["sumaKilos"];


    return $resultadoRequerimiento;
}

function addLine($y, $detalle, $requerimientos, $index_localidad, $pdf, $porc_cantidad, $porc_texto, $medidas)
{
    $heigthPerRow = 0;

    $pdf->MultiCell($porc_cantidad - 10, $medidas['alto_fila_sm'], utf8_decode($index_localidad), 0, 'C', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($pdf->startPage + $porc_cantidad - 10, $y);
    $pdf->MultiCell($porc_texto, $medidas['alto_fila_sm'], utf8_decode($detalle['nombreComprador']), 0, 'C', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($pdf->startPage + $porc_cantidad - 10 + $porc_texto, $y);
    $pdf->MultiCell($porc_cantidad, $medidas['alto_fila_sm'], utf8_decode($detalle['nombre']), 0, 'C', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($pdf->startPage + $porc_cantidad * 2 - 10  + $porc_texto, $y);
    $pdf->MultiCell($porc_cantidad, $medidas['alto_fila_sm'], utf8_decode($detalle['localidad']), 0, 'C', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($pdf->startPage + $porc_cantidad  * 3 - 10 + $porc_texto, $y);
    $pdf->MultiCell($porc_cantidad, $medidas['alto_fila_sm'], utf8_decode($detalle['tipoDeMiel']), 0, 'C', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($pdf->startPage + $porc_cantidad  * 4 - 10 + $porc_texto, $y);
    $pdf->MultiCell($porc_cantidad, $medidas['alto_fila_sm'], utf8_decode('$' . number_format($detalle['saldoDeudor'], 2, '.', ',')), 0, 'C', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($pdf->startPage + $porc_cantidad * 5 - 10 + $porc_texto, $y);
    $pdf->MultiCell($porc_cantidad, $medidas['alto_fila_sm'], utf8_decode($detalle['noTambores']), 0, 'C', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($pdf->startPage + $porc_cantidad * 6 - 10 + $porc_texto, $y);
    $pdf->MultiCell($porc_cantidad, $medidas['alto_fila_sm'], utf8_decode($detalle['peso']), 0, 'C', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($pdf->startPage + $porc_cantidad * 7 - 10 + $porc_texto, $y);
    $pdf->MultiCell($porc_cantidad, $medidas['alto_fila_sm'], utf8_decode('$' . number_format($detalle['precio'], 2, '.', ',')), 0, 'C', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($pdf->startPage + $porc_cantidad * 8 - 10 + $porc_texto, $y);
    $pdf->MultiCell($porc_cantidad, $medidas['alto_fila_sm'], utf8_decode('$' . number_format($detalle['importe'], 2, '.', ',')), 0, 'C', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($pdf->startPage + $porc_cantidad * 9 - 10 + $porc_texto, $y);
    $pdf->MultiCell($porc_cantidad, $medidas['alto_fila_sm'], utf8_decode($detalle['banco']), 0, 'C', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($pdf->startPage + $porc_cantidad * 10 - 10 + $porc_texto, $y);
    $pdf->MultiCell($porc_texto, $medidas['alto_fila_sm'], utf8_decode($detalle['observaciones']), 0, 'C', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($pdf->startPage + $porc_cantidad * 11 + $porc_texto, $y);

    // Cuadros para las celdas
    $pdf->setXY($pdf->startPage, $y);
    $pdf->cell($porc_cantidad - 10, $heigthPerRow, '', 'LBR', 'C', 0); //me
    $pdf->cell($porc_texto,    $heigthPerRow, '', 'BR', 'C', 0); //mi
    $pdf->cell($porc_cantidad, $heigthPerRow, '', 'BR', 'C', 0); //humedad
    $pdf->cell($porc_cantidad, $heigthPerRow, '', 'BR', 'C', 0); //sf
    $pdf->cell($porc_cantidad, $heigthPerRow, '', 'BR', 'C', 0); //sf
    $pdf->cell($porc_cantidad, $heigthPerRow, '', 'BR', 'C', 0); //st
    $pdf->cell($porc_cantidad, $heigthPerRow, '', 'BR', 'C', 0); //st
    $pdf->cell($porc_cantidad, $heigthPerRow, '', 'BR', 'C', 0); //c13
    $pdf->cell($porc_cantidad, $heigthPerRow, '', 'BR', 'C', 0); // hmf
    $pdf->cell($porc_cantidad, $heigthPerRow, '', 'BR', 'C', 0); // color
    $pdf->cell($porc_cantidad,    $heigthPerRow, '', 'BR', 'C', 0); // tt
    $pdf->cell($porc_texto,    $heigthPerRow, '', 'BR', 'C', 0); // tt
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

function outputPdf($idRequerimiento)
{
    global $con;
    $pdf = new PDF('L', 'mm', 'A4');
    $pdf->SetTitle('REQUERIMIENTO DE COMPRAS');
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $requerimientos = obtenerRequerimiento($idRequerimiento);
    $medidas = array(
        'alto_fila' => 5,
        'alto_fila_sm' => 4,
        'ancho_disponible' => $pdf->getPageWidth() - $pdf->startPage * 2
    );

    $pdf->setXY($pdf->startPage, $pdf->getY() - 5);
    $pdf->setFont('Arial', 'B', 10);
    $pdf->cell(0, 5, utf8_decode(strtoupper('REQUERIMIENTO DE COMPRAS. No: ' . $idRequerimiento)), 0, 2, 'C', 0);

    $pdf->setXY($pdf->startPage - 1, $pdf->getY() + 5);
    $pdf->setFont('Arial', '', 10);
    $pdf->cell(0, 5, utf8_decode('Fecha: ' . date('d/m/Y', strtotime($requerimientos['fechaRequisicion']))), 0, 2, 'L', 0);
    $pdf->cell(0, -5, utf8_decode('Código: DCO-RD-06' ), 0, 2, 'R', 0);
    $pdf->setXY($pdf->startPage, $pdf->getY());

    // Construir la tabla
    cambiar_fondo($pdf, 1);
    $pdf->setFont('Arial', '', 7);
    $pdf->setXY($pdf->startPage, $pdf->getY() + 5);
    $porc_textos = $medidas['ancho_disponible'] * .15;
    $porc_texto = $porc_textos;
    $porc_cantidades = $medidas['ancho_disponible'] - $porc_textos;
    $porc_cantidad = $porc_cantidades / 11;

    $pdf->cell($porc_cantidad - 10, $medidas['alto_fila'], utf8_decode(''), 'TLB', 0, 'C', 1);
    $pdf->cell($porc_texto, $medidas['alto_fila'], utf8_decode('COMPRADOR'), 'TLB', 0, 'C', 1);
    $pdf->cell($porc_cantidad, $medidas['alto_fila'], utf8_decode('PROVEEDOR'), 'TLB', 0, 'C', 1);
    $pdf->cell($porc_cantidad, $medidas['alto_fila'], utf8_decode('LOCALIDAD'), 'TLB', 0, 'C', 1);
    $pdf->cell($porc_cantidad, $medidas['alto_fila'], utf8_decode('MIEL'), 'TLB', 0, 'C', 1);
    $pdf->cell($porc_cantidad, $medidas['alto_fila'], utf8_decode('SALDO ACTUAL'), 'TLB', 0, 'C', 1);
    $pdf->cell($porc_cantidad, $medidas['alto_fila'], utf8_decode('TAMBOS'), 'TLB', 0, 'C', 1);
    $pdf->cell($porc_cantidad, $medidas['alto_fila'], utf8_decode('KGS.'), 'TLB', 0, 'C', 1);
    $pdf->cell($porc_cantidad, $medidas['alto_fila'], utf8_decode('PRECIO'), 'TLB', 0, 'C', 1);
    $pdf->cell($porc_cantidad, $medidas['alto_fila'], utf8_decode('IMPORTE'), 'TLB', 0, 'C', 1);
    $pdf->cell($porc_cantidad, $medidas['alto_fila'], utf8_decode('BANCO'), 'TLB', 0, 'C', 1);
    $pdf->cell($porc_texto, $medidas['alto_fila'], utf8_decode('OBSERVACIONES'), 'TRLB', 1, 'C', 1);
    $pdf->setX($pdf->startPage);

    // Datos de la tabla
    cambiar_fondo($pdf);
    $pdf->setFont('Arial', '', 7);
    $y = $pdf->getY();
    foreach ($requerimientos['requerimientosDetalle'] as $index => $detalle) {
        $index_localidad = $index + 1;
        $pdf->setXY($pdf->startPage, $y);
        $size = addLine($y, $detalle, $requerimientos, $index_localidad, $pdf, $porc_cantidad, $porc_texto, $medidas);
        $y += $size;
        if ($y > ($pdf->GetPageHeight() - $pdf->footerPoint - 20)) {
            $pdf->addPage();
            $y = 40;
        }
    };

    $pdf->setFont('Arial', '', 7);
    $pdf->setXY($pdf->startPage + 187, $pdf->getY() + 15);
    cambiar_fondo($pdf, 1);
    $pdf->cell($medidas['ancho_disponible'] / 3, $medidas['alto_fila'], utf8_decode('TOTALES'), 1, 2, 'C', 1);
    $pdf->cell($medidas['ancho_disponible'] / 3 / 3, $medidas['alto_fila'], utf8_decode('TAMBORES'), 1, 0, 'C', 1);
    $pdf->cell($medidas['ancho_disponible'] / 3 / 3, $medidas['alto_fila'], utf8_decode('IMPORTE'), 1, 0, 'C', 1);
    $pdf->cell($medidas['ancho_disponible'] / 3 / 3, $medidas['alto_fila'], utf8_decode('KILOS'), 1, 1, 'C', 1);
    $pdf->setX($pdf->startPage + 187);
    $pdf->setFont('Arial', '', 7);
    cambiar_fondo($pdf, 0);
    $pdf->cell($medidas['ancho_disponible'] / 3 / 3, $medidas['alto_fila'], utf8_decode($requerimientos['totalTambores']), 1, 0, 'C', 1);
    $pdf->cell($medidas['ancho_disponible'] / 3 / 3, $medidas['alto_fila'], utf8_decode('$' . number_format($requerimientos['importeTotal'], 2, '.', ',')), 1, 0, 'R', 1);
    $pdf->cell($medidas['ancho_disponible'] / 3 / 3, $medidas['alto_fila'], utf8_decode($requerimientos['totalKilos']), 1, 1, 'R', 1);

    // // nombres de las personas que van a firmar
    // $pdf->setXY($pdf->startPage - 1, $pdf->getY() + 3);
    // $pdf->Ln(10);
    // $pdf->Line($pdf->getX() - 1, $pdf->getY() + 3, 85, $pdf->getY() + 3);
    // $pdf->Ln(8);
    // $pdf->setFont('Arial', 'B', 8);
    // $pdf->Multicell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode('I.B.Q.A.  Lidia Lizeth Ku Puc '), 0, 0, 'C', 0);
    // $pdf->Multicell($medidas['ancho_disponible'] / 5 - 5, $medidas['alto_fila'], utf8_decode('Jefa de Laboratorio'), 0, 0, 'C', 0);


    $pdf->Output('I', 'REQUERIMIENTO DE COMPRAS.pdf', true);
}

function outputError($message)
{
    $pdf = new FPDF('L', 'mm', 'A4');
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->cell(500, 5, utf8_decode($message), 0, 2, 'L', 0);
    $pdf->Output('I', $message . '.pdf', true);
}

try {
    if (!isset($_GET['idRequerimiento'])) {
        throw new Exception('No se especificó la recolección');
    }
    outputPdf($_GET['idRequerimiento']);
} catch (Exception $e) {
    outputError($e->getMessage());
    exit();
}
