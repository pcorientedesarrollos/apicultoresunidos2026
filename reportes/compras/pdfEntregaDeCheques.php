<?php

require('../../fpdf/FPDF/fpdf.php');
include_once '../../DAOConeccion/conePDO.php';
include_once '../../controlAdministrativo/php/nombreDePersona.php';

$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function getInfo($idEncabezado)
{
    global $con;
    $resultado = array();
    try {
        // Consulta el encabeado
        $consultaEncabezado = $con->prepare("SELECT ch.*, b.banco FROM chequesentregados_encabezado ch 
        LEFT JOIN bancos b ON b.idBanco = ch.idBanco WHERE ch.idEncabezado = :idEncabezado");
        $consultaEncabezado->bindParam(':idEncabezado', $idEncabezado);
        $consultaEncabezado->execute();
        if ($consultaEncabezado == false) {
            throw new Exception($con->errorInfo());
        } else if ($consultaEncabezado->rowCount() == 0) {
            throw new Exception('El reporte no existe');
        } else {
            $resultado['encabezado'] = $consultaEncabezado->fetch(PDO::FETCH_ASSOC);
            $resultado['encabezado']['idRecibe'] = retornarNombre($con, $resultado['encabezado']['tipoCatalogo'], $resultado['encabezado']['nombreRecibe']);
        }
        $detalle = $con->prepare("SELECT ch.idEntrega, che.idEncabezado, che.fechaEntrega, che.nombreRecibe, ch.folioCheque, ch.tipoDePersona, ch.idPersona, ch.importe,
        pc.fecha AS fechaCobro
        FROM chequesentregados_detalle ch
        LEFT JOIN chequesentregados_encabezado che ON che.idEncabezado = ch.idEncabezado
        LEFT JOIN polizacheque pc ON pc.folioCheque = ch.folioCheque
        WHERE ch.idEncabezado = :idEncabezado");
        $detalle->bindParam(':idEncabezado', $idEncabezado);
        $detalle->execute();
        if ($detalle == false) {
            throw new Exception($con->errorInfo());
        }

        $array = array();
        foreach ($detalle->fetchAll(PDO::FETCH_ASSOC) as $data) {
            $data['nombre'] = retornarNombre($con, $data['tipoDePersona'], $data['idPersona']);
            array_push($array, $data);
        }
        $resultado['detalle'] = $array;

        return $resultado;
    } catch (Exception $e) {
        return ['error' => true, 'message' => $e->getMessage()];
    }
};


function addLineIngreso($y, $_info, $pdf, $GLOBALES)
{

    $heigthPerRow = 0;

    $pdf->MultiCell($GLOBALES['cuarter'], $GLOBALES['littleRow'], utf8_decode($_info['folioCheque']), 0, 'R', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($GLOBALES['initialX'] + ($GLOBALES['cuarter']), $y);

    // $pdf->MultiCell($GLOBALES['cuarter'], $GLOBALES['littleRow'], utf8_decode($_info['beneficiario']), 0, 'L', 0);
    // $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    // $pdf->setXY($GLOBALES['initialX'] + $GLOBALES['cuarter'] + $GLOBALES['cuarter'], $y);

    $pdf->MultiCell($GLOBALES['cuarter'], $GLOBALES['littleRow'], utf8_decode('$' . number_format($_info['importe'], 2, ',', ',')), 0, 'R', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    // $pdf->setXY($GLOBALES['initialX'] + $GLOBALES['cuarter'] * 2 + $GLOBALES['sixth'], $y);


    // $pdf->MultiCell($GLOBALES['sixth'], $GLOBALES['littleRow'], utf8_decode('$' . number_format($_info['precioUnitario'], 2, '.', ',')), 0, 'R', 0);
    // $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    // $pdf->setXY($GLOBALES['initialX'] + $GLOBALES['sixth'] * 2 + $GLOBALES['cuarter'] * 2, $y);

    // $pdf->MultiCell($GLOBALES['sixth'], $GLOBALES['littleRow'], utf8_decode('$' . number_format($_info['importe'], 2, ',', ',')), 0, 'R', 0);
    // $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;

    return $heigthPerRow;
};

function outputPdf($idEncabezado)
{
    global $con;
    $datos = getInfo($idEncabezado);
    // echo json_encode($datos);
    $pdf = new FPDF('P', 'mm', 'A4');
    $pdf->AddPage();

    if (!isset($datos['encabezado']['idEncabezado'])) {
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->cell(500, 5, utf8_decode('No se ha podido traer la información'), 0, 1, 'L', 0);
        $pdf->Multicell(190, 5, utf8_decode($datos['message']));
        $pdf->Output();
        exit();
    }

    $tipoDeReporte = 'CHEQUES ENTREGADOS';

    $pdf->SetAutoPageBreak(false);
    $pageWidth = 190;
    $pageHeight = $pdf->getPageHeight();

    $GLOBALS['littleRow'] = 3;
    $GLOBALS['twelve'] = $pageWidth / 12;
    $GLOBALS['sixth'] = $pageWidth / 6;
    // $GLOBALS['cuarter'] = $pageWidth / 3;
    $GLOBALS['cuarter'] = $pageWidth / 2;
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

    for ($_i = 1; $_i <= 2; $_i++) {
        if ($_i == 2) {
            foreach ($GLOBALSY as $key => $global_var) {
                $GLOBALSY[$key] = $global_var + $pageHeight / 2;
            }

            #Dashed lines
            $_dash_width = 3; #The width of the dash
            $_dashes = $pdf->getPageWidth() / $_dash_width; #How many dashes (including white spaces)
            $_dashesstarty = $pageHeight / 2; # Axis Y for dashes
            $_dashesstartx = 0; #Axis X for dashes

            for ($_n = 1; $_n <= $_dashes; $_n++) {
                if ($_n % 2 == 0) {
                    $pdf->Line($_dashesstartx, $_dashesstarty, $_dashesstartx + $_dash_width, $_dashesstarty);
                }
                $_dashesstartx += $_dash_width;
            }
        }

        $pdf->SetFillColor(137, 172, 118);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', 9);

        $pdf->Image('../img/imgMovimientoCajaChica.png', $GLOBALS['initialX'], $GLOBALSY['logoY'], $GLOBALS['logoW']);

        $pdf->setXY($GLOBALS['initialX'] + 25, $GLOBALSY['logoY']);
        $pdf->cell(100, 4, utf8_decode('OAXACA MIEL S.A. DE C.V.'), 0, 2, 'C', 0);
        $pdf->cell(100, 4, utf8_decode(' '), 0, 2, 'C', 0);
        $pdf->cell(100, 4, utf8_decode(''), 0, 2, 'C', 0);
        $pdf->cell(100, 4, utf8_decode(''), 0, 2, 'C', 0);
        $pdf->cell(100, 4, utf8_decode(''), 0, 2, 'C', 0);



        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 9);

        $pdf->setXY(($GLOBALS['sixth'] * 4) + $GLOBALS['initialX'], $GLOBALSY['documentTitleY']);
        $pdf->RoundedRect(($GLOBALS['sixth'] * 4) + $GLOBALS['initialX'], $GLOBALSY['documentTitleY'], $GLOBALS['sixth'] * 2, $GLOBALS['rowHeight'] * 1.5, 2, '1234', 'DF');
        $pdf->Cell($GLOBALS['sixth'] * 2, $GLOBALS['rowHeight'] * 1.5, utf8_decode($tipoDeReporte), 0, 0, 'C', 0);
        $pdf->Ln($GLOBALS['rowHeight'] / 2);

        $pdf->setXY(($GLOBALS['sixth'] * 4) + $GLOBALS['initialX'], $GLOBALSY['dateRowY']);

        $pdf->RoundedRect(($GLOBALS['sixth'] * 4) + $GLOBALS['initialX'], $GLOBALSY['dateRowY'], $GLOBALS['sixth'], $GLOBALS['rowHeight'], 2, '12', 'DF');
        $pdf->Cell($GLOBALS['sixth'], $GLOBALS['rowHeight'], utf8_decode('FOLIO'), 0, 0, 'C', 0);

        $pdf->RoundedRect($GLOBALS['initialX'] + ($GLOBALS['sixth'] * 5), $GLOBALSY['dateRowY'], $GLOBALS['sixth'], $GLOBALS['rowHeight'], 2, '12', 'DF');
        $pdf->Cell($GLOBALS['sixth'], $GLOBALS['rowHeight'], utf8_decode('ENTREGA'), 0, 2, 'C', 0);

        // #FECHA DE ENTREGA
        // $pdf->setFont('Arial', '', 8);
        // $pdf->SetTextColor(0, 0, 0);

        // $x = $GLOBALS['initialX'];
        // $y = $GLOBALSY['nameRowY'];
        // $pdf->setXY($x, $y);

        // $pdf->cell(20, $GLOBALS['rowHeight'], utf8_decode('ENTREGA: '), 0, 0, '', 0);
        // $pdf->setFont('Arial', 'B', 8);
        // $pdf->cell(100, $GLOBALS['rowHeight'], utf8_decode(strtoupper($datos['encabezado']['fechaEntrega'])), 0, 0, 'L', 0);
        // #FINALIZA FECHA DE ENTREGA

        #NOMBRE DE QUIEN RECIBE
        $pdf->setFont('Arial', '', 8);
        $pdf->SetTextColor(0, 0, 0);

        $x = $GLOBALS['initialX'];
        $y = $GLOBALSY['nameRowY'];
        $pdf->setXY($x, $y + 3);

        $pdf->cell(13, $GLOBALS['rowHeight'], utf8_decode('RECIBE: '), 0, 0, '', 0);
        $pdf->setFont('Arial', 'B', 8);
        $pdf->cell(100, $GLOBALS['rowHeight'], utf8_decode(strtoupper($datos['encabezado']['idRecibe'])), 0, 0, 'L', 0);
        #FINALIZA NOMBRE DE QUIEN RECIBE

        #NOMBRE DEL BANCO
        $pdf->setFont('Arial', '', 8);
        $pdf->SetTextColor(0, 0, 0);

        $x = $GLOBALS['initialX'];
        $y = $GLOBALSY['nameRowY'];
        $pdf->setXY($x, $y + 6);

        $pdf->cell(13, $GLOBALS['rowHeight'], utf8_decode('BANCO: '), 0, 0, '', 0);
        $pdf->setFont('Arial', 'B', 8);
        $pdf->cell(100, $GLOBALS['rowHeight'], utf8_decode(strtoupper($datos['encabezado']['banco'])), 0, 0, 'L', 0);
        #FINALIZA NOMBRE DEL BANCO

        $pdf->SetFillColor(255, 229, 88);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', 9);

        $x = $GLOBALS['initialX'];
        $y = $GLOBALSY['dataRowY'];
        $pdf->setXY($x, $y + 4);

        $pdf->cell($GLOBALS['cuarter'], $GLOBALS['rowHeight'], utf8_decode('Folio del cheque'), 1, 0, 'C', 1);
        // $pdf->cell($GLOBALS['cuarter'], $GLOBALS['rowHeight'], utf8_decode('Beneficiario'), 1, 0, 'C', 1);
        $pdf->cell($GLOBALS['cuarter'], $GLOBALS['rowHeight'], utf8_decode('Importe'), 1, 0, 'C', 1);
        // $pdf->cell($GLOBALS['sixth'], $GLOBALS['rowHeight'], utf8_decode('Precio Unitario'), 1, 0, 'C', 1);
        // $pdf->cell($GLOBALS['sixth'], $GLOBALS['rowHeight'], utf8_decode('Importe'), 1, 0, 'C', 1);

        $x = $GLOBALS['initialX'];
        $y = $GLOBALSY['dataRowY'] + $GLOBALS['rowHeight'];
        $pdf->setXY($x, $y + 4);

        $pdf->cell($GLOBALS['cuarter'], $GLOBALS['rowHeight'] * 9, '', 1, 0, 'L', 0);
        // $pdf->cell($GLOBALS['cuarter'], $GLOBALS['rowHeight'] * 9, '', 1, 0, 'L', 0);
        $pdf->cell($GLOBALS['cuarter'], $GLOBALS['rowHeight'] * 9, '', 1, 0, 'L', 0);
        // $pdf->cell($GLOBALS['sixth'], $GLOBALS['rowHeight'] * 9, '', 1, 0, 'L', 0);
        // $pdf->cell($GLOBALS['sixth'], $GLOBALS['rowHeight'] * 9, '', 1, 0, 'L', 0);

        // #INPUTDATA

        $pdf->Ln(25);

        // $pdf->Line($GLOBALS['initialX'] + 20, $GLOBALSY['signRowY'] + 20, $GLOBALS['initialX'] + ($GLOBALS['sixth'] * 3 - 20), $GLOBALSY['signRowY'] + 20);
        // $pdf->setXY($GLOBALS['initialX'], $GLOBALSY['signRowY'] + 20);
        // $pdf->cell($GLOBALS['middle'], $GLOBALS['rowHeight'], utf8_decode("Entregado por: "), 0, 2, 'C', 0);

        $pdf->Line($GLOBALS['initialX'] + $GLOBALS['middle'] + 25, $GLOBALSY['signRowY'] + 25, $GLOBALS['initialX'] + ($GLOBALS['sixth'] * 3 - 20), $GLOBALSY['signRowY'] + 25);
        $pdf->setXY($GLOBALS['initialX'] + $GLOBALS['middle'], $GLOBALSY['signRowY'] + 25);
        $pdf->cell($GLOBALS['initialX'], $GLOBALS['rowHeight'], utf8_decode("Recibido"), 0, 2, 'C', 0);

        $pdf->setFont('Arial', '', 8);

        $y = $GLOBALSY['dateRowY'] + $GLOBALS['rowHeight'];
        $pdf->setXY($x + ($GLOBALS['sixth'] * 4), $y);
        $pdf->RoundedRect($x + ($GLOBALS['sixth'] * 4), $y, $GLOBALS['sixth'], $GLOBALS['rowHeight'], 2, '34', '');
        $pdf->Cell($GLOBALS['sixth'], $GLOBALS['rowHeight'], utf8_decode($datos['encabezado']['idEncabezado']), 0, 0, 'C', 0);

        $pdf->RoundedRect($x + ($GLOBALS['sixth'] * 5), $y, $GLOBALS['sixth'], $GLOBALS['rowHeight'], 2, '34', '');
        $pdf->Cell($GLOBALS['sixth'], $GLOBALS['rowHeight'], utf8_decode($datos['encabezado']['fechaEntrega']), 0, 0, 'C', 0);


        $x = $GLOBALS['initialX'];
        $y = $GLOBALSY['dataRowY'] + $GLOBALS['rowHeight'] + 5; // Más 1 para separar del encabezado
        $pdf->SetFont('Arial', '', 7);
        $pdf->setXY($x, $y);

        foreach ($datos['detalle'] as $detalle) {
            $_info = array(
                'folioCheque' => $detalle['folioCheque'],
                'beneficiario' => $detalle['nombre'],
                'importe' => $detalle['importe']
            );

            $size = addLineIngreso($y, $_info, $pdf, $GLOBALS);
            $y += $size + 1;
        };


        $x = $GLOBALS['initialX'] + ($GLOBALS['cuarter'] * 3) + $GLOBALS['twelve'];
        $y = $GLOBALSY['dataRowY'] + $GLOBALS['rowHeight'] * 10;
        $pdf->setXY($x, $y);
        // $pdf->cell($GLOBALS['sixth'], $GLOBALS['rowHeight'], utf8_decode('$'), 1, 0, 'R', 0);
    }

    $pdf->Output();
}

if (isset($_GET['idEncabezado'])) {
    outputPdf($_GET['idEncabezado']);
} else {
    exit();
}
