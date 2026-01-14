<?php

include_once '../../fpdf/FPDF/fpdf.php';
include_once '../../DAOConeccion/conePDO.php';


class PDF extends FPDF
{

    var $angle = 0;
    var $widths;
    var $aligns;
    var $marginX = 10;
    var $footerY = -30;

    // Page header
    function Header()
    {
        if ($this->pageNo() != 1) {
            $this->Image('../img/encabezado_solicitud.jpg', $this->marginX, 6, $this->GetPageWidth() - $this->marginX * 2);
        }

        $this->Ln(20);
    }

    // Page footer
    function Footer()
    {
        if ($this->pageNo() != 1) {
            $this->SetY($this->footerY);
            $this->Image('../img/pie_solicitud.jpg', null, null, $this->GetPageWidth() - $this->marginX * 2);

            $this->Rotate(90, 6, 270);
            $this->SetFont('Times', 'B', 12);
            $this->Text(6, 270, utf8_decode('Página'));
            $this->SetFont('Times', 'B', 18);
            $numero_pagina = $this->pageNo() - 1;
            // Dejar los siguientes espacios, es para el formato de el número de página
            $this->Text(6, 270, utf8_decode('         ' . $numero_pagina));
            $this->SetFont('Arial', 'B', 10);
            $this->Rotate(0);
        }
    }

    function Rotate($angle, $x = -1, $y = -1)
    {
        if ($x == -1)
            $x = $this->x;
        if ($y == -1)
            $y = $this->y;
        if ($this->angle != 0)
            $this->_out('Q');
        $this->angle = $angle;
        if ($angle != 0) {
            $angle *= M_PI / 180;
            $c = cos($angle);
            $s = sin($angle);
            $cx = $x * $this->k;
            $cy = ($this->h - $y) * $this->k;
            $this->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm', $c, $s, -$s, $c, $cx, $cy, -$cx, -$cy));
        }
    }

    function SetWidths($w)
    {
        //Set the array of column widths
        $this->widths = $w;
    }

    // function SetAligns($a)
    // {
    //     //Set the array of column alignments
    //     $this->aligns=$a;
    // }

    function Row($data)
    {
        //Calculate the height of the row
        $nb = 0;
        for ($i = 0; $i < count($data); $i++)
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
        $h = 5 * $nb;
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of the row
        for ($i = 0; $i < count($data); $i++) {
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            //Save the current position
            $x = $this->GetX();
            $y = $this->GetY();
            //Draw the border
            $this->Rect($x, $y, $w, $h);
            //Print the text
            $this->MultiCell($w, 5, $data[$i], 0, $a);
            //Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }
        //Go to the next line
        $this->Ln($h);
    }

    function CheckPageBreak($h)
    {
        //If the height h would cause an overflow, add a new page immediately
        if ($this->GetY() + $h > $this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

    function NbLines($w, $txt)
    {
        //Computes the number of lines a MultiCell of width w will take
        $cw = &$this->CurrentFont['cw'];
        if ($w == 0)
            $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 and $s[$nb - 1] == "\n")
            $nb--;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ')
                $sep = $i;
            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j)
                        $i++;
                } else
                    $i = $sep + 1;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else
                $i++;
        }
        return $nl;
    }
}

function buildFile($cliente, $destino, $producto, $transporte, $fecha, $nombre_gerente, $tipo)
{

    $fecha = DateTime::createFromFormat('Y-m-d', $fecha);
    $transporte->fechaEmbarque = DateTime::createFromFormat('Y-m-d', $transporte->fechaEmbarque);

    switch ($transporte->medioTransporte) {
        case '1':
            $transporte->medioTransporte = 'Aéreo';
            break;
        case '2':
            $transporte->medioTransporte = 'Marítimo';
            break;
        case '3':
            $transporte->medioTransporte = 'Terrestre';
            break;
        default:
            $transporte->medioTransporte = '';
            break;
    }

    // Instanciation of inherited class
    $pdf = new PDF();
    $pdf->AliasNbPages();
    $pdf->SetAutoPageBreak(false);
    $pdf->SetLineWidth(0.1);
    $pageWidth = $pdf->GetPageWidth() - $pdf->marginX * 2;
    $startPage = 38;
    $sevenPart = $pageWidth / 7;
    $fifthPart = $pageWidth / 5;
    $middleWidthTable = $pageWidth / 2 - 3;
    $middleWidthTableSeparator = 3;
    $pdf->SetFillColor(217, 217, 217);

    // Portada
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 30);
    $pdf->Image('../img/LOGO.png', $pdf->marginX, $pdf->marginX, 35);
    $pdf->setXY($pdf->marginX + 35, $pdf->marginX);
    $pdf->cell(150, 25, utf8_decode('Oaxaca Miel, S.A. de C.V.'), 0, 2, 'C', 0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->setXY($pdf->marginX + 35, $pdf->marginX + 15);
    $pdf->cell(150, 15, utf8_decode('Compromiso de Calidad, Honestidad y Servicio'), 0, 2, 'C', 0);

    $pdf->Ln(2);
    $pdf->SetFont('Arial', '', 12);
    $pdf->cell($pageWidth - 20, 5, utf8_decode('Fecha de solicitud: '), 0, 0, 'R', 0);
    $pdf->setX($pdf->getX());
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->cell(20, 5, utf8_decode(date_format($fecha, "d/m/Y")), 0, 1, 'L', 0);

    $pdf->setY($pdf->getY() + 5);
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->cell($pageWidth, 5, utf8_decode('SOLICITUD CERTIFICADO ZOOSANITARIO PARA LA EXPORTACIÓN'), 0, 1, 'L', 0);

    $pdf->setY($pdf->getY() + 5);

    $pdf->SetFont('Arial', '', 10);
    $pdf->cell($pageWidth, 5, utf8_decode('Número de Lote: '), 0, 0, 'L', 0);
    $pdf->setX(50);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->cell($pageWidth, 5, utf8_decode($producto->marcaDistintiva), 0, 1, 'L', 0);

    $pdf->SetFont('Arial', '', 10);
    $pdf->cell($pageWidth, 5, utf8_decode('Fecha de embarque: '), 0, 0, 'L', 0);
    $pdf->setX(50);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->cell($pageWidth, 5, utf8_decode(date_format($transporte->fechaEmbarque, "d/m/Y")), 0, 1, 'L', 0);


    $pdf->SetFont('Arial', '', 10);
    $pdf->cell($pageWidth, 5, utf8_decode('Número de tambos: '), 0, 0, 'L', 0);
    $pdf->setX(50);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->cell($pageWidth, 5, utf8_decode($producto->cantidad), 0, 1, 'L', 0);


    $pdf->SetFont('Arial', '', 10);
    $pdf->cell($pageWidth, 5, utf8_decode('Peso Neto: '), 0, 0, 'L', 0);
    $pdf->setX(50);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->cell($pageWidth, 5, utf8_decode(number_format($producto->pesoNeto, 2, '.', ',') . ' Kilos'), 0, 1, 'L', 0);


    $pdf->SetFont('Arial', '', 10);
    $pdf->cell($pageWidth, 5, utf8_decode('Peso Bruto: '), 0, 0, 'L', 0);
    $pdf->setX(50);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->cell($pageWidth, 5, utf8_decode(number_format($producto->pesoBruto, 2, '.', ',') . ' Kilos'),  0, 1, 'L', 0);


    $pdf->SetFont('Arial', '', 10);
    $pdf->cell($pageWidth, 5, utf8_decode('País destino: '), 0, 0, 'L', 0);
    $pdf->setX(50);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->cell($pageWidth, 5, utf8_decode($producto->paisDestino), 0, 1, 'L', 0);

    $pdf->setY($pdf->getY() + 5);

    
    $pdf->SetFont('Arial', '', 10);
    $pdf->cell($pageWidth, 5, utf8_decode('Cliente: '), 0, 0, 'L', 0);
    $pdf->setX(50);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->cell($pageWidth, 5, utf8_decode($cliente->nombre), 0, 1, 'L', 0);

    $pdf->SetFont('Arial', '', 10);
    $pdf->cell($pageWidth, 5, utf8_decode('Contenedor: '), 0, 0, 'L', 0);
    $pdf->setX(50);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->cell($pageWidth, 5, utf8_decode($transporte->numeroContenedor), 0, 1, 'L', 0);
    
    $pdf->SetFont('Arial', '', 10);
    $pdf->cell($pageWidth, 5, utf8_decode('Sello: '), 0, 0, 'L', 0);
    $pdf->setX(50);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->cell($pageWidth, 5, utf8_decode($transporte->numeroFleje), 0, 1, 'L', 0);

    // $pdf->setY($pdf->getY() + 5);
    // $pdf->SetFont('Arial', 'B', 12);
    // $pdf->cell($pageWidth, 5, utf8_decode('Ing. Manuel Jesús Gongora López'), 0, 1, 'L', 0);
    // $pdf->cell($pageWidth, 5, utf8_decode('Encargado del programa de desarrollo pecuario en Yucatán'), 0, 1, 'L', 0);
    $pdf->setY($pdf->getY() + 5);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->cell($pageWidth, 5, utf8_decode('MVZ. José Martín Medina Zaldívar'), 0, 1, 'L', 0);
    $pdf->cell($pageWidth, 5, utf8_decode('Médico oficial del SENASICA'), 0, 1, 'L', 0);
    $pdf->setY($pdf->getY() + 5);
    $pdf->SetFont('Arial', '', 12);
    $pdf->cell($pageWidth, 5, utf8_decode('Estimados señores:'), 0, 1, 'L', 0);

    $pdf->setY($pdf->getY() + 5);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Multicell($pageWidth, 5, utf8_decode('Declaro bajo protesta de decir verdad que los datos arriba mencionados de número de Lote, la fecha de embarque y los kilogramos de miel de abeja, se encuentran en nuestra planta ubicada en Carretera Mérida - Cancún Km 7.5 S/N, CP 97370 Colonia San Pedro Noh Pat, Kanasín, Yucatán, México al momento de emitir esta carta.'));

    $pdf->setY($pdf->getY() + 5);
    $pdf->SetFont('Arial', '', 10);
    $pdf->cell($pageWidth, 5, utf8_decode('Así mismo, confirmo que entrego los siguientes documentos:'));
    $pdf->Ln(10);
    $pdf->setX($pdf->getX() + 10);
    $pdf->cell($pageWidth, 5, utf8_decode('1) Solicitud del Certificado Zoosanitario,'), 0, 2);
    $pdf->cell($pageWidth, 5, utf8_decode('2) Acta de Hechos,'), 0, 2);
    $pdf->cell($pageWidth, 5, utf8_decode('3) Certificado de Calidad,'), 0, 2);
    $pdf->cell($pageWidth, 5, utf8_decode('4) Trazabilidad,'), 0, 2);
    // $pdf->cell($pageWidth, 5, utf8_decode('5) Resultados de Laboratorio Certificado,'), 0, 2);
    $pdf->cell($pageWidth, 5, utf8_decode('5) Comprobante de Pago.'), 0, 2);

    $pdf->setXY($pdf->marginX, $pdf->getY() + 5);
    $pdf->SetFont('Arial', '', 8);
    $pdf->cell($pageWidth, 5, utf8_decode('*Origen de miel: Estado de Yucatán. Tipo de miel: ' . $tipo));

    $pdf->setXY($pdf->marginX, $pdf->getY() + 20);

    $pdf->Line($pdf->marginX, $pdf->getY(), $pdf->marginX + 50, $pdf->getY());
    $pdf->cell($pageWidth, 5, utf8_decode($nombre_gerente['nombre_completo']), 0, 2);
    $pdf->cell($pageWidth, 5, utf8_decode('Gerente de Planta'), 0, 2);
    $pdf->cell($pageWidth, 5, utf8_decode('Oaxaca Miel, S.A. de C.V.'), 0, 2);


    $pdf->SetY($pdf->footerY);
    $pdf->SetFont('Arial', '', 8);
    $pdf->Line($pdf->marginX, $pdf->getY(), $pdf->marginX + $pageWidth, $pdf->getY());
    $pdf->Ln();
    $pdf->cell($pageWidth, 5, utf8_decode('2ª Cerrada de Emiliano Zapata No. 19 Colonia Bosques del Sur, CP 16010 Delegación Xochimilco, México, D.F.'), 0, 2, 'C', 0);
    $pdf->cell($pageWidth, 5, utf8_decode('Tels.: 01 5556753305, 015556754796, 015555553588 Fax: 01 5555553589'), 0, 2, 'C', 0);

    // Comienza la página 1
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 13);
    $pdf->setY($startPage);
    $pdf->MultiCell($pageWidth, 5, utf8_decode('Solicitud para obtener el certificado para exportación de mercancía regulada en materia agrícola, pecuaria, acuícola y pesquera'), 0, 'C');


    // Primeras dos tablas, página 1
    $pdf->SetFont('Arial', '', 11);
    $pdf->setXY($pdf->marginX, 60);
    $pdf->cell($pageWidth / 3, 6, utf8_decode('Homoclave del formato'), 1, 0, 'C', 1);
    $pdf->cell($pageWidth / 3, 6, utf8_decode(''), 0, 0, 'C', 0);
    $pdf->cell($pageWidth / 3, 6, utf8_decode('Folio'), 1, 1, 'C', 1);

    $pdf->cell($pageWidth / 3, 9, utf8_decode('FF-SENASICA-001'), 'LBR', 0, 'C', 0);
    $pdf->cell($pageWidth / 3, 9, utf8_decode(''), 0, 0, 'C', 0);
    $pdf->cell($pageWidth / 3, 9, utf8_decode(''), 'LBR', 1, 'C', 0);


    $pdf->cell($pageWidth / 3, 6, utf8_decode('Fecha de publicación en el DOF'), 1, 0, 'C', 1);
    $pdf->cell($pageWidth / 3, 6, utf8_decode(''), 0, 0, 'C', 0);
    $pdf->cell($pageWidth / 3, 6, utf8_decode('Fecha de solicitud del trámite'), 1, 1, 'C', 1);


    $pdf->cell($pageWidth / 3, 9, utf8_decode('16/05/2016'), 'LBR', 0, 'C', 0);
    $pdf->cell($pageWidth / 3, 9, utf8_decode(''), 0, 0, 'C', 0);
    $pdf->cell($pageWidth / 3, 9, utf8_decode(date_format($fecha, "d/m/Y")), 'LBR', 0, 'C', 0);

    $pdf->Ln(15);
    $pdf->setX($pdf->marginX);
    $pdf->cell($pageWidth, 5, utf8_decode('Datos del exportador'), 1, 2, 'C', 1);

    // Tablas de los datos del exportador, página 1

    $pdf->SetFont('Arial', '', 11);
    $pdf->setXY($pdf->marginX, 105);
    $pdf->cell($middleWidthTable, 6, utf8_decode('Personas físicas'), 1, 2, 'C', 1);
    $pdf->SetFont('Arial', '', 10);
    $pdf->cell($middleWidthTable, 9, utf8_decode('RFC: '), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 9, utf8_decode('CURP: '), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 6, utf8_decode('Nombre (s): '), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 9, utf8_decode('Primer apellido: '), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 9, utf8_decode('Segundo apellido: '), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 9, utf8_decode('Sexo: '), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 9, utf8_decode('Fecha de nacimiento: DD/MM/AA'), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 9, utf8_decode('Lugar de nacimiento: '), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 9, utf8_decode('Teléfono (Lada y número): '), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 9, utf8_decode('Extensión: '), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 9, utf8_decode('Correo electrónico: '), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 9, utf8_decode('Teléfono móvil: '), 'LBR', 2, 'L', 0);


    $pdf->SetFont('Arial', '', 11);
    $pdf->setXY($pdf->marginX + $pageWidth / 2 + 3, 105);
    $pdf->cell($middleWidthTable, 6, utf8_decode('Personas morales'), 1, 2, 'C', 1);
    $pdf->SetFont('Arial', '', 10);
    $pdf->cell($middleWidthTable, 9, utf8_decode('RFC: OMI950913TV2'), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 9, utf8_decode('Razón Social: OAXACA MIEL S.A. DE C.V.'), 'LBR', 2, 'L', 0);

    $pdf->cell($middleWidthTable, 6, utf8_decode('Representante legal o apoderado'), 1, 2, 'C', 1);
    $pdf->cell($middleWidthTable, 9, utf8_decode('CURP: BACI851008HMNNHB05'), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 9, utf8_decode('RFC: BACI851008LK7'), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 9, utf8_decode('Nombre (s): ' . $nombre_gerente['nombres']), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 9, utf8_decode('Primer apellido: ' . $nombre_gerente['apellido_paterno']), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 9, utf8_decode('Segundo apellido: ' . $nombre_gerente['apellido_materno']), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 9, utf8_decode('Teléfono (Lada y número): 01 999 9880990'), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 9, utf8_decode('Extensión: NA'), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 9, utf8_decode('Correo electrónico: ' . $nombre_gerente['correo']), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 9, utf8_decode('Teléfono móvil: N/A'), 'LBR', 2, 'L', 0);

    // Leyenda, página 1 

    $pdf->setXY($pdf->marginX + 10, 250);
    $pdf->SetFont('Arial', 'I', 8);
    $pdf->MultiCell($pageWidth - 20, 4, utf8_decode('*De conformidad con los artículos 4 y 69-M, fracción V de la Ley Federal de Procedimiento Administrativo, los formatos para solicitar trámites y servicios deberán publicarse en el Diario Oficial de la Federación (DOF).'));


    // Comienza la página 2
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 11);
    $pdf->setXY($pdf->marginX, $startPage);
    $pdf->cell($pageWidth, 5, utf8_decode('Domicilio del exportador'), 1, 0, 'C', 1);

    // Tablas del docmicilio del exportador, página 2

    $pdf->SetFont('Arial', '', 10);
    $pdf->setXY($pdf->marginX, 50);
    $pdf->cell($middleWidthTable, 7, utf8_decode('Calle: 2A Cerrada de Emiliano Zapata'), 1, 2, 'L', 0);
    $pdf->cell($middleWidthTable / 2, 8, utf8_decode('Número exterior: 19'), 'LBR', 0, 'L', 0);
    $pdf->cell($middleWidthTable / 2, 8, utf8_decode('Número interior: '), 'BR', 1, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode('Código postal: 16010'), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode('Tipo de asentamiento:'), 'LR', 2, 'L', 0);
    $pdf->SetFont('Arial', '', 9);
    $pdf->Multicell($middleWidthTable, 7, utf8_decode('Colonia'), 'LBR', 2, 0);
    $pdf->SetFont('Arial', '', 10);
    $pdf->cell($middleWidthTable, 7, utf8_decode('Localidad: '), 'LR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode('Bosques del Sur'), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode('Municipio o delegación: '), 'LR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode('Xochimilco'), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode('Estado: CDMX'), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode('Entre que calles: '), 'LR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode('Privada del Bosque y Majuelos'), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode('Calle posterior: '), 'LR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 6, utf8_decode('1A Cerrada de Emiliano Zapata'), 'LBR', 2, 'L', 0);


    $pdf->setXY($pdf->marginX + $pageWidth / 2 + 3, 50);
    $pdf->SetFont('Arial', '', 10);
    $pdf->cell($middleWidthTable, 7, utf8_decode('En caso de ser carretera llenar la siguiente información'), 1, 2, 'C', 1);

    $pdf->cell($middleWidthTable, 7, utf8_decode('Tipo de administración (marcar con una X): '), 'LR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode('   Federal          Estatal          Municipal'), 'LBR', 2, 'L', 0);

    $pdf->cell($middleWidthTable, 7, utf8_decode('Derecho de tránsito (marcar con una X): '), 'LR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode('   Libre              Cuota'), 'LBR', 2, 'L', 0);

    $pdf->cell($middleWidthTable, 7, utf8_decode('Código de la carretera: N/A'), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode('Tramo de la carretera: N/A'), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode('Cadenamiento o kilómetro: N/A'), 'LBR', 2, 'L', 0);

    $pdf->cell($middleWidthTable, 7, utf8_decode('En caso de ser camino llenar la siguiente información'), 1, 2, 'C', 1);

    $pdf->cell($middleWidthTable, 7, utf8_decode('Término genérico (marcar con una X): '), 'LR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode('   Brecha           Camino         Terracería            Vereda'), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode('Tramo del camino: '), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode('Margen (marcar con una X): '), 'LR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode('   Derecho         Izquierdo'), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode('Cadenamiento o kilómetro: '), 'LBR', 2, 'L', 0);

    // Segunda sección, página 2
    $pdf->Ln(10);
    $pdf->cell($pageWidth, 5, utf8_decode('(Zoosanitario y acuícola)'), 1, 0, 'C', 1);
    $pdf->Ln(10);
    $pdf->cell($pageWidth, 5, utf8_decode('Datos del establecimiento productor o de origen'), 1, 0, 'C', 1);

    $pdf->setXY($pdf->marginX, 185);
    $pdf->cell($middleWidthTable, 7, utf8_decode('Nombre o razón social: Oaxaca Miel S.A. de C.V.'), 1, 2, 'L', 0);
    $pdf->cell($middleWidthTable, 14, utf8_decode('Número de establecimiento*: 31-08771-1'), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode('Calle: '), 'LR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 14, utf8_decode('Carretera Mérida - Cancún'), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable / 2, 7, utf8_decode('Número exterior: Km 7.5'), 'LBR', 0, 'L', 0);
    $pdf->cell($middleWidthTable / 2, 7, utf8_decode('Número interior: '), 'BR', 1, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode('Código postal: 97370'), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode('Tipo de asentamiento:'), 'LBR', 2, 'L', 0);

    $pdf->setXY($pdf->marginX + $pageWidth / 2 + 3, 185);
    $pdf->SetFont('Arial', '', 10);
    $pdf->cell($middleWidthTable, 7, utf8_decode('En caso de ser carretera llenar la siguiente información'), 1, 2, 'C', 1);

    $pdf->cell($middleWidthTable, 7, utf8_decode('Tipo de administración (marcar con una X): '), 'LR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode('   Federal          Estatal          Municipal'), 'LBR', 2, 'L', 0);

    $pdf->cell($middleWidthTable, 7, utf8_decode('Derecho de tránsito (marcar con una X): '), 'LR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode('   Libre              Cuota'), 'LBR', 2, 'L', 0);

    $pdf->cell($middleWidthTable, 7, utf8_decode('Código de la carretera: N/A'), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode('Tramo de la carretera: Kanasín'), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode('Cadenamiento o kilómetro: kilómetro 7.5'), 'LBR', 2, 'L', 0);

    $pdf->cell($middleWidthTable, 7, utf8_decode('En caso de ser camino llenar la siguiente información'), 1, 2, 'C', 1);


    // Input radio

    $pdf->SetFont('ZapfDingbats', '', 10);
    // Tipo de carretera
    $pdf->setXY($pdf->marginX + $pageWidth / 2 + 3, 65);
    $pdf->cell(5, 5, 'm', 0, 1, 'C', 0);

    $pdf->setXY($pdf->marginX + $pageWidth / 2 + 24, 65);
    $pdf->cell(5, 5, '5', 0, 1, 'C', 0);

    $pdf->setXY($pdf->marginX + $pageWidth / 2 + 45, 65);
    $pdf->cell(5, 5, 'm', 0, 1, 'C', 0);

    // Derecho tránsito carretera

    $pdf->setXY($pdf->marginX + $pageWidth / 2 + 3, 79);
    $pdf->cell(5, 5, '5', 0, 1, 'C', 0);

    $pdf->setXY($pdf->marginX + $pageWidth / 2 + 24, 79);
    $pdf->cell(5, 5, 'm', 0, 1, 'C', 0);

    // Tipo de camino

    $pdf->setXY($pdf->marginX + $pageWidth / 2 + 3, 121);
    $pdf->cell(5, 5, 'm', 0, 1, 'C', 0);

    $pdf->setXY($pdf->marginX + $pageWidth / 2 + 24, 121);
    $pdf->cell(5, 5, 'm', 0, 1, 'C', 0);

    $pdf->setXY($pdf->marginX + $pageWidth / 2 + 45, 121);
    $pdf->cell(5, 5, 'm', 0, 1, 'C', 0);

    $pdf->setXY($pdf->marginX + $pageWidth / 2 + 74, 121);
    $pdf->cell(5, 5, 'm', 0, 1, 'C', 0);

    // Derecho tránsito camino

    $pdf->setXY($pdf->marginX + $pageWidth / 2 + 3, 142);
    $pdf->cell(5, 5, 'm', 0, 1, 'C', 0);

    $pdf->setXY($pdf->marginX + $pageWidth / 2 + 24, 142);
    $pdf->cell(5, 5, 'm', 0, 1, 'C', 0);

    // Tipo de carretera
    $pdf->setXY($pdf->marginX + $pageWidth / 2 + 3, 200);
    $pdf->cell(5, 5, 'm', 0, 1, 'C', 0);

    $pdf->setXY($pdf->marginX + $pageWidth / 2 + 24, 200);
    $pdf->cell(5, 5, '5', 0, 1, 'C', 0);

    $pdf->setXY($pdf->marginX + $pageWidth / 2 + 45, 200);
    $pdf->cell(5, 5, 'm', 0, 1, 'C', 0);

    // Derecho tránsito carretera

    $pdf->setXY($pdf->marginX + $pageWidth / 2 + 3, 214);
    $pdf->cell(5, 5, '5', 0, 1, 'C', 0);

    $pdf->setXY($pdf->marginX + $pageWidth / 2 + 24, 214);
    $pdf->cell(5, 5, 'm', 0, 1, 'C', 0);


    // Comienza la página 3

    $pdf->addPage();


    $pdf->setXY($pdf->marginX, $startPage);
    $pdf->SetFont('Arial', '', 9);
    $pdf->Multicell($middleWidthTable, 7, utf8_decode('Planta Procesadora'), 1, 2, 0);
    $pdf->SetFont('Arial', '', 10);
    $pdf->cell($middleWidthTable, 7, utf8_decode('Localidad: San Pedro Noh Pat'), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode('Municipio o delegación: '), 'LR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode('Kanasín'), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 14, utf8_decode('Estado: Yucatán'), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode('Entre que calles: Calle 4 y 4b'), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode('Calle posterior: Calle 23'), 'LBR', 2, 'L', 0);

    $pdf->setXY($pdf->marginX + $pageWidth / 2 + 3, $startPage);

    $pdf->cell($middleWidthTable, 7, utf8_decode('Término genérico (marcar con una X): '), 'LTR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 14, utf8_decode('   Brecha           Camino         Terracería          Vereda'), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode('Tramo del camino: '), 'LR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode(''), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode('Margen (marcar con una X): '), 'LR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode('   Derecho         Izquierdo'), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode('Cadenamiento o kilómetro: '), 'LBR', 1, 'L', 0);

    // Datos del importador, página 3
    $pdf->Ln(10);
    $pdf->cell($pageWidth, 5, utf8_decode('Datos del importador'), 1, 0, 'C', 1);

    $pdf->setXY($pdf->marginX, 115);
    $pdf->cell($middleWidthTable, 5, utf8_decode('Nombre o razón social: '), 'LTR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 5, utf8_decode($cliente->nombre), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 14, utf8_decode('Número de establecimiento*: ' . $cliente->establecimiento), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 5, utf8_decode('Calle: '), 'LR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 14, utf8_decode($cliente->calle), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable / 2, 8, utf8_decode('Número exterior: ' . $cliente->numeroExterior), 'LBR', 0, 'L', 0);
    $pdf->cell($middleWidthTable / 2, 8, utf8_decode('Número interior: ' . $cliente->numeroInterior), 'BR', 1, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode('Código postal: ' . $cliente->codigoPostal), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 5, utf8_decode('Tipo de asentamiento: '), 'LR', 2, 'L', 0);
    $pdf->SetFont('Arial', '', 9);
    $pdf->Multicell($middleWidthTable, 5, utf8_decode($cliente->asentamiento), 'LBR', 2, 0);
    $pdf->SetFont('Arial', '', 10);
    $pdf->cell($middleWidthTable, 6, utf8_decode('Localidad: '), 'LR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 6, utf8_decode($cliente->localidad), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode('Municipio o delegación: '), 'LR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode($cliente->municipio), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode('Estado: ' . $cliente->estado), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode('Entre que calles: '), 'LR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode($cliente->entreCalles), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode('Calle posterior: '), 'LR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 6, utf8_decode($cliente->callePosterior), 'LBR', 2, 'L', 0);


    $pdf->setXY($pdf->marginX + $pageWidth / 2 + 3, 115);
    $pdf->SetFont('Arial', '', 10);
    $pdf->cell($middleWidthTable, 7, utf8_decode('En caso de ser carretera llenar la siguiente información'), 1, 2, 'C', 1);

    $pdf->cell($middleWidthTable, 7, utf8_decode('Tipo de administración (marcar con una X): '), 'LR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode('   Federal          Estatal          Municipal'), 'LBR', 2, 'L', 0);

    $pdf->cell($middleWidthTable, 7, utf8_decode('Derecho de tránsito (marcar con una X): '), 'LR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode('   Libre              Cuota'), 'LBR', 2, 'L', 0);

    $pdf->cell($middleWidthTable, 5, utf8_decode('Código de la carretera: '), 'LR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode($cliente->codigoCarretera), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 5, utf8_decode('Tramo de la carretera: '), 'LR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode($cliente->tramoCarretera), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 5, utf8_decode('Cadenamiento o kilómetro: '), 'LR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode($cliente->kilometroCarretera), 'LBR', 2, 'L', 0);

    $pdf->cell($middleWidthTable, 7, utf8_decode('En caso de ser camino llenar la siguiente información'), 1, 2, 'C', 1);

    $pdf->cell($middleWidthTable, 7, utf8_decode('Término genérico (marcar con una X): '), 'LR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode('    Brecha          Camino          Terracería          Vereda'), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 5, utf8_decode('Tramo del camino: '), 'LR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode($cliente->tramoCamino), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode('Margen (marcar con una X): '), 'LR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode('   Derecho          Izquierdo'), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 5, utf8_decode('Cadenamiento o kilómetro: '), 'LR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode($cliente->kilometroCamino), 'LBR', 2, 'L', 0);


    // Input radio

    $pdf->SetFont('ZapfDingbats', '', 10);

    // Tipo de camino

    $pdf->setXY($pdf->marginX + $pageWidth / 2 + 3, 49);
    $pdf->cell(5, 5, 'm', 0, 1, 'C', 0);

    $pdf->setXY($pdf->marginX + $pageWidth / 2 + 24, 49);
    $pdf->cell(5, 5, 'm', 0, 1, 'C', 0);

    $pdf->setXY($pdf->marginX + $pageWidth / 2 + 45, 49);
    $pdf->cell(5, 5, 'm', 0, 1, 'C', 0);

    $pdf->setXY($pdf->marginX + $pageWidth / 2 + 72, 49);
    $pdf->cell(5, 5, 'm', 0, 1, 'C', 0);

    // Margen camino

    $pdf->setXY($pdf->marginX + $pageWidth / 2 + 3, 81);
    $pdf->cell(5, 5, 'm', 0, 1, 'C', 0);

    $pdf->setXY($pdf->marginX + $pageWidth / 2 + 24, 81);
    $pdf->cell(5, 5, 'm', 0, 1, 'C', 0);

    // Datos del cliente
    // tipo de administración carretera
    $simbolo = $cliente->tipoAdministracion == '1' ? '5' : 'm';
    $pdf->setXY($pdf->marginX + $pageWidth / 2 + 3, 130);
    $pdf->cell(5, 5, $simbolo, 0, 1, 'C', 0);
    $simbolo = $cliente->tipoAdministracion == '2' ? '5' : 'm';
    $pdf->setXY($pdf->marginX + $pageWidth / 2 + 24, 130);
    $pdf->cell(5, 5, $simbolo, 0, 1, 'C', 0);
    $simbolo = $cliente->tipoAdministracion == '3' ? '5' : 'm';
    $pdf->setXY($pdf->marginX + $pageWidth / 2 + 45, 130);
    $pdf->cell(5, 5, $simbolo, 0, 1, 'C', 0);

    // Derecho de tránsito carretera
    $simbolo = $cliente->derechoTransito == '1' ? '5' : 'm';
    $pdf->setXY($pdf->marginX + $pageWidth / 2 + 3, 144);
    $pdf->cell(5, 5, $simbolo, 0, 1, 'C', 0);
    $simbolo = $cliente->derechoTransito == '2' ? '5' : 'm';
    $pdf->setXY($pdf->marginX + $pageWidth / 2 + 24, 144);
    $pdf->cell(5, 5, $simbolo, 0, 1, 'C', 0);

    // Termino genérico camino

    $simbolo = $cliente->terminoGenerico == '1' ? '5' : 'm';
    $pdf->setXY($pdf->marginX + $pageWidth / 2 + 3, 201);
    $pdf->cell(5, 5, $simbolo, 0, 1, 'C', 0);

    $simbolo = $cliente->terminoGenerico == '2' ? '5' : 'm';
    $pdf->setXY($pdf->marginX + $pageWidth / 2 + 24, 201);
    $pdf->cell(5, 5, $simbolo, 0, 1, 'C', 0);

    $simbolo = $cliente->terminoGenerico == '3' ? '5' : 'm';
    $pdf->setXY($pdf->marginX + $pageWidth / 2 + 45, 201);
    $pdf->cell(5, 5, $simbolo, 0, 1, 'C', 0);

    $simbolo = $cliente->terminoGenerico == '4' ? '5' : 'm';
    $pdf->setXY($pdf->marginX + $pageWidth / 2 + 72, 201);
    $pdf->cell(5, 5, $simbolo, 0, 1, 'C', 0);


    $simbolo = $cliente->margen == '1' ? '5' : 'm';
    $pdf->setXY($pdf->marginX + $pageWidth / 2 + 3, 227);
    $pdf->cell(5, 5, $simbolo, 0, 1, 'C', 0);
    $simbolo = $cliente->margen == '2' ? '5' : 'm';
    $pdf->setXY($pdf->marginX + $pageWidth / 2 + 24, 227);
    $pdf->cell(5, 5, $simbolo, 0, 1, 'C', 0);


    // Comienza página 4

    $pdf->addPage();
    $pdf->SetFont('Arial', '', 9);
    $pdf->setXY($pdf->marginX, $startPage);
    $pdf->cell($pageWidth, 5, utf8_decode('Datos del destinatario o establecimiento de destino'), 1, 0, 'C', 1);

    $pdf->setXY($pdf->marginX, 50);
    $pdf->cell($middleWidthTable, 7, utf8_decode('Nombre o razón social: ' . $destino->nombreDestino), 1, 2, 'L', 0);
    $pdf->cell($middleWidthTable, 6, utf8_decode('Número de establecimiento*: '), 'LR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 8, utf8_decode($destino->numeroEstablecimiento), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 6, utf8_decode('Calle: '), 'LR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 15, utf8_decode($destino->calleDestino), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable / 2, 8, utf8_decode('Número exterior: ' . $destino->numeroExteriorDestino), 'LBR', 0, 'L', 0);
    $pdf->cell($middleWidthTable / 2, 8, utf8_decode('Número interior: ' . $destino->numeroInteriorDestino), 'BR', 1, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode('Código postal: ' . $destino->codigoPostalDestino), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 5, utf8_decode('Tipo de asentamiento: '), 'LR', 2, 'L', 0);
    $pdf->SetFont('Arial', '', 9);
    $pdf->Multicell($middleWidthTable, 5, utf8_decode($destino->asentamientoDestino), 'LBR', 2, 0);
    $pdf->SetFont('Arial', '', 10);
    $pdf->cell($middleWidthTable, 6, utf8_decode('Localidad: '), 'LR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 6, utf8_decode($destino->localidadDestino), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode('Municipio o delegación: '), 'LR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode($destino->municipioDestino), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode('Estado: ' . $destino->estadoDestino), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode('Entre que calles: '), 'LR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode($destino->entreCallesDestino), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode('Calle posterior: '), 'LR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 6, utf8_decode($destino->callePosteriorDestino), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable / 2, 8, utf8_decode('Lada: ' . $destino->ladaDestino), 'LBR', 0, 'L', 0);
    $pdf->cell($middleWidthTable / 2, 8, utf8_decode('Teléfono fijo: ' . $destino->telefonoDestino), 'BR', 1, 'L', 0);
    $pdf->cell($middleWidthTable, 5, utf8_decode('Correo electrónico: '), 'LR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode($destino->correoDestino), 'LBR', 2, 'L', 0);

    $pdf->setXY($pdf->marginX + $pageWidth / 2 + 3, 50);
    $pdf->SetFont('Arial', '', 10);
    $pdf->cell($middleWidthTable, 7, utf8_decode('En caso de ser carretera llenar la siguiente información'), 1, 2, 'C', 1);
    $pdf->cell($middleWidthTable, 7, utf8_decode('Tipo de administración (marcar con una X): '), 'LR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode('   Federal          Estatal          Municipal'), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode('Derecho de tránsito (marcar con una X): '), 'LR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode('   Libre              Cuota'), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 5, utf8_decode('Código de la carretera: '), 'LR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode($destino->codigoCarreteraDestino), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 5, utf8_decode('Tramo de la carretera: '), 'LR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode($destino->tramoCarreteraDestino), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 5, utf8_decode('Cadenamiento o kilómetro: '), 'LR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode($destino->kilometroCarreteraDestino), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode('En caso de ser camino llenar la siguiente información'), 1, 2, 'C', 1);
    $pdf->cell($middleWidthTable, 7, utf8_decode('Término genérico (marcar con una X): '), 'LR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode('     Brecha         Camino         Terracería          Vereda'), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 5, utf8_decode('Tramo del camino: '), 'LR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode($destino->tramoCaminoDestino), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode('Margen (marcar con una X): '), 'LR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode('   Derecho         Izquierdo'), 'LBR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 5, utf8_decode('Cadenamiento: '), 'LR', 2, 'L', 0);
    $pdf->cell($middleWidthTable, 7, utf8_decode($destino->kilometroCaminoDestino), 'LBR', 2, 'L', 0);

    // Datos del destino
    $pdf->SetFont('ZapfDingbats', '', 10);
    // tipo de administración carretera
    $simbolo = $destino->administracionDestino == '1' ? '5' : 'm';
    $pdf->setXY($pdf->marginX + $pageWidth / 2 + 3, 65);
    $pdf->cell(5, 5, $simbolo, 0, 1, 'C', 0);
    $simbolo = $destino->administracionDestino == '2' ? '5' : 'm';
    $pdf->setXY($pdf->marginX + $pageWidth / 2 + 24, 65);
    $pdf->cell(5, 5, $simbolo, 0, 1, 'C', 0);
    $simbolo = $destino->administracionDestino == '3' ? '5' : 'm';
    $pdf->setXY($pdf->marginX + $pageWidth / 2 + 45, 65);
    $pdf->cell(5, 5, $simbolo, 0, 1, 'C', 0);

    // Derecho de tránsito carretera
    $simbolo = $destino->transitoDestino == '1' ? '5' : 'm';
    $pdf->setXY($pdf->marginX + $pageWidth / 2 + 3, 79);
    $pdf->cell(5, 5, $simbolo, 0, 1, 'C', 0);
    $simbolo = $destino->transitoDestino == '2' ? '5' : 'm';
    $pdf->setXY($pdf->marginX + $pageWidth / 2 + 24, 79);
    $pdf->cell(5, 5, $simbolo, 0, 1, 'C', 0);

    // Termino genérico camino

    $simbolo = $destino->terminoGenericoDestino == '1' ? '5' : 'm';
    $pdf->setXY($pdf->marginX + $pageWidth / 2 + 3, 136);
    $pdf->cell(5, 5, $simbolo, 0, 1, 'C', 0);

    $simbolo = $destino->terminoGenericoDestino == '2' ? '5' : 'm';
    $pdf->setXY($pdf->marginX + $pageWidth / 2 + 24, 136);
    $pdf->cell(5, 5, $simbolo, 0, 1, 'C', 0);

    $simbolo = $destino->terminoGenericoDestino == '3' ? '5' : 'm';
    $pdf->setXY($pdf->marginX + $pageWidth / 2 + 45, 136);
    $pdf->cell(5, 5, $simbolo, 0, 1, 'C', 0);

    $simbolo = $destino->terminoGenericoDestino == '4' ? '5' : 'm';
    $pdf->setXY($pdf->marginX + $pageWidth / 2 + 72, 136);
    $pdf->cell(5, 5, $simbolo, 0, 1, 'C', 0);


    $simbolo = $destino->margenDestino == '1' ? '5' : 'm';
    $pdf->setXY($pdf->marginX + $pageWidth / 2 + 3, 162);
    $pdf->cell(5, 5, $simbolo, 0, 1, 'C', 0);
    $simbolo = $destino->margenDestino == '2' ? '5' : 'm';
    $pdf->setXY($pdf->marginX + $pageWidth / 2 + 24, 162);
    $pdf->cell(5, 5, $simbolo, 0, 1, 'C', 0);


    // Comienza página 5
    $pdf->addpage();

    $pdf->setXY($pdf->marginX, $startPage);
    $pdf->Image('../img/animales_vivos.jpg', $pdf->marginX, $startPage, $pageWidth);


    // Tabla de producto o subproductos
    $Y_nueva_tabla = 130;
    $pdf->setY($Y_nueva_tabla);
    $pdf->SetFont('Arial', '', 10);
    $pdf->cell($pageWidth, 6, utf8_decode('Producto o subproducto'), 1, 2, 'C', 1);
    $pdf->SetFont('Arial', '', 9);
    $pdf->cell($pageWidth / 2, 5, utf8_decode('Especie: NA'), 'LRB', 2, 'L', 0);

    $pdf->cell($pageWidth / 2, 5, utf8_decode('País de destino'), 'LR', 2, 'L', 0);
    $pdf->cell($pageWidth / 2, 5, utf8_decode($producto->paisDestino), 'LBR', 2, 'L', 0);

    $pdf->cell($pageWidth, 5, utf8_decode('Finalidad o uso de la mercancía:'), 'LR', 2, 'L', 0);
    $pdf->cell($pageWidth, 5, utf8_decode($producto->finalidadMercancia), 'LBR', 2, 'L', 0);

    $pdf->setXY($pdf->marginX + $pageWidth / 2, $Y_nueva_tabla + 6);
    $pdf->cell($pageWidth / 2, 5, utf8_decode('Producto a exportar: NA'), 'LRB', 2, 'L', 0);

    $pdf->cell($pageWidth / 2, 5, utf8_decode('País de origen del producto: '), 'LR', 2, 'L', 0);
    $pdf->cell($pageWidth / 2, 5, utf8_decode('País de origen de la materia prima: '), 'LBR', 1, 'L', 0);
    $pdf->setXY($pageWidth / 2 + 25, $pdf->getY() - 8);
    $pdf->cell($pageWidth / 2, 5, utf8_decode($producto->origenMP), 0, null, 'C', 0);

    // $pdf->setXY($pdf->marginX + $pageWidth / 2, $Y_nueva_tabla + 16);


    // Última tabla página 5

    $tablaY = 170;
    $pdf->setXY($pdf->marginX, $tablaY);

    $pdf->SetFont('Arial', '', 7);

    $pdf->cell($fifthPart, 4, utf8_decode(''), 'LTR', 2, 'C', 1);
    $pdf->cell($fifthPart, 4, utf8_decode('Nombre de la mercancía'), 'LR', 2, 'C', 1);
    $pdf->cell($fifthPart, 4, utf8_decode(''), 'LBR', 0, 'C', 1);

    $pdf->setXY($pdf->marginX + $fifthPart, $tablaY);

    $pdf->cell($fifthPart, 4, utf8_decode('Identificación o'), 'LTR', 2, 'C', 1);
    $pdf->cell($fifthPart, 4, utf8_decode('Número de lote'), 'LR', 2, 'C', 1);
    $pdf->cell($fifthPart, 4, utf8_decode(''), 'LBR', 0, 'C', 1);


    $pdf->setXY($pdf->marginX + $fifthPart * 2, $tablaY);

    $pdf->cell($fifthPart, 4, utf8_decode(''), 'LTR', 2, 'C', 1);
    $pdf->cell($fifthPart, 4, utf8_decode('Presentación'), 'LR', 2, 'C', 1);
    $pdf->cell($fifthPart, 4, utf8_decode(''), 'LBR', 0, 'C', 1);

    $pdf->setXY($pdf->marginX + $fifthPart * 3, $tablaY);

    $pdf->cell($fifthPart / 2, 4, utf8_decode(''), 'LTR', 2, 'C', 1);
    $pdf->cell($fifthPart / 2, 4, utf8_decode('Cantidad'), 'LR', 2, 'C', 1);
    $pdf->cell($fifthPart / 2, 4, utf8_decode(''), 'LBR', 0, 'C', 1);

    $pdf->setXY($pdf->marginX + $fifthPart * 3.5, $tablaY);

    $pdf->cell($fifthPart / 2, 4, utf8_decode('Unidad de'), 'LTR', 2, 'C', 1);
    $pdf->cell($fifthPart / 2, 4, utf8_decode('medida'), 'LR', 2, 'C', 1);
    $pdf->cell($fifthPart / 2, 4, utf8_decode(''), 'LBR', 0, 'C', 1);

    // $pdf->setXY($pdf->marginX + $fifthPart * 4, $tablaY);
    // $pdf->cell($fifthPart, 4, utf8_decode(''), 'LTR', 2, 'C', 1);
    // $pdf->cell($fifthPart, 4, utf8_decode('Peso neto*'), 'LR', 2, 'C', 1);
    // $pdf->cell($fifthPart, 4, utf8_decode(''), 'LBR', 0, 'C', 1);

    $pdf->setXY($pdf->marginX + $fifthPart * 4, $tablaY);

    $pdf->cell($fifthPart / 2, 4, utf8_decode(''), 'LTR', 2, 'C', 1);
    $pdf->cell($fifthPart / 2, 4, utf8_decode('Peso Bruto*'), 'LR', 2, 'C', 1);
    $pdf->cell($fifthPart / 2, 4, utf8_decode(''), 'LBR', 0, 'C', 1);

    $pdf->setXY($pdf->marginX + $fifthPart * 4.5, $tablaY);

    $pdf->cell($fifthPart / 2, 4, utf8_decode(''), 'LTR', 2, 'C', 1);
    $pdf->cell($fifthPart / 2, 4, utf8_decode('Peso Neto*'), 'LR', 2, 'C', 1);
    $pdf->cell($fifthPart / 2, 4, utf8_decode(''), 'LBR', 0, 'C', 1);



    // Contenido de la tabla
    $pdf->setXY($pdf->marginX, $tablaY + 12);
    $pdf->SetFont('Arial', '', 7);
    $pdf->SetLineWidth(.01);
    $pdf->SetWidths(array($fifthPart, $fifthPart, $fifthPart, $fifthPart / 2, $fifthPart / 2, $fifthPart / 2, $fifthPart / 2));

    $pdf->Row(array(utf8_decode($producto->producto), utf8_decode($producto->marcaDistintiva), utf8_decode($producto->presentacion), utf8_decode(number_format($producto->cantidad, 2, '.', ',')), utf8_decode($producto->unidadMedida), utf8_decode(number_format($producto->pesoBruto, 2, '.', ',')), utf8_decode(number_format($producto->pesoNeto, 2, '.', ','))));
    $pdf->Row(array('', '', '', '', '', ''));


    // Encabezado de la tabla 2 parte

    $tablaY = $pdf->getY();
    $pdf->setXY($pdf->marginX, $tablaY);

    $pdf->SetFont('Arial', '', 7);

    $pdf->cell($fifthPart, 4, utf8_decode('Fecha de caducidad'), 'LTR', 2, 'C', 1);
    $pdf->cell($fifthPart, 4, utf8_decode('o'), 'LR', 2, 'C', 1);
    $pdf->cell($fifthPart, 4, utf8_decode('Consumo preferente'), 'LBR', 0, 'C', 1);

    $pdf->setXY($pdf->marginX + $fifthPart, $tablaY);

    $pdf->cell($fifthPart, 4, utf8_decode(''), 'LTR', 2, 'C', 1);
    $pdf->cell($fifthPart, 4, utf8_decode('Tratamiento/Proceso*'), 'LR', 2, 'C', 1);
    $pdf->cell($fifthPart, 4, utf8_decode(''), 'LBR', 0, 'C', 1);

    $pdf->setXY($pdf->marginX + $fifthPart * 2, $tablaY);

    $pdf->cell($fifthPart, 4, utf8_decode('Condiciones de almacén'), 'LTR', 2, 'C', 1);
    $pdf->cell($fifthPart, 4, utf8_decode('o transporte (refrigeración,'), 'LR', 2, 'C', 1);
    $pdf->cell($fifthPart, 4, utf8_decode('congelación o ambiente):'), 'LBR', 0, 'C', 1);

    $pdf->setXY($pdf->marginX + $fifthPart * 3, $tablaY);

    $pdf->cell($fifthPart, 4, utf8_decode('Número de'), 'LTR', 2, 'C', 1);
    $pdf->cell($fifthPart, 4, utf8_decode('autorización CITES*'), 'LR', 2, 'C', 1);
    $pdf->cell($fifthPart, 4, utf8_decode(''), 'LBR', 0, 'C', 1);

    $pdf->setXY($pdf->marginX + $fifthPart * 4, $tablaY);

    $pdf->cell($fifthPart / 2, 4, utf8_decode('Marcas'), 'LTR', 2, 'C', 1);
    $pdf->cell($fifthPart / 2, 4, utf8_decode('de embarque*'), 'LR', 2, 'C', 1);
    $pdf->cell($fifthPart / 2, 4, utf8_decode(''), 'LBR', 0, 'C', 1);

    $pdf->setXY($pdf->marginX + $fifthPart * 4.5, $tablaY);

    $pdf->cell($fifthPart / 2, 4, utf8_decode('Número'), 'LTR', 2, 'C', 1);
    $pdf->cell($fifthPart / 2, 4, utf8_decode('de fleje*'), 'LR', 2, 'C', 1);
    $pdf->cell($fifthPart / 2, 4, utf8_decode(''), 'LBR', 0, 'C', 1);

    $pdf->Ln();
    // Contenido de la tabla
    $pdf->setXY($pdf->marginX, $pdf->getY());
    $pdf->SetFont('Arial', '', 7);
    $pdf->SetLineWidth(.1);
    $pdf->SetWidths(array($fifthPart, $fifthPart, $fifthPart, $fifthPart, $fifthPart / 2, $fifthPart / 2));

    $pdf->Row(array(utf8_decode('N/A'), utf8_decode($producto->tratamientoProceso), utf8_decode('Ambiente'), utf8_decode('AI-279/2013'), utf8_decode($transporte->marcasEmbarque), utf8_decode($transporte->numeroFleje)));
    $pdf->Row(array('', '', '', '', '', ''));

    // Termina página 5
    // Comienza página 6
    $pdf->addpage();
    $pdf->SetFont('Arial', '', 10);

    $pdf->setXY($pdf->marginX, $startPage);
    $pdf->cell($pageWidth, 6, utf8_decode('Productos biológicos, químicos, farmacéuticos y alimenticios'), 1, 2, 'C', 1);
    $pdf->SetFont('Arial', '', 9);
    $pdf->cell($pageWidth / 2, 5, utf8_decode('Producto a exportar:'), 'LR', 2, 'L', 0);
    $pdf->cell($pageWidth / 2, 5, utf8_decode(''), 'LBR', 2, 'L', 0);
    $pdf->cell($pageWidth / 2, 5, utf8_decode('País de origen del producto: '), 'LR', 2, 'L', 0);
    $pdf->cell($pageWidth / 2, 7, utf8_decode('País de origen de la materia prima: '), 'LBR', 1, 'L', 0);
    $pdf->setXY($pdf->getX() + 15, $pdf->getY() - 10);
    $pdf->cell($pageWidth / 2, 7, utf8_decode(''), 0, null, 'C', 0);
    $pdf->setXY($pdf->marginX + $pageWidth / 2, $startPage + 6);
    $pdf->cell($pageWidth / 2, 5, utf8_decode('País de destino'), 'LR', 2, 'L', 0);
    $pdf->cell($pageWidth / 2, 5, utf8_decode(''), 'LBR', 2, 'L', 0);
    $pdf->setXY($pdf->marginX + $pageWidth / 2, $startPage + 16);
    $pdf->cell($pageWidth / 2, 5, utf8_decode('Finalidad o uso de la mercancía:'), 'LR', 2, 'L', 0);
    $pdf->cell($pageWidth / 2, 7, utf8_decode(''), 'LBR', 2, 'L', 0);

    // Encabezado de la tabla 1 parte

    $tablaY = 70;
    $pdf->setXY($pdf->marginX, $tablaY);

    $pdf->SetFont('Arial', '', 7);

    $pdf->cell($fifthPart, 4, utf8_decode(''), 'LTR', 2, 'C', 1);
    $pdf->cell($fifthPart, 4, utf8_decode('Nombre de la mercancía'), 'LR', 2, 'C', 1);
    $pdf->cell($fifthPart, 4, utf8_decode(''), 'LBR', 0, 'C', 1);

    $pdf->setXY($pdf->marginX + $fifthPart, $tablaY);

    $pdf->cell($fifthPart, 4, utf8_decode(''), 'LTR', 2, 'C', 1);
    $pdf->cell($fifthPart, 4, utf8_decode('Número de lote'), 'LR', 2, 'C', 1);
    $pdf->cell($fifthPart, 4, utf8_decode(''), 'LBR', 0, 'C', 1);

    $pdf->setXY($pdf->marginX + $fifthPart * 2, $tablaY);

    $pdf->cell($fifthPart, 4, utf8_decode('Número de'), 'LTR', 2, 'C', 1);
    $pdf->cell($fifthPart, 4, utf8_decode('registro/autorización'), 'LR', 2, 'C', 1);
    $pdf->cell($fifthPart, 4, utf8_decode('SAGARPA'), 'LBR', 0, 'C', 1);

    $pdf->setXY($pdf->marginX + $fifthPart * 3, $tablaY);

    $pdf->cell($fifthPart, 4, utf8_decode(''), 'LTR', 2, 'C', 1);
    $pdf->cell($fifthPart, 4, utf8_decode('Presentación'), 'LR', 2, 'C', 1);
    $pdf->cell($fifthPart, 4, utf8_decode(''), 'LBR', 0, 'C', 1);

    $pdf->setXY($pdf->marginX + $fifthPart * 4, $tablaY);

    $pdf->cell($fifthPart / 2, 4, utf8_decode(''), 'LTR', 2, 'C', 1);
    $pdf->cell($fifthPart / 2, 4, utf8_decode('Cantidad'), 'LR', 2, 'C', 1);
    $pdf->cell($fifthPart / 2, 4, utf8_decode(''), 'LBR', 0, 'C', 1);

    $pdf->setXY($pdf->marginX + $fifthPart * 4.5, $tablaY);

    $pdf->cell($fifthPart / 2, 4, utf8_decode('Unidad de'), 'LTR', 2, 'C', 1);
    $pdf->cell($fifthPart / 2, 4, utf8_decode('medida'), 'LR', 2, 'C', 1);
    $pdf->cell($fifthPart / 2, 4, utf8_decode(''), 'LBR', 0, 'C', 1);


    // Contenido de la tabla
    $pdf->setXY($pdf->marginX, 82);
    $pdf->SetFont('Arial', '', 7);
    $pdf->SetLineWidth(.01);
    $pdf->SetWidths(array($fifthPart, $fifthPart, $fifthPart, $fifthPart, $fifthPart / 2, $fifthPart / 2));

    // $pdf->Row(array(utf8_decode($producto->producto), utf8_decode($producto->marcaDistintiva), 'AI-279/2013', utf8_decode($producto->presentacion), utf8_decode(number_format($producto->cantidad, 2, '.', ',')), utf8_decode($producto->unidadMedida)));
    $pdf->Row(array('', '', '', '', '', ''));
    $pdf->Row(array('', '', '', '', '', ''));



    // Encabezado de la tabla 2 parte

    $tablaY = $pdf->getY();
    $pdf->setXY($pdf->marginX, $tablaY);

    $pdf->SetFont('Arial', '', 7);

    $pdf->cell($fifthPart, 4, utf8_decode(''), 'LTR', 2, 'C', 1);
    $pdf->cell($fifthPart, 4, utf8_decode('Peso neto*'), 'LR', 2, 'C', 1);
    $pdf->cell($fifthPart, 4, utf8_decode(''), 'LBR', 0, 'C', 1);

    $pdf->setXY($pdf->marginX + $fifthPart, $tablaY);

    $pdf->cell($fifthPart, 4, utf8_decode(''), 'LTR', 2, 'C', 1);
    $pdf->cell($fifthPart, 4, utf8_decode('Fecha de caducidad'), 'LR', 2, 'C', 1);
    $pdf->cell($fifthPart, 4, utf8_decode(''), 'LBR', 0, 'C', 1);

    $pdf->setXY($pdf->marginX + $fifthPart * 2, $tablaY);

    $pdf->cell($fifthPart, 4, utf8_decode(''), 'LTR', 2, 'C', 1);
    $pdf->cell($fifthPart, 4, utf8_decode('Tratamiento/Proceso*'), 'LR', 2, 'C', 1);
    $pdf->cell($fifthPart, 4, utf8_decode(''), 'LBR', 0, 'C', 1);

    $pdf->setXY($pdf->marginX + $fifthPart * 3, $tablaY);

    $pdf->cell($fifthPart, 4, utf8_decode('Condiciones de almacén'), 'LTR', 2, 'C', 1);
    $pdf->cell($fifthPart, 4, utf8_decode('o transporte (refrigeración,'), 'LR', 2, 'C', 1);
    $pdf->cell($fifthPart, 4, utf8_decode('congelación o ambiente):'), 'LBR', 0, 'C', 1);

    $pdf->setXY($pdf->marginX + $fifthPart * 4, $tablaY);

    $pdf->cell($fifthPart / 2, 4, utf8_decode('Marcas'), 'LTR', 2, 'C', 1);
    $pdf->cell($fifthPart / 2, 4, utf8_decode('de embarque*'), 'LR', 2, 'C', 1);
    $pdf->cell($fifthPart / 2, 4, utf8_decode(''), 'LBR', 0, 'C', 1);

    $pdf->setXY($pdf->marginX + $fifthPart * 4.5, $tablaY);

    $pdf->cell($fifthPart / 2, 4, utf8_decode('Número'), 'LTR', 2, 'C', 1);
    $pdf->cell($fifthPart / 2, 4, utf8_decode('de fleje*'), 'LR', 2, 'C', 1);
    $pdf->cell($fifthPart / 2, 4, utf8_decode(''), 'LBR', 0, 'C', 1);

    $pdf->Ln();
    // Contenido de la tabla
    $pdf->setXY($pdf->marginX, $pdf->getY());
    $pdf->SetFont('Arial', '', 7);
    $pdf->SetLineWidth(.1);
    $pdf->SetWidths(array($fifthPart, $fifthPart, $fifthPart, $fifthPart, $fifthPart / 2, $fifthPart / 2));

    // $pdf->Row(array(utf8_decode(number_format($producto->pesoNeto, 2, '.', ',')), utf8_decode('N/A'), utf8_decode($producto->tratamientoProceso), utf8_decode('Ambiente'), utf8_decode($transporte->marcasEmbarque), utf8_decode($transporte->numeroFleje)));
    $pdf->Row(array('', '', '', '', '', ''));
    $pdf->Row(array('', '', '', '', '', ''));

    $pdf->Image('../img/productos_biologicos.jpg', $pdf->marginX, $pdf->getY() + 5, $pageWidth);


    // Comienza página 7
    $pdf->addpage();

    $pdf->setXY($pdf->marginX, $startPage);
    $pdf->Image('../img/productos_subproductos.jpg', $pdf->marginX, $startPage, $pageWidth);

    // Comienza página 8
    $pdf->addpage();
    $pdf->SetFont('Arial', '', 10);
    $pdf->setXY($pdf->marginX, $startPage);
    $pdf->cell($pageWidth, 5, utf8_decode('Datos generales de la mercancía fitosanitaria a exportar'), 1, 0, 'C', 1);

    $pdf->setXY($pdf->marginX, $startPage + 10);
    $pdf->cell($pageWidth, 6, utf8_decode('Productos o subproductos'), 1, 2, 'C', 1);
    $pdf->cell($pageWidth / 2, 5, utf8_decode('País de destino: '), 'LR', 2, 'L', 0);
    $pdf->cell($pageWidth / 2, 5, utf8_decode($producto->paisDestino), 'LBR', 2, 'L', 0);
    $pdf->cell($pageWidth / 2, 5, utf8_decode('País y estado de procedencia (en caso de reexportación): '), 'LR', 2, 'L', 0);
    $pdf->cell($pageWidth / 2, 7, utf8_decode($producto->paisProcedencia), 'LBR', 2, 'L', 0);

    $pdf->setXY($pdf->marginX + $pageWidth / 2, $startPage + 16);
    $pdf->MultiCell($pageWidth / 2, 5, utf8_decode('País y estado de origen del producto o subproducto (en caso de reexportación): ' . $producto->paisOrigen), 'LBR', 'L', 0);

    $pdf->setXY($pdf->marginX + $pageWidth / 2, $startPage + 26);
    $pdf->cell($pageWidth / 2, 5, utf8_decode('Finalidad u objetivo de la mercancía: '), 'LR', 2, 'L', 0);
    $pdf->cell($pageWidth / 2, 7, utf8_decode($producto->finalidadMercancia), 'LBR', 2, 'L', 0);

    $pdf->setXY($pdf->marginX, 80);

    // Encabezado de la tabla
    $pdf->SetFont('Arial', '', 7);

    $pdf->cell($sevenPart * 2, 4, utf8_decode('Producto o'), 'LTR', 2, 'C', 1);
    $pdf->cell($sevenPart * 2, 4, utf8_decode('subproducto'), 'LR', 2, 'C', 1);
    $pdf->cell($sevenPart * 2, 4, utf8_decode(''), 'LBR', 0, 'C', 1);

    $pdf->setXY($pdf->marginX + $sevenPart * 2, 80);

    $pdf->cell($sevenPart, 4, utf8_decode('Especie (nombre '), 'LTR', 2, 'C', 1);
    $pdf->cell($sevenPart, 4, utf8_decode('científico)'), 'LR', 2, 'C', 1);
    $pdf->cell($sevenPart, 4, utf8_decode(''), 'LBR', 0, 'C', 1);

    $pdf->setXY($pdf->marginX + $sevenPart * 3, 80);

    $pdf->cell($sevenPart, 4, utf8_decode(''), 'LTR', 2, 'C', 1);
    $pdf->cell($sevenPart, 4, utf8_decode('Presentación'), 'LR', 2, 'C', 1);
    $pdf->cell($sevenPart, 4, utf8_decode(''), 'LBR', 0, 'C', 1);

    $pdf->setXY($pdf->marginX + $sevenPart * 4, 80);

    $pdf->cell($sevenPart / 2, 4, utf8_decode(''), 'LTR', 2, 'C', 1);
    $pdf->cell($sevenPart / 2, 4, utf8_decode('Cantidad'), 'LR', 2, 'C', 1);
    $pdf->cell($sevenPart / 2, 4, utf8_decode(''), 'LBR', 0, 'C', 1);

    $pdf->setXY($pdf->marginX + $sevenPart * 4.5, 80);

    $pdf->cell($sevenPart / 2, 4, utf8_decode('Unidad de'), 'LTR', 2, 'C', 1);
    $pdf->cell($sevenPart / 2, 4, utf8_decode('medida'), 'LR', 2, 'C', 1);
    $pdf->cell($sevenPart / 2, 4, utf8_decode(''), 'LBR', 0, 'C', 1);

    $pdf->setXY($pdf->marginX + $sevenPart * 5, 80);

    $pdf->cell($sevenPart, 4, utf8_decode(''), 'LTR', 2, 'C', 1);
    $pdf->cell($sevenPart, 4, utf8_decode('Marca distintiva*'), 'LR', 2, 'C', 1);
    $pdf->cell($sevenPart, 4, utf8_decode(''), 'LBR', 0, 'C', 1);

    $pdf->setXY($pdf->marginX + $sevenPart * 6, 80);

    $pdf->cell($sevenPart, 4, utf8_decode('Número de'), 'LTR', 2, 'C', 1);
    $pdf->cell($sevenPart, 4, utf8_decode(' dictamen de'), 'LR', 2, 'C', 1);
    $pdf->cell($sevenPart, 4, utf8_decode('verificación'), 'LBR', 1, 'C', 1);
    $pdf->SetWidths(array($sevenPart * 2, $sevenPart, $sevenPart, $sevenPart / 2, $sevenPart / 2, $sevenPart, $sevenPart));
    $pdf->Row(array(utf8_decode($producto->producto), utf8_decode($producto->especie), utf8_decode($producto->presentacion), utf8_decode(number_format($producto->cantidad, 2, '.', ',')), utf8_decode($producto->unidadMedida), utf8_decode($producto->marcaDistintiva), utf8_decode($producto->numeroVerificacion)));
    $pdf->Row(array('', '', '', '', '', '', ''));

    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->cell($pageWidth, 5, utf8_decode('*Incluir información cuando aplique.'), 0, 1, 'L', 0);
    $pdf->Ln(5);

    $pdf->SetFont('Arial', '', 10);
    $pdf->cell($pageWidth, 5, utf8_decode('Unidad expedidora donde realizará el trámite (oficina y estado): '), 'LTR', 2, 'L', 0);
    $pdf->cell($pageWidth, 5, utf8_decode($producto->lugarTramite), 'LBR', 2, 'L', 0);

    $tablaTransporte = $pdf->getY() + 10;
    $pdf->setXY($pdf->marginX, $tablaTransporte);

    $pdf->cell($pageWidth, 6, utf8_decode('Información del Transporte'), 1, 2, 'C', 1);
    $pdf->cell($pageWidth / 2, 5, utf8_decode('Medio de transporte: '), 'LR', 2, 'L', 0);
    $pdf->cell($pageWidth / 2, 5, utf8_decode($transporte->medioTransporte), 'LBR', 2, 'L', 0);
    $pdf->cell($pageWidth / 2, 5, utf8_decode('Identificación del transporte*:'), 'LR', 2, 'L', 0);
    $pdf->cell($pageWidth / 2, 7, utf8_decode($transporte->identificacionTransporte), 'LBR', 2, 'L', 0);
    $pdf->cell($pageWidth / 2, 5, utf8_decode('Número de contenedor*:'), 'LR', 2, 'L', 0);
    $pdf->cell($pageWidth / 2, 7, utf8_decode($transporte->numeroContenedor), 'LBR', 2, 'L', 0);
    $pdf->cell($pageWidth / 2, 5, utf8_decode('Número de fleje*:'), 'LR', 2, 'L', 0);
    $pdf->cell($pageWidth / 2, 7, utf8_decode($transporte->numeroFleje), 'LBR', 2, 'L', 0);

    $pdf->setXY($pdf->marginX + $pageWidth / 2, $tablaTransporte + 6);

    $pdf->cell($pageWidth / 2, 5, utf8_decode('Fecha de embarque*:'), 'LR', 2, 'L', 0);
    $pdf->cell($pageWidth / 2, 5, utf8_decode(date_format($transporte->fechaEmbarque, "d/m/Y")), 'LBR', 2, 'L', 0);
    $pdf->cell($pageWidth / 2, 5, utf8_decode('Lugar de embarque (aduana de salida) *:'), 'LR', 2, 'L', 0);
    $pdf->cell($pageWidth / 2, 7, utf8_decode($transporte->lugarEmbarque), 'LBR', 2, 'L', 0);
    $pdf->cell($pageWidth / 2, 5, utf8_decode('Punto de ingreso al país de destino*:'), 'LR', 2, 'L', 0);
    $pdf->cell($pageWidth / 2, 7, utf8_decode($transporte->puntoIngreso), 'LBR', 2, 'L', 0);
    $pdf->cell($pageWidth / 2, 5, utf8_decode(''), 'LR', 2, 'L', 0);
    $pdf->cell($pageWidth / 2, 7, utf8_decode(''), 'LBR', 1, 'L', 0);

    $pdf->cell($pageWidth, 5, utf8_decode('Régimen (temporal, definitiva o tránsito) *:'), 'LR', 2, 'L', 0);
    $pdf->cell($pageWidth, 7, utf8_decode($transporte->regimen), 'LBR', 2, 'L', 0);

    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->cell($pageWidth, 5, utf8_decode('*Incluir información cuando aplique.'), 0, 1, 'L', 0);
    $pdf->Ln(5);

    // Comienza página 9
    $pdf->addpage();

    $pdf->SetFont('Arial', '', 10);
    $pdf->setXY($pdf->marginX, $startPage - 3);
    $pdf->cell($pageWidth, 5, utf8_decode('Consideraciones generales'), 1, 0, 'C', 1);
    $pdf->Image('../img/pagina10.jpg', $pdf->marginX, $startPage + 3, $pageWidth);

    // Comienza página 10
    $pdf->addpage();

    $pdf->SetFont('Arial', '', 10);
    $pdf->setXY($pdf->marginX, $startPage);
    $pdf->Image('../img/pagina11.jpg', $pdf->marginX, $startPage, $pageWidth);

    $pdf->setXY($pdf->marginX + $middleWidthTable - 25, 240);
    $pdf->cell(100, 5, utf8_decode($nombre_gerente['nombre_completo']));

    ob_end_clean();
    $pdf->Output('I', 'SOLICITUD CERTIFICADO ZOOSANITARIO PARA LA EXPORTACIÓN.pdf', true);
}

try {

    if (!isset($_GET['id'])) {
        throw new Exception('No se recibieron parámetros');
    } else {
        $pdo = new conePDO();
        $con = $pdo->conectar();
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $id = $_GET['id'];
        $miel = $_GET['miel'];
        $tipo = "";
        if ($miel == '1') {
            $tipo = "Miel 100% Convencional.";
        } else if($miel == '5') {
            $tipo = "Miel 100% Mantequilla.";
        } else if($miel == '6') {
            $tipo = "Miel 100% Altiplano.";
        } else if($miel == '7') {
            $tipo = "Miel 100% Naranjo.";
        } else if($miel == '8') {
            $tipo = "Miel 100% Aguacate.";
        } else if($miel == '9') {
            $tipo = "Miel 100% Mezquite.";
        } else {
            $tipo = "Miel 100% Orgánica.";
        }
    }

    include_once '../../utilerias/php/dameNombrePersonal.php';

    $sql = $con->prepare("SELECT scze.fechaSolicitud, scze.datosProducto, scze.datosTransporte, ce.datosCliente, ce.datosDestino FROM solicitudcze scze
    LEFT JOIN clientesexportadores ce ON scze.idClienteExportador = ce.idClienteExportador
    WHERE idSolicitudCertificado = :id");
    $sql->bindParam(':id', $id);
    $sql->execute();



    $result = $sql->fetch(PDO::FETCH_ASSOC);

    if ($sql == false) {
        throw new Exception($con->errorInfo());
    } else if ($result == false) {
        throw new Exception('Registro no disponible');
    }

    $datosCliente = json_decode($result['datosCliente']);
    $datosDestino = json_decode($result['datosDestino']);
    $datosProducto = json_decode($result['datosProducto']);
    $datosTransporte = json_decode($result['datosTransporte']);
    $fechaSolicitud = $result['fechaSolicitud'];

    $nombre_gerente = dameNombrePersonal(2, $con);
    buildFile($datosCliente, $datosDestino, $datosProducto, $datosTransporte, $fechaSolicitud, $nombre_gerente, $tipo);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
    exit();
}
