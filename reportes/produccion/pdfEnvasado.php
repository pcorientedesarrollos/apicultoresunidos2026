<?php

require_once '../../vendor/autoload.php'; //SE cambia la ruta de la librería
require_once '../../clases/consultas.php';
include_once '../../DAOConeccion/conePDO.php';
use Mpdf\Mpdf; //importamos la clase

$consulta = new produccion();
$pdo = new conePDO();
$conexion = $pdo->conectar();

$idReporteEnvasado = $_GET["idReporteEnvasado"];
$tipoDeMiel = $_GET['tipoDeMiel'];

$tituloEnvasado = "CONTROL DE ENVASADO";
$subTituloEnvasado = "REPORTE DE ENVASADO DE LOTES";

$consultaDetalleEn = $consulta->reporteEnvasado($idReporteEnvasado, $tipoDeMiel);
$dataEnvasado = $conexion->prepare($consultaDetalleEn);
$dataEnvasado->execute();

while ($indfoDeatlle = $dataEnvasado->fetch()) {
    $detalleEnvasado = new stdClass();
    $detalleEnvasado->idReporteEnvasado = $indfoDeatlle["idReporteEnvasado"];
    $detalleEnvasado->fechaEnvasado      = $indfoDeatlle["fechaEnvasado"];
    $detalleEnvasado->idLoteInterno      = $indfoDeatlle["idLoteInterno"];
    $detalleEnvasado->numeroTanque       = $indfoDeatlle["numeroTanque"];
    $detalleEnvasado->horaInicio         = $indfoDeatlle["horaInicio"];
    $detalleEnvasado->horaFinal          = $indfoDeatlle["horaFinal"];
    $detalleEnvasado->tiempo             = $indfoDeatlle["tiempo"];
    $detalleEnvasado->observaciones      = utf8_encode($indfoDeatlle["observaciones"]);
    $detalleEnvasado->kilosProcesados    = $indfoDeatlle["kilosProcesados"];
    $detalleEnvasado->netosEnvasados     = $indfoDeatlle["netosEnvasados"];
    $detalleEnvasado->merma              = $indfoDeatlle["merma"];
    $detalleEnvasado->faltante           = $indfoDeatlle["faltante"];
    $detalleEnvasado->observacionesPeso  = $indfoDeatlle["observacionesDePesos"];
}

$html = '<html> 
            <body>
                <!--mpdf
                     <htmlpageheader name="myheader">
                       <div align = "right">CÓDIGO: RPR-EL-01 </div>
                       <div align = "right">REVISIÓN: 01 </div><br>
                        <table width="100%">
                            <tr>
                                <td width="25%" style="text-align: left;">
                                    <p><img src="../../reportes/img/oaxaca.png" > </p>
                                </td>
                                <th></th>
                                <th width="50%" style="color:#0000; text-align: right;">
                                    <span style="font-weight: bold; font-size: 18pt;">' . $tituloEnvasado . '</span><br/>
                                    <span style="font-weight: bold; font-size: 12pt;">' . $subTituloEnvasado . '</span>
                                </th>
                                <th></th>
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
              <div style ="font-weight: bold; font-size: 13pt;"><b>Datos de Proceso</b></div>
              <table width="100%" style="font-family: serif;" cellpadding="5">
              <tr>
                <td>Lote Interno: ' . $detalleEnvasado->idLoteInterno . '</td>
                <td>Fecha Envasado: ' . $detalleEnvasado->fechaEnvasado . '</td>
                <td>Tanque Utilizado: ' . $detalleEnvasado->numeroTanque . '</td>
              </tr>
              <tr>
                <td>Hora Inicial: ' . $detalleEnvasado->horaInicio . '</td>
                <td>Hora Final: ' .  $detalleEnvasado->horaFinal  . '</td>
                <td>Tiempo: ' . $detalleEnvasado->tiempo . '</td>
              </tr>
              </table>
              <br>
              <div style="font-weight: bold; font-size: 13pt;"><b>Personal Operativo</b></div>
        <table width="100%" style="font-family: serif; text-align: left">
            <tr>
                <th align="left">Envasado</th>
            </tr> ';
$consultaPersonalEnvasado = $consulta->personalEnvasado($idReporteEnvasado, $tipoDeMiel);
$infoPersonalEn = $conexion->prepare($consultaPersonalEnvasado);
$infoPersonalEn->execute();

while ($dataPersonalEn = $infoPersonalEn->fetch()) {

    $html .= '<tr>'
        . '<td>' . $dataPersonalEn["nombre"] . '</td>'
        . '</tr>';
}

$html .= '</table>
    <br>
            <table width="100%" style="font-family: serif; text-align: left">
            <tr>
                <th align="left">Ayudante Interno</th>
            </tr>';

$consultaAyudaInt = $consulta->ayudanteInterno($idReporteEnvasado, $tipoDeMiel);
$infoAyudInt = $conexion->prepare($consultaAyudaInt);
$infoAyudInt->execute();

while ($dataPersonalIn = $infoAyudInt->fetch()) {

    $html .= '<tr>'
        . '<td>' . $dataPersonalIn["nombre"] . '</td>'
        . '</tr>';
}
$html .= '</table>
    <br>
            <table width="100%" style="font-family: serif; text-align: left">
            <tr>
                <th align="left">Ayudate Externo</th>
            </tr>';

$consultaAyudaExt = $consulta->ayudanteExterno($idReporteEnvasado, $tipoDeMiel);
$infoAyudExt = $conexion->prepare($consultaAyudaExt);
$infoAyudExt->execute();

while ($dataPersonalExt = $infoAyudExt->fetch()) {

    $html .= '<tr>'
        . '<td>' . $dataPersonalExt["nombre"] . '</td>'
        . '</tr>';
}
$html .= '      </table>
                <br>
                <div style ="font-weight: bold; font-size: 13pt;"><b>Tambores</b></div>
                <br>
                <div style ="font-color:red; font-size: 10pt;"><b><font color = "red">ESPECIFICACIONES: Envasar el tambo dejando unos 80cm de espacio en la tapa , poner la tara correcta del tambo.</font></b></div>
                <br>
                <table width="100%" style="font-family: serif; text-align: center" cellpadding="5" border="1">
                <tr>
                    <th>No.</th> 
                    <th>P.Bruto</th> 
                    <th>Tara</th>
                    <th>P.Neto</th>
                </tr>';
$consultaTamboresEn = $consulta->TamboresEnvasado($idReporteEnvasado, $tipoDeMiel);
$dataTamboresEn = $conexion->prepare($consultaTamboresEn);
$dataTamboresEn->execute();
$cont = "1";
while ($datosdelTambor = $dataTamboresEn->fetch()) {
    $html .= '<tr>'
        . '<td align="center">' . $cont . '</td>'
        . '<td align="center">' . $datosdelTambor["bruto"] . '</td>'
        . '<td align="center">' . $datosdelTambor["tara"] . '</td>'
        . '<td align="center">' . $datosdelTambor["neto"] . '</td>'
        . '</tr>';
    $cont = $cont + 1;
}

$html .= ' </table>
        <br>
            <div style ="font-weight: bold; font-size: 13pt;"><b>Observaciones</b></div>
            <br>
            <div>' . $detalleEnvasado->observaciones  . '</div>
                <br>
             <div style ="font-weight: bold; font-size: 13pt;"><b>Conciliación de Mercancía</b></div>
             <br>
             <table width="100%" style="font-family: serif;">
                <tr>
                <th align="left">Kilogramos realmente procesados</th>
                <td>' . $detalleEnvasado->kilosProcesados . '</td>
                </tr>
                <tr>
                <th align="left">Kilogramos Netos Envasados</th>
                <td>' . $detalleEnvasado->netosEnvasados . '</td>
                </tr>
                <tr>
                <th align="left">Merma Física</th>
                <td>' . $detalleEnvasado->merma . '</td>
                </tr>
                <tr>
                    <th align="left">Faltante o Sobrante</th>
                    <td>' . $detalleEnvasado->faltante . '</td>
                </tr>
             </table>
             <br/>
            <br/><br/>
             <div style ="font-weight: bold; font-size: 13pt;"><b>Observaciones de Conciliación de Mercancía</b></div><br>
             <div>' . $detalleEnvasado->observacionesPeso . '</div>
             <br/>
             <br/>
              <table width="100%">
            <tr> 
                <td width="33%">
                <span style="font-weight: bold; font-size: 12pt;">Responsable de Envasado</span><br />
                    <br />
                   ________________________<br />
                   
                </td>
                <td width="33%">
                <span style="font-weight: bold; font-size: 12pt;">Auxiliar de Producción</span><br />
                    <br />
                   ________________________<br />
                   
                </td>
                <td width="33%">
                <span style="font-weight: bold; font-size: 12pt;">Jefe de Producción</span><br />
                    <br />
                   ________________________<br />
                   
                </td>
            </tr>
          </table>
            </body>
        </html>';

// $mpdf = new mPDF('c', 'A4', '', '', 20, 15, 47, 25, 10, 10);
// $mpdf->SetProtection(array('print'));
// $mpdf->SetTitle("AUP-Envasado de Lote");
// $mpdf->SetAuthor("PCORIENTE");
// $mpdf->showWatermarkText = true;
// $mpdf->watermark_font = 'DejaVuSanCondensed';
// $mpdf->watermarkTextAlpha = 0.1;
// $mpdf->SetDisplayMode('fullpage');
// $mpdf->WriteHTML($html);
// $mpdf->Output('AUP-Envasado de Lote.pdf', 'I');


//creamos el objeto mpdf
$mpdf = new Mpdf([
    'mode' => 'utf-8',
    'format' => 'A4',
    'margin_left' => 10,
    'margin_right' => 10,
    'margin_top' => 55,
    'margin_bottom' => 25,
    'margin_header' => 10,
    'margin_footer' => 10
]);

// Configurar propiedades del PDF
$mpdf->SetTitle("AUP-Envasado de Lote");
$mpdf->SetAuthor('PCOriente');
$mpdf->showWatermarkText = true;
$mpdf->watermark_font = 'DejaVuSansCondensed';
$mpdf->watermarkTextAlpha = 0.1;
$mpdf->SetDisplayMode('fullpage');

// No es necesario el mb_convert_encoding ya que mPDF 8.x maneja UTF-8 por defecto
$mpdf->WriteHTML($html);
$mpdf->Output('AUP-Envasado de Lote.pdf', 'I');
