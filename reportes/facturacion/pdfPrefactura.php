<?php

require('../../fpdf/FPDF/fpdf.php');
require('./rotation.php');

class PDF extends PDF_Rotate
{
    function Header()
    {
        $this->SetFont('Arial', 'B', 50);
        $this->SetTextColor(203, 203, 203);
        $this->RotatedText(70, 190, utf8_decode('N o  v á l i d o'), 45);
    }

    function RotatedText($x, $y, $txt, $angle)
    {
        $this->Rotate($angle, $x, $y);
        $this->Text($x, $y, $txt);
        $this->Rotate(0);
    }
}

include_once '../../DAOConeccion/conePDO.php';
include_once '../../controlAdministrativo/php/nombreDePersona.php';
include_once '../numberToText.php';

$pdo = new conePDO();
$con = $pdo->conectar();
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function getInfo($idTamborPeso, $tipoMiel)
{
    global $con;
    // $resultado = array();
    switch ($tipoMiel) {
        case '1':
            $entradaysalida = 'entradaysalida';
            break;
        case '2':
            $entradaysalida = 'entradaysalida_organico';
            break;
        case '5':
            $entradaysalida = 'entradaysalida_mantequilla';
            break;
        case '6':
            $entradaysalida = 'entradaysalida_altiplano';
            break;
        case '7':
            $entradaysalida = 'entradaysalida_naranjo';
            break;
        case '8':
            $entradaysalida = 'entradaysalida_aguacate';
            break;
        case '9':
            $entradaysalida = 'entradaysalida_mezquite';
            break;
        default:
            throw new Exception('Tipo de miel no válido');
            break;
    }

    try {
        $consulta = $con->prepare("SELECT rp.idTamborPeso, rp.precio, rp.importe, rp.tipoDeCliente, rp.idCliente, 
        c.lote, cm.clasificacion,
        CASE WHEN rp.condicionPago = '1' THEN 'Crédito' ELSE 'Contado' END AS condicionPago,
        CASE WHEN rp.tiempoPago IS NULL THEN 'N/A' ELSE rp.tiempoPago END AS tiempoPago,
        rp.destino, rp.totalNeto, c.fechaImpresion AS fecha, tdm.tipoDeMiel, c.idLoteInterno,
        (SELECT COUNT(idPesoTambo)FROM tamboreslistapesos WHERE idTamborPeso = rp.idTamborPeso) as cantidadTambores
        FROM listadepesos rp
        LEFT JOIN $entradaysalida c ON c.lote = rp.lote
        LEFT JOIN tiposdemiel tdm ON rp.tipoMiel = tdm.idTipoDeMiel
        LEFT JOIN clientesexportadores cle ON cle.idClienteExportador = rp.idCliente
        LEFT JOIN clasificacionesmiel cm ON cm.idClasificacionMiel = c.idClasificacion
        WHERE rp.idTamborPeso = :id");
        $consulta->bindParam(':id', $idTamborPeso);
        $consulta->execute();
        if ($consulta->rowCount() == 1) {
            $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
            if ($resultado['tipoDeCliente'] == '10') {
                $query = "SELECT *
                FROM clientesexportadores
                WHERE idClienteExportador = :idClienteExportador";
                $datosResp = $con->prepare($query);
                $datosResp->bindParam(':idClienteExportador', $resultado['idCliente']);
                $datosResp->execute();
                $result = $datosResp->fetch(PDO::FETCH_ASSOC);
                $datosCliente = json_decode($result['datosCliente']);
                $resultado['cliente'] = $datosCliente->nombre;
            } else if ($resultado['tipoDeCliente'] == '6') {
                $seleccionarClientes = $con->prepare("SELECT nombre FROM clientes WHERE idCliente = :idCliente");
                $seleccionarClientes->bindParam(':idCliente', $resultado['idCliente']);
                $seleccionarClientes->execute();
                $resultado['cliente'] = $seleccionarClientes->fetch(PDO::FETCH_ASSOC);
            }
        }
        return $resultado;
    } catch (Exception $e) {
        return ['error' => true, 'message' => $e->getMessage()];
    }
};


function addLineIngreso($y, $_info, $pdf, $GLOBALES)
{

    $heigthPerRow = 0;

    $pdf->MultiCell($GLOBALES['sixth'], $GLOBALES['littleRow'] + 22, utf8_decode($_info['cantidadTambores']), 0, 'R', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($GLOBALES['initialX'] + ($GLOBALES['sixth']), $y);

    $pdf->MultiCell($GLOBALES['cuarter'], $GLOBALES['littleRow'] + 22, utf8_decode('TAMBORES'), 0, 'L', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($GLOBALES['initialX'] + $GLOBALES['cuarter'] - 15 + $GLOBALES['sixth'], $y);

    $pdf->MultiCell($GLOBALES['cuarter'], $GLOBALES['littleRow'] + 22, utf8_decode(number_format($_info['totalNeto'], 2, '.', ',') . 'kg.'), 0, 'R', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($GLOBALES['initialX'] + $GLOBALES['cuarter'] * 2  + $GLOBALES['sixth'], $y);

    $pdf->MultiCell($GLOBALES['sixth'], $GLOBALES['littleRow'] + 22, utf8_decode('$' . number_format($_info['precio'], 2, '.', ',')), 0, 'R', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;
    $pdf->setXY($GLOBALES['initialX'] + $GLOBALES['sixth'] * 2 + $GLOBALES['cuarter'] * 2, $y);

    $pdf->MultiCell($GLOBALES['sixth'], $GLOBALES['littleRow'] + 22, utf8_decode('$' . number_format($_info['importe'], 2, '.', ',')), 0, 'R', 0);
    $heigthPerRow = $pdf->getY() - $y > $heigthPerRow ? $pdf->getY() - $y : $heigthPerRow;

    return $heigthPerRow;
};

function outputPdf($idTamborPeso, $tipoMiel)
{
    global $con;
    $datos = getInfo($idTamborPeso, $tipoMiel);
    // $pdf = new FPDF('P', 'mm', 'A4');
    // $pdf->AddPage();

    // Creación del objeto de la clase heredada
    $pdf = new PDF('P', 'mm', 'A4');
    $pdf->AliasNbPages();
    $pdf->AddPage();

    $pdf->SetTextColor(0, 0, 0);



    if (!isset($datos['idTamborPeso'])) {
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->cell(500, 5, utf8_decode('No se ha podido traer la información'), 0, 2, 'L', 0);
        $pdf->cell(500, 5, utf8_decode($datos['message']), 0, 2, 'L', 0);
        $pdf->Output();
        exit();
    }

    $tipoDeReporte = 'PREFACTURA';

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
    $GLOBALSY['dataRowY'] = 50;
    $GLOBALSY['dateRowY'] = 20;
    $GLOBALSY['signRowY'] = $pageHeight / 2 - 40;
    $GLOBALSY['logoY'] = 10;
    $GLOBALSY['documentTitleY'] = 10;
    $GLOBALSY['totalY'] = 45 + $GLOBALS['rowHeight'] * 10;


    // $pdf->temporaire(utf8_decode("NO VÁLIDO"));

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
    $pdf->Cell($GLOBALS['sixth'], $GLOBALS['rowHeight'], utf8_decode('LOTE'), 0, 0, 'C', 0);

    $pdf->RoundedRect($GLOBALS['initialX'] + ($GLOBALS['sixth'] * 5), $GLOBALSY['dateRowY'], $GLOBALS['sixth'], $GLOBALS['rowHeight'], 2, '12', 'DF');
    $pdf->Cell($GLOBALS['sixth'], $GLOBALS['rowHeight'], utf8_decode('FECHA DE CARGA'), 0, 2, 'C', 0);

    #NOMBRE DE PERSONA

    $pdf->setFont('Arial', '', 9);
    $pdf->SetTextColor(0, 0, 0);
    $x = $GLOBALS['initialX'];
    $y = $GLOBALSY['nameRowY'];
    $pdf->setXY($x, $y);
    $pdf->cell(35, $GLOBALS['rowHeight'], utf8_decode('CLIENTE: '), 0, 0, '', 0);
    $pdf->setFont('Arial', 'B', 9);
    if ($datos['tipoDeCliente'] == '10') {
        $pdf->cell(100, $GLOBALS['rowHeight'], utf8_decode(strtoupper($datos['cliente'])), 0, 1, 'L', 0);
    } else if ($datos['tipoDeCliente'] == '6') {
        $pdf->cell(100, $GLOBALS['rowHeight'], utf8_decode(strtoupper($datos['cliente']['nombre'])), 0, 1, 'L', 0);
    }

    $pdf->setXY($x, $pdf->getY());
    $pdf->setFont('Arial', '', 9);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->cell(35, $GLOBALS['rowHeight'], utf8_decode('DESTINO: '), 0, 0, '', 0);
    $pdf->setFont('Arial', 'B', 9);
    $pdf->cell(100, $GLOBALS['rowHeight'], utf8_decode(strtoupper($datos['destino'])), 0, 1, 'L', 0);

    $pdf->setXY($x, $pdf->getY());
    $pdf->setFont('Arial', '', 9);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->cell(35, $GLOBALS['rowHeight'], utf8_decode('TIPO DE VENTA: '), 0, 0, '', 0);
    $pdf->setFont('Arial', 'B', 9);
    $pdf->cell(100, $GLOBALS['rowHeight'], utf8_decode(strtoupper($datos['clasificacion'])), 0, 1, 'L', 0);

    $pdf->setXY($x, $pdf->getY());
    $pdf->setFont('Arial', '', 9);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->cell(35, $GLOBALS['rowHeight'], utf8_decode('PRODUCTO: '), 0, 0, '', 0);
    $pdf->setFont('Arial', 'B', 9);
    $pdf->cell(100, $GLOBALS['rowHeight'], utf8_decode(strtoupper($datos['tipoDeMiel'])), 0, 1, 'L', 0);


    $pdf->setXY($x, $pdf->getY());
    $pdf->setFont('Arial', '', 9);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->cell(35, $GLOBALS['rowHeight'], utf8_decode('MÉTODO DE PAGO: '), 0, 0, '', 0);
    $pdf->setFont('Arial', 'B', 9);
    $pdf->cell(100, $GLOBALS['rowHeight'], utf8_decode(strtoupper($datos['condicionPago'])), 0, 1, 'L', 0);

    $pdf->setXY($x, $pdf->getY());
    $pdf->setFont('Arial', '', 9);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->cell(35, $GLOBALS['rowHeight'], utf8_decode('DIAS DE PAGO: '), 0, 0, '', 0);
    $pdf->setFont('Arial', 'B', 9);
    $pdf->cell(100, $GLOBALS['rowHeight'], utf8_decode(strtoupper($datos['tiempoPago'])), 0, 1, 'L', 0);

    #FINALIZA NOMBRE DE PERSONA

    $pdf->SetFillColor(255, 229, 88);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 9);

    $x = $GLOBALS['initialX'];
    $y = $GLOBALSY['dataRowY'];
    $pdf->setXY($x, $y + 20);

    $pdf->cell($GLOBALS['sixth'], $GLOBALS['rowHeight'], utf8_decode('CANTIDAD'), 1, 0, 'C', 1);
    $pdf->cell($GLOBALS['sixth'], $GLOBALS['rowHeight'], utf8_decode('UNIDAD'), 1, 0, 'C', 1);
    $pdf->cell($GLOBALS['cuarter'], $GLOBALS['rowHeight'], utf8_decode('PESO NETO'), 1, 0, 'C', 1);
    $pdf->cell($GLOBALS['cuarter'], $GLOBALS['rowHeight'], utf8_decode('PRECIO POR KG.'), 1, 0, 'C', 1);
    $pdf->cell($GLOBALS['sixth'], $GLOBALS['rowHeight'], utf8_decode('IMPORTE'), 1, 0, 'C', 1);

    $x = $GLOBALS['initialX'];
    $y = $GLOBALSY['dataRowY'] + $GLOBALS['rowHeight'];
    $pdf->setXY($x, $y + 20);

    $pdf->cell($GLOBALS['sixth'], $GLOBALS['rowHeight'] * 27, '', 1, 0, 'L', 0);
    $pdf->cell($GLOBALS['sixth'], $GLOBALS['rowHeight'] * 27, '', 1, 0, 'L', 0);
    $pdf->cell($GLOBALS['cuarter'], $GLOBALS['rowHeight'] * 27, '', 1, 0, 'L', 0);
    $pdf->cell($GLOBALS['cuarter'], $GLOBALS['rowHeight'] * 27, '', 1, 0, 'L', 0);
    $pdf->cell($GLOBALS['sixth'], $GLOBALS['rowHeight'] * 27, '', 1, 0, 'L', 0);

    // #INPUTDATA



    $pdf->setFont('Arial', '', 8);
    $y = $GLOBALSY['dateRowY'] + $GLOBALS['rowHeight'];
    $pdf->setXY($x + ($GLOBALS['sixth'] * 4), $y);
    $pdf->RoundedRect($x + ($GLOBALS['sixth'] * 4), $y, $GLOBALS['sixth'], $GLOBALS['rowHeight'], 2, '34', '');
    $pdf->Cell($GLOBALS['sixth'], $GLOBALS['rowHeight'], utf8_decode($datos['lote']), 0, 0, 'C', 0);
    $pdf->RoundedRect($x + ($GLOBALS['sixth'] * 5), $y, $GLOBALS['sixth'], $GLOBALS['rowHeight'], 2, '34', '');
    $pdf->Cell($GLOBALS['sixth'], $GLOBALS['rowHeight'], utf8_decode($datos['fecha']), 0, 0, 'C', 0);

    $x = $GLOBALS['initialX'];
    $y = $GLOBALSY['dataRowY'] + $GLOBALS['rowHeight'] + 1; // Más 1 para separar del encabezado
    $pdf->SetFont('Arial', '', 9);
    $pdf->setXY($x, $y + 10);

    $size = addLineIngreso($y + 10, $datos, $pdf, $GLOBALS);
    $y += $size + 1;

    $x = $GLOBALS['initialX'] + ($GLOBALS['cuarter'] * 3) + $GLOBALS['twelve'];
    $y = $GLOBALSY['dataRowY'] + $GLOBALS['rowHeight'] * 30;
    $pdf->setXY($pdf->GetX(), $y + 10);
    $pdf->MultiCell($pdf->GetX() + 100.8, $GLOBALS['rowHeight'], utf8_decode('IMPORTE CON LETRA:' . "\n" . convertir($datos['importe']) . " M.N."), 1, 1, 0);

    $pdf->setXY($x, $y + 10);
    $pdf->cell($GLOBALS['sixth'], $GLOBALS['rowHeight'] + 5, utf8_decode('$' . number_format($datos['importe'], 2, '.', ',')), 1, 0, 'R', 0);
    $pdf->SetFont('Arial', 'B', 8);
    $x = $GLOBALS['initialX'] + ($GLOBALS['cuarter'] * 2) + $GLOBALS['twelve'];
    $y = $GLOBALSY['dataRowY'] + $GLOBALS['rowHeight'] * 30;
    $pdf->setXY($x, $y + 10);
    $pdf->cell($GLOBALS['sixth'] + 15.8, $GLOBALS['rowHeight'] + 5, utf8_decode('TOTAL'), 1, 0, 'R', 0);

    // }
    $pdf->setFont('Arial', '', 8);
    $pdf->SetTextColor(0, 0, 0);
    $x = $GLOBALS['initialX'];
    $y = $GLOBALSY['nameRowY'];
    $pdf->setXY($x, $pdf->GetY() + 75);
    $pdf->cell($pdf->GetX(), $GLOBALS['rowHeight'], utf8_decode('EL PRESENTE DOCUMENTO CARECE DE VALOR OFICIAL. SOLO TIENE CARÁCTER INFORMATIVO.'), 0, 0, '', 0);

    // 

    $nombre_reporte = $tipoDeReporte;
    $pdf->setTitle($nombre_reporte);
    $pdf->Output('I', $nombre_reporte . '.pdf', true);
}



if (isset($_GET['idTamborPeso']) && isset($_GET['tipoMiel'])) {
    outputPdf($_GET['idTamborPeso'], $_GET['tipoMiel']);
} else {
    exit();
}
