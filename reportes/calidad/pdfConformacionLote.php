<?php

require_once '../../vendor/autoload.php'; // Asegúrate de que la ruta sea correcta
require_once '../../clases/consultas.php';
include_once '../../DAOConeccion/conePDO.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$consultas = new lote();

$pdo = new conePDO();
$conexion = $pdo->conectar();

$idLoteInterno = filter_input(INPUT_GET, 'idLoteInterno', FILTER_SANITIZE_NUMBER_INT);
$tmp = filter_input(INPUT_GET, 'tmp', FILTER_SANITIZE_NUMBER_INT);
$horaActual = date("H:i:s");
$fecha = date("d-m-Y");

$lotefinal = $consultas->EncabeLote($idLoteInterno, $tmp);
$datosEncaLote = $conexion->prepare($lotefinal);
$datosEncaLote->execute();

if ($tmp == 1) {
    $iniciales = "LC26-";
} else if ($tmp == 5) {
    $iniciales = "LM26-";
} else if ($tmp == 6) {
    $iniciales = "LA26-";
} else if ($tmp == 7) {
    $iniciales = "LN26-";
} else if ($tmp == 8) {
    $iniciales = "LG25-";
} else if ($tmp == 9) {
    $iniciales = "LZ26-";
} else {
    $iniciales = "LO26-";
}

while ($EncabezadoLote = $datosEncaLote->fetch()) {
    $encaLoteInter = new stdClass();
    $encaLoteInter->fechaEntrega = $EncabezadoLote['fecha'];
    $encaLoteInter->loteInt = $EncabezadoLote['idLoteInterno'];
    $encaLoteInter->cliente = $EncabezadoLote['lote'];
    $encaLoteInter->numeroTambores = $EncabezadoLote['numeroDeTambores'];
    $encaLoteInter->observa = $EncabezadoLote['observaciones'];
    $encaLoteInter->resultadoLaboratorio = $EncabezadoLote['resultadoLaboratorio'];
    $encaLoteInter->contrato = $EncabezadoLote['contrato'];
    $encaLoteInter->numContrato = $EncabezadoLote['numContrato'];
    $encaLoteInter->infoContrato = $EncabezadoLote['infoContrato'];
}

if ($tmp == 1) {
    $tituloLote = "Conformación de lote";
    $subtitulo = "Miel 100% pura de abeja";
} else if ($tmp == 5) {
    $tituloLote = "Conformación de lote";
    $subtitulo = "Miel 100% mantequilla";
} else if ($tmp == 6) {
    $tituloLote = "Conformación de lote";
    $subtitulo = "Miel 100% Altiplano";
} else if ($tmp == 7) {
    $tituloLote = "Conformación de lote";
    $subtitulo = "Miel 100% Naranjo";
} else if ($tmp == 8) {
    $tituloLote = "Conformación de lote";
    $subtitulo = "Miel 100% Aguacate";
} else if ($tmp == 9) {
    $tituloLote = "Conformación de lote";
    $subtitulo = "Miel 100% Mezquite";
} else {
    $tituloLote = "Conformación de lote";
    $subtitulo = "Miel 100% orgánica";
}

$tablaFloaraciones = $consultas->floracionesPorLote($idLoteInterno, $tmp);
if ($encaLoteInter->contrato == '1' || $encaLoteInter->contrato == '2') {
    $tablaContrato = $consultas->lotesContratados($encaLoteInter->numContrato, $tmp);
}

$html = '<html>
         <body>
            <table width="100%">
                <tr>
                    <td width="25%" style="text-align: left;">
                        <p><img src="https://www.apicultoresunidos.com/reportes/img/LOGO.png"></p>
                    </td>
                    <td width="50%" style="color:#0000;">
                        <center>
                            <span style="font-weight: bold; font-size: 18pt;">' . $tituloLote . '</span>
                            <br>
                            <span style="font-weight: bold; font-size: 14pt;">' . $subtitulo . '</span>    
                        </center>                        
                    </td>
                </tr>
            </table>
            <br>
            <table width="100%" style="font-family: serif;" cellpadding="5">
                <tr>
                    <td width="49%">
                        Fecha Entrega: <b>' . $encaLoteInter->fechaEntrega . '</b><br/>';

if ($encaLoteInter->loteInt >= 100) {
    $html .= 'Lote Interno: <b>' . $iniciales . ' ' . $encaLoteInter->loteInt . '</b><br/>';
} else if ($encaLoteInter->loteInt < 10) {
    $html .= 'Lote Interno: <b>' . $iniciales . '00' . $encaLoteInter->loteInt . '</b><br/>';
} else {
    $html .= 'Lote Interno: <b>' . $iniciales . '0' . $encaLoteInter->loteInt . '</b><br/>';
}

if ($encaLoteInter->contrato == '1' || $encaLoteInter->contrato == '2') {
    $html .= 'Contrato: <b>' . $encaLoteInter->infoContrato . '</b><br/>';
}

$html .= 'Hora: <b>' . $horaActual . '</b><br/>
                    </td>
                    <td width="50%">
                        Cliente: <b>' . $encaLoteInter->cliente . '</b><br/>
                        Número de Tambores: <b>' . $encaLoteInter->numeroTambores . '</b><br/>
                        Floración: ';

foreach ($tablaFloaraciones as $floraciones) {
    $html .= "<b>" . $floraciones['floracion'] . ". </b>";
}

$html .= '</b><br/>
                    </td>
                </tr>
            </table>
            <br>
            <table width="100%" style="font-size: 9pt; border-collapse: collapse;" cellpadding="5">
                <thead>
                    <tr>
                        <td><b>No.</b></td>
                        <td><b>Folio</b></td>
                        <td><b>Zona</b></td>
                        <td><b>P.Bruto</b></td>
                        <td><b>Tara</b></td>
                        <td><b>P.Neto</b></td>
                        <td><b>Humedad</b></td>';

if ($encaLoteInter->resultadoLaboratorio == '1') {
    $html .= '<td><b>Color<b></td>';
}

$html .= '</tr>
                </thead>
                <tbody>';

$tablaLote = $consultas->detalleLoteInt($idLoteInterno, $tmp);

$contLote = 1;
$sumaBrutoLote = 0;
$sumaTaraLote = 0;
$sumaNetoLote = 0;

if ($encaLoteInter->resultadoLaboratorio == '1') {
    foreach ($tablaLote as $tamboresDLote) {
        $html .= "<tr>"
            . "<td>" . $contLote . "</td>"
            . "<td>" . $tamboresDLote['folioTambor'] . "</td>"
            . "<td>" . $tamboresDLote["zona"] . "</td>"
            . "<td>" . $tamboresDLote['bruto'] . "</td>"
            . "<td>" . $tamboresDLote['tara'] . "</td>"
            . "<td>" . $tamboresDLote['neto'] . "</td>"
            . "<td>" . $tamboresDLote['humedad'] . "</td>"
            . "<td>" . $tamboresDLote['color'] . "</td>"
            . "</tr>";

        $contLote++;
        $sumaBrutoLote += $tamboresDLote['bruto'];
        $sumaTaraLote += $tamboresDLote['tara'];
        $sumaNetoLote += $tamboresDLote['neto'];
    }
} else {
    foreach ($tablaLote as $tamboresDLote) {
        $html .= "<tr>"
            . "<td>" . $contLote . "</td>"
            . "<td>" . $tamboresDLote['folioTambor'] . "</td>"
            . "<td>" . $tamboresDLote["zona"] . "</td>"
            . "<td>" . $tamboresDLote['bruto'] . "</td>"
            . "<td>" . $tamboresDLote['tara'] . "</td>"
            . "<td>" . $tamboresDLote['neto'] . "</td>"
            . "<td>" . $tamboresDLote['humedad'] . "</td>"
            . "</tr>";

        $contLote++;
        $sumaBrutoLote += $tamboresDLote['bruto'];
        $sumaTaraLote += $tamboresDLote['tara'];
        $sumaNetoLote += $tamboresDLote['neto'];
    }
}

$html .= '</tbody>
            </table>
            <br/>
            <br/>
            <br/>
            <table class="items" width="100%">
                <tr>
                    <td><b>Total de P.Bruto: </b>' . number_format($sumaBrutoLote, 2, '.', ',') . '</td>
                </tr>
                <tr>
                    <td><b>Total de Tara: </b>' . number_format($sumaTaraLote, 2, '.', ',') . '</td>
                </tr>
                <tr>
                    <td><b>Total de P.Neto: </b>' . number_format($sumaNetoLote, 2, '.', ',') . '</td>
                </tr>
            </table>
            <br>
            <div><b>Observaciones: </b></div>
            <div>' . $encaLoteInter->observa . '</div>
            <br>';

if ($encaLoteInter->contrato == '1' || $encaLoteInter->contrato == '2') {
    $html .= '<table width="100%" style="font-family: serif;" cellpadding="5">
    <tr>
        <td width="50%">
            Humedad: <b>' . $tablaContrato['humedad'] . '</b><br/>
            Color: <b>' . $tablaContrato['color'] . '</b><br/>
            HMF: <b>' . $tablaContrato['hmf'] . '</b><br/>
            F/G: <b>' . $tablaContrato['fg'] . '</b><br/>
            Glucosa: <b>' . $tablaContrato['glucosa'] . '</b><br/>
            Diastasa: <b>' . $tablaContrato['diastasa'] . '</b><br/>
            Invertasa: <b>' . $tablaContrato['invertasa'] . '</b><br/>
            Sulfas: <b>' . $tablaContrato['sulfas'] . '</b><br/>
            Estrepto: <b>' . $tablaContrato['estrepto'] . '</b><br/>
        </td>
        <td width="50%">
            Tetras: <b>' . $tablaContrato['tetras'] . '</b><br/>
            Cloranphenicol: <b>' . $tablaContrato['cloranphenicol'] . '</b><br/>
            Macrolidas: <b>' . $tablaContrato['macrolidas'] . '</b><br/>
            Nitrofuranos: <b>' . $tablaContrato['nitrofuranos'] . '</b><br/>
            Fluroquinolanas: <b>' . $tablaContrato['fluroquinolanas'] . '</b><br/>
            Pesticidas: <b>' . $tablaContrato['pesticidas'] . '</b><br/>
            Glifosato: <b>' . $tablaContrato['glifosato'] . '</b><br/>
            OGM: <b>' . $tablaContrato['ogm'] . '</b><br/>
            PA´s: <b>' . $tablaContrato['pas'] . '</b><br/>
        </td>
    </tr>
 </table>';
}

$html .= '<br>
        <table width="100%">
            <tr>
                <td width="33%">
                    <span style="font-weight: bold; font-size: 10pt;">Jefe de Producción</span><br />
                    <br />
                    ________________________<br />
                </td>
                <td width="33%">
                    <span style="font-weight: bold; font-size: 10pt;">Gerente de Planta</span><br />
                    <br />
                    ________________________<br />
                </td>
                <td width="33%">
                    <span style="font-weight: bold; font-size: 10pt;">Jefe de Calidad</span><br />
                    <br />
                    ________________________<br />
                </td>
            </tr>
        </table>
        </body>
        </html>';

// Configurar Dompdf
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);

// Cargar el contenido HTML en Dompdf
$dompdf->loadHtml($html);

// (Opcional) Configurar el tamaño y la orientación del papel
$dompdf->setPaper('A4', 'portrait');

// Renderizar el PDF
$dompdf->render();

// Salvar el PDF a un archivo
$dompdf->stream('Conformacion_de_lote.pdf', ['Attachment' => false]);

?>