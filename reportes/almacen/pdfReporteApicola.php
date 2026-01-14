<?php

require('../../fpdf/FPDF/fpdf.php');
include_once '../../DAOConeccion/conePDO.php';
include_once '../../controlAdministrativo/php/nombreDePersona.php';

$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function getInfo($idAlmacen)
{
    global $con;
    $resultado = array();
    try {
        // Consulta el encabeado
        $consultaEncabezado = $con->prepare("SELECT CONCAT('AL-PAP-',al.idAlmacen) as folio, al.fecha, al.total, al.tipoPersona, al.idProveedor, al.tipo
        FROM almacenencabezadoapicola al
        WHERE al.idAlmacen = :idAlmacen");
        $consultaEncabezado->bindParam(':idAlmacen', $idAlmacen);
        $consultaEncabezado->execute();
        if ($consultaEncabezado == false) {
            throw new Exception($con->errorInfo());
        } else if ($consultaEncabezado->rowCount() == 0) {
            throw new Exception('El reporte no existe');
        } else {
            $resultado['encabezado'] = $consultaEncabezado->fetch(PDO::FETCH_ASSOC);
            $resultado['encabezado']['nombre'] = retornarNombre($con, $resultado['encabezado']['tipoPersona'], $resultado['encabezado']['idProveedor']);
        }
        // Consulta el detalle
        $detalle = $con->prepare("SELECT ac.cantidad, ac.descripcion, ac.costoUnitario, ac.importe, CONCAT(ac.subcuenta, ' - ' , ac.concepto) AS concepto 
        FROM almacenapicola ac
        WHERE idAlmacenEncabezado = :idAlmacen");
        $detalle->bindParam(':idAlmacen', $idAlmacen);
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

    $heigthPerRow = 0;

    $pdf->MultiCell($GLOBALES['sixth'], $GLOBALES['littleRow'], utf8_decode($_info['cantidad']), 0, 'R', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($GLOBALES['initialX'] + ($GLOBALES['sixth']), $y);

    $pdf->MultiCell($GLOBALES['cuarter'], $GLOBALES['littleRow'], utf8_decode($_info['unidad']), 0, 'L', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($GLOBALES['initialX'] + $GLOBALES['cuarter'] + $GLOBALES['sixth'], $y);

    $pdf->MultiCell($GLOBALES['cuarter'], $GLOBALES['littleRow'], utf8_decode($_info['descripcion']), 0, 'L', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($GLOBALES['initialX'] + $GLOBALES['cuarter'] * 2 + $GLOBALES['sixth'], $y);


    $pdf->MultiCell($GLOBALES['sixth'], $GLOBALES['littleRow'], utf8_decode('$' . number_format($_info['costoUnitario'], 2, '.', ',')), 0, 'R', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($GLOBALES['initialX'] + $GLOBALES['sixth'] * 2 + $GLOBALES['cuarter'] * 2, $y);

    $pdf->MultiCell($GLOBALES['sixth'], $GLOBALES['littleRow'], utf8_decode('$' . number_format($_info['importe'], 2, ',', ',')), 0, 'R', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;

    return $heigthPerRow;
};

function outputPdf($idAlmacen)
{
    global $con;
    $datos = getInfo($idAlmacen);
    $pdf = new FPDF('P', 'mm', 'A4');
    $pdf->AddPage();

    if (!isset($datos['encabezado']['folio'])) {
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->cell(500, 5, utf8_decode('No se ha podido traer la información'), 0, 2, 'L', 0);
        $pdf->cell(500, 5, utf8_decode($datos['message']), 0, 2, 'L', 0);
        $pdf->Output();
        exit();
    }

    $tipoDeReporte = $datos['encabezado']['tipo'] == '1' ? 'ENTRADA DE PRODUCTOS APÍCOLAS' : 'SALIDA DE PRODUCTOS APÍCOLAS';
    $codigos = $datos['encabezado']['tipo'] == '1' ? 'CÓDIGO: RAL-EPA-01   REVISIÓN: 01' : 'CÓDIGO: RAL-SPA-01   REVISIÓN: 01';

    $pdf->SetAutoPageBreak(false);
    $pageWidth = 190;
    $pageHeight = $pdf->getPageHeight();

    $GLOBALS['littleRow'] = 3;
    $GLOBALS['twelve'] = $pageWidth / 12;
    $GLOBALS['sixth'] = $pageWidth / 6;
    $GLOBALS['cuarter'] = $pageWidth / 4;
    $GLOBALS['middle'] = $pageWidth / 2;
    $GLOBALS['rowHeight'] = 5;
    $GLOBALS['initialX'] = 10;
    $GLOBALS['logoW'] = 22;

    $GLOBALSY['initialY'] = 25;
    $GLOBALSY['nameRowY'] = 37;
    $GLOBALSY['dataRowY'] = 45;
    $GLOBALSY['dateRowY'] = 20;
    $GLOBALSY['signRowY'] = $pageHeight / 2 - 40;
    $GLOBALSY['logoY'] = 10;
    $GLOBALSY['documentTitleY'] = 10;
    $GLOBALSY['totalY'] = 45 + $GLOBALS['rowHeight'] * 10;

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
        $pdf->RoundedRect(($GLOBALS['sixth'] * 4) + $GLOBALS['initialX'], $GLOBALSY['documentTitleY'], $GLOBALS['sixth'] * 2, $GLOBALS['rowHeight'] * 2, 2, '1234', 'DF');
        $pdf->Cell($GLOBALS['sixth'] * 2, $GLOBALS['rowHeight'] , utf8_decode($tipoDeReporte), 0, 2, 'C', 0);
        $pdf->Cell($GLOBALS['sixth'] * 2, $GLOBALS['rowHeight'] , utf8_decode($codigos), 0, 0, 'C', 0);

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

        $pdf->cell(25, $GLOBALS['rowHeight'], utf8_decode('PROVEEDOR: '), 0, 0, '', 0);
        $pdf->setFont('Arial', 'B', 8);
        $pdf->cell(100, $GLOBALS['rowHeight'], utf8_decode(strtoupper($datos['encabezado']['nombre'])), 0, 0, 'L', 0);

        #FINALIZA NOMBRE DE PERSONA

        $pdf->SetFillColor(255, 229, 88);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', 9);

        $x = $GLOBALS['initialX'];
        $y = $GLOBALSY['dataRowY'];
        $pdf->setXY($x, $y);

        $pdf->cell($GLOBALS['sixth'], $GLOBALS['rowHeight'], utf8_decode('CANTIDAD'), 1, 0, 'C', 1);
        $pdf->cell($GLOBALS['cuarter'], $GLOBALS['rowHeight'], utf8_decode('UNIDAD'), 1, 0, 'C', 1);
        $pdf->cell($GLOBALS['cuarter'], $GLOBALS['rowHeight'], utf8_decode('DESCRIPCIÓN'), 1, 0, 'C', 1);
        $pdf->cell($GLOBALS['sixth'], $GLOBALS['rowHeight'], utf8_decode('P.U.'), 1, 0, 'C', 1);
        $pdf->cell($GLOBALS['sixth'], $GLOBALS['rowHeight'], utf8_decode('IMPORTE'), 1, 0, 'C', 1);

        $x = $GLOBALS['initialX'];
        $y = $GLOBALSY['dataRowY'] + $GLOBALS['rowHeight'];
        $pdf->setXY($x, $y);

        $pdf->cell($GLOBALS['sixth'], $GLOBALS['rowHeight'] * 9, '', 1, 0, 'L', 0);
        $pdf->cell($GLOBALS['cuarter'], $GLOBALS['rowHeight'] * 9, '', 1, 0, 'L', 0);
        $pdf->cell($GLOBALS['cuarter'], $GLOBALS['rowHeight'] * 9, '', 1, 0, 'L', 0);
        $pdf->cell($GLOBALS['sixth'], $GLOBALS['rowHeight'] * 9, '', 1, 0, 'L', 0);
        $pdf->cell($GLOBALS['sixth'], $GLOBALS['rowHeight'] * 9, '', 1, 0, 'L', 0);

        // #INPUTDATA

        $pdf->Ln(25);

        $pdf->Line($GLOBALS['initialX'] + 20, $GLOBALSY['signRowY'] + 20, $GLOBALS['initialX'] + ($GLOBALS['sixth'] * 3 - 20), $GLOBALSY['signRowY'] + 20);
        $pdf->setXY($GLOBALS['initialX'], $GLOBALSY['signRowY'] + 20);
        $pdf->cell($GLOBALS['middle'], $GLOBALS['rowHeight'], utf8_decode("Entregado por: "), 0, 2, 'C', 0);

        $pdf->Line($GLOBALS['initialX'] + $GLOBALS['middle'] + 20, $GLOBALSY['signRowY'] + 20, $GLOBALS['initialX'] + ($GLOBALS['sixth'] * 6 - 20), $GLOBALSY['signRowY'] + 20);
        $pdf->setXY($GLOBALS['initialX'] + $GLOBALS['middle'], $GLOBALSY['signRowY'] + 20);
        $pdf->cell($GLOBALS['middle'], $GLOBALS['rowHeight'], utf8_decode("Recibido por: "), 0, 2, 'C', 0);

        $pdf->setFont('Arial', '', 8);

        $y = $GLOBALSY['dateRowY'] + $GLOBALS['rowHeight'];
        $pdf->setXY($x + ($GLOBALS['sixth'] * 4), $y);
        $pdf->RoundedRect($x + ($GLOBALS['sixth'] * 4), $y, $GLOBALS['sixth'], $GLOBALS['rowHeight'], 2, '34', '');
        $pdf->Cell($GLOBALS['sixth'], $GLOBALS['rowHeight'], utf8_decode($datos['encabezado']['folio']), 0, 0, 'C', 0);

        $pdf->RoundedRect($x + ($GLOBALS['sixth'] * 5), $y, $GLOBALS['sixth'], $GLOBALS['rowHeight'], 2, '34', '');
        $pdf->Cell($GLOBALS['sixth'], $GLOBALS['rowHeight'], utf8_decode($datos['encabezado']['fecha']), 0, 0, 'C', 0);


        $x = $GLOBALS['initialX'];
        $y = $GLOBALSY['dataRowY'] + $GLOBALS['rowHeight'] + 1; // Más 1 para separar del encabezado
        $pdf->SetFont('Arial', '', 7);
        $pdf->setXY($x, $y);

        foreach ($datos['detalle'] as $detalle) {
            $_info = array(
                'cantidad' => $detalle['cantidad'],
                'unidad' => $detalle['concepto'],
                'descripcion' => $detalle['descripcion'],
                'costoUnitario' => $detalle['costoUnitario'],
                'importe' => $detalle['importe']
            );

            $size = addLineIngreso($y, $_info, $pdf, $GLOBALS);
            $y += $size + 1;
        };

        $x = $GLOBALS['initialX'] + ($GLOBALS['cuarter'] * 3) + $GLOBALS['twelve'];
        $y = $GLOBALSY['dataRowY'] + $GLOBALS['rowHeight'] * 10;
        $pdf->setXY($x, $y);
        $pdf->cell($GLOBALS['sixth'], $GLOBALS['rowHeight'], utf8_decode('$' . number_format($datos['encabezado']['total'], 2, '.', ',')), 1, 0, 'R', 0);
    }

    $pdf->Output();
}

if (isset($_GET['idAlmacen'])) {
    outputPdf($_GET['idAlmacen']);
} else {
    exit();
}
