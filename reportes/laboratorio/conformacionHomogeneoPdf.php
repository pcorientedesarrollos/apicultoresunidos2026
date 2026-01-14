<?php
include_once '../../fpdf/FPDF/fpdf.php';
include_once '../../DAOConeccion/conePDO.php';

$pdo = new conePDO();
$conexion = $pdo->conectar();

//CONSULTAS
$conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$resultado = new stdClass();

try {

    if (!isset($_GET['idSalida'])) {
        throw new Exception('No se recibieron datos');
    } else {
        $idSalida = $_GET['idSalida'];
        $miel = $_GET['miel'];

        switch ($miel) {
            case '1':
                $conformacion = 'conformacionhomogeneo_encabezado';
                $folios = 'conformacionhomogeneo_folios';
                $almacen = 'almacen';
                $almacenEncabezado = 'almacenencabezado';
                $reactivos = 'conformacionhomogeneo_reactivos';
                break;
            case '2':
                $conformacion = 'conformacionhomogeneo_encabezado_organico';
                $folios = 'conformacionhomogeneo_folios_organico';
                $almacen = 'almacen_organico';
                $almacenEncabezado = 'almacenencabezado_organico';
                $reactivos = 'conformacionhomogeneo_reactivos_organico';
                break;
        };
    }

    $sql = "SELECT ce.idEncabezado, ce.fecha, ce.tipoAnalisis, ce.numeroTambos, ce.resultado, ce.personal,
    CASE WHEN ce.experimental IS NOT NULL THEN ce.experimental ELSE 'N/A' END AS experimental, ce.busqueda,
    $miel AS tipoMiel 
    FROM $conformacion ce WHERE ce.idEncabezado = :idSalida";
    $datos = $conexion->prepare($sql);
    $datos->bindParam(':idSalida', $idSalida);
    $datos->execute();
    if ($datos == FALSE) {
        throw new Exception($conexion->errorInfo());
    }
    $resultado = $datos->fetch(PDO::FETCH_ASSOC);
    $fecha = $resultado['fecha'];

    if ($resultado['tipoAnalisis']) {
        $tipoAnalisis = $resultado['tipoAnalisis'];
        $sqlA = "SELECT analisis FROM analisisconformacion WHERE idAnalisis = :tipoAnalisis";
        $resul = $conexion->prepare($sqlA);
        $resul->bindParam(':tipoAnalisis', $tipoAnalisis);
        $resul->execute();
        if ($resul == FALSE) {
            throw new Exception($conexion->errorInfo());
        }
        $resultadoAnalisis = $resul->fetch(PDO::FETCH_ASSOC);
        $tipoA = $resultadoAnalisis['analisis'];
    }

    if ($resultado['tipoMiel'] == 1) {
        $tipodMiel = 'Miel 100% pura de abeja';
    } else {
        $tipodMiel = 'Miel 100% orgánica';
    }

    if ($resultado['personal']) {
        $personal = $resultado['personal'];
        $sqlP = "SELECT nombre FROM personaloaxaca WHERE idArea = 3 AND estado = 0 AND idPersonalOM = :personal ";
        $result = $conexion->prepare($sqlP);
        $result->bindParam(':personal', $personal);
        $result->execute();
        if ($result == FALSE) {
            throw new Exception($conexion->errorInfo());
        }
        $resultadoPersonal = $result->fetch(PDO::FETCH_ASSOC);
        $tipoPerson = $resultadoPersonal['nombre'];
    }

    if ($resultado['resultado']) {
        $resp = $resultado['resultado'];
        $sqlR = "SELECT resultado FROM resultadofinal WHERE idresultadoFinal = :resp";
        $respuest =  $conexion->prepare($sqlR);
        $respuest->bindParam(':resp', $resp);
        $respuest->execute();
        if ($respuest == FALSE) {
            throw new Exception($conexion->errorInfo());
        }
        $resulResp = $respuest->fetch(PDO::FETCH_ASSOC);
        $resulResp = $resulResp['resultado'];
    }

    $foliosLst = [];

    if ($resultado['busqueda'] == '3') {
        $sqlDivisor1 = $conexion->prepare("SELECT folio, nombre, localidad, tipoMiel FROM conformacionhomogeneo_sinfolios WHERE idEncabezado = :idSalida AND tipoMiel = :tipoMiel");
        $sqlDivisor1->bindParam(':idSalida', $idSalida);
        $sqlDivisor1->bindParam(':tipoMiel', $miel);
        $sqlDivisor1->execute();
        $resultado['listaFolios'] =  $sqlDivisor1->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $sqlDivisor = $conexion->prepare("SELECT folio, almacen, sobrante FROM $folios WHERE idEncabezado = :idSalida");
        $sqlDivisor->bindParam(':idSalida', $idSalida);
        $sqlDivisor->execute();
        foreach ($sqlDivisor->fetchAll(PDO::FETCH_ASSOC) as $id) {
            if ($id['almacen'] == '1') {
                $sqlDetalle = $conexion->prepare("SELECT f.folio, p.nombre, l.localidad
                FROM $folios f 
                LEFT JOIN $almacen a ON a.idAlmacen = f.folio
                LEFT JOIN $almacenEncabezado e ON e.idAlmacen = a.idAlmacenEncabezado
                LEFT JOIN proveedor p ON p.idProveedor = e.idProveedor
                LEFT JOIN direccion d ON d.idDireccion = p.idDireccion
                LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad
                WHERE f.folio = :folio AND sobrante = 0");
                $sqlDetalle->bindParam(':folio', $id['folio']);
                $sqlDetalle->execute();
                if ($sqlDetalle == FALSE) {
                    throw new Exception($conexion->errorInfo());
                }
                $resp1 = $sqlDetalle->fetch(PDO::FETCH_ASSOC);
                $foliosId['folio'] = $resp1["folio"];
                $foliosId['nombre'] = $resp1["nombre"];
                $foliosId['localidad'] = $resp1["localidad"];
            } else {
                $sql2 = "SELECT CONCAT(s.codigo, '-', a.consecutivo) AS folio, s.nombre, '--' AS localidad
                FROM $folios f 
                INNER JOIN almacensobrantes a ON a.consecutivo = f.folio AND a.sobrante = f.sobrante
                INNER JOIN sobrantes s ON s.idSobrante = f.sobrante
                WHERE a.consecutivo = :folio AND a.sobrante = :sobrante AND a.tipoDeMiel = $miel";
                $sqlDetalle2 = $conexion->prepare($sql2);
                $sqlDetalle2->bindParam(':folio', $id['folio']);
                $sqlDetalle2->bindParam(':sobrante', $id['sobrante']);
                $sqlDetalle2->execute();
                if ($sqlDetalle2 == FALSE) {
                    throw new Exception($conexion->errorInfo());
                }
                $resp1 = $sqlDetalle2->fetch(PDO::FETCH_ASSOC);
                $foliosId['folio'] = $resp1["folio"];
                $foliosId['nombre'] = $resp1["nombre"];
                $foliosId['localidad'] = $resp1["localidad"];
            }
            array_push($foliosLst, $foliosId);
        }
        $resultado['listaFolios'] = $foliosLst;
    }

    $sqlR = "SELECT r.cantidad, r.resultado AS resultadoReactivo, cr.reactivo AS nombre
    FROM $reactivos r 
    LEFT JOIN catalogoreactivos cr ON cr.idReactivo = r.reactivo
    WHERE r.idEncabezado = :idSalida";
    $sqlDetalleRe = $conexion->prepare($sqlR);
    $sqlDetalleRe->bindParam(':idSalida', $idSalida);
    $sqlDetalleRe->execute();

    if ($sqlDetalleRe == FALSE) {
        throw new Exception($conexion->errorInfo());
    } else {
        $resultado['reactivos'] = $sqlDetalleRe->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Exception $e) {
    exit();
}

// class PDF extends FPDF
// {
//     var $startPage = 15;
//     var $footerPoint = 15;

//     function Header()
//     {
//         $this->Image('../img/LOGO.png', 15, 15, 25);
//         $this->setXY(0, 15);
//         $this->setFont('Arial', 'B', 11);
//         $this->cell($this->getPageWidth(), 4, 'OAXACA MIEL S.A. DE C.V.', 0, 1, 'C', 0);
//         $this->setFont('Arial', '', 9);
//         $this->setX(0);
//         $this->cell($this->getPageWidth(), 4, ' ', 0, 1, 'C', 0);
//         $this->setX(0);
//         $this->cell($this->getPageWidth(), 4, utf8_decode(''), 0, 1, 'C', 0);
//         $this->setX(0);
//         $this->cell($this->getPageWidth(), 4, utf8_decode(''), 0, 1, 'C', 0);
//         $this->setX(0);
//         $this->cell($this->getPageWidth(), 4, 'Tel: (999) 9.88.09.90', 0, 1, 'C', 0);
//         $this->setX(0);
//         $this->Ln(10);
//     }
// }


$pdf = new FPDF();
$pdf->AddPage('P', 'A4');
// DEFINIR VARIABLES PARA LAS DIMENSIONES DEL DOCUMENTO
$variables = new stdClass();
$variables->anchoLogo = 20;
$variables->margenes = 20;
$variables->anchoPagina = $pdf->GetPageWidth();
$variables->anchoDocumento = $variables->anchoPagina - ($variables->margenes * 2);
$variables->xInicial = $variables->margenes;
$variables->xFinal = $variables->anchoPagina - $variables->margenes;

#ENCABEZADO
$pdf->SetFont('Arial', 'B', 14);
$pdf->Image('../img/LOGO.png', 10, 15, 25);
$pdf->setXY($variables->margenes, 15);
$pdf->Cell(0, 6, utf8_decode('Apicultores Unidos de la Peninsula S.A. de C.V.'), 0, 1, 'C', 0);
$pdf->Cell(0, 20, utf8_decode('CONFORMACION DE HOMOGENEO No.' . $resultado['idEncabezado']), 0, 1, 'C', 0);

$pdf->setXY($variables->margenes - 10, $pdf->getY() + 3);
$pdf->setFont('Arial', '', 10);
$pdf->cell(0, 5, utf8_decode(strtoupper('FECHA: ' . date('d/m/Y', strtotime($fecha)))), 0, 2, 'L', 0);
$pdf->setXY($variables->margenes - 10, $pdf->getY());
$pdf->setFont('Arial', '', 10);
$pdf->cell(0, 5, utf8_decode('TIPO DE ANALISIS: ' . $tipoA), 0, 2, 'L', 0);
$pdf->setXY($variables->margenes - 10, $pdf->getY());
$pdf->setFont('Arial', '', 10);
$pdf->cell(0, 5, utf8_decode('TIPO DE MIEL: ' . $tipodMiel), 0, 2, 'L', 0);
$pdf->setFont('Arial', '', 10);
$pdf->cell(0, 5, utf8_decode('MARCA INTERNA: ' . $resultado['experimental']), 0, 2, 'L', 0);

//tabla
$pdf->SetFillColor(255, 229, 88);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetXY(10, 65);
$pdf->Cell(15, 5, utf8_decode('N°'), 1, 0, 'C', 1);
$pdf->Cell(25, 5, 'FOLIO', 1, 0, 'C', 1);
$pdf->Cell(95, 5, 'PROVEEDOR', 1, 0, 'C', 1);
$pdf->Cell(55, 5, 'LOCALIDAD', 1, 0, 'C', 1);

$pdf->SetXY(10, 70);
$pdf->SetTextColor(0, 0, 0);
$variables->increment = 55;
$variables->incrementVertical = 4;
$pdf->SetFont('Arial', '', 9);
foreach ($resultado['listaFolios'] as $k => $lista) {
    $pdf->Cell(15, $variables->incrementVertical, utf8_decode($k + 1), 1, 0, 'C', 0);
    $pdf->Cell(25, $variables->incrementVertical, utf8_decode($lista['folio']), 1, 0, 'C', 0);
    $pdf->Cell(95, $variables->incrementVertical, utf8_decode($lista['nombre']), 1, 0, 'L', 0);
    $pdf->Cell($variables->increment, $variables->incrementVertical, utf8_decode($lista['localidad']), 1, 1, 'L', 0);
}

//Reactivos y resultado
$pdf->SetFillColor(255, 229, 88);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetXY(10, $variables->incrementVertical + $pdf->GetY());
$pdf->Cell(110, 5, utf8_decode('REACTIVOS'), 1, 0, 'C', 1);

$pdf->SetXY(10, $variables->incrementVertical + 1 + $pdf->GetY());
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', '', 9);
foreach ($resultado['reactivos'] as $k => $lista) {
    $pdf->Cell(35, 4, utf8_decode($lista['cantidad']), 1, 0, 'C', 0);
    $pdf->Cell(35, 4, utf8_decode($lista['resultadoReactivo']), 1, 0, 'C', 0);
    $pdf->Cell(40, 4, utf8_decode($lista['nombre']), 1, 1, 'C', 0);
}

// $pdf->SetFillColor(255, 229, 88);
// $pdf->SetTextColor(0, 0, 0);
// $pdf->SetFont('Arial', 'B', 10);
// $pdf->setXY(10, $variables->incrementVertical + $pdf->GetY());
// $pdf->Cell($variables->margenes, $variables->incrementVertical, utf8_decode('REACTIVOS'), 1, 0, 'L', 1);
// $pdf->SetXY(10, $pdf->GetY());
// $pdf->SetTextColor(0, 0, 0);
// // $variables->increment = 55;
// $variables->incrementVertical = 4;
// $pdf->SetFont('Arial', '', 9);
// foreach ($resultado['listaFolios'] as $k => $lista) {
//     $pdf->Cell(15, $variables->incrementVertical, utf8_decode($k + 1), 1, 0, 'C', 0);
//     $pdf->Cell(25, $variables->incrementVertical, utf8_decode($lista['folio']), 1, 0, 'C', 0);
//     $pdf->Cell(95, $variables->incrementVertical, utf8_decode($lista['nombre']), 1, 0, 'L', 0);
//     $pdf->Cell($variables->increment, $variables->incrementVertical, utf8_decode($lista['localidad']), 1, 1, 'L', 0);
// }


// $pdf->SetFont('Arial', '', 10);
// $pdf->setXY($variables->margenes, $variables->incrementVertical + $pdf->GetY());
// $variables->reacMedia = $variables->anchoDocumento / 2;
// $pdf->Cell($variables->reacMedia, $variables->incrementVertical, utf8_decode('Reactivos'), 0, 0, 'L', 0);

// $pdf->SetFont('Arial', '', 9);
// $pdf->setXY($variables->margenes + 95, $pdf->GetY());
// $pdf->Cell($variables->reacMedia, $variables->incrementVertical, utf8_decode('Reactivos'), 0, 0, 'L', 0);
// $pdf->setFont('Arial', '', 10);
// $pdf->cell(0, 5, utf8_decode('TIPO DE MIEL: ' . $tipodMiel), 0, 2, 'L', 0);

$pdf->setXY($variables->margenes - 10, $pdf->getY());
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 25, utf8_decode('RESULTADO: ' . $resulResp), 0, 2, 'L', 0);
// $pdf->setXY($variables->margenes - 10, $pdf->getY() - 20);
// $pdf->SetFont('Arial', '', 10);
// $pdf->Cell(0, 25, utf8_decode('ANALIZADO POR: ' . $tipoPerson), 0, 2, 'L', 0);

// $pdf->setXY($variables->margenes,  $pdf->GetY());
// $variables->reacMedia = $variables->anchoDocumento / 2;
// $pdf->setXY(10,  $pdf->GetY() - 12);
// $variables->disY = $pdf->GetY() - 12;
// foreach ($resultado['reactivos'] as $k => $lista) {
//     $pdf->Cell(35, 4, utf8_decode($lista['cantidad']), 1, 0, 'C', 0);
//     $pdf->Cell(35, 4, utf8_decode($lista['resultadoReactivo']), 1, 0, 'C', 0);
//     $pdf->Cell(40, 4, utf8_decode($lista['nombre']), 1, 1, 'C', 0);
// }
// $pdf->Cell($variables->reacMedia / 3, 20, utf8_decode('Tetra'), 1, 0, 'C', 0);
// $pdf->Cell($variables->reacMedia / 3, 20, utf8_decode('1'), 1, 0, 'C', 0);
// $pdf->Cell($variables->reacMedia / 3, 20, utf8_decode('aprobado'), 1, 0, 'C', 0);
// $pdf->SetFont('Arial', '', 10);
// $pdf->setXY($variables->margenes + 95, $variables->disY + 10);
// $pdf->Cell($variables->anchoDocumento / 4, 20, utf8_decode($resulResp), 0, 0, 'C', 0);
// $pdf->SetFont('Arial', '', 9);
// $pdf->Cell($variables->anchoDocumento / 4, 20, utf8_decode($tipoPerson), 0, 1, 'C', 0);

//firma

// $pdf->Cell($variables->anchoDocumento / 2, 4, utf8_decode(''), 0, 0, 'C', 0);
// $pdf->Cell($variables->anchoDocumento / 2, 4, utf8_decode(''), 0, 0, 'C', 0);


$pdf->Ln(10);

$pdf->Line(10 + 20, $pdf->GetY() + 20, 10 + (31.60 * 3 - 20), $pdf->GetY() + 20);
$pdf->setXY(10, $pdf->GetY() + 20);
$pdf->cell(95, 5, utf8_decode("ANALIZADO POR:"), 0, 2, 'C', 0);
$pdf->cell(95, 5, utf8_decode($tipoPerson), 0, 2, 'C', 0);

$pdf->Line(10 + 95 + 20, $pdf->GetY() - 10, 10 + (31.60 * 6 - 20), $pdf->GetY() - 10);
$pdf->setXY(10 + 95, $pdf->GetY() - 10);
$pdf->cell(95, 5, utf8_decode("VERIFICADO POR:"), 0, 2, 'C', 0);
$pdf->cell(95, 5, utf8_decode(" "), 0, 2, 'C', 0);
$pdf->Output();
