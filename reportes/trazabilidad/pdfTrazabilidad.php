<?php

require_once '../../vendor/autoload.php'; //SE cambia la ruta de la librería
require_once 'obtenerTrazabilidad.php';
use Mpdf\Mpdf; //importamos la clase

$lote = $_GET['lote'];
$miel = $_GET['tipoMiel'];

$consulta = new trazabilidad();
$resultado = $consulta->obtenerTrazabilidad($lote, $miel);

if ($miel == '1') {
    $tituloTrazabilidad = "MIEL 100% PURA DE MIEL";
} else if ($miel == '2') {
    $tituloTrazabilidad = "MIEL 100% ORGANICA";
}


if ($resultado['procesoEnvasado']['fechaProceso'] == "1969-12-31") {
    $resultado['procesoEnvasado']['fechaProceso'] = "";
}

if ($resultado['procesoEnvasado']['fechaEnvasado'] == "1969-12-31") {
    $resultado['procesoEnvasado']['fechaEnvasado'] = "";
}



$html = '<html>  
            <body>
                <!--mpdf
                    <htmlpageheader name="myheader">
                        <table width="100%">
                            <tr>
                                <td width="30%" style="text-align: left;">
                                    <p ><img src="../../reportes/img/logoSF.png" > </p>                     
                                </td>
                                <td width="50%" style="color:#0000; text-align:center: margin-left: 30%">
                            
                                        <span style="font-size: 12pt;"><b>OAXACA MIEL S.A. DE C.V.</b></span><br/>
                                        <span style="font-size: 10pt;"><b>' . $tituloTrazabilidad . '</b></span><br/>
                                        <span style="font-size: 10pt;"><b>LOTE ' . $resultado['lote']['lote'] . '</b></span><br/>     
                              
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
                        <td width="25%">
                            <center text-align="center">
                                <img src="../../iconos/localidades_3.png" alt="Localidades/Floración">
                            </center>
                        </td>           
                        <td width="25%">
                            <center text-align="center">
                                <img src="../../iconos/entrada_3.png" alt="Entrada">
                            </center>
                        </td>
                        <td width="25%">
                            <center text-align="center">
                                <img src="../../iconos/proceso_3.png" alt="Proceso">
                            </center>
                        </td>
                        <td width="25%">
                            <center text-align="center">
                                <img src="../../iconos/envasado_3.png" alt="Envasado">
                            </center>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <center text-align="center">';
foreach ($resultado['localidades'] as $localidad) {
    $html .=  $localidad["localidad"] . '<br>';
}
$html .= '
                            </center>
                        </td>
                        <td>
                            <center text-align="center">
                                <p style="font-size: 15px;"> Fecha: ' . $resultado['fechaEntrada']['fechaEntrada'] . ' </p>
                            </center>                                              
                        </td>
                        <td>
                            <center text-align="center">';
if ($resultado['procesoEnvasado']['fechaProceso'] == "") {
    $html .= '<p>Fecha </p>';
} else {
    $html .= '<p>Fecha: ' . date('d/m/Y', strtotime($resultado['procesoEnvasado']['fechaProceso'])) . '</p>';
}
$html .= '<p>Neto: ' . $resultado['procesoEnvasado']['kilosProcesados'] . ' kgs. </p>
                                <p>Homogeneizado: ' . $resultado['procesoEnvasado']['tiempoHomogeneizacion'] . ' hrs.</p>
                                <p>Reposo: ' . $resultado['procesoEnvasado']['tiempoReposo'] . ' hrs.</p>
                            </center>
                        </td>
                        <td>
                            <center text-align="center">';
if ($resultado['procesoEnvasado']['fechaEnvasado'] == "") {
    $html .= '<p>Fecha </p>';
} else {
    $html .= 'Fecha: ' . date('d/m/Y', strtotime($resultado['procesoEnvasado']['fechaEnvasado'])) . '<br>';
}
$html .= '<p>Neto: ' . $resultado['procesoEnvasado']['kilosEnvasados'] . ' kgs.</p>
                            </center>
                        </td>
                    </tr>
                    <tr>
                        <td width="25%">
                            <center text-align="center">
                                <img src="../../iconos/productoTerminado_3.png" alt="Producto terminado">
                            </center>
                        </td>           
                        <td width="25%">
                        <center text-align="center">
                                <img src="../../iconos/salida_3.png" alt="Salida">
                            </center>
                         
                        </td>
                        <td width="25%">
                        <center text-align="center">
                                <img src="../../iconos/embarque_3.png" alt="Embarque">
                            </center>
                       </td>
                    </tr>
                                   <tr>
                        <td>
                            <center>';
if ($resultado['fechaPT']['fecha'] == "") {
    $html .= '<p style="font-size: 15px;">Fecha de análisis </p></center>';
} else {
    $html .= '<p style="font-size: 15px;">Fecha de análisis: ' . date('d/m/Y', strtotime($resultado['fechaPT']['fecha'])) . '</p></center>';
}
$html .= '</td>
                        <td>
                            <center>
                                <p style="font-size: 15px;">Fecha: ' . date('d/m/Y', strtotime($resultado['fechaSalida']['fechaSalida'])) . '</p>                                
                            </center>
                        </td>
                        <td>
                            <center text-align="center">';
if ($resultado['fechaEmbarque'] == "") {
    $html .= '<p style="font-size: 15px;">Fecha </p></center>';
} else {
    $html .= '<p style="font-size: 15px;">Fecha: ' . date('d/m/Y', strtotime($resultado['fechaEmbarque'])) . '</p></center>';
}
$html .= '</td>
                    </tr>
                    
                    
                </table>';

// echo $resultado;

// $obtenerId = $consulta->obtenerId($lote);
// $queryObtenerId = $conexion->prepare($obtenerId);
// $queryObtenerId->execute();
// $idLoteInterno = $queryObtenerId->fetch();

// $consultaTrazabilidadEn = $consulta->trazabilidadEntrada($tipo, $idLoteInterno['idLoteInterno']);
// $dataTrazabilidaEn = $conexion->prepare($consultaTrazabilidadEn);
// $dataTrazabilidaEn->execute();

// 10 margen izquierdo. 5 margen derecho. 35 margen superior. 20 no se. 4 margen encabezado. 4 margen pie

// $mpdf = new mPDF('c', 'A4-L', '', '', 2, 0, 40, 20, 10, 4);
// $mpdf->SetProtection(array('print'));
// $mpdf->SetTitle("AUP-Trazabilidad");
// $mpdf->SetAuthor("PCOriente");
// $mpdf->showWatermarkText = true;
// $mpdf->watermark_font = 'DejaVuSansCondensed';
// $mpdf->watermarkTextAlpha = 0.1;
// $mpdf->SetDisplayMode('fullpage');
// $stylesheet = file_get_contents('pdf.css'); // la ruta a tu css
// $mpdf->WriteHTML($stylesheet, 1);
// $mpdf->WriteHTML($html, 2);
// // $mpdf->WriteHTML($html);
// // $mpdf->WriteHTML($html);
// $mpdf->Output('AUP-Trazabilidad.pdf', 'I');


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
$mpdf->SetTitle("AUP-Trazabilidad");
$mpdf->SetAuthor('PCOriente');
$mpdf->showWatermarkText = true;
$mpdf->watermark_font = 'DejaVuSansCondensed';
$mpdf->watermarkTextAlpha = 0.1;
$mpdf->SetDisplayMode('fullpage');

// No es necesario el mb_convert_encoding ya que mPDF 8.x maneja UTF-8 por defecto
$mpdf->WriteHTML($html);
$mpdf->Output('AUP-Trazabilidad.pdf', 'I');
