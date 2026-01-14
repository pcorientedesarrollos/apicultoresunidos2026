<?php

require_once '../../vendor/autoload.php'; //SE cambia la ruta de la librería
require_once '../../clases/consultas.php';
include_once '../../DAOConeccion/conePDO.php';

use Mpdf\Mpdf; //importamos la clase


$consulta = new trazabilidad();
$pdo = new conePDO();
$conexion = $pdo->conectar();

$idLoteInterno = $_GET["idLoteInterno"];
$tipo = $_GET['tipo'];
if($tipo == '1'){
    $tituloTrazabilida = "Trazabilidad de Entrada Miel 100% Pura de Abeja";
}else if($tipo == '2'){
    $tituloTrazabilida = "Trazabilidad de Entrada Miel 100% Orgánica";
}else if($tipo == '5'){
    $tituloTrazabilida = "Trazabilidad de Entrada Miel 100% Mantequilla";
}else if($tipo == '6'){
    $tituloTrazabilida = "Trazabilidad de Entrada Miel 100% Altiplano";
}else if($tipo == '7'){
    $tituloTrazabilida = "Trazabilidad de Entrada Miel 100% Naranjo";
}else if($tipo == '8'){
    $tituloTrazabilida = "Trazabilidad de Entrada Miel 100% Aguacate";
}else if($tipo == '9'){
    $tituloTrazabilida = "Trazabilidad de Entrada Miel 100% Mezquite";
}

$consultaTrazabilidadEn = $consulta->trazabilidadEntrada($tipo, $idLoteInterno);
$dataTrazabilidaEn = $conexion->prepare($consultaTrazabilidadEn);
$dataTrazabilidaEn->execute();

$html = '<html>  
            <body>
                <!--mpdf
                    <htmlpageheader name="myheader">
                        <table width="100%">
                            <tr>
                                <td width="25%" style="text-align: left;">
                                    <p ><img src="../../reportes/img/trazabilidadEntrada.png" > </p>
                                 </td>
                               <!-- <td width="50%" style="color:#0000; ">
                                    <span style="font-weight: bold; font-size: 18pt;">' . $tituloTrazabilida . '</span>
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
                       <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 20px;" colspan="6">' . $tituloTrazabilida . '</th>
                    </tr>
                    <tr> 
                        <th style="text-transform: none; font-weight: 600; background: none" align="center">Fecha de Recepción (9)</th>
                        <th style="text-transform: none; font-weight: 600; background: none" align="center">N° de Lote (10)</th>
                        <th style="text-align: right; text-transform: none; font-weight: 600; background: none" align="center">Número de ID del Proveedor de Miel (11)</th>
                        <th style="text-align: right; text-transform: none; font-weight: 600; background: none" align="center">Dirección del Proveedor (12)</th>
                        <th style="text-align: right; text-transform: none; font-weight: 600; background: none" align="center">Volumen Kg (13)</th>
                        <th style="text-align: right; text-transform: none; font-weight: 600; background: none" align="center">N° de Muestra (14)</th>
                    </tr>';
                        while ($infoTrazabilidadEn = $dataTrazabilidaEn->fetch()){
                            $html.='<tr>
                                        <td>'.$infoTrazabilidadEn["fecha"].'</td>
                                        <td>'.$infoTrazabilidadEn["marcaFinalCliente"].'</td>
                                        <td>'.$infoTrazabilidadEn["idSagarpa"].'</td>
                                        <td>'.$infoTrazabilidadEn["domicilio"].'</td>
                                        <td>'.$infoTrazabilidadEn["kilos"].'</td>
                                        <td></td>
                                     </tr>';
                            $sumaVolumen = $sumaVolumen + $infoTrazabilidadEn["kilos"];
                        }

                
$html.='  <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td><b>'. number_format($sumaVolumen,0,'.',',').'</b></td>
            <td></td>
          </tr>                 
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
$mpdf->SetTitle("AUP-Trazabilidad Entrada");
$mpdf->SetAuthor('PCOriente');
$mpdf->showWatermarkText = true;
$mpdf->watermark_font = 'DejaVuSansCondensed';
$mpdf->watermarkTextAlpha = 0.1;
$mpdf->SetDisplayMode('fullpage');

// No es necesario el mb_convert_encoding ya que mPDF 8.x maneja UTF-8 por defecto
$mpdf->WriteHTML($html);
$mpdf->Output('AUP-Trazabilidad Entrada.pdf', 'I');
