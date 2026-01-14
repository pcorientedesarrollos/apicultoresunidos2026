<?php
require_once '../../vendor/autoload.php'; //SE cambia la ruta de la librería
require_once '../clases/consultas.php';
use Mpdf\Mpdf; //importamos la clase

$dao = new consultas();

$idRequisicion = $_GET["idRequisicion"];


$Dp = $dao->Rencabezado($idRequisicion);

$titulo = 'Requerimientos de Depósito de Compra';

//while($resD = mysql_fetch_array($Dp)){
while ($resD = $Dp->fetch()) {
    $enca = new stdClass();
    $enca->FechaRe = $resD['fechaRequisicion'];
    $enca->FechaIm = $resD['fechaImpresion'];
    $enca->importe = number_format($resD['importeTotal'], 2, '.', ',');
    $enca->Tambores = $resD['totalTambores'];
    $enca->kilos = number_format($resD['totalKilos'], 2, '.', ',');
}

$html = '<html>
    <body>
           <!--mpdf
<htmlpageheader name="myheader">
            <table width="100%">
              <tr> 
                <td width="25%" style="text-align: left;">
                 <p ><img src="../reportes/img/oaxaca.png"></p>
                </td>
                <td width="65%" style="#0000; text-align: center;">
                 <span style="font-weight: bold; font-size: 18pt;">' . $titulo . '</span><br>
                    
                </td>
                <td width="15%" >
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

<table width="100%" style="font-family: serif;" cellpadding="5">
            <tr>
                <td width="49%" >
                    <span style=" font-size: 10pt; color: #555555; font-family: sans;">Fecha de Reporte: </span>' . $enca->FechaRe . '
                </td>
                <td width="50%" >
                    <span style=" font-size: 10pt; color: #555555; font-family: sans;">Fecha de Impresión: </span>' . $enca->FechaIm . '
                 </td>      
            </tr>
</table><br>
<!--<hr> -->
<table width="100%" style="font-size: 8pt; border-collapse: collapse; text-align: center;" cellpadding="7" border="1">
    <thead>
        <tr>
            <th>N°</th>
            <th>Proveedor</th>
            <th>Localidad</th>
            <th>Miel</th>            
            <th>Tambores</th>
            <th>Peso</th>
            <th>Precio</th>
            <th>Importe</th>
            <th>Banco</th>
            <th>Observaciones</th>
        </tr>
    </thead>';

$tab = $dao->RTablaDeposito($idRequisicion);
$sumaKilos = 0;
$contProvee = 1;
//while ($tabs = mysql_fetch_array($tab)) {
while ($tabs = $tab->fetch()) {

    $html .= "<tr>"
            . "<td>" . $contProvee . "</td>"
            . "<td>" . $tabs['nombre'] . "</td>"
            . "<td>" . $tabs['localidad'] . "</td>"
            . "<td>" . $tabs['tipoDeMiel'] . "</td>"
            . "<td>" . $tabs['noTambores'] . "</td>"
            . "<td>" . number_format($tabs['peso'], 2, '.', ',') . "</td>"
            . "<td>$" . number_format($tabs['precio'], 2, '.', ',') . "</td>"
            . "<td>$" . number_format($tabs['importe'], 2, '.', ',') . "</td>"
            . "<td>" . $tabs['banco'] . "</td>"
            . "<td>" . $tabs['observaciones'] . "</td>"
            . "</tr>";

    $sumaKilos = $sumaKilos + number_format($tabs['peso'], 2, '.', ',');
    $contProvee = $contProvee + 1;
}

$html .='
</table><br/>
        <table  align="right" style="font-size: 11pt; border-collapse: collapse; text-align: center;">
            <tr> 
                <td><b>T.Tambores:</b></td>
                <td style="text-align: right;"> <b>' . $enca->Tambores . '</b></td>              
            </tr>
           <tr>
                <td style="text-align: left;"><b>T.Importe:</b></td>
                <td style="text-align: right;"><b>$ ' . $enca->importe . '</b></td>
           </tr>
           <tr>
                <td style="text-align: left;"><b>T.kilos(kg):  </b></td>
                <td style="text-align: right;"><b>' . $enca->kilos . '</b></td>
           </tr>
        </table>
    </body>
</html>';

// $mpdf = new mPDF('c', 'A4', '', '', 20, 15, 38, 25, 10, 10);
// $mpdf->SetProtection(array('print'));
// $mpdf->SetTitle("AUP-Requerimientos de Depósito De Compra");
// $mpdf->SetAuthor("PCOriente");
// //$mpdf->SetWatermarkText("Pagado");
// $mpdf->showWatermarkText = true;
// $mpdf->watermark_font = 'DejaVuSansCondensed';
// $mpdf->watermarkTextAlpha = 0.1;
// $mpdf->SetDisplayMode('fullpage');
// $mpdf->WriteHTML($html);
// $mpdf->Output('AUP-Requerimientos de Depósito De Compra.pdf', 'I');
// exit;


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
$mpdf->SetTitle("AUP-Requerimientos de Depósito De Compra");
$mpdf->SetAuthor('PCOriente');
$mpdf->showWatermarkText = true;
$mpdf->watermark_font = 'DejaVuSansCondensed';
$mpdf->watermarkTextAlpha = 0.1;
$mpdf->SetDisplayMode('fullpage');

// No es necesario el mb_convert_encoding ya que mPDF 8.x maneja UTF-8 por defecto
$mpdf->WriteHTML($html);
$mpdf->Output('AUP-Requerimientos de Depósito De Compra.pdf', 'I');
 ?>


