<?php

require_once '../../vendor/autoload.php'; //SE cambia la ruta de la librería
include_once '../../clases/consultas.php';
include_once '../../DAOConeccion/conePDO.php';
use Mpdf\Mpdf; //importamos la clase

$consulta = new calidad(); $pdo = new conePDO();
$conexion = $pdo->conectar();
date_default_timezone_set('America/Merida');

try {
    if(!isset($_GET['i']) || !isset($_GET['f']) || !isset($_GET['tdm'])){
        throw new Exception('No se han recibido los parámetros necesarios.');
    } else {
        $inicial = $_GET['i'];
        $final = $_GET['f'];
        $tipoDeMiel = $_GET['tdm'];
    }

    $tituloVerHumedad = $tipoDeMiel == '1' ? "VERIFICACIÓN DE HUMEDADES MIEL 100% PURA DE ABEJA" : "VERIFICACIÓN DE HUMEDADES MIEL 100% ORGÁNICA";
    $fecha = date("d-m-y");
    
    $consultaFolios = $consulta->verificacionHumedades($inicial, $final, $tipoDeMiel);
    $dataFolios = $conexion->prepare($consultaFolios);
    $dataFolios->execute();
    if($dataFolios == FALSE){
        throw new Exception($conexion->errorInfo());
    }

    $html = '
    <html> 
        <body>
            <!--mpdf
                <htmlpageheader name="myheader">
                    <table width="100%">
                        <tr>
                            <td width="25%" style="text-align: left;">
                                <p ><img src="../../reportes/img/oaxaca.png" > </p>
                            </td>
                            <td width="50%" style="color:#0000; text-align: center;">
                                <span style="font-weight: bold; font-size: 15pt;">'.$tituloVerHumedad.'</span>
                            </td>
                            <td>
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
             
            <table width="100%">
                <tr>
                    <td>Código: VLB-VH-01</td>
                    <td>Fecha Emisión: ' . $fecha . '</td>
                    <td>N° Revisión:</td>
                </tr>
            </table>
            <br/>
             
            <div><b>Instrucción:</b> Utilice letras de molde y tinta azul o negra, no use corrector, ni tinta roja, no deje espacios en blanco coloque una linea diagonal.</div><br/>
       
            <table  width="100%" style="font-size: 9pt; border-collapse: collapse; text-align: center; " cellpadding="4" border="1">
                <tr>
                    <th width="5%">N°</th>
                    <th width="10%">Fecha</th>
                    <th width="10%"># Folio</th>
                    <th width="17%">Apicultor</th>
                    <th width="12%">Procedencia</th>
                    <th width="12%">%H</th>
                    <th width="12%">Firma</th>
                    <th width="12%">Comentario</th>
                </tr>';
    foreach($dataFolios->fetchAll(PDO::FETCH_ASSOC) as $key=>$dato){
        $contador = $key + 1;
        $html .= '
            <tr>
                <td>'.$contador.'</td>
                <td>'.$dato['fecha'].'</td>
                <td>'.$dato['idAlmacen'].'</td>
                <td>'.$dato['nombre'].'</td>
                <td>'.$dato['localidad'].'</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>';
    }

    $html .= '
        </table>
        </body>
        </html>';

    // $mpdf = new mPDF('c', 'A4', '', '', 15, 10, 47, 25, 10, 10);
    // $mpdf->SetProtection(array('print'));
    // $mpdf->SetTitle("AUP-Verificacion de Humedades");
    // $mpdf->SetAuthor("PCORIENTE");
    // $mpdf->showWatermarkText = true;
    // $mpdf->watermark_font = 'DejaVuSanCondensed';
    // $mpdf->watermarkTextAlpha = 0.1;
    // $mpdf->SetDisplayMode('fullpage');
    // $mpdf->WriteHTML($html);
    // $mpdf->Output('AUP-Verificacion de Humedades.pdf', 'I');


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
$mpdf->SetTitle("AUP-Verificacion de Humedades");
$mpdf->SetAuthor('PCOriente');
$mpdf->showWatermarkText = true;
$mpdf->watermark_font = 'DejaVuSansCondensed';
$mpdf->watermarkTextAlpha = 0.1;
$mpdf->SetDisplayMode('fullpage');

// No es necesario el mb_convert_encoding ya que mPDF 8.x maneja UTF-8 por defecto
$mpdf->WriteHTML($html);
$mpdf->Output('AUP-Verificacion de Humedades.pdf', 'I');
} catch (Exception $e) {
    echo $e->getMessage();
}
exit();
// $idAlmacenInicial = $_GET['folioIni'];
// $idAlmacenFinal   = $_GET['foliofinal'];
// //$idAlmacenInicial = '100';
// //$idAlmacenFinal = '200';

// $html = '<html> 
//             <body>
//            <!--mpdf
//                      <htmlpageheader name="myheader">
//                         <table width="100%">
//                             <tr>
//                                 <td width="25%" style="text-align: left;">
//                                 <p ><img src="../../reportes/img/oaxaca.png" > </p>
//                                 </td>
//                                 <td width="50%" style="color:#0000; text-align: center;">
//                                  <span style="font-weight: bold; font-size: 15pt;">'.$tituloVerHumedad.'</span></td>
//                                 <td></td>
//                             </tr>
//                         </table>
//                     </htmlpageheader>

//                     <htmlpagefooter name="myfooter">
//                         <div style="border-top: 1px solid #000000; font-size: 9pt; text-align: center; padding-top: 3mm; ">
//                         Página {PAGENO} de {nb}
//                         </div>
//                     </htmlpagefooter>

//                     <sethtmlpageheader name="myheader" value="on" show-this-page="1" />
//                     <sethtmlpagefooter name="myfooter" value="on" />

//                      mpdf-->
                     
//                      <table width="100%">
//                         <tr>
//                             <td>Código: VLB-VH-01</td>
//                             <td>Fecha Emisión: ' . $fecha . '</td>
//                             <td>N° Revisión:</td>
//                         </tr>
//                      </table>
//                      <br/>
                     
//                <div><b>Instrucción:</b> Utilice letras de molde y tinta azul o negra, no use corrector, ni tinta roja, no deje espacios en blanco coloque una linea diagonal.</div><br/>
               
//               <table  width="100%" style="font-size: 9pt; border-collapse: collapse; text-align: center; " cellpadding="4" border="1">
//               <tr>
//               <th width="5%">N°</th>
//               <th width="10%">Fecha</th>
//               <th width="10%"># Folio</th>
//               <th width="17%">Apicultor</th>
//               <th width="12%">Procedencia</th>
//               <th width="12%">%H</th>
//               <th width="12%">Firma</th>
//               <th width="12%">Comentario</th>
//               </tr>';
            //   $contadorFol=1;
            //   $consultaFolios = $consulta->verificacionHumedades($idAlmacenInicial, $idAlmacenFinal);
            //   $dataFolios = $conexion->prepare($consultaFolios);
            //   $dataFolios->execute();
//               while ($infoFolios = $dataFolios->fetch()){
//                   $html.='<tr>
//                             <td>'.$contadorFol.'</td>
//                             <td>'.$infoFolios['fecha'].'</td>
//                             <td>'.$infoFolios['idAlmacen'].'</td>
//                             <td>'.$infoFolios['nombre'].'</td>
//                             <td>'.$infoFolios['localidad'].'</td>
//                             <td></td>
//                             <td></td>
//                             <td></td>
//                           </tr>';
                  
//                   $contadorFol = $contadorFol + 1;
//               }

// $html.='
//               </table>
//             </body>
//         </html>';

// $mpdf = new mPDF('c', 'A4', '', '', 15, 10, 47, 25, 10, 10);
// $mpdf->SetProtection(array('print'));
// $mpdf->SetTitle("AUP-Verificacion de Humedades");
// $mpdf->SetAuthor("PCORIENTE");
// $mpdf->showWatermarkText = true;
// $mpdf->watermark_font = 'DejaVuSanCondensed';
// $mpdf->watermarkTextAlpha = 0.1;
// $mpdf->SetDisplayMode('fullpage');
// $mpdf->WriteHTML($html);
// $mpdf->Output('AUP-Verificacion de Humedades.pdf', 'I');