<?php

include_once '../../fpdf/FPDF/fpdf.php';
include_once '../../DAOConeccion/conePDO.php';


class PDF extends FPDF
{
    var $marginX = 10;
    var $angle = 0;
    var $footerY = -30;
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

    function Footer()
    {
        $this->SetY(-30);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(0, 20, 'Page ' . $this->PageNo() . ' OF {nb}', 0, 0, 'C');
    }
}

function buildFile($datosAnexos, $datosProducto, $datosTransporte, $datosCliente, $datosDestino)
{

    $datosTransporte->fechaEmbarque = DateTime::createFromFormat('Y-m-d', $datosTransporte->fechaEmbarque);

    $pdf = new PDF();
    $pdf->AliasNbPages();
    $pdf->SetAutoPageBreak(false);
    $pdf->SetFillColor(217, 217, 217);
    $contentWidth = $pdf->GetPageWidth() - $pdf->marginX * 2;

    // Página 1
    $pdf->addPage('P', 'A4');
    $pdf->setXY($pdf->marginX, 10);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Multicell($contentWidth, 5, utf8_decode('HEALTH CERTIFICATE FOR IMPORTS OF HONEY AND OTHER APICULTURE PRODUCTS INTENDED FOR HUMAN CONSUMPTION'), 0, 'C', 0);

    $table_titles = $pdf->getY() + 10;
    $pdf->setXY($pdf->marginX, $table_titles);

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->cell($contentWidth / 2, 5, utf8_decode('COUNTRY'), 0, 0, 'L', 0);
    $pdf->cell($contentWidth / 2, 5, utf8_decode('VETERINARY CERTIFICATE TO EU'), 0, 1, 'R', 0);

    // 1.1
    $alto_1d1 = 25;
    $Id1 = $pdf->getY() + 1;
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->setXY($pdf->marginX, $Id1);
    $pdf->cell($contentWidth / 2, $alto_1d1 * .2, utf8_decode('I.1 Consignor'), 'LTR', 2, 'L', 0);
    $pdf->cell($contentWidth / 2, $alto_1d1 * .2, utf8_decode('Name'), 'LR', 2, 'L', 0);
    $pdf->cell($contentWidth / 2, $alto_1d1 * .2, utf8_decode('Adress'), 'LR', 2, 'L', 0);
    $pdf->cell($contentWidth / 2, $alto_1d1 * .2, utf8_decode('Postal code'), 'LR', 2, 'L', 0);
    $pdf->cell($contentWidth / 2, $alto_1d1 * .2, utf8_decode('Tel'), 'LBR', 2, 'L', 0);

    // 1.1 data
    $longitud_variable = 21;
    $pdf->SetFont('Arial', '', 8);
    $pdf->setXY($pdf->marginX + $longitud_variable, $Id1);
    $pdf->cell($contentWidth / 2 - $longitud_variable, 4, utf8_decode('OAXACA MIEL S.A. DE C.V.'), 0, 2, 'L', 0);
    $pdf->cell($contentWidth / 2 - $longitud_variable, 4, utf8_decode('2DA. CDA. DE EMILIANO ZAPATA #19'), 0, 2, 'L', 0);
    $pdf->cell($contentWidth / 2 - $longitud_variable, 4, utf8_decode('COL. BOSQUES DEL SUR DELEGACION'), 0, 2, 'L', 0);
    $pdf->cell($contentWidth / 2 - $longitud_variable, 4, utf8_decode('XOCHIMILCO 16010'), 0, 2, 'L', 0);
    $pdf->cell($contentWidth / 2 - $longitud_variable, 4, utf8_decode('Registration: 31-08771-1'), 0, 2, 'L', 0);
    $pdf->cell($contentWidth / 2 - $longitud_variable, 4, utf8_decode('55 56 75 33 05  55 55 55 3589'), 0, 2, 'L', 0);


    // 1.2
    $alto_celda = 4;
    $x_celdas = $pdf->marginX + $contentWidth * .5;
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->setXY($x_celdas, $Id1);
    $pdf->cell($contentWidth / 4, $alto_celda, utf8_decode('I.2. Certificate reference number'), 'TR', 2, 'L', 0);
    $pdf->SetFont('Arial', '', 8);
    $pdf->cell($contentWidth / 4, $alto_celda, utf8_decode(''), 'BR', 0, 'L', 0);

    // 1.2.a
    $x_celdas = $pdf->marginX + $contentWidth * .75;
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->setXY($x_celdas, $Id1);
    $pdf->cell($contentWidth / 4, $alto_celda, utf8_decode('I.2.a.'), 'TR', 2, 'L', 0);
    $pdf->SetFont('Arial', '', 8);
    $pdf->cell($contentWidth / 4, $alto_celda, utf8_decode(''), 'BR', 0, 'L', 0);

    // 1.3

    $x_celdas = $pdf->marginX + $contentWidth * .5;
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->setXY($x_celdas, $Id1 + $alto_celda * 2);
    $pdf->cell($contentWidth / 2, $alto_celda, utf8_decode('I.3. Central Competente Authority'), 'R', 2, 'L', 0);
    $pdf->SetFont('Arial', '', 8);
    $pdf->cell($contentWidth / 2, $alto_celda, utf8_decode('SAGARPA'), 'BR', 0, 'L', 0);

    // 1.4

    $x_celdas = $pdf->marginX + $contentWidth * .5;
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->setXY($x_celdas, $Id1 + $alto_celda * 4);
    $pdf->cell($contentWidth / 2, $alto_celda, utf8_decode('I.4. Local Competente Authority'), 'R', 2, 'L', 0);
    $pdf->SetFont('Arial', '', 8);
    $pdf->cell($contentWidth / 2, $alto_celda + 1, utf8_decode('SENASICA, SAGARPA'), 'BR', 0, 'L', 0);


    // 1.5
    $alto_1d5 = 20;
    $Id5 = $pdf->getY() + $alto_celda + 1;
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->setXY($pdf->marginX, $Id5);
    $pdf->cell($contentWidth / 2, $alto_1d5 * .25, utf8_decode('I.5. Consignee'), 'LR', 2, 'L', 0);
    $pdf->cell($contentWidth / 2, $alto_1d5 * .25, utf8_decode('Adress'), 'LR', 2, 'L', 0);
    $pdf->cell($contentWidth / 2, $alto_1d5 * .25, utf8_decode('Postal code'), 'LR', 2, 'L', 0);
    $pdf->cell($contentWidth / 2, $alto_1d5 * .25, utf8_decode('Tel'), 'LBR', 2, 'L', 0);

    // 1.5 data
    $longitud_variable = 21;
    $pdf->SetFont('Arial', '', 8);
    $pdf->setXY($pdf->marginX + $longitud_variable, $Id5);
    $pdf->cell($contentWidth / 2 - $longitud_variable, 5, utf8_decode($datosCliente->nombre), 0, 2, 'L', 0);
    $pdf->cell($contentWidth / 2 - $longitud_variable, 4, utf8_decode($datosCliente->calle . ' ' . $datosCliente->numeroExterior), 0, 2, 'L', 0);
    $pdf->cell($contentWidth / 2 - $longitud_variable, 5, utf8_decode($datosCliente->codigoPostal), 0, 2, 'L', 0);
    $pdf->cell($contentWidth / 2 - $longitud_variable, 5, utf8_decode($datosDestino->ladaDestino . ' ' . $datosDestino->telefonoDestino), 0, 2, 'L', 0);


    // 1.6
    $alto_celda = 20;
    $x_celdas = $pdf->marginX + $contentWidth * .5;
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->setXY($x_celdas, $Id5);
    $pdf->cell($contentWidth / 2, $alto_celda * .25, utf8_decode('I.6.'), 'R', 2, 'L', 0);
    $pdf->cell($contentWidth / 2, $alto_celda * .75, utf8_decode(''), 'RB', 2, 'L', 0);
    $pdf->Line($x_celdas, $Id5 + $alto_celda, $pdf->marginX + $contentWidth, $Id5);

    // 1.7
    $alto_1d7 = 10;
    $ancho_1d7 = $contentWidth / 2 * .4;
    $Id7 = $pdf->getY();
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->setXY($pdf->marginX, $Id7);
    $pdf->cell($ancho_1d7, $alto_1d7 * .5, utf8_decode('I.7. Country of origin'), 'L', 2, 'L', 0);
    $pdf->SetFont('Arial', '', 8);
    $pdf->cell($ancho_1d7, $alto_1d7 * .5, utf8_decode($datosProducto->origenMP), 'LBR', null, 'C', 0);

    // 1.7 ISO Code
    $ancho_1d7ISO_Code = $contentWidth / 2 * .3;
    $pdf->setXY($pdf->marginX + $ancho_1d7, $Id7);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->cell($ancho_1d7ISO_Code, 5, utf8_decode('ISO-Code'), 'R', 2, 'C', 0);
    $pdf->SetFont('Arial', '', 8);
    $pdf->cell($ancho_1d7ISO_Code, 5, utf8_decode($datosProducto->codigoOrigen), 'RB', null, 'C', 0);

    // 1.8

    $ancho_Id8 = $contentWidth / 2 * .3;
    $x_celda = $pdf->marginX + $ancho_1d7 + $ancho_1d7ISO_Code;
    $alto_celda = 10;
    $pdf->setXY($x_celda, $Id7);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->cell($ancho_Id8, $alto_celda / 2, utf8_decode('I.8.'), 'R', 2, 'L', 0);
    $pdf->cell($ancho_Id8, $alto_celda / 2, utf8_decode(''), 'RB', null, 'L', 0);
    $pdf->Line($x_celda, $Id7 + $alto_celda, $pdf->marginX + $contentWidth / 2, $Id7);

    // 1.9
    $ancho_1d9 = $contentWidth / 2 * .4;
    $Id9 = $Id7;
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->setXY($pdf->marginX + $contentWidth / 2, $Id9);
    $pdf->cell($ancho_1d9, 5, utf8_decode('I.9. Country of destination'), 'L', 2, 'L', 0);
    $pdf->SetFont('Arial', '', 8);
    $pdf->cell($ancho_1d7, 5, utf8_decode($datosProducto->paisDestino), 'LBR', null, 'C', 0);

    // 1.9 ISO Code
    $ancho_1d9ISO_Code = $contentWidth / 2 * .3;
    $pdf->setXY($pdf->marginX + $ancho_1d9 + $contentWidth / 2, $Id9);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->cell($ancho_1d9ISO_Code, 5, utf8_decode('ISO-Code'), 'R', 2, 'C', 0);
    $pdf->SetFont('Arial', '', 8);
    $pdf->cell($ancho_1d9ISO_Code, 5, utf8_decode($datosProducto->codigoDestino), 'RB', null, 'C', 0);

    // 1.10
    $ancho_Id10 = $contentWidth / 2 * .3;
    $x_celda = $pdf->marginX + $ancho_1d9 + $ancho_1d9ISO_Code + $contentWidth / 2;
    $alto_celda = 10;
    $pdf->setXY($x_celda, $Id9);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->cell($ancho_Id10, $alto_celda / 2, utf8_decode('I.10.'), 'R', 2, 'L', 0);
    $pdf->cell($ancho_Id10, $alto_celda / 2, utf8_decode(''), 'RB', null, 'L', 0);

    // 1.11
    $alto_1d11 = 30;
    $Id11 = $pdf->getY() + $alto_celda / 2;
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->setXY($pdf->marginX, $Id11);
    $pdf->cell($contentWidth / 2, $alto_1d11 / 6, utf8_decode('I.11. Place of origin'), 'LR', 2, 'L', 0);
    $pdf->cell($contentWidth / 2, $alto_1d11 / 6, utf8_decode(''), 'LR', 2, 'L', 0);
    $pdf->cell($contentWidth / 2, $alto_1d11 / 6, utf8_decode('Name'), 'LR', 2, 'L', 0);
    $pdf->cell($contentWidth / 2, $alto_1d11 / 6, utf8_decode(''), 'LR', 2, 'L', 0);
    $pdf->cell($contentWidth / 2, $alto_1d11 / 6, utf8_decode('Adress'), 'LR', 2, 'L', 0);
    $pdf->cell($contentWidth / 2, $alto_1d11 / 6, utf8_decode(''), 'LBR', 2, 'L', 0);

    // 1.11 data
    $longitud_variable = 21;
    $pdf->SetFont('Arial', '', 8);
    $pdf->setXY($pdf->marginX + $longitud_variable, $Id11 + 7);
    $pdf->cell($contentWidth / 2 - $longitud_variable, 4, utf8_decode('OAXACA MIEL S.A. DE C.V.'), 0, 2, 'L', 0);
    $pdf->cell($contentWidth / 2 - $longitud_variable, 4, utf8_decode('CARRETERA MÉRIDA-CANCÚN KM. 7.5,'), 0, 2, 'L', 0);
    $pdf->cell($contentWidth / 2 - $longitud_variable, 4, utf8_decode('SAN PEDRO NOH PAT, KANASÍN,'), 0, 2, 'L', 0);
    $pdf->cell($contentWidth / 2 - $longitud_variable, 4, utf8_decode('YUCATÁN, C.P. 94590.'), 0, 2, 'L', 0);
    $pdf->cell($contentWidth / 2 - $longitud_variable, 4, utf8_decode('TEL.: (01 999) 9 88 09 90'), 0, 2, 'L', 0);

    // 1.12
    $x_celda = $pdf->marginX + $contentWidth / 2;
    $Id12 = $Id11;
    $alto_titulo = 5;
    $alto_celda = 25;
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->setXY($x_celda, $Id12);
    $pdf->cell($contentWidth / 2, $alto_titulo, utf8_decode('I.12.'), 'R', 2, 'L', 0);
    $pdf->cell($contentWidth / 2, $alto_celda, utf8_decode(''), 'RB', null, 'L', 0);
    $pdf->Line($x_celda, $Id12 + $alto_celda + $alto_titulo, $pdf->marginX + $contentWidth, $Id12);

    // 1.13
    $alto_1d13 = 10;
    $Id13 = $pdf->getY() + $alto_celda;
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->setXY($pdf->marginX, $Id13);
    $pdf->cell($contentWidth / 2, $alto_1d13 / 2, utf8_decode('I.13. Place of loading'), 'LR', 2, 'L', 0);
    $pdf->SetFont('Arial', '', 8);
    $pdf->cell($contentWidth / 2, $alto_1d13 / 2, utf8_decode($datosTransporte->lugarEmbarque), 'LRB', null, 'L', 0);

    // 1.14
    $Id14 = $Id13;
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->setXY($pdf->marginX + $contentWidth / 2, $Id14);
    $pdf->cell($contentWidth / 2, 5, utf8_decode('I.14. Date of departure'), 'R', 2, 'L', 0);
    $pdf->SetFont('Arial', '', 8);
    $pdf->cell($contentWidth / 2, 5, utf8_decode(date_format($datosTransporte->fechaEmbarque, "d/m/Y")), 'RB', null, 'L', 0);

    // Part 1 Title
    $lateral_title_y = $pdf->getY();
    $x_title = $pdf->marginX - 2;
    $pdf->setXY($pdf->marginX - 7, $Id1);
    $alto_title_1 = $alto_1d1 + $alto_1d5 + $alto_1d7 + $alto_1d11 + $alto_1d13;
    $pdf->cell(7, $alto_title_1, '', 'LTB', null, '', 0);
    $pdf->Rotate(90, $x_title, $lateral_title_y);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Text($x_title, $lateral_title_y, utf8_decode('Part I: Details of dispatched consignment'));
    $pdf->Rotate(0);

    // 1.15

    $Id15 = $Id13 + $alto_1d13;
    $alto_1d15 = 30;
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->setXY($pdf->marginX, $Id15);
    $pdf->cell($contentWidth / 2, $alto_1d15 / 6, utf8_decode('I.15. Means of transport'), 'LR', 2, 'L', 0);
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->cell(($contentWidth / 2) / 3, $alto_1d15 / 6, utf8_decode('Aeroplane'), 'L', 0, 'L', 0);
    $pdf->cell(($contentWidth / 2) / 3, $alto_1d15 / 6, utf8_decode('Ship'), '', 0, 'L', 0);
    $pdf->cell(($contentWidth / 2) / 3, $alto_1d15 / 6, utf8_decode('Railway wagon'), 'R', 2, 'L', 0);
    $pdf->setX($pdf->marginX);
    $pdf->cell(($contentWidth / 2) / 3, $alto_1d15 / 6, utf8_decode('Road Vehicle'), 'L', 0, 'L', 0);
    $pdf->cell(($contentWidth / 2) / 3, $alto_1d15 / 6, utf8_decode('Road Vehicle'), '', 0, 'L', 0);
    $pdf->cell(($contentWidth / 2) / 3, $alto_1d15 / 6, utf8_decode(''), 'R', 2, 'L', 0);
    $pdf->setX($pdf->marginX);
    $pdf->cell($contentWidth / 2, $alto_1d15 / 6, utf8_decode('Identification: Ship: ' . $datosAnexos->identificacion), 'LR', 2, 'L', 0);
    $pdf->cell($contentWidth / 2, $alto_1d15 / 6, utf8_decode('Documentary references: BL: ' . $datosAnexos->referenciasDocumentales), 'LBR', 0, 'L', 0);

    // 1.15 data

    $pdf->setXY($pdf->marginX, $Id15 + $alto_1d15 / 6);
    $pdf->SetFont('Arial', '', 8);
    $pdf->setX($pdf->marginX + $pdf->GetStringWidth('Aeroplane') + 2);
    $pdf->SetFont('ZapfDingbats', '', 10);
    $simbolo = $datosAnexos->medioTransporte == '1' ? '5' : 'r';
    $pdf->cell(5, $alto_1d15 / 6, utf8_decode($simbolo), 0, 0, 'L', 0);

    $pdf->SetFont('Arial', '', 8);
    $pdf->setX($pdf->marginX + ($contentWidth / 2) / 3 + $pdf->GetStringWidth('Ship') + 2);
    $pdf->SetFont('ZapfDingbats', '', 10);
    $simbolo = $datosAnexos->medioTransporte == '2' ? '5' : 'r';
    $pdf->cell(5, $alto_1d15 / 6, utf8_decode($simbolo), 0, 0, 'L', 0);

    $pdf->SetFont('Arial', '', 8);
    $pdf->setX($pdf->marginX + (($contentWidth / 2) / 3) * 2 + $pdf->GetStringWidth('Railway wagon') + 2);
    $pdf->SetFont('ZapfDingbats', '', 10);
    $simbolo = $datosAnexos->medioTransporte == '3' ? '5' : 'r';
    $pdf->cell(5, $alto_1d15 / 6, utf8_decode($simbolo), 0, 0, 'L', 0);


    $pdf->setXY($pdf->marginX, $Id15 + ($alto_1d15 / 6) * 2);
    $pdf->SetFont('Arial', '', 8);
    $pdf->setX($pdf->marginX + $pdf->GetStringWidth('Road Vehicle') + 2);
    $pdf->SetFont('ZapfDingbats', '', 10);
    $simbolo = $datosAnexos->medioTransporte == '4' ? '5' : 'r';
    $pdf->cell(5, $alto_1d15 / 6, utf8_decode($simbolo), 0, 0, 'L', 0);

    $pdf->SetFont('Arial', '', 8);
    $pdf->setX($pdf->marginX + ($contentWidth / 2) / 3 + $pdf->GetStringWidth('Road Vehicle') + 2);
    $pdf->SetFont('ZapfDingbats', '', 10);
    $pdf->cell(5, $alto_1d15 / 6, utf8_decode('r'), 0, 0, 'L', 0);


    $pdf->setXY($pdf->marginX, $Id15 + ($alto_1d15 / 6) * 3);
    $pdf->SetFont('Arial', 'B', 7);

    // 1.16
    $pdf->setXY($pdf->marginX + $contentWidth / 2, $Id15);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->cell($contentWidth / 2, 5, utf8_decode('I.16. Entry BIP in EU'), 'R', 2, 'L', 0);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->cell($contentWidth / 2, 10, utf8_decode($datosTransporte->puntoIngreso), 'RB', 2, 'L', 0);

    // 1.17
    $Id17 = $pdf->getY();
    $x_celda = $pdf->marginX + $contentWidth / 2;
    $alto_1d17 = 10;
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->setX($x_celda);
    $pdf->cell($contentWidth / 2, $alto_1d17 / 2, utf8_decode('I.17.'), 'R', 2, 'L', 0);
    $pdf->cell($contentWidth / 2, $alto_1d17 / 2, utf8_decode(''), 'BR', null, 'L', 0);
    $pdf->Line($x_celda, $Id17 + $alto_1d17, $pdf->marginX + $contentWidth, $Id17);

    // 1.18
    $ancho_estandar_Id18 = $contentWidth * .8;
    $id18 = $pdf->getY() + $alto_1d17 / 2;
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->setXY($pdf->marginX, $id18);
    $pdf->cell($contentWidth * .7, 5, utf8_decode('I.18. Description of commodity'), 'L', 2, 'L', 0);
    $pdf->cell($ancho_estandar_Id18, 11, utf8_decode($datosProducto->producto), 'LB', 2, 'L', 0);

    // 1.19

    $pdf->setXY($pdf->marginX + $contentWidth * .7, $id18);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->cell($contentWidth * .3, 4, utf8_decode('I.19. Commodity Code (HS-Code)'), 'LR', 2, 'L', 0);
    $pdf->cell($contentWidth * .3, 4, utf8_decode('04.09'), 'LBR', null, 'L', 0);

    // 1.20
    $pdf->setXY($pdf->marginX + $ancho_estandar_Id18, $id18 + 8);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->cell($contentWidth * .2, 4, utf8_decode('I.20. Quantity'), 'LR', 2, 'L', 0);
    $pdf->cell($contentWidth * .2, 4, utf8_decode(number_format($datosAnexos->cantidad, 2, '.', ',') . ' / ' . number_format($datosProducto->pesoNeto, 2, '.', ',')), 'LBR', 2, 'L', 0);


    // 1.21
    $Id21 = $pdf->getY();
    $pdf->setXY($pdf->marginX, $Id21);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->cell($ancho_estandar_Id18, 5, utf8_decode('I.21. Temperature of product'), 'LR', 2, 'L', 0);
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->cell($ancho_estandar_Id18 / 3, 5, utf8_decode('Ambient'), 'LB', 0, 'L', 0);
    $pdf->cell($ancho_estandar_Id18 / 3, 5, utf8_decode('Chilled'), 'B', 0, 'L', 0);
    $pdf->cell($ancho_estandar_Id18 / 3, 5, utf8_decode('Frozen'), 'RB', 0, 'L', 0);


    // 1.21 data
    $pdf->setXY($pdf->marginX, $Id21 + 5);
    $pdf->SetFont('Arial', '', 8);
    $pdf->setX($pdf->marginX + $pdf->GetStringWidth('Ambient') + 2);
    $pdf->SetFont('ZapfDingbats', '', 10);
    $pdf->cell(5, 5, utf8_decode('5'), 0, 0, 'L', 0);

    $pdf->SetFont('Arial', '', 8);
    $pdf->setX($pdf->marginX + $ancho_estandar_Id18 / 3 + $pdf->GetStringWidth('Chilled') + 2);
    $pdf->SetFont('ZapfDingbats', '', 10);
    $pdf->cell(5, 5, utf8_decode('r'), 0, 0, 'L', 0);

    $pdf->SetFont('Arial', '', 8);
    $pdf->setX($pdf->marginX + ($ancho_estandar_Id18 / 3) * 2 + $pdf->GetStringWidth('Frozen') + 2);
    $pdf->SetFont('ZapfDingbats', '', 10);
    $pdf->cell(5, 5, utf8_decode('r'), 0, 0, 'L', 0);

    // 1.22
    $pdf->setXY($pdf->marginX + $ancho_estandar_Id18, $Id21);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->cell($contentWidth * .2, 5, utf8_decode('I.22. Number of packages'), 'R', 2, 'L', 0);
    $pdf->cell($contentWidth * .2, 5, utf8_decode(number_format($datosProducto->cantidad, 0, '.', ',')), 'RB', 1, 'L', 0);

    // 1.23
    $Id23 = $pdf->getY();
    $pdf->setXY($pdf->marginX, $Id23);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->cell($ancho_estandar_Id18, 5, utf8_decode('I.23. Identification of container/Seal number'), 'LR', 2, 'L', 0);
    $pdf->cell($ancho_estandar_Id18 / 2, 5, utf8_decode('CONTAINER: ' . $datosAnexos->idContenedor), 'LB', 0, 'L', 0);
    $pdf->cell($ancho_estandar_Id18 / 2, 5, utf8_decode('SEAL: ' . $datosAnexos->sello), 'BR', null, 'L', 0);

    // 1.24
    $pdf->setXY($pdf->marginX + $ancho_estandar_Id18, $Id23);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->cell($contentWidth * .2, 5, utf8_decode('I.24. Type of packaging'), 'R', 2, 'L', 0);
    $pdf->cell($contentWidth * .2, 5, utf8_decode($datosProducto->presentacion), 'RB', 2, 'L', 0);

    // 1.25
    $Id25 = $pdf->getY();
    $pdf->setXY($pdf->marginX, $Id25);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->cell($contentWidth, 5, utf8_decode('I.25. Commodities certified for'), 'LR', 2, 'L', 0);
    $pdf->SetFont('Arial', '', 8);
    $pdf->cell($contentWidth, 5, utf8_decode('Human consumption'), 'LBR', 0, 'L', 0);

    // 1.25 data
    $pdf->setXY($pdf->marginX, $Id25 + 5);
    $pdf->SetFont('Arial', '', 8);
    $pdf->setX($pdf->marginX + $pdf->GetStringWidth('Human consumption') + 2);
    $pdf->SetFont('ZapfDingbats', '', 10);
    $pdf->cell(5, 5, utf8_decode('5'), 0, 0, 'L', 0);

    // 1.26
    $Id26 = $pdf->getY() + 5;
    $pdf->setXY($pdf->marginX, $Id26);
    $pdf->SetFont('Arial', '', 8);
    $pdf->cell($contentWidth / 2, 5, utf8_decode('I.26.'), 'LR', 2, 'L', 0);
    $pdf->cell($contentWidth / 2, 10, utf8_decode(''), 'LRB', 2, 'L', 0);
    $pdf->Line($pdf->marginX, $Id26 + 15, $pdf->marginX + $contentWidth / 2, $Id26);

    // 1.27
    $Id27 = $Id26;
    $pdf->setXY($pdf->marginX + $contentWidth / 2, $Id27);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->cell($contentWidth / 2, 5, utf8_decode('I.27. For import or admission into EU'), 'LR', 2, 'L', 0);
    $pdf->SetFont('ZapfDingbats', '', 10);
    $pdf->cell($contentWidth / 2, 10, utf8_decode('5'), 'LRB', 2, 'L', 0);

    // 1.28

    $Id28 = $pdf->getY();
    $ancho_columnas = $contentWidth / 6;
    $pdf->setXY($pdf->marginX, $Id28);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->cell($contentWidth, 5, utf8_decode('I.28. Identification of the commodities'), 'LR', 2, 'L', 0);
    $pdf->SetFont('Arial', '', 8);
    $pdf->cell($contentWidth, 5, utf8_decode(''), 'LR', 2, 'L', 0);
    $pdf->cell($ancho_columnas, 4, utf8_decode('Species'), 'L', 0, 'C', 0);
    $pdf->cell($ancho_columnas, 4, utf8_decode('Treatment type'), 0, 0, 'C', 0);
    $pdf->cell($ancho_columnas * 2, 4, utf8_decode('Approval number of establishments'), 0, 0, 'C', 0);
    $pdf->cell($ancho_columnas, 4, utf8_decode('Number of packages'), 0, 0, 'C', 0);
    $pdf->cell($ancho_columnas, 4, utf8_decode('Net Weight'), 'R', 1, 'C', 0);

    $pdf->cell($ancho_columnas, 4, utf8_decode('(Scientific Name)'), 'L', 0, 'C', 0);
    $pdf->cell($ancho_columnas, 4, utf8_decode(''), 0, 0, 'C', 0);
    $pdf->cell($ancho_columnas * 2, 4, utf8_decode('Manufacturing plant'), 0, 0, 'C', 0);
    $pdf->cell($ancho_columnas, 4, utf8_decode(''), 0, 0, 'C', 0);
    $pdf->cell($ancho_columnas, 4, utf8_decode(''), 'R', 1, 'C', 0);

    $pdf->cell($ancho_columnas, 4, utf8_decode(''), 'L', 0, 'C', 0);
    $pdf->cell($ancho_columnas, 4, utf8_decode(''), 0, 0, 'C', 0);
    $pdf->cell($ancho_columnas * 2, 4, utf8_decode(''), 0, 0, 'C', 0);
    $pdf->cell($ancho_columnas, 4, utf8_decode(''), 0, 0, 'C', 0);
    $pdf->cell($ancho_columnas, 4, utf8_decode(''), 'R', 1, 'C', 0);

    // 1.28 data
    $y_actual = $pdf->getY();

    $pdf->SetFont('Arial', 'B', 8);

    // Primera columna
    $pdf->cell($ancho_columnas, 30, '', 'LB', null, 'L', 0);
    $pdf->setXY($pdf->marginX, $y_actual);
    $pdf->Multicell($ancho_columnas, 5, utf8_decode($datosProducto->especie), 0, 'C', 0);

    // Segunda columna
    $pdf->setXY($pdf->marginX + $ancho_columnas, $y_actual);
    $pdf->cell($ancho_columnas, 30, '', 'B', null, 'L', 0);
    $pdf->setXY($pdf->marginX + $ancho_columnas, $y_actual);
    $pdf->Multicell($ancho_columnas, 5, utf8_decode($datosProducto->tratamientoProceso), 0, 'C', 0);

    // Tercera columna
    $pdf->SetFont('Arial', '', 8);
    $pdf->setXY($pdf->marginX + $ancho_columnas * 2, $y_actual);
    $pdf->cell($ancho_columnas * 2, 30, '', 'B', null, 'L', 0);
    $pdf->setXY($pdf->marginX + $ancho_columnas * 2, $y_actual);
    $pdf->cell($ancho_columnas * 2, 5, utf8_decode('OAXACA MIEL S.A. DE C.V.'), 0, 2, 'C', 0);
    $pdf->cell($ancho_columnas * 2, 5, utf8_decode('CARRETERA MÉRIDA-CANCÚN KM. 7.5,'), 0, 2, 'C', 0);
    $pdf->cell($ancho_columnas * 2, 5, utf8_decode('SAN PEDRO NOH PAT, KANASÍN,'), 0, 2, 'C', 0);
    $pdf->cell($ancho_columnas * 2, 5, utf8_decode('YUCATÁN, C.P. 97370.'), 0, 2, 'C', 0);
    $pdf->cell($ancho_columnas * 2, 5, utf8_decode('TEL.: (01 999) 9 88 09 90'), 0, 2, 'C', 0);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->cell($ancho_columnas * 2, 5, utf8_decode('31-08771-1'), 0, 2, 'C', 0);

    // Cuarta columna
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->setXY($pdf->marginX + $ancho_columnas * 4, $y_actual);
    $pdf->cell($ancho_columnas, 30, '', 'B', null, 'L', 0);
    $pdf->setXY($pdf->marginX + $ancho_columnas * 4, $y_actual);
    $pdf->cell($ancho_columnas, 5, utf8_decode(number_format($datosProducto->cantidad, 0, '.', ',')), 0, 2, 'C', 0);
    $pdf->cell($ancho_columnas, 5, utf8_decode($datosProducto->marcaDistintiva), 0, 2, 'C', 0);

    // Quinta columna

    $pdf->SetFont('Arial', 'B', 8);
    $pdf->setXY($pdf->marginX + $ancho_columnas * 5, $y_actual);
    $pdf->cell($ancho_columnas, 30, '', 'BR', null, 'L', 0);
    $pdf->setXY($pdf->marginX + $ancho_columnas * 5, $y_actual);
    $pdf->cell($ancho_columnas, 5, utf8_decode(number_format($datosProducto->pesoNeto, 2, '.', ',') . ' ' . $datosProducto->unidadMedida), 0, 2, 'C', 0);


    // Página 2
    $pdf->addPage('P', 'A4');
    $pdf->setXY($pdf->marginX, 10);
    $pdf->SetFont('Arial', 'B', 10);
    // $pdf->Multicell($contentWidth, 5, utf8_decode('HEALTH CERTIFICATE FOR IMPORTS OF HONEY AND OTHER APICULTURE PRODUCTS INTENDED FOR HUMAN CONSUMPTION'), 0, 'C', 0);

    $table_titles = $pdf->getY();
    $pdf->setXY($pdf->marginX, $table_titles);

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->cell($contentWidth / 2, 5, utf8_decode('COUNTRY'), 0, 0, 'L', 0);
    $pdf->cell($contentWidth / 2, 5, utf8_decode('VETERINARY CERTIFICATE TO EU'), 0, 1, 'R', 0);

    $begining_of_square = $pdf->getY();

    // Part II Title
    $pdf->setXY($pdf->marginX - 7, $begining_of_square);
    $pdf->cell(7, $alto_title_1, '', 'LTB', null, '', 0);
    $lateral_title_y = $pdf->getY() + $alto_title_1 - 10;
    $pdf->Rotate(90, $x_title, $lateral_title_y);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Text($x_title, $lateral_title_y, utf8_decode('Part II: Certification'));
    $pdf->Rotate(0);

    // II. Health info
    $II = $begining_of_square;
    $pdf->setXY($pdf->marginX, $II);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->cell($contentWidth, 20, utf8_decode('II. Health information'), 'LTR', 2, 'L', 0);

    // text
    $pdf->SetFont('Arial', '', 8);
    $pdf->setXY($pdf->marginX, $pdf->getY());
    $pdf->Multicell($contentWidth, 5, utf8_decode('
    I, the undersigned, declare that I am aware of the relevant provisions of Regulation (EC) No 178/2002 of the European Parliament and of the Council
    of 28 January 2002 laying down the general principles and requirements of food law, establishing the European Food Safety Authority and laying
    down procedures in matters of food safety (OJ L 31, 1.2.2002,p.1), Regulation (EC) No 852/2004 of the European Parliament and of the Council
    of 29 April 2004 on the hygiene of foodstuffs (OJ L 139, 30.4.2004, p.1) and Regulation (EC) No 853/2004 of the European Parliament and of the
    Council of 29 April 2004, laying down specific hygiene rules for food of animal origin (OJ L 139, 30.4.2004, p. 55) and certify that honey, royal
    jelly and other apiculture products described above were produced in accordance with those requirements, in particular that they:
    
    -   come from (an) establishment(s) implementing a programme based on the HACCP principles in accordance with Regulation (EC) No 852/2004,
    -   have been handled and, where appropriate, prepared, packaged and stored in a hygienic manner in accordance with the requirements of
        Annex II to regulation (EC)  No 852/2004
    and
    -   the guarantees covering live animals and products thereof provided by the residue plans submitted in accordance with Council Directive
        96/23/EC of 29 April 1996 on measures to monitor certain substances and residues thereof in live animals and animal products and
        repealing Directives 85/358/EEC and 86/469/EEC and Decisions 89/187/EEC and 91/664/EEC (OJ L 125, 23.5.1996, p. 10), and in particular
        Article 29 thereof, are fulfilled.'), 'LR', 'L', 0);

    // $pdf->cell($contentWidth, 10, '', 'LR', 2, '', 0);

    $pdf->SetFont('Arial', 'B', 8);
    $pdf->cell($contentWidth, 10, utf8_decode('    Notes'), 'LR', 2, 'L', 0);

    $pdf->SetFont('Arial', '', 8);
    $pdf->Multicell($contentWidth, 5, utf8_decode('
    Part I:
    - Box reference I.11: Place of origin: name and address of the dispatch establishment. Approval number
                        means registration number.
    - Box reference I.15: Registration number (railway wagons or container and lorries), flight number (aircraft) or
                        name (ship). Separate information is to be provided in case of unloading and reloading.
    - Box reference I.19: Use the appropriate HS code under the following headings: 04.09, 04.10.
    - Box reference I.20: Indicate total gross weight and total net weight.
    - Box reference I.23: Identification of container/seal number: only where applicable.
    - Box reference I.28: Treatmen type: Indicate \'ultrasonication\', \'homogenisation\', \'ultrafiltration\', \'pasteurisation\',
                        \'nothermal treatment\'.
                        Approval number of establishments: approval number or competent authority identification number or
                        competent authority identification number as appropriate.
    Part II:
    - The colour of the stamp and signature must be different to that of the other particulars in the certificate.
    '), 'LBR', 'L', 0);

    $last_square = $pdf->getY();
    $ancho_columna = $contentWidth / 6;
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->setXY($pdf->marginX, $last_square);
    $pdf->cell($contentWidth, 7, utf8_decode('Official Inspector'), 'RL', 2, 'L', 0);
    $pdf->cell($ancho_columna, 7, utf8_decode('Name (in capitals):'), 'L', 2, 'R', 0);
    $pdf->cell($ancho_columna, 5, utf8_decode('Date:'), 'L', 2, 'R', 0);
    $pdf->cell($ancho_columna, 5, utf8_decode(''), 'L', 2, 'R', 0);
    $pdf->cell($ancho_columna, 5, utf8_decode('Stamp:'), 'L', 2, 'R', 0);
    $pdf->cell($ancho_columna, 5, utf8_decode(''), 'LB', 2, 'R', 0);

    $pdf->setXY($pdf->marginX + $ancho_columna, $last_square + 7);
    $pdf->SetFont('Arial', '', 8);
    $pdf->cell($ancho_columna * 3, 7, utf8_decode(''), 0, 2, 'L', 0); // Here goes the name
    $pdf->cell($ancho_columna * 3, 5, utf8_decode(''), 0, 2, 'L', 0); // Date
    $pdf->cell($ancho_columna * 3, 5, utf8_decode(''), 0, 2, 'L', 0);
    $pdf->cell($ancho_columna * 3, 5, utf8_decode(''), 0, 2, 'L', 0); //Stamp
    $pdf->cell($ancho_columna * 3, 5, utf8_decode(''), 'B', 2, 'L', 0);

    $pdf->setXY($pdf->marginX + $ancho_columna * 4, $last_square + 7);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->cell($ancho_columna * 2, 7, utf8_decode('Qualification and title:'), 'R', 2, 'L', 0);
    $pdf->cell($ancho_columna * 2, 10, utf8_decode('OFFICIAL VETERINARY EXPORT MANAGER'), 'R', 2, 'L', 0);
    $pdf->cell($ancho_columna * 2, 5, utf8_decode('Signature:'), 'R', 2, 'L', 0);
    $pdf->cell($ancho_columna * 2, 5, utf8_decode(''), 'RB', 2, 'L', 0);

    // Certificate reference number

    $pdf->SetFont('Arial', 'B', 8);
    $pdf->setXY($pdf->marginX + $contentWidth / 2, $II);
    $pdf->cell($contentWidth / 4, 5, utf8_decode('II.a . Certificate reference number'), 'L', 2, 'L', 0);
    $pdf->cell($contentWidth / 4, 10, utf8_decode(''), 'LB', null, 'L', 0);

    $pdf->setXY($pdf->marginX + $contentWidth * .75, $II);
    $pdf->cell($contentWidth / 4, 5, utf8_decode('II.b.'), 'L', 2, 'L', 0);
    $pdf->cell($contentWidth / 4, 10, utf8_decode(''), 'LB', null, 'L', 0);
    $pdf->Line($pdf->marginX + $contentWidth * .75, $II + 15, $pdf->marginX + $contentWidth, $II);

    // Output the file
    $pdf->Output('I', 'ANEXO SOLICITUD CERTIFICADO ZOOSANITARIO PARA LA EXPORTACIÓN.pdf', true);
}

try {

    if (!isset($_GET['id'])) {
        throw new Exception('No se recibieron parámetros');
    } else {
        $pdo = new conePDO();
        $con = $pdo->conectar();
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $id = $_GET['id'];
    }

    $sql = $con->prepare("SELECT scze.datosAnexo, scze.datosProducto, scze.datosTransporte, ce.datosCliente, ce.datosDestino
    FROM solicitudcze scze
    LEFT JOIN clientesexportadores ce ON scze.idClienteExportador = ce.idClienteExportador
    WHERE idSolicitudCertificado = :id");
    $sql->bindParam(':id', $id);
    $sql->execute();

    $result = $sql->fetch(PDO::FETCH_ASSOC);

    if (!$sql) {
        throw new Exception($con->errorInfo());
    } else if (!$result) {
        throw new Exception('Registro no disponible');
    }

    $datosAnexos = json_decode($result['datosAnexo']);
    $datosProducto = json_decode($result['datosProducto']);
    $datosTransporte = json_decode($result['datosTransporte']);
    $datosCliente = json_decode($result['datosCliente']);
    $datosDestino = json_decode($result['datosDestino']);

    if ($datosAnexos == null || $datosProducto == null || $datosTransporte == null || $datosCliente == null || $datosDestino == null) {
        throw new Exception('Registro no disponible');
    }

    buildFile($datosAnexos, $datosProducto, $datosTransporte, $datosCliente, $datosDestino);

} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
    exit();
}
