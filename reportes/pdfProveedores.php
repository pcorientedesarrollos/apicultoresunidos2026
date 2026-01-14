<?php

require_once '../../vendor/autoload.php'; //SE cambia la ruta de la librería
include_once '../clases/consultas.php';
include_once '../DAOConeccion/conePDO.php';
use Mpdf\Mpdf; //importamos la clase


$dao = new consultas();
$pdo = new conePDO();
$conexion = $pdo->conectar();

$titulo = 'Relación de Proveedores';
$subtitulo = ' (Registrados en el sistema)';

$html = '<html>        
        <body>
        <!--mpdf
<htmlpageheader name="myheader">
            <table width="100%">
              <tr> 
                <td width="25%" style="text-align: left;">
                 <p ><img src="../reportes/img/oaxaca.png"></p>
                </td>
                <td width="50%" style="#0000; text-align: center;">
                 <span style="font-weight: bold; font-size: 18pt;">' . $titulo . '</span><br>
                 <span style="font-weight: bold; font-size: 12pt;">' . $subtitulo . '</span>
                     
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
<table  width="100%" style="font-size: 9pt; border-collapse: collapse; text-align: center;" cellpadding="6">
<thead>
                <tr  >
                    <th class="no" width="5%">No</th>
                    <th class="prover" width="25%">Proveedor</th>
                    <th class="sagar" width="10%">Sagarpa</th>
                    <th class="sagar" width="15%">Zona</th>
                    <th class="loc" width="10%">Localidad</th>
                    <th class="es" width="10%">Estado</th>
                    <th class="com" width="20%">Comprador</th>                   
                </tr>
</thead>
<tbody>';

$consultaProveedores = $dao ->Proveedor();
$proveedores = $conexion ->prepare($consultaProveedores);
$proveedores->execute();

$cont= 1 ;
while ($datosProveedores = $proveedores->fetch()){
    $html .= "<tr>"
            . "<td>" . $cont . "</td>"
            . "<td>" . $datosProveedores['nombre'] . "</td>"
            . "<td>" . $datosProveedores['idSagarpa'] . "</td>"
            . "<td>" . $datosProveedores['zona'] . "</td>"
            . "<td>" . $datosProveedores['localidad'] . "</td>"
            . "<td>" . $datosProveedores['estado'] . "</td>"
            . "<td>" . $datosProveedores['comprador'] . "</td>"
            . ""
            . "</tr>";
    $cont=$cont+1;
}


$html .= '
   
    </tbody>
</table>';

// $mpdf = new mPDF('c', 'A4', '', '', 10, 5, 38, 25, 10, 10);
// $mpdf->SetProtection(array('print'));
// $mpdf->SetTitle("AUP-Lista de Proveedores");
// $mpdf->SetAuthor("PCOriente");
// //$mpdf->SetWatermarkText("Pagado");
// $mpdf->showWatermarkText = true;
// $mpdf->watermark_font = 'DejaVuSansCondensed';
// $mpdf->watermarkTextAlpha = 0.1;
// $mpdf->SetDisplayMode('fullpage');
// $mpdf->WriteHTML($html);
// $mpdf->Output('AUP-Lista de Proveedores.pdf', 'I');


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
$mpdf->SetTitle("AUP-Lista de Proveedores");
$mpdf->SetAuthor('PCOriente');
$mpdf->showWatermarkText = true;
$mpdf->watermark_font = 'DejaVuSansCondensed';
$mpdf->watermarkTextAlpha = 0.1;
$mpdf->SetDisplayMode('fullpage');

// No es necesario el mb_convert_encoding ya que mPDF 8.x maneja UTF-8 por defecto
$mpdf->WriteHTML($html);
$mpdf->Output('AUP-Lista de Proveedores.pdf', 'I');