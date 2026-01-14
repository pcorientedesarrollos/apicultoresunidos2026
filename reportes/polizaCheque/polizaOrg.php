<?php

require('../../fpdf/FPDF/fpdf.php');
include_once '../../DAOConeccion/conePDO.php';
include_once '../numberToText.php';
include_once '../../controlAdministrativo/php/nombreDePersona.php';

$pdo = new conePDO();
$con = $pdo->conectar();

function getInfo($dbh, $idPoliza)
{
    $resultado = array();
    try {
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $consulta = $dbh->prepare("SELECT pc.idPolizaCheque, pc.fecha, pc.folioCheque, pc.tipoPersona, pc.persona, adb.descripcion,
        pc.persona2, pc.cantidad, pc.concepto, b.banco, cb.numDeCuenta, b.logotipo
        FROM polizacheque pc
        LEFT JOIN bancos b ON pc.idBanco = b.idBanco
        LEFT JOIN cuentasbancarias cb ON pc.idCuenta = cb.idCuenta
        LEFT JOIN relaciondemovimientos rdm ON pc.idPolizaCheque = rdm.poliza
        LEFT JOIN auxiliardebancos adb ON rdm.idMovimiento = adb.idAuxiliar
        WHERE pc.idPolizaCheque = :idPoliza");
        $consulta->bindParam(':idPoliza', $idPoliza);
        $consulta->execute();
        
        if ($consulta->rowCount() == 1) {
            $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
            $resultado['nombre'] = retornarNombre($dbh, $resultado['tipoPersona'], $resultado['persona']);
            $resultado['nombre2'] = $resultado['persona2'];
            return $resultado;
        } else {
            return [];
        }
    } catch (Exception $e) {
        return [];
        exit();
    }
};

function outputPdf($con, $idPoliza)
{
    $datos = getInfo($con, $idPoliza);

    $pdf = new FPDF('P', 'mm', 'A4');
    $pdf->AddPage();

    if (isset($datos['idPolizaCheque'])) {
        $pageWidth = 190;
        $pageHeight = $pdf->getPageHeight();
        $numeroDeFilas = 8;
    
        $GLOBALS['nameRowY'] = 40;
        $GLOBALS['dataRowY'] = 45;
        $GLOBALS['folioY'] = 50;
        $GLOBALS['seven'] = $pageWidth / 7;
        $GLOBALS['sixth'] = $pageWidth / 6;
        $GLOBALS['cuarter'] = $pageWidth / 4;
        $GLOBALS['middle'] = $pageWidth / 2;
        $GLOBALS['third'] = $pageWidth / 3;
        $GLOBALS['rowHeight'] = 5;
        $GLOBALS['initialX'] = 10;
        $GLOBALS['initialY'] = 10;
        $GLOBALS['logoWidth'] = 25;
    
        $GLOBALS['chequeAlto'] = 50;
        $GLOBALS['logoBancoAncho'] = 60;
        $GLOBALS['logoBancoAlto'] = 10;
        $GLOBALS['conceptoY'] = 100;
        $GLOBALS['tablaY'] = 170;
        $GLOBALS['tablaDY'] = 220;
        $GLOBALS['tablaAlto'] = $numeroDeFilas * 5 + 5;
        $GLOBALS['tablaDAlto'] = 10;
    
        $pdf->SetFillColor(56, 84, 40);
        $pdf->SetTextColor(0, 0, 0);

        
    
        $pdf->Image('../img/imgMovimientoCajaChica.png', $GLOBALS['initialX'], $GLOBALS['initialY'], $GLOBALS['logoWidth']);
        $pdf->setXY($GLOBALS['initialX'], $GLOBALS['initialY']);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->cell($pageWidth, 4, utf8_decode('OAXACA MIEL S.A. DE C.V.'), 0, 2, 'C', 0);
        $pdf->SetFont('Arial', '', 9);
        $pdf->cell($pageWidth, 4, utf8_decode(' '), 0, 2, 'C', 0);
        $pdf->cell($pageWidth, 4, utf8_decode(''), 0, 2, 'C', 0);
        $pdf->cell($pageWidth, 4, utf8_decode(''), 0, 2, 'C', 0);
        $pdf->cell($pageWidth, 4, utf8_decode(''), 0, 2, 'C', 0);

        $pdf->SetFillColor(56, 84, 40);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 9);

    
        #NOMBRE DE PERSONA
    
        $pdf->setFont('Arial', 'B', 10);
        $pdf->SetTextColor(0, 0, 0);
    
        $x = $GLOBALS['initialX'];
        $y = $GLOBALS['nameRowY'];
        $pdf->setXY(9, $y);
        $pdf->cell(50, $GLOBALS['rowHeight'], utf8_decode('PÓLIZA DE CHEQUE.'), 0, 0, '', 0);
        $pdf->cell(50, $GLOBALS['rowHeight'], utf8_decode($datos['banco'] . ' ' . $datos['numDeCuenta']), 0, 0, 'L', 0);
        #CHEQUE
        $pdf->SetFont('Arial', '', 9);
        $x = $GLOBALS['initialX'];
        $y = $GLOBALS['dataRowY'];
        $pdf->setXY($x, $y);
    
    
        $imageUrl = 'img/' . $datos['logotipo'];
    
        $pdf->cell($pageWidth, $GLOBALBANCOS['chequeAlto'], utf8_decode(''), 1, 1, 'C', 0);
        if ($imageUrl != 'img/') {
            $pdf->Image($imageUrl, $x + 2, $y + 2, $GLOBALBANCOS['logoBancoAncho'], $GLOBALBANCOS['logoBancoAlto']);
        }

        #Folio del cheque
        $pdf->setFont('Arial', 'B', 13);
        $pdf->setXY($GLOBALS['cuarter'] * 3, $GLOBALS['folioY']);
        $pdf->cell($GLOBALS['cuarter'], $GLOBALS['rowHeight'], utf8_decode($datos['folioCheque']), 0, 0, 'C', 0);
        $pdf->setFont('Arial', '', 9);
        
        $x = $GLOBALS['initialX'];
        $y = $GLOBALS['dataRowY'] + $GLOBALBANCOS['logoBancoAlto'] + 5;
        $pdf->setXY($x, $y);
        $pdf->cell($GLOBALS['middle'], $GLOBALS['rowHeight'], utf8_decode('Páguese por este cheque a la orden de: '), 0, 0, 'L', 0);
        $pdf->cell($GLOBALS['cuarter'], $GLOBALS['rowHeight'], utf8_decode('Fecha: ' . $datos['fecha']), 'B', 1, 'L', 0);
        $pdf->Ln(5);
        $pdf->cell($GLOBALS['third'] * 2, $GLOBALS['rowHeight'], strtoupper(utf8_decode($datos['nombre2'])), 'BR', 0, 'C', 0);
        $pdf->cell(10, $GLOBALS['rowHeight'], utf8_decode('$'), 0, 0, 'L', 0);
        $pdf->cell($GLOBALS['cuarter'], $GLOBALS['rowHeight'], utf8_decode(number_format($datos['cantidad'], 2, '.', ',')), 1, 1, 'C', 0);
        $pdf->Ln(5);
        $pdf->setFont('Arial', '', 7);
        $pdf->cell(20, $GLOBALS['rowHeight'], "CON LETRA: ", 'B', 0, 'L', 0);
        $pdf->setFont('Arial', 'B', 10);
        $pdf->cell($GLOBALS['third'] * 2, $GLOBALS['rowHeight'], utf8_decode(convertir($datos['cantidad']) . " M.N."), 'B', 0, 'L', 0);
        $pdf->setFont('Arial', '', 9);
        // $pdf->cell($GLOBALS['third'], $GLOBALS['rowHeight'], utf8_decode('Moneda Nacional'), 0, 1, 'L', 0);
    
        #CONCEPTO Y FIRMA
        $pdf->setXY($GLOBALS['initialX'], $GLOBALBANCOS['conceptoY']);
        $pdf->setFont('Arial', 'B', 9);
        $pdf->cell(100, $GLOBALS['rowHeight'], utf8_decode('Observaciones: '), 0, 1, 'L', 0);
        $pdf->setFont('Arial', '', 9);
        $pdf->Multicell(100, $GLOBALS['rowHeight'], utf8_decode('Concepto: ' . $datos['concepto']), 0, 'L', 0);
        $pdf->MultiCell($pageWidth, $GLOBALS['rowHeight'], utf8_decode('Descripción: ' . $datos['descripcion']), 0, 'L', 0);
        $pdf->MultiCell($pageWidth, $GLOBALS['rowHeight'], utf8_decode('Nombre 2: ' . $datos['nombre']), 0, 'L', 0);


        $y = $pdf->getY() + 30;
    
        $pdf->setXY($GLOBALS['third'] * 2, $y);
        $pdf->Line($GLOBALS['third'] * 2, $y, $pageWidth, $y);
        $pdf->cell($GLOBALS['third'], $GLOBALS['rowHeight'], utf8_decode('Firma Cheque Recibido'), 0, 2, 'C', 0);
        $pdf->cell($GLOBALS['third'], $GLOBALS['rowHeight'], strtoupper(utf8_decode($datos['nombre2'])), 0, 1, 'C', 0);
    
        #TABLA
    
        $pdf->setXY($GLOBALS['initialX'], $GLOBALBANCOS['tablaY']);
        $pdf->RoundedRect($GLOBALS['initialX'], $GLOBALBANCOS['tablaY'], $pageWidth, $GLOBALBANCOS['tablaAlto'], 3, '1234', '');
        $pdf->cell($GLOBALS['seven'], $GLOBALS['rowHeight'], utf8_decode('CUENTA'), 0, 0, 'C', 0);
        $pdf->cell($GLOBALS['seven'], $GLOBALS['rowHeight'], utf8_decode('SUB-CUENTA'), 0, 0, 'C', 0);
        $pdf->cell($GLOBALS['seven'] * 2, $GLOBALS['rowHeight'], utf8_decode('NOMBRE'), 0, 0, 'C', 0);
        $pdf->cell($GLOBALS['seven'], $GLOBALS['rowHeight'], utf8_decode('PARCIAL'), 0, 0, 'C', 0);
        $pdf->cell($GLOBALS['seven'], $GLOBALS['rowHeight'], utf8_decode('DEBE'), 0, 0, 'C', 0);
        $pdf->cell($GLOBALS['seven'], $GLOBALS['rowHeight'], utf8_decode('HABER'), 0, 1, 'C', 0);
    
        for ($i = 1; $i <= $numeroDeFilas; $i++) {
            if ($i % 2 == 0) {
                $pdf->SetFillColor(236, 240, 241);
            } else {
                $pdf->SetFillColor(255, 255, 255);
            }
    
            $pdf->cell($GLOBALS['seven'], $GLOBALS['rowHeight'], utf8_decode(''), '', 0, 'C', 1);
            $pdf->cell($GLOBALS['seven'], $GLOBALS['rowHeight'], utf8_decode(''), 'L', 0, 'C', 1);
            $pdf->cell($GLOBALS['seven'] * 2, $GLOBALS['rowHeight'], utf8_decode(''), 'L', 0, 'C', 1);
            $pdf->cell($GLOBALS['seven'], $GLOBALS['rowHeight'], utf8_decode(''), 'L', 0, 'C', 1);
            $pdf->cell($GLOBALS['seven'], $GLOBALS['rowHeight'], utf8_decode(''), 'L', 0, 'C', 1);
            $pdf->cell($GLOBALS['seven'], $GLOBALS['rowHeight'], utf8_decode(''), 'L', 1, 'C', 1);
        }
    
        #Estructura de la segunda tabla
    
        $pdf->setXY($GLOBALS['initialX'], $GLOBALBANCOS['tablaDY']);
        $pdf->RoundedRect($GLOBALS['initialX'], $GLOBALBANCOS['tablaDY'], $pageWidth, $GLOBALBANCOS['tablaDAlto'], 3, '1234', '');
        $pdf->cell($GLOBALS['sixth'], $GLOBALS['rowHeight'], utf8_decode('HECHO POR'), 0, 0, 'C', 0);
        $pdf->cell($GLOBALS['sixth'], $GLOBALS['rowHeight'], utf8_decode('REVISADO'), 0, 0, 'C', 0);
        $pdf->cell($GLOBALS['sixth'], $GLOBALS['rowHeight'], utf8_decode('AUTORIZADO'), 0, 0, 'C', 0);
        $pdf->cell($GLOBALS['sixth'], $GLOBALS['rowHeight'], utf8_decode('AUXILIARES'), 0, 0, 'C', 0);
        $pdf->cell($GLOBALS['sixth'], $GLOBALS['rowHeight'], utf8_decode('DIARIO'), 0, 0, 'C', 0);
        $pdf->cell($GLOBALS['sixth'], $GLOBALS['rowHeight'], utf8_decode('PÓLIZA No.'), 0, 1, 'C', 0);
    
        #Llenado de la segunda tabla
    
        $pdf->cell($GLOBALS['sixth'], $GLOBALS['rowHeight'], utf8_decode(''), 'T', 0, 'C', 0);
        $pdf->cell($GLOBALS['sixth'], $GLOBALS['rowHeight'], utf8_decode(''), 'LT', 0, 'C', 0);
        $pdf->cell($GLOBALS['sixth'], $GLOBALS['rowHeight'], utf8_decode(''), 'LT', 0, 'C', 0);
        $pdf->cell($GLOBALS['sixth'], $GLOBALS['rowHeight'], utf8_decode(''), 'LT', 0, 'C', 0);
        $pdf->cell($GLOBALS['sixth'], $GLOBALS['rowHeight'], utf8_decode(''), 'LT', 0, 'C', 0);
        $pdf->cell($GLOBALS['sixth'], $GLOBALS['rowHeight'], utf8_decode(''), 'LT', 1, 'C', 0);
    } else {
        $pdf->setFont('Arial', 'B', 10);
        $pdf->cell(100, 5, utf8_decode('No se ha podido completar la solicitud'), 0, 1, 'L', 0);
    }

    $pdf->Output();
}

if (isset($_GET['idPoliza'])) {
    outputPdf($con, $_GET['idPoliza']);
} else {
    exit();
}
