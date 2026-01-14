<?php
// Usar autoload de Composer en lugar de require directo
require('../../fpdf/FPDF/fpdf.php');
include_once '../../DAOConeccion/conePDO.php';
include_once '../numberToText.php';
include_once '../../controlAdministrativo/php/nombreDePersona.php';
// Crear instancia de la clase para conexión a la BD
$pdo = new conePDO();
$con = $pdo->conectar();

/**
 * Obtiene la información de la póliza de cheque
 * 
 * @param PDO $dbh Conexión a la base de datos
 * @param int $idPoliza ID de la póliza
 * @return array Información de la póliza o array vacío si no se encuentra
 */
function getInfo(PDO $dbh, int $idPoliza): array
{
    $resultado = [];
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
        }
        return [];
    } catch (Exception $e) {
        // En PHP 8.4 es mejor loguear el error en lugar de ignorarlo silenciosamente
        error_log("Error en getInfo: " . $e->getMessage());
        return [];
    }
}


/**
 * Genera el PDF con la información de la póliza
 * 
 * @param PDO $con Conexión a la base de datos
 * @param int $idPoliza ID de la póliza
 * @return void
 */
function outputPdf(PDO $con, int $idPoliza): void
{
    $datos = getInfo($con, $idPoliza);

    // Usar namespace en lugar de la clase global para FPDF
    $pdf = new \FPDF('P', 'mm', 'A4');
    $pdf->AddPage();

    if (isset($datos['idPolizaCheque'])) {
        $pageWidth = 190;
        $pageHeight = $pdf->getPageHeight();
        $numeroDeFilas = 8;
    
        // En PHP 8.4, es preferible usar variables locales en lugar de $GLOBALS
        $globals = [
            'nameRowY' => 40,
            'dataRowY' => 45,
            'folioY' => 50,
            'seven' => $pageWidth/7,
            'sixth' => $pageWidth/6,
            'cuarter' => $pageWidth/4,
            'middle' => $pageWidth/2,
            'third' => $pageWidth/3,
            'rowHeight' => 5,
            'initialX' => 10,
            'initialY' => 10,
            'logoWidth' => 25
        ];
    
        $globalBancos = [
            'chequeAlto' => 50,
            'logoBancoAncho' => 60,
            'logoBancoAlto' => 10,
            'conceptoY' => 100,
            'tablaY' => 170,
            'tablaDY' => 220,
            'tablaAlto' => $numeroDeFilas * 5 + 5,
            'tablaDAlto' => 10
        ];
    
        $pdf->SetFillColor(56, 84, 40);
        $pdf->SetTextColor(0, 0, 0);
    
        $pdf->Image('../img/imgMovimientoCajaChica.png', $globals['initialX'], $globals['initialY'], $globals['logoWidth']);
        $pdf->setXY($globals['initialX'], $globals['initialY']);
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
    
        // NOMBRE DE PERSONA
        $pdf->setFont('Arial', 'B', 10);
        $pdf->SetTextColor(0, 0, 0);
    
        $x = $globals['initialX'];
        $y = $globals['nameRowY'];
        $pdf->setXY(9, $y);
        $pdf->cell(50, $globals['rowHeight'], utf8_decode('PÓLIZA DE CHEQUE.'), 0, 0, '', 0);
        $pdf->cell(50, $globals['rowHeight'], utf8_decode($datos['banco'] . ' ' . $datos['numDeCuenta']), 0, 0, 'L', 0);
        
        // CHEQUE
        $pdf->SetFont('Arial', '', 9);
        $x = $globals['initialX'];
        $y = $globals['dataRowY'];
        $pdf->setXY($x, $y);
    
        $imageUrl = 'img/' . $datos['logotipo'];
    
        $pdf->cell($pageWidth, $globalBancos['chequeAlto'], utf8_decode(''), 1, 1, 'C', 0);
        if ($imageUrl != 'img/' && file_exists($imageUrl)) {
            $pdf->Image($imageUrl, $x + 2, $y + 2, $globalBancos['logoBancoAncho'], $globalBancos['logoBancoAlto']);
        }

        // Folio del cheque
        $pdf->setFont('Arial', 'B', 13);
        $pdf->setXY($globals['cuarter'] * 3, $globals['folioY']);
        $pdf->cell($globals['cuarter'], $globals['rowHeight'], utf8_decode($datos['folioCheque']), 0, 0, 'C', 0);
        $pdf->setFont('Arial', '', 9);
        
        $x = $globals['initialX'];
        $y = $globals['dataRowY'] + $globalBancos['logoBancoAlto'] + 5;
        $pdf->setXY($x, $y);
        $pdf->cell($globals['middle'], $globals['rowHeight'], utf8_decode('Páguese por este cheque a la orden de: '), 0, 0, 'L', 0);
        $pdf->cell($globals['cuarter'], $globals['rowHeight'], utf8_decode('Fecha: ' . $datos['fecha']), 'B', 1, 'L', 0);
        $pdf->Ln(5);
        $pdf->cell($globals['third'] * 2, $globals['rowHeight'], strtoupper(utf8_decode($datos['nombre2'])), 'BR', 0, 'C', 0);
        $pdf->cell(10, $globals['rowHeight'], utf8_decode('$'), 0, 0, 'L', 0);
        $pdf->cell($globals['cuarter'], $globals['rowHeight'], utf8_decode(number_format($datos['cantidad'], 2, '.', ',')), 1, 1, 'C', 0);
        $pdf->Ln(5);
        $pdf->setFont('Arial', '', 7);
        $pdf->cell(20, $globals['rowHeight'], "CON LETRA: ", 'B', 0, 'L', 0);
        $pdf->setFont('Arial', 'B', 10);
        $pdf->cell($globals['third'] * 2, $globals['rowHeight'], utf8_decode(convertir($datos['cantidad']) . " M.N."), 'B', 0, 'L', 0);
        $pdf->setFont('Arial', '', 9);
    
        // CONCEPTO Y FIRMA
        $pdf->setXY($globals['initialX'], $globalBancos['conceptoY']);
        $pdf->setFont('Arial', 'B', 9);
        $pdf->cell(100, $globals['rowHeight'], utf8_decode('Observaciones: '), 0, 1, 'L', 0);
        $pdf->setFont('Arial', '', 9);
        $pdf->Multicell(100, $globals['rowHeight'], utf8_decode('Concepto: ' . $datos['concepto']), 0, 'L', 0);
        $pdf->MultiCell($pageWidth, $globals['rowHeight'], utf8_decode('Descripción: ' . $datos['descripcion']), 0, 'L', 0);
        $pdf->MultiCell($pageWidth, $globals['rowHeight'], utf8_decode('Nombre 2: ' . $datos['nombre']), 0, 'L', 0);

        $y = $pdf->getY() + 30;
    
        $pdf->setXY($globals['third'] * 2, $y);
        $pdf->Line($globals['third'] * 2, $y, $pageWidth, $y);
        $pdf->cell($globals['third'], $globals['rowHeight'], utf8_decode('Firma Cheque Recibido'), 0, 2, 'C', 0);
        $pdf->cell($globals['third'], $globals['rowHeight'], strtoupper(utf8_decode($datos['nombre2'])), 0, 1, 'C', 0);
    
        // TABLA
        $pdf->setXY($globals['initialX'], $globalBancos['tablaY']);
        $pdf->RoundedRect($globals['initialX'], $globalBancos['tablaY'], $pageWidth, $globalBancos['tablaAlto'], 3, '1234', '');
        $pdf->cell($globals['seven'], $globals['rowHeight'], utf8_decode('CUENTA'), 0, 0, 'C', 0);
        $pdf->cell($globals['seven'], $globals['rowHeight'], utf8_decode('SUB-CUENTA'), 0, 0, 'C', 0);
        $pdf->cell($globals['seven'] * 2, $globals['rowHeight'], utf8_decode('NOMBRE'), 0, 0, 'C', 0);
        $pdf->cell($globals['seven'], $globals['rowHeight'], utf8_decode('PARCIAL'), 0, 0, 'C', 0);
        $pdf->cell($globals['seven'], $globals['rowHeight'], utf8_decode('DEBE'), 0, 0, 'C', 0);
        $pdf->cell($globals['seven'], $globals['rowHeight'], utf8_decode('HABER'), 0, 1, 'C', 0);
    
        for ($i = 1; $i <= $numeroDeFilas; $i++) {
            $pdf->SetFillColor($i % 2 == 0 ? 236 : 255, $i % 2 == 0 ? 240 : 255, $i % 2 == 0 ? 241 : 255);
    
            $pdf->cell($globals['seven'], $globals['rowHeight'], utf8_decode(''), '', 0, 'C', 1);
            $pdf->cell($globals['seven'], $globals['rowHeight'], utf8_decode(''), 'L', 0, 'C', 1);
            $pdf->cell($globals['seven'] * 2, $globals['rowHeight'], utf8_decode(''), 'L', 0, 'C', 1);
            $pdf->cell($globals['seven'], $globals['rowHeight'], utf8_decode(''), 'L', 0, 'C', 1);
            $pdf->cell($globals['seven'], $globals['rowHeight'], utf8_decode(''), 'L', 0, 'C', 1);
            $pdf->cell($globals['seven'], $globals['rowHeight'], utf8_decode(''), 'L', 1, 'C', 1);
        }
    
        // Estructura de la segunda tabla
        $pdf->setXY($globals['initialX'], $globalBancos['tablaDY']);
        $pdf->RoundedRect($globals['initialX'], $globalBancos['tablaDY'], $pageWidth, $globalBancos['tablaDAlto'], 3, '1234', '');
        $pdf->cell($globals['sixth'], $globals['rowHeight'], utf8_decode('HECHO POR'), 0, 0, 'C', 0);
        $pdf->cell($globals['sixth'], $globals['rowHeight'], utf8_decode('REVISADO'), 0, 0, 'C', 0);
        $pdf->cell($globals['sixth'], $globals['rowHeight'], utf8_decode('AUTORIZADO'), 0, 0, 'C', 0);
        $pdf->cell($globals['sixth'], $globals['rowHeight'], utf8_decode('AUXILIARES'), 0, 0, 'C', 0);
        $pdf->cell($globals['sixth'], $globals['rowHeight'], utf8_decode('DIARIO'), 0, 0, 'C', 0);
        $pdf->cell($globals['sixth'], $globals['rowHeight'], utf8_decode('PÓLIZA No.'), 0, 1, 'C', 0);
    
        // Llenado de la segunda tabla
        $pdf->cell($globals['sixth'], $globals['rowHeight'], utf8_decode(''), 'T', 0, 'C', 0);
        $pdf->cell($globals['sixth'], $globals['rowHeight'], utf8_decode(''), 'LT', 0, 'C', 0);
        $pdf->cell($globals['sixth'], $globals['rowHeight'], utf8_decode(''), 'LT', 0, 'C', 0);
        $pdf->cell($globals['sixth'], $globals['rowHeight'], utf8_decode(''), 'LT', 0, 'C', 0);
        $pdf->cell($globals['sixth'], $globals['rowHeight'], utf8_decode(''), 'LT', 0, 'C', 0);
        $pdf->cell($globals['sixth'], $globals['rowHeight'], utf8_decode(''), 'LT', 1, 'C', 0);
    } else {
        $pdf->setFont('Arial', 'B', 10);
        $pdf->cell(100, 5, utf8_decode('No se ha podido completar la solicitud'), 0, 1, 'L', 0);
    }

    $pdf->Output();
}

// Verificar que la variable GET exista y sea de tipo numérico
if (isset($_GET['idPoliza']) && is_numeric($_GET['idPoliza'])) {
    outputPdf($con, (int)$_GET['idPoliza']);
} else {
    header('HTTP/1.1 400 Bad Request');
    echo 'Se requiere un ID de póliza válido';
    exit();
}