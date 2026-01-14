<?php

require_once '../../vendor/autoload.php'; //SE cambia la ruta de la librería
require_once '../../clases/consultas.php';
include_once '../../DAOConeccion/conePDO.php';
use Mpdf\Mpdf; //importamos la clase

$consultas = new lote();

$pdo = new conePDO();
$conexion = $pdo->conectar();

$idLoteInterno = $_GET['idLoteInterno'];
$tmp = $_GET["tmp"];
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
}  else if ($tmp == 7) {
    $iniciales = "LN26-";
} else if ($tmp == 8) {
    $iniciales = "LG25-";
}  else if ($tmp == 9) {
    $iniciales = "LZ26-";
}   else {
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
}  else if ($tmp == 7) {
    $tituloLote = "Conformación de lote";
    $subtitulo = "Miel 100% Naranjo";
}  else if ($tmp == 8) {
    $tituloLote = "Conformación de lote";
    $subtitulo = "Miel 100% Aguacate";
}  else if ($tmp == 9) {
    $tituloLote = "Conformación de lote";
    $subtitulo = "Miel 100% Mezquite";
}  else {
    $tituloLote = "Conformación de lote";
    $subtitulo = "Miel 100% orgánica";
}
$tablaFloaraciones = $consultas->floracionesPorLote($idLoteInterno, $tmp);
if ($encaLoteInter->contrato == '1' || $encaLoteInter->contrato == '2') {
    $tablaContrato = $consultas->lotesContratados($encaLoteInter->numContrato, $tmp);
}
$html = '<html>
         <body>
         <!--mpdf
            <htmlpageheader name="myheader">
                <table width="100%">
                    <tr>
                        <td width="25%" style="text-align: left;">
                            <p ><img src="../../reportes/img/oaxaca.png" > </p>
                        </td>
                        <td width="50%" style="color:#0000; ">
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
                <td width="49%" >
                    Fecha Entrega: <b>' . $encaLoteInter->fechaEntrega . '</b><br/>';

    if ($encaLoteInter->loteInt >= 100) {
        $html .= 'Lote Interno: <b>' . $iniciales . ' ' . $encaLoteInter->loteInt . '</b><br/>';
    }else if($encaLoteInter->loteInt < 10){
        $html .= 'Lote Interno: <b>' . $iniciales . '00' . $encaLoteInter->loteInt . '</b><br/>';
    }else
    {
        $html .= 'Lote Interno: <b>' . $iniciales . '0' . $encaLoteInter->loteInt . '</b><br/>';
    }
if ($encaLoteInter->contrato == '1' || $encaLoteInter->contrato == '2') {
    $html .= 'Contrato: <b>' . $encaLoteInter->infoContrato . '</b><br/>';
}
$html .= 'Hora: <b>' . $horaActual . '</b><br/>
                    
                </td>
                <td width="50%">
                Cliente : <b>' . $encaLoteInter->cliente . '</b><br/>
                Número de Tambores : <b>' . $encaLoteInter->numeroTambores . '</b><br/>

Floración: ';
foreach ($tablaFloaraciones as $floraciones) {
    $html .= "<b>" . $floraciones['floracion'] . ". </b>";
}
$html .= '</b><br/>
                </td>
            </tr>
          
         </table>
            </htmlpageheader>
        <htmlpagefooter name="myfooter">
            <div style="border-top: 1px solid #000000; font-size: 9pt; text-align: center; padding-top: 3mm; ">
                Página {PAGENO} de {nb}
            </div>
        </htmlpagefooter>

            <sethtmlpageheader name="myheader" value="on" show-this-page="1" />
            <sethtmlpagefooter name="myfooter" value="on" />

            mpdf-->
            
        
         
            <table  width="100%" style="font-size: 9pt; border-collapse: collapse; " cellpadding="5">
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

        $contLote = $contLote + 1;
        $sumaBrutoLote = $sumaBrutoLote + $tamboresDLote['bruto'];
        $sumaTaraLote = $sumaTaraLote + $tamboresDLote['tara'];
        $sumaNetoLote = $sumaNetoLote + $tamboresDLote['neto'];
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

        $contLote = $contLote + 1;
        $sumaBrutoLote = $sumaBrutoLote + $tamboresDLote['bruto'];
        $sumaTaraLote = $sumaTaraLote + $tamboresDLote['tara'];
        $sumaNetoLote = $sumaNetoLote + $tamboresDLote['neto'];
    }
}



$html .= ' </tbody>
            </table>
          <br/>
          <br/>
          <br/>
          <table class="items" width="100%">
            <tr>
                <td style=""><b>Total de P.Bruto: </b>' . number_format($sumaBrutoLote, 2, '.', ',') . '</td>
                    
            </tr>
             <tr>
                <td style=""><b>Total de Tara: </b>' . number_format($sumaTaraLote, 2, '.', ',') . '</td>
                    
            </tr>
             <tr>
                <td style=""><b>Total de P.Neto: </b>' . number_format($sumaNetoLote, 2, '.', ',') . '</td>
                    
            </tr>
          </table>
          <br>
          <div><b>Observaciones: </br></div>
          <div>' . $encaLoteInter->observa . '</div>
           <br>';
if ($encaLoteInter->contrato == '1' || $encaLoteInter->contrato == '2') {
    $html .= '<table width="100%" style="font-family: serif;" cellpadding="5">
    <tr>
        <td width="50%" >
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
        <td width="50%" >
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

// $especificacionesLotePdf = $consultas->especificaciones($idLoteInterno, $tmp);
// $datosEspecificacionPdf = $conexion->prepare($especificacionesLotePdf);
// $datosEspecificacionPdf->execute();
// $conRows = $datosEspecificacionPdf->rowCount();
// if ($conRows > 0) {
//     $html .= '
//         <table width="100%" style=" border-collapse: collapse; text-align: center;"> 
//                         <tr style="">
//                             <th>Parámetros</th> 
//                             <th>Especificaciones del Cliente</th> 
//                             <th>Lab.Oaxaca Miel</th>
//                         </tr> ';
//     while ($infoEspecificacion = $datosEspecificacionPdf->fetch()) {
//         $tablaEspecificacionpdf = new stdClass;
//         $tablaEspecificacionpdf->humedad = $infoEspecificacion["humedad"];
//         $tablaEspecificacionpdf->color = $infoEspecificacion["color"];
//         $tablaEspecificacionpdf->adulteracion = $infoEspecificacion["adulteracion"];
//         $tablaEspecificacionpdf->sf = $infoEspecificacion["sf"];
//         $tablaEspecificacionpdf->st = $infoEspecificacion["st"];
//         $tablaEspecificacionpdf->tt = $infoEspecificacion["tt"];
//         $tablaEspecificacionpdf->hmf = $infoEspecificacion["hmf"];
//         $tablaEspecificacionpdf->tipo = $infoEspecificacion["tipo"];

//         if ($tablaEspecificacionpdf->tipo == 1) {
//             $especiClientePdf = $tablaEspecificacionpdf;
//         } else {
//             $especiLaboratorioPdf = $tablaEspecificacionpdf;
//         }
//     }

//     $html .= '            <tr>
//                             <td>% Humedad</td>
//                             <td>'.$especiClientePdf->humedad.'</td>
//                             <td>'.$especiLaboratorioPdf->humedad.'</td>
//                           </tr>

//                          <tr>
//                             <td>Color</td>
//                             <td>'.$especiClientePdf->color.'</td>
//                             <td>'.$especiLaboratorioPdf->color.'</td>
//                           </tr>

//                          <tr>
//                             <td>Adulteracion</td>
//                             <td>'.$especiClientePdf->adulteracion.'</td>
//                             <td>'.$especiLaboratorioPdf->adulteracion.'</td>
//                           </tr>

//                           <tr>
//                             <td>SF</td>
//                             <td>'.$especiClientePdf->sf.'</td>
//                             <td>'.$especiLaboratorioPdf->sf.'</td>
//                           </tr>

//                           <tr>
//                             <td>ST</td>
//                             <td>'.$especiClientePdf->st.'</td>
//                             <td>'.$especiLaboratorioPdf->st.'</td>
//                           </tr>

//                           <tr>
//                             <td>TT</td>
//                             <td>'.$especiClientePdf->tt.'</td>
//                             <td>'.$especiLaboratorioPdf->tt.'</td>
//                           </tr>

//                           <tr>
//                             <td>HMF</td>
//                             <td>'.$especiClientePdf->hmf.'</td>
//                             <td>'.$especiLaboratorioPdf->hmf.'</td>
//                           </tr>
//                        </table>
//                        <br>';                
// } else {
//     $html .= "No hay especificaciones registradas en este Lote"
//             . "<br>";
// }

$html .= ' <br> 
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


// $mpdf = new mPDF('c', 'A4', '', '', 20, 15, 57, 25, 10, 10);
// $mpdf->SetProtection(array('print'));
// $mpdf->SetTitle("AUP-Conformacion del Lote");
// $mpdf->SetAuthor("PCORIENTE");
// $mpdf->showWatermarkText = true;
// $mpdf->watermark_font = 'DejaVuSanCondensed';
// $mpdf->watermarkTextAlpha = 0.1;
// $mpdf->SetDisplayMode('fullpage');
// $mpdf->WriteHTML($html);
// $mpdf->Output('Conformación de lote.pdf', 'I');


// Configurar propiedades del PDF
$mpdf->SetTitle("AUP-Conformacion del Lote");
$mpdf->SetAuthor('PCOriente');
$mpdf->showWatermarkText = true;
$mpdf->watermark_font = 'DejaVuSansCondensed';
$mpdf->watermarkTextAlpha = 0.1;
$mpdf->SetDisplayMode('fullpage');

// No es necesario el mb_convert_encoding ya que mPDF 8.x maneja UTF-8 por defecto
$mpdf->WriteHTML($html);
$mpdf->Output('AUP-Conformacion del Lote.pdf', 'I');
