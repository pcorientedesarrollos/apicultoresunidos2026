<?php

require('../../fpdf/FPDF/fpdf.php');
include_once '../../DAOConeccion/conePDO.php';

$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function getInfo($idSolicitudServicio)
{
    global $con;
    $resultado = array();
    try {
        // Consulta el encabeado
        $consultaEncabezado = $con->prepare("SELECT al.*, a.area, p.nombre,
        (SELECT SUM(s.costo) AS importe FROM solicitudservicio_conceptos s WHERE s.idSolicitudServicio = :idSolicitudServicio) as importe
        FROM solicitudservicio_encabezado al
        LEFT JOIN areas a ON a.idArea = al.departamento
        LEFT JOIN personaloaxaca p ON p.idPersonalOM = al.solicita
        WHERE al.idSolicitudServicio = :idSolicitudServicio");
        $consultaEncabezado->bindParam(':idSolicitudServicio', $idSolicitudServicio);
        $consultaEncabezado->execute();
        if ($consultaEncabezado == false) {
            throw new Exception($con->errorInfo());
        } else if ($consultaEncabezado->rowCount() == 0) {
            throw new Exception('El reporte no existe');
        } else {
            $resultado['encabezado'] = $consultaEncabezado->fetch(PDO::FETCH_ASSOC);
        }
        // Consulta el detalle
        $detalle = $con->prepare("SELECT c.*, p.nombreProveedor AS proveedor 
        FROM solicitudservicio_conceptos c
        LEFT JOIN proveedoresmantto p ON p.idProveedorMantto = c.idProveedor
        WHERE c.idSolicitudServicio = :idSolicitudServicio");
        $detalle->bindParam(':idSolicitudServicio', $idSolicitudServicio);
        $detalle->execute();
        if ($detalle == false) {
            throw new Exception($con->errorInfo());
        }
        $resultado['detalle'] = $detalle->fetchAll(PDO::FETCH_ASSOC);
        return $resultado;
    } catch (Exception $e) {
        return ['error' => true, 'message' => $e->getMessage()];
    }
};


function addLineIngreso($y, $_info, $pdf, $GLOBALES)
{
    $pdf->setY($y + 2);

    $heigthPerRow = 0;

    $pdf->MultiCell($GLOBALES['sixth'], $GLOBALES['littleRow'], utf8_decode($_info['cantidad']), 0, 'R', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($GLOBALES['initialX'] + ($GLOBALES['sixth']), $y);

    $pdf->MultiCell($GLOBALES['cuarter'], $GLOBALES['littleRow'], utf8_decode($_info['proveedor']), 0, 'L', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($GLOBALES['initialX'] + $GLOBALES['cuarter'] + $GLOBALES['sixth'], $y);

    $pdf->MultiCell($GLOBALES['cuarter'] + $GLOBALES['sixth'], $GLOBALES['littleRow'], utf8_decode(strtoupper($_info['especificaciones'])), 0, 'L', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($GLOBALES['initialX'] + $GLOBALES['cuarter'] * 2 + $GLOBALES['sixth'] * 2, $y);

    $pdf->MultiCell($GLOBALES['sixth'], $GLOBALES['littleRow'], utf8_decode('$' . number_format($_info['costo'], 2, ',', ',')), 0, 'R', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;

    $lineY = $heigthPerRow + $y + .5;
    $pdf->Line($GLOBALES['initialX'], $lineY, $GLOBALES['pageWidth'] - $GLOBALES['initialX'], $lineY);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    return $heigthPerRow + 1;
};

function outputPdf($idSolicitudServicio)
{
    global $con;
    $datos = getInfo($idSolicitudServicio);
    $pdf = new FPDF('P', 'mm', 'A4');
    $pdf->AddPage();

    if (!isset($datos['encabezado']['idSolicitudServicio'])) {
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->cell(500, 5, utf8_decode('No se ha podido traer la información'), 0, 2, 'L', 0);
        $pdf->cell(500, 5, utf8_decode($datos['message']), 0, 2, 'L', 0);
        $pdf->Output();
        exit();
    }

    $tipoDeReporte = 'SOLICITUD DE SERVICIOS';
    $codigos = 'CÓDIGO: OC21-AR-001   No. REV.00';

    $pdf->SetAutoPageBreak(false);
    $pageWidth = 190;
    $pageHeight = $pdf->getPageHeight();

    $GLOBALS['pageWidth'] = $pdf->GetPageWidth();
    $GLOBALS['littleRow'] = 3;
    $GLOBALS['twelve'] = $pageWidth / 12;
    $GLOBALS['sixth'] = $pageWidth / 6;
    $GLOBALS['cuarter'] = $pageWidth / 4;
    $GLOBALS['middle'] = $pageWidth / 2;
    $GLOBALS['rowHeight'] = 5;
    $GLOBALS['initialX'] = 10;
    $GLOBALS['logoW'] = 22;

    $GLOBALSY = [
        'initialY' => 25,
        'nameRowY' => 37,
        'dataRowY' => 45,
        'dateRowY' => 20,
        'signRowY' => $pageHeight / 2 - 40,
        'logoY' => 10,
        'documentTitleY' => 10,
        'totalY' => 45 + $GLOBALS['rowHeight'] * 10
    ];

    // for ($_i = 1; $_i <= 2; $_i++) {
    // if ($_i == 2) {
    //     foreach ($GLOBALSY as $key => $global_var) {
    //         $GLOBALSY[$key] = $global_var + $pageHeight / 2;
    //     }

    //     #Dashed lines
    //     $_dash_width = 3; #The width of the dash
    //     $_dashes = $pdf->getPageWidth() / $_dash_width; #How many dashes (including white spaces)
    //     $_dashesstarty = $pageHeight / 2; # Axis Y for dashes
    //     $_dashesstartx = 0; #Axis X for dashes

    //     for ($_n = 1; $_n <= $_dashes; $_n++) {
    //         if ($_n % 2 == 0) {
    //             $pdf->Line($_dashesstartx, $_dashesstarty, $_dashesstartx + $_dash_width, $_dashesstarty);
    //         }
    //         $_dashesstartx += $_dash_width;
    //     }
    // }

    $pdf->SetFillColor(137, 172, 118);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 9);

    $pdf->Image('../img/imgMovimientoCajaChica.png', $GLOBALS['initialX'], $GLOBALSY['logoY'], $GLOBALS['logoW']);

    $pdf->setXY($GLOBALS['initialX'] + 25, $GLOBALSY['logoY']);
    $pdf->cell(100, 4, utf8_decode('OAXACA MIEL S.A. DE C.V.'), 0, 2, 'C', 0);
    $pdf->cell(100, 4, utf8_decode(''), 0, 2, 'C', 0);
    $pdf->cell(100, 4, utf8_decode(''), 0, 2, 'C', 0);
    $pdf->setXY($GLOBALS['initialX'], $GLOBALSY['logoY'] + 15);
    $pdf->cell(100, 4, utf8_decode('Departamento: ' . ' ' . $datos['encabezado']['area']), 0, 2, 'L', 0);
    $pdf->cell(100, 4, utf8_decode('Solicita: ' . ' ' . $datos['encabezado']['nombre']), 0, 2, 'L', 0);



    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('Arial', 'B', 9);

    $pdf->setXY(($GLOBALS['sixth'] * 4) + $GLOBALS['initialX'], $GLOBALSY['documentTitleY']);
    $pdf->RoundedRect(($GLOBALS['sixth'] * 4) + $GLOBALS['initialX'], $GLOBALSY['documentTitleY'], $GLOBALS['sixth'] * 2, $GLOBALS['rowHeight'] * 2, 2, '1234', 'DF');
    $pdf->Cell($GLOBALS['sixth'] * 2, $GLOBALS['rowHeight'], utf8_decode($tipoDeReporte), 0, 2, 'C', 0);
    $pdf->Cell($GLOBALS['sixth'] * 2, $GLOBALS['rowHeight'], utf8_decode($codigos), 0, 0, 'C', 0);

    $pdf->setXY(($GLOBALS['sixth'] * 4) + $GLOBALS['initialX'], $GLOBALSY['dateRowY']);

    $pdf->RoundedRect(($GLOBALS['sixth'] * 4) + $GLOBALS['initialX'], $GLOBALSY['dateRowY'], $GLOBALS['sixth'], $GLOBALS['rowHeight'], 2, '12', 'DF');
    $pdf->Cell($GLOBALS['sixth'], $GLOBALS['rowHeight'], utf8_decode('FOLIO'), 0, 0, 'C', 0);

    $pdf->RoundedRect($GLOBALS['initialX'] + ($GLOBALS['sixth'] * 5), $GLOBALSY['dateRowY'], $GLOBALS['sixth'], $GLOBALS['rowHeight'], 2, '12', 'DF');
    $pdf->Cell($GLOBALS['sixth'], $GLOBALS['rowHeight'], utf8_decode('FECHA'), 0, 2, 'C', 0);

    #NOMBRE DE PERSONA

    $pdf->setFont('Arial', '', 8);
    $pdf->SetTextColor(0, 0, 0);

    $x = $GLOBALS['initialX'];
    $y = $GLOBALSY['nameRowY'];
    $pdf->setXY($x, $y);

    $pdf->cell(25, $GLOBALS['rowHeight'], utf8_decode(''), 0, 0, '', 0);
    $pdf->setFont('Arial', 'B', 8);
    $pdf->cell(100, $GLOBALS['rowHeight'], utf8_decode(strtoupper('')), 0, 0, 'L', 0);

    #FINALIZA NOMBRE DE PERSONA

    $pdf->SetFillColor(255, 229, 88);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 9);

    $x = $GLOBALS['initialX'];
    $y = $GLOBALSY['dataRowY'];
    $pdf->setXY($x, $y);

    $pdf->cell($GLOBALS['sixth'], $GLOBALS['rowHeight'], utf8_decode('CANTIDAD'), 1, 0, 'C', 1);
    $pdf->cell($GLOBALS['cuarter'], $GLOBALS['rowHeight'], utf8_decode('PROVEEDOR'), 1, 0, 'C', 1);
    $pdf->cell($GLOBALS['cuarter'] + $GLOBALS['sixth'], $GLOBALS['rowHeight'], utf8_decode('ESPECIFICACIONES'), 1, 0, 'C', 1);
    $pdf->cell($GLOBALS['sixth'], $GLOBALS['rowHeight'], utf8_decode('COSTO'), 1, 0, 'C', 1);

    // $x = $GLOBALS['initialX'];
    // $y = $GLOBALSY['dataRowY'] + $GLOBALS['rowHeight'];
    // $pdf->setXY($x, $y);

    // $pdf->cell($GLOBALS['sixth'], $GLOBALS['rowHeight'] * 9, '', 1, 0, 'L', 0);
    // $pdf->cell($GLOBALS['cuarter'], $GLOBALS['rowHeight'] * 9, '', 1, 0, 'L', 0);
    // $pdf->cell($GLOBALS['cuarter'] + $GLOBALS['sixth'], $GLOBALS['rowHeight'] * 9, '', 1, 0, 'L', 0);
    // $pdf->cell($GLOBALS['sixth'], $GLOBALS['rowHeight'] * 9, '', 1, 0, 'L', 0);

    // #INPUTDATA

    $pdf->Ln(25);

    $pdf->Line($GLOBALS['initialX'] + 20, $GLOBALSY['signRowY'] + 155, $GLOBALS['initialX'] + ($GLOBALS['sixth'] * 3 - 20), $GLOBALSY['signRowY'] + 155);
    $pdf->setXY($GLOBALS['initialX'], $GLOBALSY['signRowY'] + 78);
    $pdf->cell($GLOBALS['middle'], $GLOBALS['rowHeight'] + 155, utf8_decode("YUNUEM SUAREZ PANTOJA"), 0, 2, 'C', 0);

    $pdf->Line($GLOBALS['initialX'] + $GLOBALS['middle'] + 20, $GLOBALSY['signRowY'] + 155, $GLOBALS['initialX'] + ($GLOBALS['sixth'] * 6 - 20), $GLOBALSY['signRowY'] + 155);
    $pdf->setXY($GLOBALS['initialX'] + $GLOBALS['middle'], $GLOBALSY['signRowY'] + 78);
    $pdf->cell($GLOBALS['middle'], $GLOBALS['rowHeight'] + 155, utf8_decode("DAVID SANTOS REDONDO"), 0, 2, 'C', 0);

    $pdf->Line($GLOBALS['initialX'] + 20, $GLOBALSY['signRowY'] + 175, $GLOBALS['initialX'] + ($GLOBALS['sixth'] * 3 - 20), $GLOBALSY['signRowY'] + 175);
    $pdf->setXY($GLOBALS['initialX'], $GLOBALSY['signRowY'] + 175);
    $pdf->cell($GLOBALS['middle'], $GLOBALS['rowHeight'], utf8_decode($datos['encabezado']['nombre']), 0, 2, 'C', 0);

    $pdf->setFont('Arial', '', 8);

    $y = $GLOBALSY['dateRowY'] + $GLOBALS['rowHeight'];
    $pdf->setXY($x + ($GLOBALS['sixth'] * 4), $y);
    $pdf->RoundedRect($x + ($GLOBALS['sixth'] * 4), $y, $GLOBALS['sixth'], $GLOBALS['rowHeight'], 2, '34', '');
    $pdf->Cell($GLOBALS['sixth'], $GLOBALS['rowHeight'], utf8_decode($datos['encabezado']['idSolicitudServicio']), 0, 0, 'C', 0);

    $pdf->RoundedRect($x + ($GLOBALS['sixth'] * 5), $y, $GLOBALS['sixth'], $GLOBALS['rowHeight'], 2, '34', '');
    $pdf->Cell($GLOBALS['sixth'], $GLOBALS['rowHeight'], utf8_decode($datos['encabezado']['fecha']), 0, 0, 'C', 0);


    $x = $GLOBALS['initialX'];
    $y = $GLOBALSY['dataRowY'] + $GLOBALS['rowHeight'] + 1; // Más 1 para separar del encabezado
    $pdf->SetFont('Arial', '', 7);
    $pdf->setXY($x, $y);

    foreach ($datos['detalle'] as $detalle) {
        $_info = array(
            'cantidad' => $detalle['cantidad'],
            'proveedor' => $detalle['proveedor'],
            'movimiento' => $detalle['movimiento'],
            'subcuenta' => $detalle['subcuenta'],
            'concepto' => $detalle['concepto'],
            'descripcion' => $detalle['descripcion'],
            'costo' => $detalle['costo'],
            'activo' => $detalle['activo'],
            'especificaciones' => $detalle['subcuenta'] . ' / ' . $detalle['descripcion'] . ' / ' . 'ACTIVO: ' . $detalle['activo']
        );

        $size = addLineIngreso($y, $_info, $pdf, $GLOBALS);
        $y += $size;
    };

    // $x = $GLOBALS['initialX'] + ($GLOBALS['cuarter'] * 3) + $GLOBALS['twelve'];
    // $y = $GLOBALSY['dataRowY'] + $GLOBALS['rowHeight'] * 10;
    $pdf->setXY($x + ($GLOBALS['cuarter'] * 3) + $GLOBALS['twelve'], $y);
    $pdf->cell($GLOBALS['sixth'], $GLOBALS['rowHeight'], utf8_decode('$' . number_format($datos['encabezado']['importe'], 2, '.', ',')), 0, 0, 'R', 0);
    // }

    $pdf->Output();
}

if (isset($_GET['idSolicitudServicio'])) {
    outputPdf($_GET['idSolicitudServicio']);
} else {
    exit();
}
