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
    $pdf->Multicell($contentWidth, 5, utf8_decode('MUSTER DER AMTLlCHEN BESCHEINIGUNG FÜR DEN EINGANG IN DIE UNION ZUM INVERKEHRBRINGEN VON HONIG UND ANDEREN IMKEREIERZEUGNISSEN FÜR DEN MENSCHLICHEN VERZEHR'), 0, 'C', 0);

    $table_titles = $pdf->getY() + 10;
    $pdf->setXY($pdf->marginX, $table_titles);

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->cell($contentWidth / 2, 5, utf8_decode('LAND'), 0, 0, 'L', 0);
    $pdf->cell($contentWidth / 2, 5, utf8_decode('Amtliche Bescheinigung für den Eingang in die EU'), 0, 1, 'R', 0);

    // 1.1
    $alto_1d1 = 25;
    $Id1 = $pdf->getY() + 1;
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->setXY($pdf->marginX, $Id1);
    $pdf->cell($contentWidth / 2, $alto_1d1 * .2, utf8_decode('I.1 Absender/Ausführer'), 'LTR', 2, 'L', 0);
    $pdf->cell($contentWidth / 2, $alto_1d1 * .2, utf8_decode('Name'), 'LR', 2, 'L', 0);
    $pdf->cell($contentWidth / 2, $alto_1d1 * .2, utf8_decode('Anschrift'), 'LR', 2, 'L', 0);
    $pdf->cell($contentWidth / 2, $alto_1d1 * .2, utf8_decode(''), 'LR', 2, 'L', 0);
    $pdf->cell($contentWidth / 2, $alto_1d1 * .2, utf8_decode('Tel.-Nr.'), 'LBR', 2, 'L', 0);

    // 1.1 data
    $longitud_variable = 21;
    $pdf->SetFont('Arial', '', 8);
    $pdf->setXY($pdf->marginX + $longitud_variable, $Id1);
    $pdf->cell($contentWidth / 2 - $longitud_variable, 4, utf8_decode(''), 0, 2, 'L', 0);
    $pdf->cell($contentWidth / 2 - $longitud_variable, 4, utf8_decode('OAXACA MIEL S.A. DE C.V.'), 0, 2, 'L', 0);
    $pdf->cell($contentWidth / 2 - $longitud_variable, 4, utf8_decode('2DA. CDA. DE EMILIANO ZAPATA #19'), 0, 2, 'L', 0);
    $pdf->cell($contentWidth / 2 - $longitud_variable, 4, utf8_decode('COL. BOSQUES DEL SUR DELEGACION'), 0, 2, 'L', 0);
    $pdf->cell($contentWidth / 2 - $longitud_variable, 4, utf8_decode('XOCHIMILCO 16010'), 0, 2, 'L', 0);
    // $pdf->cell($contentWidth / 2 - $longitud_variable, 4, utf8_decode('Registration: 31-08771-1'), 0, 2, 'L', 0);
    $pdf->cell($contentWidth / 2 - $longitud_variable, 4, utf8_decode('55 56 75 33 05  55 55 55 3589'), 0, 2, 'L', 0);


    // 1.2
    $alto_celda = 4;
    $x_celdas = $pdf->marginX + $contentWidth * .5;
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->setXY($x_celdas, $Id1);
    $pdf->cell($contentWidth / 4, $alto_celda, utf8_decode('I.2. Bezugsnr. der Bescheinigung'), 'TR', 2, 'L', 0);
    $pdf->SetFont('Arial', '', 8);
    $pdf->cell($contentWidth / 4, $alto_celda, utf8_decode(''), 'BR', 0, 'L', 0);

    // 1.2.a
    $x_celdas = $pdf->marginX + $contentWidth * .75;
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->setXY($x_celdas, $Id1);
    $pdf->cell($contentWidth / 4, $alto_celda, utf8_decode('I.2.a IMSOC-Bezugsnr.'), 'TR', 2, 'L', 0);
    $pdf->SetFont('Arial', '', 8);
    $pdf->cell($contentWidth / 4, $alto_celda, utf8_decode(''), 'BR', 0, 'L', 0);

    // 1.3

    $x_celdas = $pdf->marginX + $contentWidth * .5;
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->setXY($x_celdas, $Id1 + $alto_celda * 2);
    $pdf->cell($contentWidth / 2, $alto_celda, utf8_decode('I.3. Zuständige oberste Behörde'), 'R', 2, 'L', 0);
    $pdf->SetFont('Arial', '', 8);
    $pdf->cell($contentWidth / 2, $alto_celda, utf8_decode('SAGARPA'), 'BR', 0, 'L', 0);

    // 1.4

    $x_celdas = $pdf->marginX + $contentWidth * .5;
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->setXY($x_celdas, $Id1 + $alto_celda * 4);
    $pdf->cell($contentWidth / 2, $alto_celda, utf8_decode('I.4. Zuständige örtliche Behörde'), 'R', 2, 'L', 0);
    $pdf->SetFont('Arial', '', 8);
    $pdf->cell($contentWidth / 2, $alto_celda + 1, utf8_decode('SENASICA, SAGARPA'), 'BR', 0, 'L', 0);


    // 1.5
    $alto_1d5 = 20;
    $Id5 = $pdf->getY() + $alto_celda + 1;
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->setXY($pdf->marginX, $Id5);
    $pdf->cell($contentWidth / 2, $alto_1d5 * .20, utf8_decode('I.5. Empfänger/Einfüher'), 'LR', 2, 'L', 0);
    $pdf->cell($contentWidth / 2, $alto_1d5 * .20, utf8_decode('Name'), 'LR', 2, 'L', 0);
    $pdf->cell($contentWidth / 2, $alto_1d5 * .20, utf8_decode('Anschrift'), 'LR', 2, 'L', 0);
    $pdf->cell($contentWidth / 2, $alto_1d5 * .20, utf8_decode('Postleitzahl'), 'LR', 2, 'L', 0);
    $pdf->cell($contentWidth / 2, $alto_1d5 * .20, utf8_decode('Tel.-Nr'), 'LBR', 2, 'L', 0);

    // 1.5 data
    $longitud_variable = 21;
    $pdf->SetFont('Arial', '', 8);
    $pdf->setXY($pdf->marginX + $longitud_variable, $Id5);
    $pdf->cell($contentWidth / 2 - $longitud_variable, 4, utf8_decode(''), 0, 2, 'L', 0);
    $pdf->cell($contentWidth / 2 - $longitud_variable, 4, utf8_decode($datosCliente->nombre), 0, 2, 'L', 0);
    $pdf->cell($contentWidth / 2 - $longitud_variable, 4, utf8_decode($datosCliente->calle . ' ' . $datosCliente->numeroExterior), 0, 2, 'L', 0);
    $pdf->cell($contentWidth / 2 - $longitud_variable, 4, utf8_decode($datosCliente->codigoPostal), 0, 2, 'L', 0);
    $pdf->cell($contentWidth / 2 - $longitud_variable, 4, utf8_decode($datosDestino->ladaDestino . ' ' . $datosDestino->telefonoDestino), 0, 2, 'L', 0);


    // 1.6
    $alto_celda = 20;
    $x_celdas = $pdf->marginX + $contentWidth * .5;
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->setXY($x_celdas, $Id5);
    $pdf->cell($contentWidth / 2, $alto_celda * .25, utf8_decode('I.6. Für die Sendung verantwortlicher Unternehmer'), 'R', 2, 'L', 0);
    $pdf->cell($contentWidth / 2, $alto_celda * .75, utf8_decode(''), 'RB', 2, 'L', 0);
    $pdf->Line($x_celdas, $Id5 + $alto_celda, $pdf->marginX + $contentWidth, $Id5);

    // 1.7
    $alto_1d7 = 10;
    $ancho_1d7 = $contentWidth / 2 * .4;
    $Id7 = $pdf->getY();
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->setXY($pdf->marginX, $Id7);
    $pdf->cell($ancho_1d7, $alto_1d7 * .5, utf8_decode('I.7. Ursprungsland'), 'L', 2, 'L', 0);
    $pdf->SetFont('Arial', '', 8);
    $pdf->cell($ancho_1d7, $alto_1d7 * .5, utf8_decode($datosProducto->origenMP), 'LBR', null, 'C', 0);

    // 1.7 ISO Code
    $ancho_1d7ISO_Code = $contentWidth / 2 * .3;
    $pdf->setXY($pdf->marginX + $ancho_1d7, $Id7);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->cell($ancho_1d7ISO_Code, 5, utf8_decode('ISO'), 'R', 2, 'C', 0);
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
    $pdf->cell($ancho_1d9, 5, utf8_decode('I.9. Bestimmungsland'), 'L', 2, 'L', 0);
    $pdf->SetFont('Arial', '', 8);
    $pdf->cell($ancho_1d7, 5, utf8_decode($datosProducto->paisDestino), 'LBR', null, 'C', 0);

    // 1.9 ISO Code
    $ancho_1d9ISO_Code = $contentWidth / 2 * .3;
    $pdf->setXY($pdf->marginX + $ancho_1d9 + $contentWidth / 2, $Id9);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->cell($ancho_1d9ISO_Code, 5, utf8_decode('ISO'), 'R', 2, 'C', 0);
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
    $pdf->cell($contentWidth / 2.86, $alto_1d11 / 6, utf8_decode('I.11. Versandort'), 'LR', 2, 'L', 0);
    $pdf->cell($contentWidth / 2.86, $alto_1d11 / 6, utf8_decode('Name'), 'LR', 2, 'L', 0);
    $pdf->cell($contentWidth / 2.86, $alto_1d11 / 6, utf8_decode('Anschrift'), 'LR', 2, 'L', 0);
    $pdf->cell($contentWidth / 2.86, $alto_1d11 / 6, utf8_decode(''), 'LR', 2, 'L', 0);
    $pdf->cell($contentWidth / 2.86, $alto_1d11 / 6, utf8_decode(''), 'LR', 2, 'L', 0);
    $pdf->cell($contentWidth / 2.86, $alto_1d11 / 6, utf8_decode(''), 'LBR', 2, 'L', 0);

    // 1.11 data
    $longitud_variable = 21;
    $pdf->SetFont('Arial', '', 8);
    $pdf->setXY($pdf->marginX + $longitud_variable, $Id11 + 7);
    $pdf->cell($contentWidth / 2 - $longitud_variable, 4, utf8_decode('OAXACA MIEL S.A. DE C.V.'), 0, 2, 'L', 0);
    $pdf->cell($contentWidth / 2 - $longitud_variable, 4, utf8_decode('Ctra. MÉRIDA-CANCÚN KM.7.5,'), 0, 2, 'L', 0);
    $pdf->cell($contentWidth / 2 - $longitud_variable, 4, utf8_decode('SAN PEDRO NOH PAT, KANASÍN,'), 0, 2, 'L', 0);
    $pdf->cell($contentWidth / 2 - $longitud_variable, 4, utf8_decode('YUCATÁN, C.P. 94590.'), 0, 2, 'L', 0);
    $pdf->cell($contentWidth / 2 - $longitud_variable, 4, utf8_decode(''), 0, 2, 'L', 0);

    $ancho_Id8 = $contentWidth / 2 * .3;
    $Id12 = $Id11;
    $x_celda = $pdf->marginX + $ancho_1d7 + $ancho_1d7ISO_Code;
    $alto_celda = 30;
    $pdf->setXY($x_celda, $Id12);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->cell($ancho_Id8, $alto_celda / 2, utf8_decode('Zulassungsnummer'), 'R', 2, 'L', 0);
    $pdf->cell($ancho_Id8, $alto_celda / 2, utf8_decode(''), 'RB', null, 'L', 0);

    // 1.12
    $x_celda = $pdf->marginX + $contentWidth / 2;
    $Id12 = $Id11;
    $alto_titulo = 5;
    $alto_celda = 25;
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->setXY($x_celda, $Id12);
    $pdf->cell($contentWidth / 2.86, $alto_titulo, utf8_decode('I.12.Bestimmungsort'), 'R', 2, 'L', 0);
    $pdf->cell($contentWidth / 2.86, 2, utf8_decode('Anschrift'), 'LR', 2, 'L', 0);
    $pdf->cell($contentWidth / 2.86, 23, utf8_decode('Name'), 'RB', null, 'L', 0);
    // $pdf->Line($x_celda, $Id12 + $alto_celda + $alto_titulo, $pdf->marginX + $contentWidth, $Id12);

    $ancho_Id10 = $contentWidth / 2 * .3;
    $Id12 = $Id11;
    $x_celda = $pdf->marginX + $ancho_1d9 + $ancho_1d9ISO_Code + $contentWidth / 2;
    $alto_celda = 30;
    $pdf->setXY($x_celda, $Id12);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->cell($ancho_Id10, $alto_celda / 2, utf8_decode(''), 'R', 2, 'L', 0);
    $pdf->cell($ancho_Id10, $alto_celda / 2, utf8_decode(''), 'RB', null, 'L', 0);

    // 1.13
    $alto_1d13 = 10;
    $Id13 = $pdf->getY() + 15;
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->setXY($pdf->marginX, $Id13);
    $pdf->cell($contentWidth / 2, $alto_1d13 / 2, utf8_decode('I.13. Verladeort'), 'LR', 2, 'L', 0);
    $pdf->SetFont('Arial', '', 8);
    $pdf->cell($contentWidth / 2, $alto_1d13 / 2, utf8_decode($datosTransporte->lugarEmbarque), 'LRB', null, 'L', 0);

    // 1.14
    $Id14 = $Id13;
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->setXY($pdf->marginX + $contentWidth / 2, $Id14);
    $pdf->cell($contentWidth / 2, 5, utf8_decode('I.14. Datum und Uhrzeit des Abtransports'), 'R', 2, 'L', 0);
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
    $pdf->Text($x_title, $lateral_title_y, utf8_decode('Teil I: Angaben zur Sendung'));
    $pdf->Rotate(0);

    // 1.15

    $Id15 = $Id13 + $alto_1d13;
    $alto_1d15 = 60;
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->setXY($pdf->marginX, $Id15);
    $pdf->cell($contentWidth / 2, $alto_1d15 / 6, utf8_decode('I.15. Transportmittel'), 'LR', 2, 'L', 0);
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->cell(($contentWidth / 2) / 3, $alto_1d15 / 6, utf8_decode('Flugzeug'), 'L', 0, 'L', 0);
    $pdf->cell(($contentWidth / 2) / 3, $alto_1d15 / 6, utf8_decode('Schiff'), '', 0, 'L', 0);
    $pdf->cell(($contentWidth / 2) / 3, $alto_1d15 / 6, utf8_decode('Andere'), 'R', 2, 'L', 0);
    $pdf->setX($pdf->marginX);
    $pdf->cell(($contentWidth / 2) / 3, $alto_1d15 / 6, utf8_decode('Strassenfahrzeug'), 'L', 0, 'L', 0);
    $pdf->cell(($contentWidth / 2) / 3, $alto_1d15 / 6, utf8_decode('Eisenbahn'), '', 0, 'L', 0);
    $pdf->cell(($contentWidth / 2) / 3, $alto_1d15 / 6, utf8_decode(''), 'R', 2, 'L', 0);
    $pdf->setX($pdf->marginX);
    $pdf->cell($contentWidth / 2, $alto_1d15 / 6, utf8_decode(''), 'LR', 2, 'L', 0);
    $pdf->cell($contentWidth / 2, $alto_1d15 / 6, utf8_decode('Kennzeichnung: ' . $datosAnexos->identificacion), 'LBR', 2, 'L', 0);
    // $pdf->cell($contentWidth / 2, $alto_1d15 / 6, utf8_decode('Identification: Ship: ' . $datosAnexos->identificacion), 'LR', 2, 'L', 0);
    // $pdf->cell($contentWidth / 2, $alto_1d15 / 6, utf8_decode('Documentary references: BL: ' . $datosAnexos->referenciasDocumentales), 'LBR', 0, 'L', 0);

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
    $pdf->setX($pdf->marginX + 3 + $pdf->GetStringWidth('Road Vehicle') + 2);
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
    $pdf->cell($contentWidth / 2, 5, utf8_decode('I.16. Eingangsgrenzkontrollstelle'), 'R', 2, 'L', 0);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->cell($contentWidth / 2, 10, utf8_decode($datosTransporte->puntoIngreso), 'RB', 2, 'L', 0);

    // 1.17
    $Id17 = $pdf->getY();
    $x_celda = $pdf->marginX + $contentWidth / 2;
    $alto_1d17 = 50;
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->setX($x_celda);
    $pdf->cell($contentWidth / 2, $alto_1d17 / 2, utf8_decode('I.17. Begleitdokumente '), 'LR', 2, 'L', 0);
    $pdf->cell($contentWidth / 2, $alto_1d17 / 2, utf8_decode('Art'), 'LR', 2, 'L', 0);
    $pdf->cell($contentWidth / 2, $alto_1d17 / 2, utf8_decode('Nr.'), 'LBR', 2, 'L', 0);
    // $pdf->Line($x_celda, $Id17 + $alto_1d17, $pdf->marginX + $contentWidth, $Id17);

    // 1.18
    $Id18 = $Id15 + $alto_1d15;
    $alto_1d18 = 33;
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->setXY($pdf->marginX, $Id18 - 10);
    $pdf->cell($contentWidth * .7, 7, utf8_decode('I.18. Beförderungsbedingungen'), 'L', 2, 'L', 0);
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->cell(($contentWidth / 2) / 3, $alto_1d18, utf8_decode('Umgebungstemperatur'), 'LB', 0, 'L', 0);
    $pdf->cell(($contentWidth / 2) / 3, $alto_1d18, utf8_decode('Gekühlt'), 'B', 0, 'C', 0);
    $pdf->cell(($contentWidth / 2) / 3, $alto_1d18, utf8_decode('Gefroren'), 'BR', 2, 'L', 0);
    // 1.18 data
    $pdf->setXY($pdf->marginX, $Id18 + $alto_1d18 - 36);
    $pdf->SetFont('Arial', '', 8);
    $pdf->setX($pdf->marginX + (($contentWidth / 2) / 3) * .5 + $pdf->GetStringWidth('Ambient') + 2);
    $pdf->SetFont('ZapfDingbats', '', 10);
    $pdf->cell(5, $alto_1d18, utf8_decode('5'), 0, 0, 'L', 0);

    $pdf->SetFont('Arial', '', 8);
    $pdf->setX($pdf->marginX + (($contentWidth / 2) / 3) * 1.5 + $pdf->GetStringWidth('Chilled') + 2);
    $pdf->SetFont('ZapfDingbats', '', 10);
    $pdf->cell(5, $alto_1d18, utf8_decode('r'), 0, 0, 'L', 0);

    $pdf->SetFont('Arial', '', 8);
    $pdf->setX($pdf->marginX + (($contentWidth / 2) / 3) * 2 + $pdf->GetStringWidth('Frozen') + 2);
    $pdf->SetFont('ZapfDingbats', '', 10);
    $pdf->cell(5, $alto_1d18, utf8_decode('r'), 0, 0, 'L', 0);

    // 1.19
    $Id19 = $pdf->getY();
    $ancho_columnas = $contentWidth / 6;
    $pdf->setXY($pdf->marginX, $Id19 + 33);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->cell($contentWidth, 5, utf8_decode('I.19. Container-/Plombennummer'), 'LR', 2, 'L', 0);
    $pdf->SetFont('Arial', '', 8);
    $pdf->cell($contentWidth, 5, utf8_decode(''), 'LBR', 2, 'L', 0);

    //-----------------------------------------------------------------------------------------------

    $pdf->addPage('P', 'A4');
    $pdf->setXY($pdf->marginX, 10);
    $pdf->SetFont('Arial', 'B', 10);
    // $pdf->Multicell($contentWidth, 5, utf8_decode('HEALTH CERTIFICATE FOR IMPORTS OF HONEY AND OTHER APICULTURE PRODUCTS INTENDED FOR HUMAN CONSUMPTION'), 0, 'C', 0);

    $table_titles = $pdf->getY();
    $pdf->setXY($pdf->marginX, $table_titles + 10);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->cell($contentWidth / 2, 5, utf8_decode('LAND'), 'B', 0, 'L', 0);
    $pdf->cell($contentWidth / 2, 5, utf8_decode('Amtliche Bescheinigung für den Eingang in die EU'), 'B', 1, 'R', 0);



    // 1.20
    $pdf->setXY($pdf->marginX, 25);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->cell($contentWidth / 4, 4, utf8_decode('I.20. Waren zertifiziert als:'), 'LR', 2, 'L', 0);
    $pdf->cell($contentWidth / 4, 20, utf8_decode('Lebensmittel'), 'LBR', 0, 'L', 0);
    // $pdf->cell($contentWidth * .2, 4, utf8_decode(number_format($datosAnexos->cantidad, 2, '.', ',') . ' / ' . number_format($datosProducto->pesoNeto, 2, '.', ',')), 'LBR', 2, 'L', 0);

    //1.20 data
    $pdf->setXY($pdf->marginX, $pdf->getY() + 8);
    $pdf->SetFont('Arial', '', 8);
    $pdf->setX($pdf->marginX + $pdf->GetStringWidth('Human consumption') + 4);
    $pdf->SetFont('ZapfDingbats', '', 10);
    $pdf->cell(5, 5, utf8_decode('5'), 0, 0, 'L', 0);

    // vacio
    $pdf->setXY($pdf->marginX + 47.5, 25);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->cell($contentWidth / 4, 4, utf8_decode(''), '', 2, 'L', 0);
    $pdf->cell($contentWidth / 4, 20, utf8_decode(''), 'B', 0, 'L', 0);
    // $pdf->cell($contentWidth * .2, 4, utf8_decode(number_format($datosAnexos->cantidad, 2, '.', ',') . ' / ' . number_format($datosProducto->pesoNeto, 2, '.', ',')), 'LBR', 2, 'L', 0);
    // vacio2
    $pdf->setXY($pdf->marginX + 95, 25);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->cell($contentWidth / 4, 4, utf8_decode(''), 'L', 2, 'L', 0);
    $pdf->cell($contentWidth / 4, 20, utf8_decode(''), 'LB', 0, 'L', 0);
    // $pdf->cell($contentWidth * .2, 4, utf8_decode(number_format($datosAnexos->cantidad, 2, '.', ',') . ' / ' . number_format($datosProducto->pesoNeto, 2, '.', ',')), 'LBR', 2, 'L', 0);
    // vacio3
    $pdf->setXY($pdf->marginX + 142.35, 25);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->cell($contentWidth / 4, 4, utf8_decode(''), 'LR', 2, 'L', 0);
    $pdf->cell($contentWidth / 4, 20, utf8_decode(''), 'LBR', 0, 'L', 0);
    // $pdf->cell($contentWidth * .2, 4, utf8_decode(number_format($datosAnexos->cantidad, 2, '.', ',') . ' / ' . number_format($datosProducto->pesoNeto, 2, '.', ',')), 'LBR', 2, 'L', 0);


    // 1.21
    $Id21 = $pdf->getY() + 20;
    $pdf->setXY($pdf->marginX, $Id21);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->cell($contentWidth / 2, 4, utf8_decode('1.21'), 'LR', 2, 'L', 0);
    $pdf->cell($contentWidth / 2, 20, utf8_decode(''), 'LBR', 0, 'L', 0);
    // $pdf->SetFont('Arial', 'B', 8);
    // $pdf->cell($ancho_estandar_Id18, 5, utf8_decode('I.21. Temperature of product'), 'LR', 2, 'L', 0);
    // $pdf->SetFont('Arial', 'B', 7);
    // $pdf->cell($ancho_estandar_Id18 / 3, 5, utf8_decode('Ambient'), 'LB', 0, 'L', 0);
    // $pdf->cell($ancho_estandar_Id18 / 3, 5, utf8_decode('Chilled'), 'B', 0, 'L', 0);
    // $pdf->cell($ancho_estandar_Id18 / 3, 5, utf8_decode('Frozen'), 'RB', 0, 'L', 0);


    // 1.21 data
    $pdf->setXY($pdf->marginX, $Id21 + 5);
    // $pdf->SetFont('Arial', '', 8);
    // $pdf->setX($pdf->marginX + $pdf->GetStringWidth('Ambient') + 2);
    // $pdf->SetFont('ZapfDingbats', '', 10);
    // $pdf->cell(5, 5, utf8_decode('5'), 0, 0, 'L', 0);

    // $pdf->SetFont('Arial', '', 8);
    // $pdf->setX($pdf->marginX + $ancho_estandar_Id18 / 3 + $pdf->GetStringWidth('Chilled') + 2);
    // $pdf->SetFont('ZapfDingbats', '', 10);
    // $pdf->cell(5, 5, utf8_decode('r'), 0, 0, 'L', 0);

    // $pdf->SetFont('Arial', '', 8);
    // $pdf->setX($pdf->marginX + ($ancho_estandar_Id18 / 3) * 2 + $pdf->GetStringWidth('Frozen') + 2);
    // $pdf->SetFont('ZapfDingbats', '', 10);
    // $pdf->cell(5, 5, utf8_decode('r'), 0, 0, 'L', 0);

    // 1.22
    $Id22 = $pdf->getY() - 5;
    $pdf->setXY($pdf->marginX + 94.90, $Id22);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->cell($contentWidth / 2, 4, utf8_decode('1.22'), 'R', 2, 'L', 0);
    $pdf->cell($contentWidth / 2, 20, utf8_decode(''), 'BR', 0, 'L', 0);
    //$pdf->setXY($pdf->marginX + $ancho_estandar_Id18, $Id21);
    // $pdf->SetFont('Arial', 'B', 8);
    // $pdf->cell($contentWidth * .2, 5, utf8_decode('I.22. Number of packages'), 'R', 2, 'L', 0);
    //$pdf->cell($contentWidth * .2, 5, utf8_decode(number_format($datosProducto->cantidad, 0, '.', ',')), 'RB', 1, 'L', 0);

    // 1.23
    $Id23 = $pdf->getY() + 20;
    $pdf->setXY($pdf->marginX, $Id23);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->cell($contentWidth / 4, 5, utf8_decode('I.23. Anzahl Packstücke'), 'LR', 2, 'L', 0);
    $pdf->cell($contentWidth / 4, 12, utf8_decode(number_format($datosProducto->cantidad, 0, '.', ',')), 'LBR', 0, 'L', 0);
    // $pdf->cell($ancho_estandar_Id18 / 2, 5, utf8_decode('SEAL: ' . $datosAnexos->sello), 'BR', null, 'L', 0);

    // 1.24
    $pdf->setXY($pdf->marginX + 47.5, $Id23);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->cell($contentWidth / 4, 5, utf8_decode('I.24. Menge Gesamtanzahl'), 'R', 2, 'L', 0);
    $pdf->cell($contentWidth / 4, 12, utf8_decode(number_format($datosAnexos->cantidad, 2, '.', ',') . ' / ' . number_format($datosProducto->pesoNeto, 2, '.', ',')), 'B', 2, 'L', 0);
    // $pdf->cell($contentWidth * .2, 5, utf8_decode($datosProducto->presentacion), 'RB', 2, 'L', 0);

    //totalNetWeight
    $pdf->setXY($pdf->marginX + 95, $Id23);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->cell($contentWidth / 4, 5, utf8_decode(number_format($datosProducto->pesoNeto, 2, '.', ',') . ' ' . $datosProducto->unidadMedida), 'R', 2, 'C', 0);
    $pdf->cell($contentWidth / 4, 12, utf8_decode('Gesamtnettogewicht (Kg)'), 'LBR', 2, 'L', 0);

    //totalGrossWeight(Kg)
    $pdf->setXY($pdf->marginX + 142.35, $Id23);
    $pdf->SetFont('Arial', 'B', 8);
    // $pdf->cell($contentWidth / 4, 5, utf8_decode('13'), 'R', 2, 'C', 0);
    $pdf->cell($contentWidth / 4, 5, utf8_decode(number_format($datosProducto->pesoBruto, 2, '.', ',') . ' ' . $datosProducto->unidadMedida), 'R', 2, 'C', 0);
    $pdf->cell($contentWidth / 4, 12, utf8_decode('Gesamtbruttogewicht (Kg)'), 'BR', 2, 'L', 0);

    // 1.25
    $Id25 = $pdf->getY();
    $pdf->setXY($pdf->marginX - .05, $Id25);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->cell($contentWidth, 5, utf8_decode('1.25. Beschreibung der Ware'), 'LR', 2, 'L', 0);
    $pdf->cell($contentWidth / 4, 20, utf8_decode('    Nr'), 'LB', 0, 'L', 0);
    $pdf->cell($contentWidth / 1.333, 20, utf8_decode('Code und KN-Bezeichnung'), 'BR', 2, 'L', 0);


    // 1.25 data
    $pdf->setXY($pdf->marginX, $Id25 + 5);
    // $pdf->SetFont('Arial', '', 8);
    // $pdf->setX($pdf->marginX + $pdf->GetStringWidth('Human consumption') + 2);
    // $pdf->SetFont('ZapfDingbats', '', 10);
    // $pdf->cell(5, 5, utf8_decode('5'), 0, 0, 'L', 0);

    //Species
    $IdF = $pdf->GetY();
    $pdf->setXY($pdf->marginX, $IdF + 20);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->cell($contentWidth / 3, 10, utf8_decode('Art (wissenschaftliche Bezeichnung)'), 'LR', 2, 'C', 0);    
    $pdf->cell($contentWidth / 6, 10, utf8_decode('Endverbraucher'), 'L', 0, 'C', 0);
    $pdf->cell($contentWidth / 6, 10, utf8_decode('Anzahl Packstücke'), 'R', 0, 'C', 0);

    //species data
    $IdF = $pdf->GetY();
    $pdf->setXY($pdf->marginX, $IdF + 10);
    $pdf->SetFont('Arial', 'B', 8);
    // $pdf->cell($contentWidth / 6, .75, utf8_decode($datosProducto->especie), 0, 'C', 0);
    $pdf->SetFont('ZapfDingbats', '', 8);
    $pdf->cell($contentWidth / 6, 10, utf8_decode('r'), 'LB', 0, 'C', 0);
    $pdf->cell($contentWidth / 6, 10, utf8_decode(''), 'BR', 2, 'C', 0);

    //Manufacturing
    $IdF = $pdf->GetY();
    $pdf->setXY($pdf->marginX + 63.5, $IdF - 30);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->cell($contentWidth / 3, 10, utf8_decode('Herstellungsbetrieb'), 'R', 2, 'C', 0);
    $pdf->cell($contentWidth / 6, 10, utf8_decode('Nettogewicht'), '', 0, 'C', 0);
    $pdf->cell($contentWidth / 6, 10, utf8_decode('Chargen-Nr.'), 'R', 0, 'C', 0);

    //Manufacturing data
    $IdF = $pdf->GetY();
    $pdf->setXY($pdf->marginX + 63.5, $IdF + 10);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->cell($contentWidth / 6, 10, utf8_decode(''), 'B', 0, 'C', 0);
    $pdf->cell($contentWidth / 6, 10, utf8_decode(''), 'BR', 2, 'C', 0);

    //Treatment
    $IdF = $pdf->GetY();
    $pdf->setXY($pdf->marginX + 126.70, $IdF - 30);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->cell($contentWidth / 3, 10, utf8_decode('Art der Behandlung Kühllager'), 'R', 2, 'C', 0);
    $pdf->cell($contentWidth / 3, 10, utf8_decode('Art der Verpackung'), 'R', 2, 'C', 0);
    $pdf->cell($contentWidth / 3, 10, utf8_decode(''), 'BR', 2, 'C', 0);

    // ----------------------------------------------------------------

    // 1.26
    $Id26 = $pdf->getY() + 5;
    $pdf->setXY($pdf->marginX, $Id26);
    // $pdf->SetFont('Arial', '', 8);
    // $pdf->cell($contentWidth / 2, 5, utf8_decode('I.26.'), 'LR', 2, 'L', 0);
    // $pdf->cell($contentWidth / 2, 10, utf8_decode(''), 'LRB', 2, 'L', 0);
    // //$pdf->Line($pdf->marginX, $Id26 + 15, $pdf->marginX + $contentWidth / 2, $Id26);

    // 1.27
    $Id27 = $Id26;
    $pdf->setXY($pdf->marginX + $contentWidth / 2, $Id27);
    // $pdf->SetFont('Arial', 'B', 8);
    // $pdf->cell($contentWidth / 2, 5, utf8_decode('I.27. For import or admission into EU'), 'LR', 2, 'L', 0);
    // $pdf->SetFont('ZapfDingbats', '', 10);
    // $pdf->cell($contentWidth / 2, 10, utf8_decode('5'), 'LRB', 2, 'L', 0);

    // 1.28

    $Id28 = $pdf->getY();
    $ancho_columnas = $contentWidth / 6;
    $pdf->setXY($pdf->marginX, $Id28);
    // $pdf->SetFont('Arial', 'B', 8);
    // $pdf->cell($contentWidth, 5, utf8_decode('I.28. Identification of the commodities'), 'LR', 2, 'L', 0);
    // $pdf->SetFont('Arial', '', 8);
    // $pdf->cell($contentWidth, 5, utf8_decode(''), 'LR', 2, 'L', 0);
    // $pdf->cell($ancho_columnas, 4, utf8_decode('Species'), 'L', 0, 'C', 0);
    // $pdf->cell($ancho_columnas, 4, utf8_decode('Treatment type'), 0, 0, 'C', 0);
    // $pdf->cell($ancho_columnas * 2, 4, utf8_decode('Approval number of establishments'), 0, 0, 'C', 0);
    // $pdf->cell($ancho_columnas, 4, utf8_decode('Number of packages'), 0, 0, 'C', 0);
    // $pdf->cell($ancho_columnas, 4, utf8_decode('Net Weight'), 'R', 1, 'C', 0);

    // $pdf->cell($ancho_columnas, 4, utf8_decode('(Scientific Name)'), 'L', 0, 'C', 0);
    // $pdf->cell($ancho_columnas, 4, utf8_decode(''), 0, 0, 'C', 0);
    // $pdf->cell($ancho_columnas * 2, 4, utf8_decode('Manufacturing plant'), 0, 0, 'C', 0);
    // $pdf->cell($ancho_columnas, 4, utf8_decode(''), 0, 0, 'C', 0);
    // $pdf->cell($ancho_columnas, 4, utf8_decode(''), 'R', 1, 'C', 0);

    // $pdf->cell($ancho_columnas, 4, utf8_decode(''), 'L', 0, 'C', 0);
    // $pdf->cell($ancho_columnas, 4, utf8_decode(''), 0, 0, 'C', 0);
    // $pdf->cell($ancho_columnas * 2, 4, utf8_decode(''), 0, 0, 'C', 0);
    // $pdf->cell($ancho_columnas, 4, utf8_decode(''), 0, 0, 'C', 0);
    // $pdf->cell($ancho_columnas, 4, utf8_decode(''), 'R', 1, 'C', 0);

    // 1.28 data
    $y_actual = $pdf->getY();

    $pdf->SetFont('Arial', 'B', 8);

    // Primera columna
    // $pdf->cell($ancho_columnas, 30, '', 'LB', null, 'L', 0); 
    $pdf->setXY($pdf->marginX, $y_actual);
    // $pdf->Multicell($ancho_columnas, 5, utf8_decode($datosProducto->especie), 0, 'C', 0);

    // Segunda columna
    $pdf->setXY($pdf->marginX + $ancho_columnas, $y_actual);
    // $pdf->cell($ancho_columnas, 30, '', 'B', null, 'L', 0);
    $pdf->setXY($pdf->marginX + $ancho_columnas, $y_actual);
    // $pdf->Multicell($ancho_columnas, 5, utf8_decode($datosProducto->tratamientoProceso), 0, 'C', 0);

    // Tercera columna
    // $pdf->SetFont('Arial', '', 8);
    $pdf->setXY($pdf->marginX + $ancho_columnas * 2, $y_actual);
    // $pdf->cell($ancho_columnas * 2, 30, '', 'B', null, 'L', 0);
    $pdf->setXY($pdf->marginX + $ancho_columnas * 2, $y_actual);
    // $pdf->cell($ancho_columnas * 2, 5, utf8_decode('OAXACA MIEL S.A. DE C.V.'), 0, 2, 'C', 0);
    // $pdf->cell($ancho_columnas * 2, 5, utf8_decode('CARRETERA MÉRIDA-CANCÚN KM. 7.5,'), 0, 2, 'C', 0);
    // $pdf->cell($ancho_columnas * 2, 5, utf8_decode('SAN PEDRO NOH PAT, KANASÍN,'), 0, 2, 'C', 0);
    // $pdf->cell($ancho_columnas * 2, 5, utf8_decode('YUCATÁN, C.P. 97370.'), 0, 2, 'C', 0);
    // $pdf->cell($ancho_columnas * 2, 5, utf8_decode('TEL.: (01 999) 9 88 09 90'), 0, 2, 'C', 0);
    // $pdf->SetFont('Arial', 'B', 10);
    // $pdf->cell($ancho_columnas * 2, 5, utf8_decode('31-08771-1'), 0, 2, 'C', 0);

    // Cuarta columna
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->setXY($pdf->marginX + $ancho_columnas * 4, $y_actual);
    // $pdf->cell($ancho_columnas, 30, '', 'B', null, 'L', 0);
    $pdf->setXY($pdf->marginX + $ancho_columnas * 4, $y_actual);
    // $pdf->cell($ancho_columnas, 5, utf8_decode(number_format($datosProducto->cantidad, 0, '.', ',')), 0, 2, 'C', 0);
    // $pdf->cell($ancho_columnas, 5, utf8_decode($datosProducto->marcaDistintiva), 0, 2, 'C', 0);

    // Quinta columna

    $pdf->SetFont('Arial', 'B', 8);
    $pdf->setXY($pdf->marginX + $ancho_columnas * 5, $y_actual);
    // $pdf->cell($ancho_columnas, 30, '', 'BR', null, 'L', 0);
    $pdf->setXY($pdf->marginX + $ancho_columnas * 5, $y_actual);
    // $pdf->cell($ancho_columnas, 5, utf8_decode(number_format($datosProducto->pesoNeto, 2, '.', ',') . ' ' . $datosProducto->unidadMedida), 0, 2, 'C', 0);



    //--------------------------------------------------------------------------------------

    // // 1.20
    // $pdf->setXY($pdf->marginX + $ancho_estandar_Id18, $id18 + 8);
    // $pdf->SetFont('Arial', 'B', 8);
    // $pdf->cell($contentWidth * .2, 4, utf8_decode('I.20. Quantity'), 'LR', 2, 'L', 0);
    // $pdf->cell($contentWidth * .2, 4, utf8_decode(number_format($datosAnexos->cantidad, 2, '.', ',') . ' / ' . number_format($datosProducto->pesoNeto, 2, '.', ',')), 'LBR', 2, 'L', 0);


    // // 1.21
    // $Id21 = $pdf->getY();
    // $pdf->setXY($pdf->marginX, $Id21);
    // $pdf->SetFont('Arial', 'B', 8);
    // $pdf->cell($ancho_estandar_Id18, 5, utf8_decode('I.21. Temperature of product'), 'LR', 2, 'L', 0);
    // $pdf->SetFont('Arial', 'B', 7);
    // $pdf->cell($ancho_estandar_Id18 / 3, 5, utf8_decode('Ambient'), 'LB', 0, 'L', 0);
    // $pdf->cell($ancho_estandar_Id18 / 3, 5, utf8_decode('Chilled'), 'B', 0, 'L', 0);
    // $pdf->cell($ancho_estandar_Id18 / 3, 5, utf8_decode('Frozen'), 'RB', 0, 'L', 0);


    // // 1.21 data
    // $pdf->setXY($pdf->marginX, $Id21 + 5);
    // $pdf->SetFont('Arial', '', 8);
    // $pdf->setX($pdf->marginX + $pdf->GetStringWidth('Ambient') + 2);
    // $pdf->SetFont('ZapfDingbats', '', 10);
    // $pdf->cell(5, 5, utf8_decode('5'), 0, 0, 'L', 0);

    // $pdf->SetFont('Arial', '', 8);
    // $pdf->setX($pdf->marginX + $ancho_estandar_Id18 / 3 + $pdf->GetStringWidth('Chilled') + 2);
    // $pdf->SetFont('ZapfDingbats', '', 10);
    // $pdf->cell(5, 5, utf8_decode('r'), 0, 0, 'L', 0);

    // $pdf->SetFont('Arial', '', 8);
    // $pdf->setX($pdf->marginX + ($ancho_estandar_Id18 / 3) * 2 + $pdf->GetStringWidth('Frozen') + 2);
    // $pdf->SetFont('ZapfDingbats', '', 10);
    // $pdf->cell(5, 5, utf8_decode('r'), 0, 0, 'L', 0);

    // // 1.22
    // $pdf->setXY($pdf->marginX + $ancho_estandar_Id18, $Id21);
    // $pdf->SetFont('Arial', 'B', 8);
    // $pdf->cell($contentWidth * .2, 5, utf8_decode('I.22. Number of packages'), 'R', 2, 'L', 0);
    // $pdf->cell($contentWidth * .2, 5, utf8_decode(number_format($datosProducto->cantidad, 0, '.', ',')), 'RB', 1, 'L', 0);

    // // 1.23
    // $Id23 = $pdf->getY();
    // $pdf->setXY($pdf->marginX, $Id23);
    // $pdf->SetFont('Arial', 'B', 8);
    // $pdf->cell($ancho_estandar_Id18, 5, utf8_decode('I.23. Identification of container/Seal number'), 'LR', 2, 'L', 0);
    // $pdf->cell($ancho_estandar_Id18 / 2, 5, utf8_decode('CONTAINER: ' . $datosAnexos->idContenedor), 'LB', 0, 'L', 0);
    // $pdf->cell($ancho_estandar_Id18 / 2, 5, utf8_decode('SEAL: ' . $datosAnexos->sello), 'BR', null, 'L', 0);

    // // 1.24
    // $pdf->setXY($pdf->marginX + $ancho_estandar_Id18, $Id23);
    // $pdf->SetFont('Arial', 'B', 8);
    // $pdf->cell($contentWidth * .2, 5, utf8_decode('I.24. Type of packaging'), 'R', 2, 'L', 0);
    // $pdf->cell($contentWidth * .2, 5, utf8_decode($datosProducto->presentacion), 'RB', 2, 'L', 0);

    // // 1.25
    // $Id25 = $pdf->getY();
    // $pdf->setXY($pdf->marginX, $Id25);
    // $pdf->SetFont('Arial', 'B', 8);
    // $pdf->cell($contentWidth, 5, utf8_decode('I.25. Commodities certified for'), 'LR', 2, 'L', 0);
    // $pdf->SetFont('Arial', '', 8);
    // $pdf->cell($contentWidth, 5, utf8_decode('Human consumption'), 'LBR', 0, 'L', 0);

    // // 1.25 data
    // $pdf->setXY($pdf->marginX, $Id25 + 5);
    // $pdf->SetFont('Arial', '', 8);
    // $pdf->setX($pdf->marginX + $pdf->GetStringWidth('Human consumption') + 2);
    // $pdf->SetFont('ZapfDingbats', '', 10);
    // $pdf->cell(5, 5, utf8_decode('5'), 0, 0, 'L', 0);

    // // 1.26
    // $Id26 = $pdf->getY() + 5;
    // $pdf->setXY($pdf->marginX, $Id26);
    // $pdf->SetFont('Arial', '', 8);
    // $pdf->cell($contentWidth / 2, 5, utf8_decode('I.26.'), 'LR', 2, 'L', 0);
    // $pdf->cell($contentWidth / 2, 10, utf8_decode(''), 'LRB', 2, 'L', 0);
    // $pdf->Line($pdf->marginX, $Id26 + 15, $pdf->marginX + $contentWidth / 2, $Id26);

    // // 1.27
    // $Id27 = $Id26;
    // $pdf->setXY($pdf->marginX + $contentWidth / 2, $Id27);
    // $pdf->SetFont('Arial', 'B', 8);
    // $pdf->cell($contentWidth / 2, 5, utf8_decode('I.27. For import or admission into EU'), 'LR', 2, 'L', 0);
    // $pdf->SetFont('ZapfDingbats', '', 10);
    // $pdf->cell($contentWidth / 2, 10, utf8_decode('5'), 'LRB', 2, 'L', 0);

    // // 1.28

    // $Id28 = $pdf->getY();
    // $ancho_columnas = $contentWidth / 6;
    // $pdf->setXY($pdf->marginX, $Id28);
    // $pdf->SetFont('Arial', 'B', 8);
    // $pdf->cell($contentWidth, 5, utf8_decode('I.28. Identification of the commodities'), 'LR', 2, 'L', 0);
    // $pdf->SetFont('Arial', '', 8);
    // $pdf->cell($contentWidth, 5, utf8_decode(''), 'LR', 2, 'L', 0);
    // $pdf->cell($ancho_columnas, 4, utf8_decode('Species'), 'L', 0, 'C', 0);
    // $pdf->cell($ancho_columnas, 4, utf8_decode('Treatment type'), 0, 0, 'C', 0);
    // $pdf->cell($ancho_columnas * 2, 4, utf8_decode('Approval number of establishments'), 0, 0, 'C', 0);
    // $pdf->cell($ancho_columnas, 4, utf8_decode('Number of packages'), 0, 0, 'C', 0);
    // $pdf->cell($ancho_columnas, 4, utf8_decode('Net Weight'), 'R', 1, 'C', 0);

    // $pdf->cell($ancho_columnas, 4, utf8_decode('(Scientific Name)'), 'L', 0, 'C', 0);
    // $pdf->cell($ancho_columnas, 4, utf8_decode(''), 0, 0, 'C', 0);
    // $pdf->cell($ancho_columnas * 2, 4, utf8_decode('Manufacturing plant'), 0, 0, 'C', 0);
    // $pdf->cell($ancho_columnas, 4, utf8_decode(''), 0, 0, 'C', 0);
    // $pdf->cell($ancho_columnas, 4, utf8_decode(''), 'R', 1, 'C', 0);

    // $pdf->cell($ancho_columnas, 4, utf8_decode(''), 'L', 0, 'C', 0);
    // $pdf->cell($ancho_columnas, 4, utf8_decode(''), 0, 0, 'C', 0);
    // $pdf->cell($ancho_columnas * 2, 4, utf8_decode(''), 0, 0, 'C', 0);
    // $pdf->cell($ancho_columnas, 4, utf8_decode(''), 0, 0, 'C', 0);
    // $pdf->cell($ancho_columnas, 4, utf8_decode(''), 'R', 1, 'C', 0);

    // // 1.28 data
    // $y_actual = $pdf->getY();

    // $pdf->SetFont('Arial', 'B', 8);

    // // Primera columna
    // $pdf->cell($ancho_columnas, 30, '', 'LB', null, 'L', 0);
    // $pdf->setXY($pdf->marginX, $y_actual);
    // $pdf->Multicell($ancho_columnas, 5, utf8_decode($datosProducto->especie), 0, 'C', 0);

    // // Segunda columna
    // $pdf->setXY($pdf->marginX + $ancho_columnas, $y_actual);
    // $pdf->cell($ancho_columnas, 30, '', 'B', null, 'L', 0);
    // $pdf->setXY($pdf->marginX + $ancho_columnas, $y_actual);
    // $pdf->Multicell($ancho_columnas, 5, utf8_decode($datosProducto->tratamientoProceso), 0, 'C', 0);

    // // Tercera columna
    // $pdf->SetFont('Arial', '', 8);
    // $pdf->setXY($pdf->marginX + $ancho_columnas * 2, $y_actual);
    // $pdf->cell($ancho_columnas * 2, 30, '', 'B', null, 'L', 0);
    // $pdf->setXY($pdf->marginX + $ancho_columnas * 2, $y_actual);
    // $pdf->cell($ancho_columnas * 2, 5, utf8_decode('OAXACA MIEL S.A. DE C.V.'), 0, 2, 'C', 0);
    // $pdf->cell($ancho_columnas * 2, 5, utf8_decode('CARRETERA MÉRIDA-CANCÚN KM. 7.5,'), 0, 2, 'C', 0);
    // $pdf->cell($ancho_columnas * 2, 5, utf8_decode('SAN PEDRO NOH PAT, KANASÍN,'), 0, 2, 'C', 0);
    // $pdf->cell($ancho_columnas * 2, 5, utf8_decode('YUCATÁN, C.P. 97370.'), 0, 2, 'C', 0);
    // $pdf->cell($ancho_columnas * 2, 5, utf8_decode('TEL.: (01 999) 9 88 09 90'), 0, 2, 'C', 0);
    // $pdf->SetFont('Arial', 'B', 10);
    // $pdf->cell($ancho_columnas * 2, 5, utf8_decode('31-08771-1'), 0, 2, 'C', 0);

    // // Cuarta columna
    // $pdf->SetFont('Arial', 'B', 8);
    // $pdf->setXY($pdf->marginX + $ancho_columnas * 4, $y_actual);
    // $pdf->cell($ancho_columnas, 30, '', 'B', null, 'L', 0);
    // $pdf->setXY($pdf->marginX + $ancho_columnas * 4, $y_actual);
    // $pdf->cell($ancho_columnas, 5, utf8_decode(number_format($datosProducto->cantidad, 0, '.', ',')), 0, 2, 'C', 0);
    // $pdf->cell($ancho_columnas, 5, utf8_decode($datosProducto->marcaDistintiva), 0, 2, 'C', 0);

    // // Quinta columna

    // $pdf->SetFont('Arial', 'B', 8);
    // $pdf->setXY($pdf->marginX + $ancho_columnas * 5, $y_actual);
    // $pdf->cell($ancho_columnas, 30, '', 'BR', null, 'L', 0);
    // $pdf->setXY($pdf->marginX + $ancho_columnas * 5, $y_actual);
    // $pdf->cell($ancho_columnas, 5, utf8_decode(number_format($datosProducto->pesoNeto, 2, '.', ',') . ' ' . $datosProducto->unidadMedida), 0, 2, 'C', 0);


    // --------------------------------------------------------------------------------------


    // Página 2
    $pdf->addPage('P', 'A4');
    $pdf->setXY($pdf->marginX, 10);
    $pdf->SetFont('Arial', 'B', 10);
    // $pdf->Multicell($contentWidth, 5, utf8_decode('HEALTH CERTIFICATE FOR IMPORTS OF HONEY AND OTHER APICULTURE PRODUCTS INTENDED FOR HUMAN CONSUMPTION'), 0, 'C', 0);

    $table_titles = $pdf->getY();
    $pdf->setXY($pdf->marginX, $table_titles);

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->cell($contentWidth / 2, 5, utf8_decode('LAND'), 0, 0, 'L', 0);
    $pdf->cell($contentWidth / 2, 5, utf8_decode('Muster HON'), '', 2, 'R', 0);
    $pdf->cell($contentWidth / 2, 5, utf8_decode(' Honig und andere Imkereierzeugnisse fUr den menschlichen Verzehr'), 0, 1, 'R', 0);

    $begining_of_square = $pdf->getY();

    // Part II Title
    $pdf->setXY($pdf->marginX - 7, $begining_of_square);
    $pdf->cell(7, $alto_title_1, '', 'LTB', null, '', 0);
    $lateral_title_y = $pdf->getY() + $alto_title_1 - 10;
    $pdf->Rotate(90, $x_title, $lateral_title_y);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Text($x_title, $lateral_title_y, utf8_decode('Teil II: Bescheinigung'));
    $pdf->Rotate(0);

    // II. Health info
    $II = $begining_of_square;
    $pdf->setXY($pdf->marginX, $II);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->cell($contentWidth, 20, utf8_decode('II. Gesundheltsinformationen'), 'LTR', 2, 'L', 0);
    $pdf->cell($contentWidth, 5, utf8_decode('II. Genusstaugllchkeitsbescheinigung'), 'LR', 2, 'L', 0);
    // $beta = utf8_decode('\&#x03B2');
    // $beta =%CE%B2
    // text
    // $beta= chr(225);
    // $beta = utf8_encode('&szlig;');
    // $cadena_a_convertir = '';
    // $beta = string htmlspecialchars_decode(&beta);

    // $text = '© ® ™ £ € ¥';
    // $beta = htmlentities($text);' . $beta . ' 
    $pdf->SetFont('Arial', '', 7);
    $pdf->setXY($pdf->marginX, $pdf->getY());
    $pdf->Multicell($contentWidth, 5, utf8_decode('
    Der/Die Unterzeichnete bestätigt. mit den einschläqiqen Vorschriften der Verordnung (EG) Nr. 178/2002 des Europäischen Parlaments und des Rates vom 28. 
    Januar 2002 zur Festlegung der aligemeinen Grundsätze und Anforderungen des Lebensmittelrechts, zur Errichtung der Europäischen Behörde für 
    Lebensmittelsicherheit und zur Festlegung von Verfahren zur Lebensmittelsicherheit (ABI. L 31 vom 1.2.2002, S. 1), der Verordnung (EG) Nr. 852/2004
    des Europäischen Parlaments und des Rates vom 29. April 2004 uber Lebensmittelhygiene (ABI. L 139 vom 30.4.2004, S. 1), der Verordnung (EG) Nr. 853/2004 
    des Europäischen Parlaments und des Rates vom 29. April 2004 mit spezifischen Hygienevorschriften för Lebensmittel tierischen Ursprungs 
    (ABI. L 139 vom 30.4.2004, S. 55) und der Verordnung (EU) 2017/625 des Europäischen Parlaments und des Rates vom 15. März 2017 über amtiiche Kontrolien 
    und andere amtiiche Tätigkeiten zur Gewährleistung der Anwendung des Lebens- und Futtermittelrechts und der Vorschriften über Tiergesundheit und 
    Tierschutz. Pflanzengesundheit und Pflanzenschutzmittel. zur Änderung derVerordnungen (EG) Nr. 999/2001, (EG) Nr. 396/2005, (EG) Nr. 1069/2009, (EG) 
    Nr. 1107/2009, (EU) Nr. 1151/2012, (EU) Nr. 652/2014, (EU) 2016/429 und (EU) 2016/2031 des Europäischen Parlaments und des Rates, der Verordnungen (EG)
    Nr. 1/2005 und (EG) Nr. 1099/2009 des Rates sowie der Richtlinien 98/58/EG, 1999f74/EG, 2007/43/EG, 2008/119/EG und 2008/120/EG des Rates und zur 
    Aufhebung der Verordnungen (EG) Nr. 854/2004 und (EG) Nr. 882/2004 des Europäischen Parlaments und des Rates, der Richtlinien 89/608/EWG, 89/662/EWG,
    90/425/EWG, 91/496/EWG, 96/23/EG,  96/93/EG und 97f78/EG des Rates und des Beschlusses 92/438/EWG des Rates (Verordnung uber amtliche Kontrollen)
    (ABI. L 95 vom 7.4.2017, S. 1) vertraut zu sein, und bescheinigt, dass der vorstehend bezeichnete Honig und die anderen Imkereierzeugnisse unter 
    Einhaltung dieser Vorschriften gewonnen wurden und insbesondere folgende Anforderungen erfüllen:
    - Sie kommen aus einem Betrieb/Betrieben, der/die ein auf den HACCP-Grundsätzen (HACCP = Hazard Analysis and Critical Control POints) basierendes 
    Programm gemäß  Artikel 5 der Verordnung (EG) Nr. 852/2004 durchführt/durchführen;
    - sie wurden gemäß den Anforderungen von Anhang II der Verordnung (EG) Nr. 852/2004 unter hygienisch einwandfreien Bedingungen gehandhabt und gegebenenfalls
    zubereitet, verpackt und gelagert; und die Garantien fur lebende Tiere und tlertsche Erzeugnisse gemäß den Rückstandsüberwachunqsplänen im Sinne
    - der Richtlinie 96/23/EG des Rates vom 29. April 1996 über Kontrollmaßnahmen hinsichtlich bestimmter Stoffe und ihrer Rückstande in lebenden Tieren und tierischen
    Erzeugnissen und zur Aufhebung der Richtlinien 85/358/EWG Lind 86/469/EWG und der Entscheidungen 89/187/EWG und 91/664/EWG 
    (ABI. L 125 vom 23.5.1996, S. 10), insbesondere ihres Artikels 29, werden eingehalten.'), 'LR', 'L', 0);

    // $pdf->cell($contentWidth, 10, '', 'LR', 2, '', 0);

    $pdf->SetFont('Arial', 'B', 8);
    $pdf->cell($contentWidth, 10, utf8_decode('    Erläuterungen'), 'LR', 2, 'L', 0);
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Multicell($contentWidth, 5, utf8_decode('
    Siehe Erlauterungen in Anhang II der Durchführungsverordnung (EU) 2019/628 der Kommission vom 8. April 2019 betreffend die Muster amtlicher 
    Bescheinigungen fUr bestimmte Tlere und Waren und zur Anderung der Verordnung (EG) Nr. 2074/2005 und der Durchführungsverordnung (EU) 2016/759 im
    Hinblick auf diese Musterbescheinigungen (ABI. L 131 vom 17.5.2019,S. 101).'), 'LR', 'L', 0);
    $pdf->SetFont('Arial', '', 8);
    $pdf->Multicell($contentWidth, 5, utf8_decode('
    Teil I:
    - Feld 1.11:Versandort: Zulassungsnummer bedeute! Registrierungsnummer.
    - Feld 1.25:Den/Die entsprechenden Code/s des Harmonisierten Systems (HS) angeben wie 0409,0410,0510,1521,1702 oder 2106.
    - Feld 1.25:Art der Behandlung: \unraschanbehanolunc"\'. \'Hornoqenlslerunq"\', \'Ultrafiltration"\', \'Pasteurislerunq"\' oder 
    .kelne Wärmebehandlung"\' angeben.
    Teil II:
    - Stempel und Unterschrift rnussen sich farblich von den übrigenAngaben in der Bescheinigung absetzen.'), 'LBR', 'L', 0);
    $last_square = $pdf->getY();
    $ancho_columna = $contentWidth / 6;
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->setXY($pdf->marginX, $last_square);
    $pdf->cell($contentWidth, 7, utf8_decode('Amtlicher Inspektor/Amtiiche Inspektorin'), 'RL', 2, 'L', 0);
    // $pdf->cell($ancho_columna, 7, utf8_decode('Name
    //  (in Großbuchstaben):'), 'L', 2, 'R', 0);
    $pdf->cell($ancho_columna, 5, utf8_decode('Name'), 'L', 2, 'R', 0);
    $pdf->cell($ancho_columna, 5, utf8_decode('(in Großbuchstaben):'), 'L', 2, 'R', 0);
    $pdf->cell($ancho_columna, 5, utf8_decode('Datum:'), 'L', 2, 'R', 0);
    // $pdf->cell($ancho_columna, 5, utf8_decode(''), 'L', 2, 'R', 0);
    $pdf->cell($ancho_columna, 5, utf8_decode('Stempel:'), 'L', 2, 'R', 0);
    $pdf->cell($ancho_columna, 5, utf8_decode(''), 'LB', 2, 'R', 0);

    $pdf->setXY($pdf->marginX + $ancho_columna, $last_square + 7);
    $pdf->SetFont('Arial', '', 8);
    $pdf->cell($ancho_columna * 3, 5, utf8_decode(''), 0, 2, 'L', 0); // Here goes the name
    $pdf->cell($ancho_columna * 3, 5, utf8_decode(''), 0, 2, 'L', 0); // Date
    $pdf->cell($ancho_columna * 3, 5, utf8_decode(''), 0, 2, 'L', 0);
    $pdf->cell($ancho_columna * 3, 5, utf8_decode(''), 0, 2, 'L', 0); //Stamp
    $pdf->cell($ancho_columna * 3, 5, utf8_decode(''), 'B', 2, 'L', 0);

    $pdf->setXY($pdf->marginX + $ancho_columna * 4, $last_square + 7);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->cell($ancho_columna * 2, 5, utf8_decode('Qualifikation und Amtsbezeichnung:'), 'R', 2, 'L', 0);
    $pdf->cell($ancho_columna * 2, 10, utf8_decode('Unterschrift:'), 'R', 2, 'L', 0);
    $pdf->cell($ancho_columna * 2, 5, utf8_decode(''), 'R', 2, 'L', 0);
    $pdf->cell($ancho_columna * 2, 5, utf8_decode(''), 'RB', 2, 'L', 0);

    // Certificate reference number

    $pdf->SetFont('Arial', 'B', 8);
    $pdf->setXY($pdf->marginX + $contentWidth / 2, $II);
    $pdf->cell($contentWidth / 4, 5, utf8_decode('II.a. Bezugsnr. der Bescheinigung'), 'L', 2, 'L', 0);
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
