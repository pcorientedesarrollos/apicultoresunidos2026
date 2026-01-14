<?php

require('../../fpdf/FPDF/fpdf.php');
include_once '../../DAOConeccion/conePDO.php';
include_once '../../controlAdministrativo/php/nombreDePersona.php';

$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function getInfo($idSalidaEnvases)
{
    global $con;
    $resultado = array();
    try {
        // Consulta el encabeado
        $consultaEncabezado = $con->prepare("SELECT m.idSalidaEnvases, m.fecha, m.cantidadTotal, m.importeTotal, m.idProveedor, m.tipoCliente
        FROM envasesfrascosencabezadosalidas m
        WHERE idSalidaEnvases = '$idSalidaEnvases'");
        $consultaEncabezado->execute();
        if ($consultaEncabezado == false) {
            throw new Exception($con->errorInfo());
        } else if ($consultaEncabezado->rowCount() == 0) {
            throw new Exception('El reporte no existe');
        } else {

            $resultado['encabezado'] = $consultaEncabezado->fetch(PDO::FETCH_ASSOC);
            $resultado['encabezado']['nombre'] = retornarNombre($con, $resultado['encabezado']['tipoCliente'], $resultado['encabezado']['idProveedor']);
        }
        $detalle = $con->prepare("SELECT * FROM envasesfrascosdetallesalidas WHERE idSalidaEnvases = :idSalidaEnvases");
        $detalle->bindParam(':idSalidaEnvases', $idSalidaEnvases);
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

    $pdf->MultiCell($GLOBALES['cuarter'], $GLOBALES['littleRow'], utf8_decode($_info['tipo']), 0, 'L', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($GLOBALES['initialX'] + ($GLOBALES['sixth']), $y);

    $pdf->MultiCell($GLOBALES['sixth'], $GLOBALES['littleRow'], utf8_decode($_info['cantidad']), 0, 'R', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($GLOBALES['initialX'] + $GLOBALES['cuarter'] + $GLOBALES['sixth'], $y);

    $pdf->MultiCell($GLOBALES['cuarter'], $GLOBALES['littleRow'], utf8_decode($_info['descripcion']), 0, 'L', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($GLOBALES['initialX'] + $GLOBALES['cuarter'] * 2 + $GLOBALES['sixth'], $y);


    $pdf->MultiCell($GLOBALES['sixth'], $GLOBALES['littleRow'], utf8_decode('$' . number_format($_info['precioUnitario'], 2, '.', ',')), 0, 'R', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($GLOBALES['initialX'] + $GLOBALES['sixth'] * 2 + $GLOBALES['cuarter'] * 2, $y);

    $pdf->MultiCell($GLOBALES['sixth'], $GLOBALES['littleRow'], utf8_decode('$' . number_format($_info['importe'], 2, ',', ',')), 0, 'R', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;

    return $heigthPerRow;
};

function outputPdf($idSalidaEnvases)
{
    global $con;
    $datos = getInfo($idSalidaEnvases);
    // echo json_encode($datos);
    $pdf = new FPDF('P', 'mm', 'A4');
    $pdf->AddPage();

    if (!isset($datos['encabezado']['idSalidaEnvases'])) {
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->cell(500, 5, utf8_decode('No se ha podido traer la información'), 0, 1, 'L', 0);
        $pdf->Multicell(190, 5, utf8_decode($datos['message']));
        $pdf->Output();
        exit();
    }

    $tipoDeReporte = 'SALIDA DE ENVASES Y FRASCOS';

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
        $pdf->Cell($GLOBALS['sixth'] * 2, $GLOBALS['rowHeight'] * 1.5, utf8_decode($tipoDeReporte), 0, 2, 'C', 0);
        // $pdf->Ln($GLOBALS['rowHeight'] / 2);

        $pdf->Cell($GLOBALS['sixth'] * 2 , $GLOBALS['rowHeight'] * .2, utf8_decode('CÓDIGO:  - REVISIÓN: '), 0, 0, 'C', 0);
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

        $pdf->cell(25, $GLOBALS['rowHeight'], utf8_decode('PROVEEDOR: '), 0, 0, '', 0);
        $pdf->setFont('Arial', 'B', 8);
        $pdf->cell(100, $GLOBALS['rowHeight'], utf8_decode(strtoupper($datos['encabezado']['nombre'])), 0, 0, 'L', 0);
    
        #FINALIZA NOMBRE DE PERSONA

        $pdf->setFont('Arial', '', 8);
        $pdf->SetTextColor(0, 0, 0);

        $x = $GLOBALS['initialX'];
        $y = $GLOBALSY['nameRowY'];
        $pdf->setXY($x, $y + 3);

        // $pdf->cell(25, $GLOBALS['rowHeight'], utf8_decode('MOTIVO: '), 0, 0, '', 0);
        // $pdf->setFont('Arial', 'B', 8);
        // $pdf->cell(100, $GLOBALS['rowHeight'], utf8_decode(strtoupper($datos['encabezado']['condicionSalida'])), 0, 0, 'L', 0);
    

        $pdf->SetFillColor(255, 229, 88);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', 9);

        $x = $GLOBALS['initialX'];
        $y = $GLOBALSY['dataRowY'];
        $pdf->setXY($x, $y);

        $pdf->cell($GLOBALS['cuarter'], $GLOBALS['rowHeight'], utf8_decode('Tipo'), 1, 0, 'C', 1);
        $pdf->cell($GLOBALS['sixth'], $GLOBALS['rowHeight'], utf8_decode('Cantidad'), 1, 0, 'C', 1);
        $pdf->cell($GLOBALS['cuarter'], $GLOBALS['rowHeight'], utf8_decode('Descripcion'), 1, 0, 'C', 1);
        $pdf->cell($GLOBALS['sixth'], $GLOBALS['rowHeight'], utf8_decode('Precio Unitario'), 1, 0, 'C', 1);
        $pdf->cell($GLOBALS['sixth'], $GLOBALS['rowHeight'], utf8_decode('Importe'), 1, 0, 'C', 1);

        $x = $GLOBALS['initialX'];
        $y = $GLOBALSY['dataRowY'] + $GLOBALS['rowHeight'];
        $pdf->setXY($x, $y);

        $pdf->cell($GLOBALS['cuarter'], $GLOBALS['rowHeight'] * 9, '', 1, 0, 'L', 0);
        $pdf->cell($GLOBALS['sixth'], $GLOBALS['rowHeight'] * 9, '', 1, 0, 'L', 0);
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
        $pdf->Cell($GLOBALS['sixth'], $GLOBALS['rowHeight'], utf8_decode($datos['encabezado']['idSalidaEnvases']), 0, 0, 'C', 0);

        $pdf->RoundedRect($x + ($GLOBALS['sixth'] * 5), $y, $GLOBALS['sixth'], $GLOBALS['rowHeight'], 2, '34', '');
        $pdf->Cell($GLOBALS['sixth'], $GLOBALS['rowHeight'], utf8_decode($datos['encabezado']['fecha']), 0, 0, 'C', 0);


        $x = $GLOBALS['initialX'];
        $y = $GLOBALSY['dataRowY'] + $GLOBALS['rowHeight'] + 1; // Más 1 para separar del encabezado
        $pdf->SetFont('Arial', '', 7);
        $pdf->setXY($x, $y);

        foreach ($datos['detalle'] as $detalle) {
            $_info = array(
                'tipo' => $detalle['subcuenta'] . ' - ' . $detalle['concepto'],
                'cantidad' => $detalle['cantidad'],
                'descripcion' => $detalle['descripcion'],
                'precioUnitario' => $detalle['precioUnitario'],
                'importe' => $detalle['importe']
            );

            $size = addLineIngreso($y, $_info, $pdf, $GLOBALS);
            $y += $size + 1;
        };


        $x = $GLOBALS['initialX'] + ($GLOBALS['cuarter'] * 3) + $GLOBALS['twelve'];
        $y = $GLOBALSY['dataRowY'] + $GLOBALS['rowHeight'] * 10;
        $pdf->setXY($x, $y);
        $pdf->cell($GLOBALS['sixth'], $GLOBALS['rowHeight'], utf8_decode('$' . number_format($datos['encabezado']['importeTotal'], 2, '.', ',')), 1, 0, 'R', 0);
    }

    $pdf->Output();
}

if (isset($_GET['idSalidaEnvases'])) {
    outputPdf($_GET['idSalidaEnvases']);
} else {
    exit();
}
