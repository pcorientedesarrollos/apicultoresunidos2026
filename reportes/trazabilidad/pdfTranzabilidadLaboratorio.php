<?php

require_once '../../vendor/autoload.php'; //SE cambia la ruta de la librería
require_once '../../clases/consultas.php';
include_once '../../DAOConeccion/conePDO.php';

use Mpdf\Mpdf; //importamos la clase


$consulta = new trazabilidad();
$pdo = new conePDO();
$conexion = $pdo->conectar();

$idLoteInterno = $_GET["idLoteInterno"];
$tipo = $_GET['tipoMiel'];
if($tipo == '1'){
    $tituloTrazabilidad = "Trazabilidad de Laboratorio Miel 100% Pura de Abeja";
}else if($tipo == '2'){
    $tituloTrazabilidad = "Trazabilidad de Laboratorio Miel 100% Orgánica";
}

$consultaLab = $consulta->trazabilidadAnalisis($tipo, $idLoteInterno);
$dataLab = $conexion->prepare($consultaLab);
$dataLab->execute();
$html = '<html>  
            <body>
                <!--mpdf
                    <htmlpageheader name="myheader">
                        <table width="100%">
                            <tr>
                                <td width="90%" style="text-align: left;">
                                    <p ><img src="../../reportes/img/trazabilidadLaboratorio.png" > </p>
                                 </td>
                              <!--  <td width="50%" style="color:#0000; ">
                                    <span style="font-weight: bold; font-size: 18pt;">' . $tituloTrazabilidad . '</span>
                                </td> -->
                                <td></td>
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
                        
                   <br>
                   <table width="100%" style="font-size: 9pt; border-collapse: collapse; text-align: center; " cellpadding="4" border="1">
                    <tr> 
                       <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 20px; background-color: gold" colspan="6">' . $tituloTrazabilidad . '</th>
                    </tr>
                    <tr> 
                        <th style="text-transform: none; font-weight: 600; background: none" align="center">N° de Muestra (9)</th>
                        <th style="text-transform: none; font-weight: 600; background: none" align="center">N° de Lote (10)</th>
                        <th style="text-align: right; text-transform: none; font-weight: 600; background: none" align="center">N° de ID del Proveedor (11)</th>
                        <th style="text-align: right; text-transform: none; font-weight: 600; background: none" align="center">Nombre del Laboratorio (12)</th>
                        <th style="text-align: right; text-transform: none; font-weight: 600; background: none" align="center">Fecha de Protocolo (13)</th>
                        <th style="text-align: right; text-transform: none; font-weight: 600; background: none" align="center">N° de Folio de la Constancia del Protocolo (14)</th>
                    </tr>';

while ($infoTransLab = $dataLab->fetch()) {
    $html.='<tr> '
            . '<td></td>'
//            . '<td>'.$infoTransLab["lote"].'</td>'
            . '<td>' . $infoTransLab["marcaFinalCliente"] . '</td>'
            . '<td>' . $infoTransLab["idSagarpa"] . '</td>'
            . '<td>' . $infoTransLab["nombreLaboratorio"] . '</td>'
            . '<td>' . $infoTransLab["fechaProtocolo"] . '</td>'
            . '<td>' . $infoTransLab["folioProtocolo"] . '</td>'
            . '</tr>';
}

$html.='                   
                </table>
            </body>
    </html>';

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
$mpdf->SetTitle("AUP-Trazabilidad Laboratorio");
$mpdf->SetAuthor('PCOriente');
$mpdf->showWatermarkText = true;
$mpdf->watermark_font = 'DejaVuSansCondensed';
$mpdf->watermarkTextAlpha = 0.1;
$mpdf->SetDisplayMode('fullpage');

// No es necesario el mb_convert_encoding ya que mPDF 8.x maneja UTF-8 por defecto
$mpdf->WriteHTML($html);
$mpdf->Output('AUP-Trazabilidad Laboratorio.pdf', 'I');
