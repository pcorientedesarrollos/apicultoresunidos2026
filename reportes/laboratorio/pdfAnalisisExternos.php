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

function obtenerAnalisis($idEntrada)
{
    global $con;
    //OBTENER ENCABEZADO
    $sqlEncabezado = "SELECT (SELECT CASE WHEN porcentaje = '1' THEN CONCAT('Humedad', '. ') ELSE null  END AS porcentaje   
    FROM analisisexternosencabezado
    WHERE idAnalisisEncabezado = :idEntrada)porcentaje, 
    (SELECT CASE WHEN sf = '1' THEN CONCAT('SF', '. ') ELSE null END AS sf   
    FROM analisisexternosencabezado
    WHERE idAnalisisEncabezado = :idEntrada) AS sf, 
    (SELECT CASE WHEN st = '1' THEN CONCAT('ST', '. ') ELSE null END AS st   
    FROM analisisexternosencabezado
    WHERE idAnalisisEncabezado = :idEntrada) AS st, 
    (SELECT CASE WHEN c13 = '1' THEN CONCAT('C13', '. ') ELSE null END AS c13   
    FROM analisisexternosencabezado
    WHERE idAnalisisEncabezado = :idEntrada) AS c13, 
    (SELECT CASE WHEN hmf = '1' THEN CONCAT('HMF', '. ') ELSE null END AS hmf   
    FROM analisisexternosencabezado
    WHERE idAnalisisEncabezado = :idEntrada) AS hmf, 
    (SELECT CASE WHEN color = '1' THEN CONCAT('Color', '. ') ELSE null END AS color   
    FROM analisisexternosencabezado
    WHERE idAnalisisEncabezado = :idEntrada) AS color, 
    (SELECT CASE WHEN tt = '1' THEN CONCAT('TT', '. ') ELSE null END AS tt   
    FROM analisisexternosencabezado
    WHERE idAnalisisEncabezado = :idEntrada) AS tt,
        aee.idAnalisisEncabezado, aee.fecha, aee.empaque, aee.numeroTambos, aee.interpretacion, aee.tipoAnalisis,
        ex.nombre AS cliente, tpm.tipoDeMiel, f.floracion
        FROM analisisexternosencabezado aee
        LEFT JOIN empresasexternas ex ON ex.idExterno = aee.idExterno
        LEFT JOIN tiposdemiel tpm ON tpm.idTipoDeMiel = aee.tipoMiel
        LEFT JOIN floraciones f ON f.idFloracion = aee.idFloracion
        WHERE aee.idAnalisisEncabezado = :idEntrada";
    $queryEncabezado = $con->prepare($sqlEncabezado);
    $queryEncabezado->bindParam(':idEntrada', $idEntrada);
    $queryEncabezado->execute();
    if (!$queryEncabezado) {
        throw new Exception($con->errorInfo());
    }

    $resultadoAnalisis = $queryEncabezado->fetch(PDO::FETCH_ASSOC);

    //OBTENER DETALLE
    $sqlAnalisisRequeridos = "SELECT idAnalisisDetalle, idAnalisisEncabezado, marcaEmpresa, marcaAup, 
    CASE WHEN porcentaje IS NOT NULL THEN porcentaje ELSE 'N/A' END AS porcentaje, 
    CASE WHEN sf IS NOT NULL THEN sf ELSE 'N/A' END AS sf, 
    CASE WHEN st IS NOT NULL THEN st ELSE 'N/A' END AS st, 
    CASE WHEN c13 IS NOT NULL THEN c13 ELSE 'N/A' END AS c13,
    CASE WHEN hmf IS NOT NULL THEN hmf ELSE 'N/A' END AS hmf, 
    CASE WHEN color IS NOT NULL THEN color ELSE 'N/A' END AS color, 
    CASE WHEN tt IS NOT NULL THEN tt ELSE 'N/A' END AS tt, 
    CASE WHEN resultado = '1' THEN 'Calidad A' ELSE 'Calidad B' END AS resultado,
    CASE WHEN resultadoSf IS NULL OR resultadoSf = '' THEN 'N/A' ELSE resultadoSf END AS resultadoSf, 
    CASE WHEN resultadoSt IS NULL OR resultadoSt = '' THEN 'N/A' ELSE resultadoSt END AS resultadoSt,
    CASE WHEN resultadoTt IS NULL OR resultadoTt = '' THEN 'N/A' ELSE resultadoTt END AS resultadoTt
    FROM analisisexternosdetalle 
    WHERE idAnalisisEncabezado = :idEntrada";
    $queryAnalisisRequeridos = $con->prepare($sqlAnalisisRequeridos);
    $queryAnalisisRequeridos->bindParam(':idEntrada', $idEntrada);
    $queryAnalisisRequeridos->execute();
    if (!$queryAnalisisRequeridos) {
        throw new Exception($con->errorInfo());
    }
    $resultadoAnalisis['analisis'] = $queryAnalisisRequeridos->fetchAll(PDO::FETCH_ASSOC);

    $sqlConfiguracion = "SELECT o.opciones, rn.simbolo, cl.rango1, cl.rango2, cl.descripcion
    FROM configuracionlaboratorio cl
    INNER JOIN rangos rn ON cl.signo = rn.idRangos
    LEFT JOIN opcioneslaboratorio o ON o.idopcionesLaboratorio = cl.idOpcionLab
    WHERE idOpcionLab = 2
    UNION
    SELECT o.opciones, rn.simbolo, cl.rango1, cl.rango2, cl.descripcion
    FROM configuracionlaboratorio cl
    INNER JOIN rangos rn ON cl.signo = rn.idRangos
    LEFT JOIN opcioneslaboratorio o ON o.idopcionesLaboratorio = cl.idOpcionLab
    WHERE idOpcionLab = 3
    UNION
    SELECT o.opciones, rn.simbolo, cl.rango1, cl.rango2, cl.descripcion
    FROM configuracionlaboratorio cl
    INNER JOIN rangos rn ON cl.signo = rn.idRangos
    LEFT JOIN opcioneslaboratorio o ON o.idopcionesLaboratorio = cl.idOpcionLab
    WHERE idOpcionLab = 7";
    $queryConfiguracion = $con->prepare($sqlConfiguracion);
    $queryConfiguracion->execute();
    if (!$queryConfiguracion) {
        throw new Exception($con->errorInfo());
    }
    $resultadoAnalisis['configuracion'] = $queryConfiguracion->fetchAll(PDO::FETCH_ASSOC);

    return $resultadoAnalisis;
}

function addLine($y, $detalle, $analisis, $index_localidad, $pdf, $porc_cantidad, $porc_texto, $medidas)
{
    $heigthPerRow = 0;

    $pdf->MultiCell($porc_cantidad, $medidas['alto_fila_sm'], utf8_decode($detalle['marcaEmpresa']), 0, 'C', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($pdf->startPage + $porc_cantidad, $y);
    $pdf->MultiCell($porc_texto, $medidas['alto_fila_sm'], utf8_decode($detalle['marcaAup']), 0, 'C', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($pdf->startPage + $porc_cantidad + $porc_texto, $y);
    $pdf->MultiCell($porc_cantidad, $medidas['alto_fila_sm'], utf8_decode($detalle['porcentaje']), 0, 'C', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($pdf->startPage + $porc_cantidad * 2  + $porc_texto, $y);
    $pdf->MultiCell($porc_cantidad, $medidas['alto_fila_sm'], utf8_decode($detalle['sf']), 0, 'C', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($pdf->startPage + $porc_cantidad  * 3 + $porc_texto, $y);
    $pdf->MultiCell($porc_cantidad, $medidas['alto_fila_sm'], utf8_decode($detalle['resultadoSf']), 0, 'C', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($pdf->startPage + $porc_cantidad  * 4 + $porc_texto, $y);
    $pdf->MultiCell($porc_cantidad, $medidas['alto_fila_sm'], utf8_decode($detalle['st']), 0, 'C', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($pdf->startPage + $porc_cantidad * 5 + $porc_texto, $y);
    $pdf->MultiCell($porc_cantidad, $medidas['alto_fila_sm'], utf8_decode($detalle['resultadoSt']), 0, 'C', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($pdf->startPage + $porc_cantidad * 6 + $porc_texto, $y);
    $pdf->MultiCell($porc_cantidad, $medidas['alto_fila_sm'], utf8_decode($detalle['c13']), 0, 'C', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($pdf->startPage + $porc_cantidad * 7 + $porc_texto, $y);
    $pdf->MultiCell($porc_cantidad, $medidas['alto_fila_sm'], utf8_decode($detalle['hmf']), 0, 'C', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($pdf->startPage + $porc_cantidad * 8 + $porc_texto, $y);
    $pdf->MultiCell($porc_cantidad, $medidas['alto_fila_sm'], utf8_decode($detalle['color']), 0, 'C', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($pdf->startPage + $porc_cantidad * 9 + $porc_texto, $y);
    $pdf->MultiCell($porc_cantidad, $medidas['alto_fila_sm'], utf8_decode($detalle['tt']), 0, 'C', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($pdf->startPage + $porc_cantidad * 10 + $porc_texto, $y);
    $pdf->MultiCell($porc_cantidad, $medidas['alto_fila_sm'], utf8_decode($detalle['resultadoTt']), 0, 'C', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($pdf->startPage + $porc_cantidad * 11 + $porc_texto, $y);
    $pdf->MultiCell($porc_cantidad, $medidas['alto_fila_sm'], utf8_decode($detalle['resultado']), 0, 'C', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($pdf->startPage + $porc_cantidad  * 12 + $porc_texto, $y);

    // Cuadros para las celdas
    $pdf->setXY($pdf->startPage, $y);
    $pdf->cell($porc_cantidad, $heigthPerRow, '', 'LBR', 'C', 0); //me
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
    $pdf->cell($porc_cantidad,    $heigthPerRow, '', 'BR', 'C', 0); // tt
    $pdf->cell($porc_cantidad, $heigthPerRow, '', 'BR', 'C', 0); // Resultado
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

function outputPdf($idEntrada)
{
    global $con;
    $pdf = new PDF('L', 'mm', 'A4');
    $pdf->SetTitle('REPORTE DE ANALISIS');
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $analisis = obtenerAnalisis($idEntrada);
    $medidas = array(
        'alto_fila' => 5,
        'alto_fila_sm' => 4,
        'ancho_disponible' => $pdf->getPageWidth() - $pdf->startPage * 2
    );

    $pdf->setXY($pdf->startPage, $pdf->getY() - 5);
    $pdf->setFont('Arial', 'B', 10);
    $pdf->cell(0, 5, utf8_decode(strtoupper('REPORTE DE ANALISIS. No: ' . $idEntrada)), 0, 2, 'C', 0);

    $pdf->setXY($pdf->startPage - 1, $pdf->getY() + 5);
    $pdf->setFont('Arial', '', 10);
    $pdf->cell(0, 5, utf8_decode('Fecha: ' . date('d/m/Y', strtotime($analisis['fecha']))), 0, 2, 'L', 0);
    $pdf->setXY($pdf->startPage, $pdf->getY());

    $pdf->setXY($pdf->startPage - 1, $pdf->getY());
    $pdf->setFont('Arial', '', 10);
    $pdf->cell(0, 5, utf8_decode('Cliente: ' . $analisis['cliente']), 0, 2, 'L', 0);
    $pdf->setXY($pdf->startPage, $pdf->getY());

    $pdf->setXY($pdf->startPage - 1, $pdf->getY());
    $pdf->setFont('Arial', '', 10);
    $pdf->cell(0, 5, utf8_decode('Muestras: ' . $analisis['numeroTambos']), 0, 2, 'L', 0);
    $pdf->setXY($pdf->startPage, $pdf->getY());

    $pdf->setXY($pdf->startPage - 1, $pdf->getY());
    $pdf->setFont('Arial', '', 10);
    $pdf->cell(0, 5, utf8_decode('Producto: ' . $analisis['tipoDeMiel']), 0, 2, 'L', 0);
    $pdf->setXY($pdf->startPage, $pdf->getY());

    $pdf->setXY($pdf->startPage - 1, $pdf->getY());
    $pdf->setFont('Arial', '', 10);
    $pdf->cell(0, 5, utf8_decode('Descripción de la muestra: ' . $analisis['floracion']), 0, 2, 'L', 0);
    $pdf->setXY($pdf->startPage, $pdf->getY());

    $pdf->setXY($pdf->startPage - 1, $pdf->getY());
    $pdf->setFont('Arial', '', 10);
    $pdf->cell(0, 5, utf8_decode('Empaque: ' . $analisis['empaque']), 0, 2, 'L', 0);
    $pdf->setXY($pdf->startPage, $pdf->getY());

    $pdf->setXY($pdf->startPage - 1, $pdf->getY());
    $pdf->setFont('Arial', '', 10);
    $pdf->cell(0, 5, utf8_decode('Análisis requeridos: ' . $analisis['porcentaje'] . $analisis['sf'] . $analisis['st'] . $analisis['c13'] . $analisis['hmf'] . $analisis['color'] . $analisis['tt']), 0, 2, 'L', 0);
    $pdf->setXY($pdf->startPage, $pdf->getY() + 2);

    $pdf->setFont('Arial', '', 8);
    $pdf->setXY($pdf->startPage + 133, $pdf->getY() - 40);
    cambiar_fondo($pdf, 1);
    $pdf->cell($medidas['ancho_disponible'] / 2, $medidas['alto_fila'], utf8_decode('CONFIGURACION DE RANGOS'), 1, 2, 'C', 1);
    $pdf->cell($medidas['ancho_disponible'] / 2 / 5, $medidas['alto_fila'], utf8_decode('ANALISIS'), 1, 0, 'C', 1);
    $pdf->cell($medidas['ancho_disponible'] / 2 / 5, $medidas['alto_fila'], utf8_decode('CONDICION'), 1, 0, 'C', 1);
    $pdf->cell($medidas['ancho_disponible'] / 2 / 5, $medidas['alto_fila'], utf8_decode('RANGO 1'), 1, 0, 'C', 1);
    $pdf->cell($medidas['ancho_disponible'] / 2 / 5, $medidas['alto_fila'], utf8_decode('RANGO 2'), 1, 0, 'C', 1);
    $pdf->cell($medidas['ancho_disponible'] / 2 / 5, $medidas['alto_fila'], utf8_decode('RESULTADO'), 1, 1, 'C', 1);
    $pdf->setX($pdf->startPage + 133);
    $pdf->setFont('Arial', '', 7);
    foreach ($analisis['configuracion'] as $index => $configuracion) {
        cambiar_fondo($pdf, 0);
        $pdf->cell($medidas['ancho_disponible'] / 2 / 5, $medidas['alto_fila'], utf8_decode($configuracion['opciones']), 1, 0, 'L', 1);
        $pdf->cell($medidas['ancho_disponible'] / 2 / 5, $medidas['alto_fila'], utf8_decode($configuracion['simbolo']), 1, 0, 'C', 1);
        $pdf->cell($medidas['ancho_disponible'] / 2 / 5, $medidas['alto_fila'], utf8_decode($configuracion['rango1']), 1, 0, 'R', 1);
        $pdf->cell($medidas['ancho_disponible'] / 2 / 5, $medidas['alto_fila'], utf8_decode($configuracion['rango2']), 1, 0, 'R', 1);
        $pdf->cell($medidas['ancho_disponible'] / 2 / 5, $medidas['alto_fila'], utf8_decode($configuracion['descripcion']), 1, 1, 'L', 1);
        $pdf->setX($pdf->startPage + 133);
    };

    // Construir la tabla
    cambiar_fondo($pdf, 1);
    $pdf->setFont('Arial', '', 9);
    $pdf->setXY($pdf->startPage, $pdf->getY() + 5);
    $porc_textos = $medidas['ancho_disponible'] * .15;
    $porc_texto = $porc_textos;
    $porc_cantidades = $medidas['ancho_disponible'] - $porc_textos;
    $porc_cantidad = $porc_cantidades / 12;

    $pdf->cell($porc_cantidad, $medidas['alto_fila'], utf8_decode('MARCA'), 'TL', 0, 'C', 1);
    $pdf->cell($porc_texto,    $medidas['alto_fila'], utf8_decode('MARCA'), 'TL', 0, 'C', 1);
    $pdf->cell($porc_cantidad, $medidas['alto_fila'], utf8_decode('HUMEDAD'), 'TL', 0, 'C', 1);
    $pdf->cell($porc_cantidad * 2, $medidas['alto_fila'], utf8_decode('SULFA'), 'TLB', 0, 'C', 1);
    $pdf->cell($porc_cantidad * 2, $medidas['alto_fila'], utf8_decode('ESTREPTO'), 'TLB', 0, 'C', 1);
    $pdf->cell($porc_cantidad, $medidas['alto_fila'], utf8_decode('C13'), 'TL', 0, 'C', 1);
    $pdf->cell($porc_cantidad, $medidas['alto_fila'], utf8_decode('HMF'), 'TL', 0, 'C', 1);
    $pdf->cell($porc_cantidad, $medidas['alto_fila'], utf8_decode('COLOR'), 'TL', 0, 'C', 1);
    $pdf->cell($porc_cantidad * 2, $medidas['alto_fila'], utf8_decode('TETRACICLINA'), 'TLB', 0, 'C', 1);
    $pdf->cell($porc_cantidad, $medidas['alto_fila'], utf8_decode('RESULTADO'), 'TLR', 1, 'C', 1);
    $pdf->setX($pdf->startPage);
    $pdf->cell($porc_cantidad, $medidas['alto_fila'], utf8_decode('EMPRESA'), 'LB', 0, 'C', 1);
    $pdf->cell($porc_texto,    $medidas['alto_fila'], utf8_decode('INTERNA'), 'LB', 0, 'C', 1);
    $pdf->cell($porc_cantidad, $medidas['alto_fila'], utf8_decode(''), 'LB', 0, 'C', 1);
    $pdf->cell($porc_cantidad, $medidas['alto_fila'], utf8_decode('RESULTADO'), 'TLB', 0, 'C', 1);
    $pdf->cell($porc_cantidad, $medidas['alto_fila'], utf8_decode('INTERPRE.'), 'TLB', 0, 'C', 1);
    $pdf->cell($porc_cantidad, $medidas['alto_fila'], utf8_decode('RESULTADO'), 'TLB', 0, 'C', 1);
    $pdf->cell($porc_cantidad, $medidas['alto_fila'], utf8_decode('INTERPRE.'), 'TLB', 0, 'C', 1);
    $pdf->cell($porc_cantidad, $medidas['alto_fila'], utf8_decode(''), 'LB', 0, 'C', 1);
    $pdf->cell($porc_cantidad, $medidas['alto_fila'], utf8_decode(''), 'LB', 0, 'C', 1);
    $pdf->cell($porc_cantidad, $medidas['alto_fila'], utf8_decode(''), 'LB', 0, 'C', 1);
    $pdf->cell($porc_cantidad, $medidas['alto_fila'], utf8_decode('RESULTADO'), 'TLB', 0, 'C', 1);
    $pdf->cell($porc_cantidad, $medidas['alto_fila'], utf8_decode('INTERPRE.'), 'TLB', 0, 'C', 1);
    $pdf->cell($porc_cantidad, $medidas['alto_fila'], utf8_decode('FINAL'), 'LBR', 1, 'C', 1);
    $pdf->setX($pdf->startPage);

    // Datos de la tabla
    cambiar_fondo($pdf);
    $pdf->setFont('Arial', '', 7);
    $y = $pdf->getY();
    foreach ($analisis['analisis'] as $index => $detalle) {
        $index_localidad = $index + 1;
        $pdf->setXY($pdf->startPage, $y);
        $size = addLine($y, $detalle, $analisis, $index_localidad, $pdf, $porc_cantidad, $porc_texto, $medidas);
        $y += $size;
        if ($y > ($pdf->GetPageHeight() - $pdf->footerPoint - 20)) {
            $pdf->addPage();
            $y = 40;
        }
    };

    // Observaciones
    $pdf->setFont('Arial', 'B', 10);
    $pdf->setXY($pdf->startPage, $pdf->getY() + 10);
    $pdf->cell($medidas['ancho_disponible'] / 2, $medidas['alto_fila'], utf8_decode('Interpretación'), 0, 2, 'L', 0);
    $pdf->setFont('Arial', '', 9);
    $pdf->MultiCell($medidas['ancho_disponible'], $medidas['alto_fila'], utf8_decode($analisis['interpretacion']), 0, 'L', 0);

    // nombres de las personas que van a firmar
    $pdf->setXY($pdf->startPage - 1, $pdf->getY() + 3);
    $pdf->Ln(10);
    $pdf->Line($pdf->getX() - 1, $pdf->getY() + 3, 85, $pdf->getY() + 3);
    $pdf->Ln(8);
    $pdf->setFont('Arial', 'B', 8);
    $pdf->Multicell($medidas['ancho_disponible'] / 5, $medidas['alto_fila'], utf8_decode(' '), 0, 0, 'C', 0);
    $pdf->Multicell($medidas['ancho_disponible'] / 5 - 5, $medidas['alto_fila'], utf8_decode('Jefe de Laboratorio'), 0, 0, 'C', 0);


    $pdf->Output('I', 'RECOLECCION DE TAMBORES.pdf', true);
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
    if (!isset($_GET['idEntrada'])) {
        throw new Exception('No se especificó la recolección');
    }
    outputPdf($_GET['idEntrada']);
} catch (Exception $e) {
    outputError($e->getMessage());
    exit();
}
