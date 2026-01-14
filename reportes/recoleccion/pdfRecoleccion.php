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

function obtenerRecoleccion($idRecoleccion) {
    global $con;
    $sqlRecoleccion = "SELECT idRecoleccion, fecha, observaciones, totalTambores, totalImporteCompra, totalPrecioPromedio FROM recoleccionencabezado
    WHERE idRecoleccion = :idRecoleccion";

    $queryRecoleccion = $con->prepare($sqlRecoleccion);
    $queryRecoleccion->bindParam(':idRecoleccion', $idRecoleccion);
    $queryRecoleccion->execute();
    if (!$queryRecoleccion) {
        throw new Exception($con->errorInfo());
    }

    $resultadoRecoleccion = $queryRecoleccion->fetch(PDO::FETCH_ASSOC);

    // Obtener personal y transporte de la recoleccion y operadores

    // $sqlPersonalRecoleccion = "SELECT p.nombre
    // FROM recoleccionpersonal rp
    // LEFT JOIN personaloaxaca p ON rp.idPersonalOM = p.idPersonalOM
    // WHERE idRecoleccion = :idRecoleccion";
    $sqlPersonalRecoleccion = "SELECT rp.nombre
    FROM recoleccionpersonal rp
    WHERE idRecoleccion = :idRecoleccion";

    $sqlTransporteRecoleccion = "SELECT t.transporte
    FROM recolecciontransporte rt
    LEFT JOIN transportes t ON rt.idTransporte = t.idTransporte
    WHERE idRecoleccion = :idRecoleccion";

    $sqlOperadoresRecoleccion = "SELECT p.nombre
    FROM recoleccionoperador rp
    LEFT JOIN personaloaxaca p ON rp.idPersonalOM = p.idPersonalOM
    WHERE idRecoleccion = :idRecoleccion";

    // Personal:
    $queryPersonalRecoleccion = $con->prepare($sqlPersonalRecoleccion);
    $queryPersonalRecoleccion->bindParam(':idRecoleccion', $idRecoleccion);
    $queryPersonalRecoleccion->execute();
    if (!$queryPersonalRecoleccion) {
        throw new Exception($con->errorInfo());
    }
    $personalRecoleccion = $queryPersonalRecoleccion->fetchAll(PDO::FETCH_ASSOC);
    $resultadoRecoleccion['personal'] = $personalRecoleccion;
    // Transportes:
    $queryTransporteRecoleccion = $con->prepare($sqlTransporteRecoleccion);
    $queryTransporteRecoleccion->bindParam(':idRecoleccion', $idRecoleccion);
    $queryTransporteRecoleccion->execute();
    if (!$queryTransporteRecoleccion) {
        throw new Exception($con->errorInfo());
    }
    $transportesRecoleccion = $queryTransporteRecoleccion->fetchAll(PDO::FETCH_ASSOC);
    $resultadoRecoleccion['transporte'] = $transportesRecoleccion;
    // Operadores
    $queryOperadoresRecoleccion = $con->prepare($sqlOperadoresRecoleccion);
    $queryOperadoresRecoleccion->bindParam(':idRecoleccion', $idRecoleccion);
    $queryOperadoresRecoleccion->execute();
    if (!$queryOperadoresRecoleccion) {
        throw new Exception($con->errorInfo());
    }
    $operadoresRecoleccion = $queryOperadoresRecoleccion->fetchAll(PDO::FETCH_ASSOC);
    $resultadoRecoleccion['operadores'] = $operadoresRecoleccion;
    // Crear la propiedad par alos nombres de transportes en un string

    $solo_ids_transporte = array_map(function ($transporteObj) {
        return $transporteObj['transporte'];
    }, $transportesRecoleccion);
    $resultadoRecoleccion['transporte'] = join(', ', $solo_ids_transporte);


    // Obtener las localidades visitadas en la recoleccion

    $sqlLocalidadesRecoleccion = "SELECT r.idRecoleccionDetalle, r.anterior,
    r.nuevo, r.recoleccion, r.total, r.precio, r.humedad, r.importe, l.localidad, l.idlocalidad as idLocalidad
    FROM recoleccion r
    LEFT JOIN localidades l ON r.idLocalidad = l.idlocalidad
    WHERE idRecoleccion = :idRecoleccion
    ORDER BY idRecoleccionDetalle";
    $queryLocalidadesRecoleccion = $con->prepare($sqlLocalidadesRecoleccion);
    $queryLocalidadesRecoleccion->bindParam(':idRecoleccion', $idRecoleccion);
    $queryLocalidadesRecoleccion->execute();
    if (!$queryLocalidadesRecoleccion) {
        throw new Exception($con->errorInfo());
    }
    $resultadoRecoleccion['localidades'] = $queryLocalidadesRecoleccion->fetchAll(PDO::FETCH_ASSOC);

    // contar cuantos tambores son
    $totalTambores = 0;
    foreach ($resultadoRecoleccion['localidades'] as $localidad) {
        $totalTambores += $localidad['recoleccion'];
    }
    return $resultadoRecoleccion;
}

function addLine($y, $detalle, $index_localidad, $pdf, $porc_cantidad, $porc_texto, $medidas)
{
    $heigthPerRow = 0;

    $pdf->MultiCell($porc_cantidad, $medidas['alto_fila_sm'], utf8_decode(number_format($index_localidad, 0, '.', ',')), 0, 'C', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($pdf->startPage + $porc_cantidad, $y);

    $pdf->MultiCell($porc_texto, $medidas['alto_fila_sm'], utf8_decode($detalle['localidad']), 0, 'L', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($pdf->startPage + $porc_cantidad + $porc_texto, $y);

    $pdf->MultiCell($porc_cantidad, $medidas['alto_fila_sm'], utf8_decode(number_format($detalle['anterior'], 0, '.', ',')), 0, 'C', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($pdf->startPage + $porc_cantidad*2 + $porc_texto, $y);

    $pdf->MultiCell($porc_cantidad, $medidas['alto_fila_sm'], utf8_decode(number_format(intval($detalle['nuevo']), 0, '.', ',')), 0, 'C', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($pdf->startPage + $porc_cantidad * 3 + $porc_texto, $y);

    $pdf->MultiCell($porc_cantidad, $medidas['alto_fila_sm'], utf8_decode(number_format(intval($detalle['recoleccion']), 0, '.', ',')), 0, 'C', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($pdf->startPage + $porc_cantidad * 4 + $porc_texto, $y);

    $pdf->MultiCell($porc_cantidad, $medidas['alto_fila_sm'], utf8_decode(number_format(intval($detalle['total']), 0, '.', ',')), 0, 'R', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($pdf->startPage + $porc_cantidad * 5 + $porc_texto, $y);

    $pdf->MultiCell($porc_cantidad, $medidas['alto_fila_sm'],'$ ' . utf8_decode(number_format(floatval($detalle['precio']), 2, '.', ',')), 0, 'R', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($pdf->startPage + $porc_cantidad * 6 + $porc_texto, $y);

    $pdf->MultiCell($porc_cantidad, $medidas['alto_fila_sm'], utf8_decode(number_format(floatval($detalle['humedad']), 2, '.', ',')), 0, 'R', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($pdf->startPage + $porc_cantidad * 7 + $porc_texto, $y);

    $pdf->MultiCell($porc_cantidad, $medidas['alto_fila_sm'],'$ ' . utf8_decode(number_format(floatval($detalle['importe']), 2, '.', ',')), 0, 'R', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;


    // Cuadros para las celdas
    $pdf->setXY($pdf->startPage, $y);
    $pdf->cell($porc_cantidad, $heigthPerRow, '', 'LBR', 'C', 0);
    $pdf->cell($porc_texto,    $heigthPerRow, '', 'BR', 'C', 0);
    $pdf->cell($porc_cantidad, $heigthPerRow, '', 'BR', 'C', 0);
    $pdf->cell($porc_cantidad, $heigthPerRow, '', 'BR', 'C', 0);
    $pdf->cell($porc_cantidad, $heigthPerRow, '', 'BR', 'C', 0);
    $pdf->cell($porc_cantidad, $heigthPerRow, '', 'BR', 'C', 0);
    $pdf->cell($porc_cantidad, $heigthPerRow, '', 'BR', 'C', 0); // Precio
    $pdf->cell($porc_cantidad, $heigthPerRow, '', 'BR', 'C', 0); // Humedad
    $pdf->cell($porc_cantidad,    $heigthPerRow, '', 'BR', 'C', 0); // Importe
    $pdf->cell($porc_cantidad, $heigthPerRow, '', 'BR', 'C', 0); // Real

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

function outputPdf($idRecoleccion)
{
    $idProveedor = 1;
    global $con;
    $pdf = new PDF('P', 'mm', 'A4');
    $pdf->SetTitle('RECOLECCION DE TAMBORES');
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $recoleccion = obtenerRecoleccion($idRecoleccion);
    $medidas = array(
        'alto_fila' => 5,
        'alto_fila_sm' => 4,
        'ancho_disponible' => $pdf->getPageWidth() - $pdf->startPage * 2
    );

    $pdf->setXY($pdf->startPage, $pdf->getY()+5);
    $pdf->setFont('Arial', 'B', 10);
    $pdf->cell(0, 5, utf8_decode(strtoupper('RECOLECCION DE TAMBORES. FOLIO: ' . $idRecoleccion)), 0, 2, 'C', 0);

    $pdf->setXY($pdf->startPage-1, $pdf->getY()+5);
    $pdf->setFont('Arial', '', 10);
    $pdf->cell(0, -5, utf8_decode(strtoupper('FECHA: ' . date('d/m/Y', strtotime($recoleccion['fecha'])))), 0, 2, 'L', 0);
    $pdf->cell(0, 5, utf8_decode(strtoupper('Código: DCO-RM-06')), 0, 2, 'R', 0);
    $pdf->setXY($pdf->startPage, $pdf->getY());

    // Construir la tabla
    cambiar_fondo($pdf, 1);
    $pdf->setFont('Arial', '', 8);
    $porc_textos = $medidas['ancho_disponible'] * .15;
    $porc_texto = $porc_textos;
    $porc_cantidades = $medidas['ancho_disponible'] - $porc_textos;
    $porc_cantidad = $porc_cantidades /9;

    $pdf->cell($porc_cantidad, $medidas['alto_fila'], utf8_decode('No.'), 'TLB', 0, 'C', 1);
    $pdf->cell($porc_texto,    $medidas['alto_fila'], utf8_decode('LOCALIDAD'), 'TLB', 0, 'C', 1);
    $pdf->cell($porc_cantidad, $medidas['alto_fila'], utf8_decode('ANTER.'), 'TLB', 0, 'C', 1);
    $pdf->cell($porc_cantidad, $medidas['alto_fila'], utf8_decode('NUEVO'), 'TLB', 0, 'C', 1);
    $pdf->cell($porc_cantidad, $medidas['alto_fila'], utf8_decode('REC.'), 'TLB', 0, 'C', 1);
    $pdf->cell($porc_cantidad, $medidas['alto_fila'], utf8_decode('TOTAL'), 'TLB', 0, 'C', 1);
    $pdf->cell($porc_cantidad, $medidas['alto_fila'], utf8_decode('PRECIO'), 'TLB', 0, 'C', 1);
    $pdf->cell($porc_cantidad, $medidas['alto_fila'], utf8_decode('HUM.'), 'TLB', 0, 'C', 1);
    $pdf->cell($porc_cantidad, $medidas['alto_fila'], utf8_decode('IMPORTE'), 'TLB', 0, 'C', 1);
    $pdf->cell($porc_cantidad, $medidas['alto_fila'], utf8_decode('REAL'), 1, 1, 'C', 1);

    $pdf->setX($pdf->startPage);

    // Datos de la tabla
    cambiar_fondo($pdf);
    $pdf->setFont('Arial', '', 6);
    $y = $pdf->getY();
    foreach ($recoleccion['localidades'] as $index=>$detalle) {
        $index_localidad = $index+1;
        $pdf->setXY($pdf->startPage, $y);
        $size = addLine($y, $detalle, $index_localidad, $pdf, $porc_cantidad, $porc_texto, $medidas);
        $y += $size;
        if ($y > ($pdf->GetPageHeight() - $pdf->footerPoint - 20)) {
            $pdf->addPage();
            $y = 40;
        }
    };

    // Totales
    $pdf->setFont('Arial', '', 10);
    $pdf->setXY($pdf->startPage, $pdf->getY()+10);
    cambiar_fondo($pdf, 1);
    $pdf->cell($medidas['ancho_disponible']/2, $medidas['alto_fila'], utf8_decode('Totales'), 1, 2, 'C', 1);
    cambiar_fondo($pdf, 0);
    $pdf->cell($medidas['ancho_disponible']/4, $medidas['alto_fila'], utf8_decode('Total de tambores'), 1, 0, 'C', 1);
    $pdf->cell($medidas['ancho_disponible']/4, $medidas['alto_fila'], utf8_decode(number_format(floatval($recoleccion['totalTambores']), 0, '.', ',')), 1, 1, 'R', 0);
    $pdf->setX($pdf->startPage);
    $pdf->cell($medidas['ancho_disponible']/4, $medidas['alto_fila'], utf8_decode('Importe total de la compra'), 1, 0, 'C', 1);
    $pdf->cell($medidas['ancho_disponible']/4, $medidas['alto_fila'], utf8_decode('$' . number_format(floatval($recoleccion['totalImporteCompra']), 2, '.', ',')), 1, 1, 'R', 0);
    $pdf->setX($pdf->startPage);
    $pdf->cell($medidas['ancho_disponible']/4, $medidas['alto_fila'], utf8_decode('Precio promedio de la miel'), 1, 0, 'C', 1);
    $pdf->cell($medidas['ancho_disponible']/4, $medidas['alto_fila'], utf8_decode('$' . number_format(floatval($recoleccion['totalPrecioPromedio']), 2, '.', ',')), 1, 1, 'R', 0);

    // Nombres de los transportes
    $pdf->setFont('Arial', 'B', 10);
    $pdf->setXY($pdf->startPage, $pdf->getY()+5);
    $pdf->cell($medidas['ancho_disponible']/2, $medidas['alto_fila'], utf8_decode('Transportes utilizados'), 0, 2, 'L', 0);
    $pdf->setFont('Arial', '', 10);
    $pdf->MultiCell($medidas['ancho_disponible'], $medidas['alto_fila'], utf8_decode($recoleccion['transporte']), 0, 'L', 0);
        
    // Observaciones
    $pdf->setFont('Arial', 'B', 10);
    $pdf->setXY($pdf->startPage, $pdf->getY()+5);
    $pdf->cell($medidas['ancho_disponible']/2, $medidas['alto_fila'], utf8_decode('Observaciones'), 0, 2, 'L', 0);
    $pdf->setFont('Arial', '', 10);
    $pdf->MultiCell($medidas['ancho_disponible'], $medidas['alto_fila'], utf8_decode($recoleccion['observaciones']), 0, 'L', 0);

    // nombres de las personas que van a firmar

    $pdf->setFont('Arial', 'B', 10);
    $pdf->setXY($pdf->startPage, $pdf->getY()+5);
    $pdf->cell($medidas['ancho_disponible'], $medidas['alto_fila'], utf8_decode('Personal de carga'), 0, 0, 'L', 0);
    $pdf->setFont('Arial', '', 10);

    // Por cada personal de carga
    foreach($recoleccion['personal'] as $personal) {
        $pdf->setX($pdf->startPage);
        $pdf->Ln(10);
        $pdf->cell($medidas['ancho_disponible']/2, $medidas['alto_fila'], utf8_decode($personal['nombre']), 0, 0, 'C', 0);
        $pdf->Line($pdf->getX(), $pdf->getY()+5, 170, $pdf->getY()+5);
    }

    $pdf->setXY($pdf->startPage, $pdf->getY()+10);
    $pdf->setFont('Arial', 'B', 10);
    $pdf->cell($medidas['ancho_disponible'], $medidas['alto_fila'], utf8_decode('Operadores'), 0, 2, 'L', 0);
    $pdf->setFont('Arial', '', 10);

    // Por cada operador
    foreach($recoleccion['operadores'] as $personal) {
        $pdf->setX($pdf->startPage);
        $pdf->Ln(10);
        $pdf->cell($medidas['ancho_disponible']/2, $medidas['alto_fila'], utf8_decode($personal['nombre']), 0, 0, 'C', 0);
        $pdf->Line($pdf->getX(), $pdf->getY()+5, 170, $pdf->getY()+5);
    }
    
    $pdf->Output('I', 'RECOLECCION DE TAMBORES.pdf', true);
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
    if (!isset($_GET['idRecoleccion'])) {
        throw new Exception('No se especificó la recolección');
    }
    outputPdf($_GET['idRecoleccion']);
} catch (Exception $e) {
    outputError($e->getMessage());
    exit();
}
