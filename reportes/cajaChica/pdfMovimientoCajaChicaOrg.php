<?php

require('../../fpdf/FPDF/fpdf.php');
include_once '../../DAOConeccion/conePDO.php';
include_once '../numberToText.php';
include_once '../../controlAdministrativo/php/nombreDePersona.php';

$pdo = new conePDO();
$con = $pdo->conectar();

function findConcept($dbh, $idSubcuenta)
{
    $concepto = '';
    $seleccionaConcepto = $dbh->prepare("SELECT subcuenta FROM subcuentas WHERE idSubcuenta = $idSubcuenta");
    $seleccionaConcepto->execute();
    $seleccionaConcepto->bindColumn('subcuenta', $concepto);
    $seleccionaConcepto->fetch(PDO::FETCH_BOUND);
    return $concepto;
};

function getInfo($dbh, $idCajaChica)
{
    
    $resultado = array();
    try {
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $consulta = $dbh->prepare("SELECT * FROM cajachica WHERE idCajaChica = :idCajaChica");
        $consulta->bindParam(':idCajaChica', $idCajaChica);
        $consulta->execute();
        if ($consulta->rowCount() < 1) {
            return [];
        }
        
        $resultado['encabezado'] = $consulta->fetch(PDO::FETCH_ASSOC);
        $resultado['encabezado']['tipoLg'] = $resultado['encabezado']['tipo'] == 0 ? 'COMPROBANTE DE INGRESO' : 'COMPROBANTE DE EGRESO';
        
        $resultado['encabezado']['nombre'] = retornarNombre($dbh, $resultado['encabezado']['tipoDeCliente'], $resultado['encabezado']['nombre']);
        $detalle = $dbh->prepare("SELECT ccd.*, b.banco, cb.numDeCuenta FROM cajachicadetalle ccd
                                    LEFT JOIN bancos b ON ccd.idBanco = b.idBanco
                                    LEFT JOIN cuentasbancarias cb ON ccd.idCuenta = cb.idCuenta
                                    WHERE idCajaChica = :idCajaChica");
        $detalle->bindParam(':idCajaChica', $idCajaChica);
        $detalle->execute();

        $resultado['detallePre'] = $detalle->fetchAll(PDO::FETCH_ASSOC);
        $resultado['detalle'] = array();

        foreach ($resultado['detallePre'] as $detallePre) {
            if (is_numeric($detallePre['concepto'])) {
                $detallePre['concepto'] = findConcept($dbh, $detallePre['concepto']);
            } else {
                $detallePre['concepto'] = $detallePre['concepto'];
            }

            if ($resultado['encabezado']['tipo'] == '0') {
                $detallePre['total'] = $detallePre['importe'];
            } elseif ($resultado['encabezado']['tipo'] == '1') {
                $detallePre['total'] = $detallePre['cantidad'];
            }

            array_push($resultado['detalle'], $detallePre);
        }

        return $resultado;
    } catch (Exception $e) {
        return ['error'=>$e->getMessage()];
        exit();
    }
};


function addLineIngreso($y, $_info, $pdf, $GLOBALES)
{
    $heigthPerRow = 0;

    $pdf->MultiCell($GLOBALES['cuarter'], $GLOBALES['littleRow'], utf8_decode($_info['producto']), 0, 'L', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($GLOBALES['initialX'] + $GLOBALES['cuarter'], $y);

    $pdf->MultiCell($GLOBALES['cuarter'], $GLOBALES['littleRow'], utf8_decode($_info['descripcion']), 0, 'L', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($GLOBALES['initialX'] + $GLOBALES['cuarter'] * 2, $y);

    $pdf->MultiCell($GLOBALES['sixth'], $GLOBALES['littleRow'], utf8_decode($_info['cantidad']), 0, 'R', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($GLOBALES['initialX'] + ($GLOBALES['sixth'] * 4), $y);

    $pdf->MultiCell($GLOBALES['sixth'], $GLOBALES['littleRow'], utf8_decode('$' . number_format($_info['precio'], 2, '.', ',')), 0, 'R', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($GLOBALES['initialX'] + $GLOBALES['sixth'] * 5, $y);

    $pdf->MultiCell($GLOBALES['sixth'], $GLOBALES['littleRow'], utf8_decode('$' . number_format($_info['importe'], 2, ',', ',')), 0, 'R', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    
    return $heigthPerRow;
};


function addLineEgreso($y, $_info, $pdf, $GLOBALES)
{
    $heigthPerRow = 0;

    $pdf->MultiCell($GLOBALES['middle'], $GLOBALES['littleRow'], utf8_decode($_info['descripcion']), 0, 'L', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($GLOBALES['initialX'] + $GLOBALES['middle'], $y);

    $pdf->MultiCell($GLOBALES['sixth'], $GLOBALES['littleRow'], utf8_decode($_info['concepto']), 0, 'R', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($GLOBALES['initialX'] + ($GLOBALES['sixth'] * 4), $y);

    $pdf->MultiCell($GLOBALES['sixth'], $GLOBALES['littleRow'], utf8_decode($_info['movimiento']), 0, 'R', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($GLOBALES['initialX'] + $GLOBALES['sixth'] * 5, $y);

    $pdf->MultiCell($GLOBALES['sixth'], $GLOBALES['littleRow'], utf8_decode('$' . number_format($_info['importe'], 2, '.', ',')), 0, 'R', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    
    return $heigthPerRow;
};


function outputPdf($con, $idCajaChica)
{
    $datos = getInfo($con, $idCajaChica);
    $pdf = new FPDF('P', 'mm', 'A4');
    $pdf->AddPage();

    if (!isset($datos['encabezado']['idCajaChica'])) {
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->cell(500, 5, utf8_decode('No se ha podido traer la información'), 0, 2, 'L', 0);
        $pdf->cell(500, 5, utf8_decode($datos['error']), 0, 2, 'L', 0);
        $pdf->Output();
        exit();
    }

    $pdf->SetAutoPageBreak(false);
    $pdf->SetCreator('');
    $pageWidth = 190;
    $pageHeight = $pdf->getPageHeight();

    $GLOBALS = [
        'littleRow'=>3,
        'twelve' => $pageWidth/12,
        'sixth' => $pageWidth/6,
        'cuarter' => $pageWidth/4,
        'middle' => $pageWidth/2,
        'rowHeight' => 5,
        'initialX' => 10,
        'logoW'=>22

    ];

    $GLOBALSY = [
        'initialY' => 25,
        'nameRowY' => 37,
        'dataRowY' => 45,
        'dateRowY' => 20,
        'signRowY'=>$pageHeight / 2 - 40,
        'logoY' => 10,
        'documentTitleY'=>10,
        'totalY'=> 45 + $GLOBALS['rowHeight']*10
    ];

    for ($_i = 1; $_i <=2; $_i ++) {
        if ($_i == 2) {
            foreach ($GLOBALSY as $key => $global_var) {
                $GLOBALSY[$key] = $global_var + $pageHeight / 2;
            }

            #Dashed lines
            $_dash_width = 3; #The width of the dash
            $_dashes = $pdf->getPageWidth() / $_dash_width; #How many dashes (including white spaces)
            $_dashesstarty = $pageHeight / 2; # Axis Y for dashes
            $_dashesstartx = 0; #Axis X for dashes

            for ($_n = 1; $_n <= $_dashes; $_n ++) {
                if ($_n % 2 == 0) {
                    $pdf->Line($_dashesstartx, $_dashesstarty, $_dashesstartx + $_dash_width, $_dashesstarty );
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
        $pdf->Cell($GLOBALS['sixth'] * 2, $GLOBALS['rowHeight'] * 1.5, utf8_decode($datos['encabezado']['tipoLg']), 0, 0, 'C', 0);
        $pdf->Ln($GLOBALS['rowHeight'] / 2);

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
    
        $pdf->cell(25, $GLOBALS['rowHeight'], utf8_decode('A NOMBRE DE: '), 0, 0, '', 0);
        $pdf->setFont('Arial', 'B', 8);
        $pdf->cell(100, $GLOBALS['rowHeight'], utf8_decode(strtoupper($datos['encabezado']['nombre'])), 0, 0, 'L', 0);
    
        #FINALIZA NOMBRE DE PERSONA


        $pdf->SetFillColor(255, 229, 88);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', 9);
    
        $x = $GLOBALS['initialX'];
        $y = $GLOBALSY['dataRowY'];
        $pdf->setXY($x, $y);
    
        if ($datos['encabezado']['tipo'] == 0) {
            $pdf->cell($GLOBALS['cuarter'], $GLOBALS['rowHeight'], utf8_decode('PRODUCTO'), 1, 0, 'C', 1);
            $pdf->cell($GLOBALS['cuarter'], $GLOBALS['rowHeight'], utf8_decode('DESCRIPCIÓN'), 1, 0, 'C', 1);
            $pdf->cell($GLOBALS['sixth'], $GLOBALS['rowHeight'], utf8_decode('CANTIDAD'), 1, 0, 'C', 1);
            $pdf->cell($GLOBALS['sixth'], $GLOBALS['rowHeight'], utf8_decode('PRECIO'), 1, 0, 'C', 1);
            $pdf->cell($GLOBALS['sixth'], $GLOBALS['rowHeight'], utf8_decode('IMPORTE'), 1, 0, 'C', 1);
    
            $x = $GLOBALS['initialX'];
            $y = $GLOBALSY['dataRowY'] + $GLOBALS['rowHeight'];
            $pdf->setXY($x, $y);
        
            $pdf->cell($GLOBALS['cuarter'], $GLOBALS['rowHeight'] * 9, '', 1, 0, 'L', 0);
            $pdf->cell($GLOBALS['cuarter'], $GLOBALS['rowHeight'] * 9, '', 1, 0, 'L', 0);
            $pdf->cell($GLOBALS['sixth'], $GLOBALS['rowHeight'] * 9, '', 1, 0, 'L', 0);
            $pdf->cell($GLOBALS['sixth'], $GLOBALS['rowHeight'] * 9, '', 1, 0, 'L', 0);
            $pdf->cell($GLOBALS['sixth'], $GLOBALS['rowHeight'] * 9, '', 1, 0, 'L', 0);
        } elseif ($datos['encabezado']['tipo'] == 1) {
            $pdf->cell($GLOBALS['middle'], $GLOBALS['rowHeight'], utf8_decode('DESCRIPCIÓN'), 1, 0, 'C', 1);
            $pdf->cell($GLOBALS['sixth'], $GLOBALS['rowHeight'], utf8_decode('CONCEPTO'), 1, 0, 'C', 1);
            $pdf->cell($GLOBALS['sixth'], $GLOBALS['rowHeight'], utf8_decode('MOVIMIENTO'), 1, 0, 'C', 1);
            $pdf->cell($GLOBALS['sixth'], $GLOBALS['rowHeight'], utf8_decode('IMPORTE'), 1, 0, 'C', 1);
    
            $x = $GLOBALS['initialX'];
            $y = $GLOBALSY['dataRowY'] + $GLOBALS['rowHeight'];
            $pdf->setXY($x, $y);
        
            $pdf->cell($GLOBALS['middle'], $GLOBALS['rowHeight'] * 9, '', 1, 0, 'L', 0);
            $pdf->cell($GLOBALS['sixth'], $GLOBALS['rowHeight'] * 9, '', 1, 0, 'L', 0);
            $pdf->cell($GLOBALS['sixth'], $GLOBALS['rowHeight'] * 9, '', 1, 0, 'L', 0);
            $pdf->cell($GLOBALS['sixth'], $GLOBALS['rowHeight'] * 9, '', 1, 0, 'L', 0);
        }
       
        
        // #INPUTDATA
    
        $pdf->setFont('Arial', 'B', 8);
        $pdf->SetFillColor(137, 172, 118);
        $pdf->SetTextColor(255, 255, 255);

        $pdf->setXY($GLOBALS['initialX'], $GLOBALSY['signRowY']);
        $pdf->RoundedRect($GLOBALS['initialX'], $GLOBALSY['signRowY'], $GLOBALS['middle'], $GLOBALS['rowHeight'], 2, '12', 'DF');
        $pdf->Cell($GLOBALS['middle'], $GLOBALS['rowHeight'], utf8_decode('TOTAL EN LETRA'), 0, 0, 'C', 0);

        $pdf->setFont('Arial', '', 7);
        $pdf->SetTextColor(0, 0, 0);

        $pdf->setXY($GLOBALS['initialX'], $GLOBALSY['signRowY'] + $GLOBALS['rowHeight']);
        $pdf->RoundedRect($GLOBALS['initialX'], $GLOBALSY['signRowY'] + $GLOBALS['rowHeight'], $GLOBALS['middle'], $GLOBALS['rowHeight'], 2, '34', '');
        $pdf->Cell($GLOBALS['middle'], $GLOBALS['rowHeight'], utf8_decode(convertir($datos['encabezado']['total']) . ' M.N.'), 0, 0, 'C', 0);

        $pdf->setFont('Arial', '', 8);

        $pdf->setXY($GLOBALS['initialX'], $GLOBALSY['signRowY'] + 20);
        $pdf->MultiCell($GLOBALS['middle'], $GLOBALS['rowHeight'], utf8_decode('En caso de requerir factura tiene 1 semana posterior a la impresión para solicitarla'), 0, 'L', 0);
        $pdf->setXY($GLOBALS['initialX'] + $GLOBALS['middle'], $GLOBALSY['signRowY']);
        if ($datos['encabezado']['tipo'] == 0) {
            $pdf->cell($GLOBALS['middle'], $GLOBALS['rowHeight'], utf8_decode("FIRMA DE CONFORMIDAD CLIENTE"), 0, 2, 'C', 0);
        } elseif ($datos['encabezado']['tipo'] == 1) {
            $pdf->cell($GLOBALS['middle'], $GLOBALS['rowHeight'], utf8_decode("RECIBO CANTIDAD PREVIAMENTE SOLICITADA"), 0, 2, 'C', 0);
        }
        $pdf->Ln(25);
        $pdf->Line($GLOBALS['initialX'] + $GLOBALS['middle'] + 20, $GLOBALSY['signRowY'] + 25, $GLOBALS['initialX'] + ($GLOBALS['sixth'] * 6 - 20), $GLOBALSY['signRowY'] + 25);
        $pdf->setXY($GLOBALS['initialX'] + $GLOBALS['middle'], $GLOBALSY['signRowY'] + 25);
        $pdf->cell($GLOBALS['middle'], $GLOBALS['rowHeight'], utf8_decode("Firma de recibido"), 0, 2, 'C', 0);
        $pdf->setFont('Arial', 'B', 8);
        $pdf->cell($GLOBALS['middle'], $GLOBALS['rowHeight'], utf8_decode(strtoupper($datos['encabezado']['nombre'])), 0, 2, 'C', 0);
        $pdf->setFont('Arial', '', 8);
    
        $y = $GLOBALSY['dateRowY'] + $GLOBALS['rowHeight'];
        $pdf->setXY($x + ($GLOBALS['sixth']*4), $y);
        $pdf->RoundedRect($x + ($GLOBALS['sixth']*4), $y, $GLOBALS['sixth'], $GLOBALS['rowHeight'], 2, '34', '');
        $pdf->Cell($GLOBALS['sixth'], $GLOBALS['rowHeight'], utf8_decode($datos['encabezado']['idCajaChica']), 0, 0, 'C', 0);
    
        $pdf->RoundedRect($x + ($GLOBALS['sixth']*5), $y, $GLOBALS['sixth'], $GLOBALS['rowHeight'], 2, '34', '');
        $pdf->Cell($GLOBALS['sixth'], $GLOBALS['rowHeight'], utf8_decode($datos['encabezado']['fecha']), 0, 0, 'C', 0);
    
    
        $x = $GLOBALS['initialX'];
        $y = $GLOBALSY['dataRowY'] + $GLOBALS['rowHeight'] + 1; // Más 1 para separar del encabezado
        $pdf->SetFont('Arial', '', 
        7);
        $pdf->setXY($x, $y);
    
        if ($datos['encabezado']['tipo'] == 0) {
            foreach ($datos['detalle'] as $detalle) {
                $_info = array(
                    'producto' => $detalle['concepto'],
                    'descripcion' => $detalle['descripcion'],
                    'cantidad' => $detalle['kg'],
                    'precio' => $detalle['precio'],
                    'importe' => $detalle['importe']
                );
            
                $size = addLineIngreso($y, $_info, $pdf, $GLOBALS);
                $y += $size + 1;
            };
        } elseif ($datos['encabezado']['tipo'] == 1) {
            foreach ($datos['detalle'] as $detalle) {
                $_info = array(
                    'descripcion' => $detalle['descripcion'],
                    'concepto' => $detalle['concepto'],
                    'movimiento' => $detalle['movimiento'],
                    'importe' => $detalle['cantidad']
                );
            
                $size = addLineEgreso($y, $_info, $pdf, $GLOBALS);
                $y += $size + 1;
            };
        }
        
        $x = $GLOBALS['initialX'] + ($GLOBALS['cuarter'] * 3) + $GLOBALS['twelve'];
        $y = $GLOBALSY['dataRowY'] +  $GLOBALS['rowHeight'] * 10;
        $pdf->setXY($x, $y);
        $pdf->cell($GLOBALS['sixth'], $GLOBALS['rowHeight'], utf8_decode('$' . number_format($datos['encabezado']['total'], 2, '.', ',')), 1, 0, 'R', 0);
    }

    $pdf->Output();
}

if (isset($_GET['idCajaChica'])) {
    outputPdf($con, $_GET['idCajaChica']);
} else {
    exit();
}
