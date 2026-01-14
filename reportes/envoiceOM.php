<?php

require_once __DIR__ . '/../vendor/autoload.php';
include_once '../clases/consultas.php';
include_once '../DAOConeccion/conePDO.php';


$dao = new consultas();
$pdo = new conePDO();
$conexion = $pdo->conectar();



use Mpdf\Mpdf;

try {
    $dao = new consultas();
    $pdo = new conePDO();
    $conexion = $pdo->conectar();

    // Validación de parámetros
    $idAlmacen = isset($_GET['idAlmacen']) ? filter_var($_GET['idAlmacen'], FILTER_SANITIZE_NUMBER_INT) : null;
    $folioEntradaTambor = isset($_GET['folioEntradaTambor']) ? filter_var($_GET['folioEntradaTambor'], FILTER_SANITIZE_STRING) : null;
    $tmp = isset($_GET['tmp']) ? filter_var($_GET['tmp'], FILTER_SANITIZE_NUMBER_INT) : null;

    if (!$idAlmacen || !$folioEntradaTambor || !$tmp) {
        throw new Exception('Parámetros inválidos');
    }

    // Configuración de mPDF
    $mpdf = new Mpdf([
        'mode' => 'utf-8',
        'format' => 'A4',
        'margin_left' => 20,
        'margin_right' => 15,
        'margin_top' => 36,
        'margin_bottom' => 25,
        'margin_header' => 10,
        'margin_footer' => 10
    ]);

$idAlmacen = isset($_GET['idAlmacen']) ? filter_var($_GET['idAlmacen'], FILTER_SANITIZE_NUMBER_INT) : null;
if (!$idAlmacen) {
    die('ID de almacén inválido');
}
$folioEntradaTambor = $_GET['folioEntradaTambor'];
$tmp = $_GET['tmp'];
$info = $dao->encabezadoPrecios($idAlmacen, $folioEntradaTambor, $tmp);
if($tmp == 1){
    $Titulo='Comprobante de pago de Miel 100% pura de abeja';    
} else if($tmp == 5) {
    $Titulo='Comprobante de pago de Miel 100% mantequilla';    
} else if($tmp == 6) {
    $Titulo='Comprobante de pago de Miel 100% altiplano';    
} else if($tmp == 7) {
    $Titulo='Comprobante de pago de Miel 100% naranjo';    
}  else if($tmp == 8) {
    $Titulo='Comprobante de pago de Miel 100% aguacate';    
} else if($tmp == 9) {
    $Titulo='Comprobante de pago de Miel 100% mezquite';    
} else{
    $Titulo='Comprobante de pago de Miel 100% orgánica';    
}
if ($folioEntradaTambor) {
   
    // while ($resps = mysql_fetch_array($info)) {
    while ($resps = $info->fetch()) {
        
        $enca = new stdClass();
        // $enca->nombre = utf8_encode($resps['nombre']);
        $enca->nombre = mb_convert_encoding($resps['nombre'], 'UTF-8');
        $enca->localidad = utf8_encode($resps['localidad']);
        $enca->idSagarpa = $resps['idSagarpa'];
        $enca->folio = $resps['idAlmacen'];
        $enca->totalCompra = number_format($resps['totalCompra'], 2, '.', ',');
        $enca->fecha = $resps['fecha'];
    }
} else {
    
    // while ($resps = mysql_fetch_array($info)) {
        while ($resps = $info->fetch()) {
        $enca = new stdClass();
        $enca->nombre = utf8_encode($resps['nombre']);
        $enca->localidad = utf8_encode($resps['localidad']);
        $enca->idSagarpa = $resps['idSagarpa'];
        $enca->folio = $resps['folio'];
        $enca->totalCompra = number_format($resps['totalCompra'], 2, '.', ',');
        $enca->fecha = $resps['fecha'];
    }
}

$html2 = "<h1>Hola mundo</h1>";
//
$html = '
<html>
<body>

<!--mpdf
<htmlpageheader name="myheader">
        <table width="100%">
            <tr>
                <td width="25%" style="text-align: left;">
                    <p ><img src="../reportes/img/oaxaca.png" > </p>
                </td>
                <td width="50%" style="color:#0000; ">
                    <span style="font-weight: bold; font-size: 18pt;">'.$Titulo.'</span>
                </td>
                <td width="25%" style="text-align: right;">
                    
                    <span style="font-weight: bold; font-size: 12pt;">Entrada-Folio:' . $enca->folio . '</span>
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

<div style="text-align: right">Fecha: ' . $enca->fecha . '</div>
<table width="100%" style="font-family: serif;" cellpadding="5">
            <tr>
                <td width="49%" >
                    <span style="font-size: 7pt; color: #555555; font-family: sans;">CLIENTE:</span><br />' .
        'Nombre:<b> ' . $enca->nombre . '</b><br />' .
        'Localidad: ' . $enca->localidad . '<br />' .
        'ID Sagarpa: ' . $enca->idSagarpa . '<br />
                </td>
                <td width="50%" >
                    <span style="font-size: 7pt; color: #555555; font-family: sans;">COMPRADOR:</span><br />
                    <span style="font-weight: bold; font-size: 10pt;">Apicultores Unidos de la Península, S.A. de C.V.</span><br />
                    Carretera Mérida - Cancún Km 7.5 S/N<br />
                    Kanasín, Yucatán.<br />
                    Teléfonos: (9999)880980  /  (9992)122858<br />
                    
            </tr>
</table><br>

    
<!--<hr>-->

<table class="items" width="100%" style="font-size: 9pt; border-collapse: collapse; " cellpadding="8" border="1">
<thead>
                <tr >
                    <td width="10%">Folio</td>
                    <td width="8%">Zona</td>
                    <td width="8%">Lista</td>
                    <td width="8%">Bruto</td>
                    <td width="8%">Tara</td>
                    <td width="8%">Neto</td>
                    <td width="8%">Dif.</td>
                    <td width="8%">Humedad</td>
                    <td width="10%">Precio</td>
                    <td width="20%" style="text-align: center;">Costo</td>
                </tr>
</thead>
<tbody>';
$infoTabla = $dao->tablaPrecios($idAlmacen, $folioEntradaTambor, $tmp);
$sumaNeto=0;
$sumaDiferencia=0;
// while ($resultp = mysql_fetch_array($infoTabla)) {
    while ($resultp = $infoTabla->fetch()) {
    $html .= "<tr>"
            . "<td>" . $resultp['idAlmacen'] . "</td>"
            . "<td>" . $resultp['zona'] . "</td>"
            . "<td>" . number_format($resultp['pesoLista'], 0, '.', ',') . "</td>"
            . "<td>" . number_format($resultp['bruto'], 0, '.', ',') . "</td>"
            . "<td>" . number_format($resultp['tara'], 0, '.', ',') . "</td>"
            . "<td>" . number_format($resultp['neto'], 0, '.', ',') . "</td>"
            . "<td>" . number_format($resultp['diferencia'], 0, '.', ',') . "</td>"
            . "<td>" . number_format($resultp['humedad'], 2, '.', ',') . "</td>"
            . "<td>" . number_format($resultp['precio'], 2, '.', ',') . "</td>"
            . "<td style='text-align: right;'>" . number_format($resultp['costoTotal'], 2, '.', ',') . "</td>"
            . ""
            . "</tr>";
    
    $sumaNeto=$sumaNeto+number_format($resultp['neto'], 2, '.', ',');
    $sumaDiferencia=$sumaDiferencia+number_format($resultp['diferencia'], 2, '.', ',');

}


$html .= '
    </tbody>
</table><br>
<table align="right" style="font-size: 11pt; border-collapse: collapse; text-align: center;">
    <tr>
        <td style="text-align: left;"><b>T. Neto (Kgs): </b></td>
        <td style="text-align: right;"><b> ' .number_format($sumaNeto, 0,'.',',' ) . '</b></td>
    </tr>
    <tr>
        <td style="text-align: left;"><b>Total : </b></td>
        <td style="text-align: right;"> <b>$ ' . $enca->totalCompra . '</b></td>
    </tr>
    <tr>
    <td style="text-align: left;"><b>T. Diferencia (Kgs): </b></td>
    <td style="text-align: right;"><b> ' .number_format($sumaDiferencia, 0,'.',',' ) . '</b></td>
</tr>
</table><br>

<!--<hr>-->
<table width="100%" style="font-family: serif;" cellpadding="10">
            <tr>
                <td width="80%" style="border: 0.1mm solid #888888;" style="font-size: 8pt;">
                    <span style="font-weight: bold; font-size: 10pt;" >INFORMACION IMPORTANTE</span><br />
                     Favor de NO COBRAR EL CHEQUE hasta ser CONFIRMADO por el personal autorizado de la empresa; en caso de que el CHEQUE SE COBRE SIN AUTORIZACION, y éste sea REBOTADO, se le DESCONTARÁ la cantidad correspondiente a la SANCION que el banco aplica por la Comisión de Cheque sin Fondos. / ES OBLIGACION DEL PROVEEDOR DE MIEL CONFIRMAR EL CHEQUE ANTES DE PASARLO AL BANCO para evitar problemas y cargos futuros.<br />
                    <span style="font-weight: bold; font-size: 10pt;" >Descuento por cheque rebotado según el banco:</span><br />
                    Bancomer  - $1,124.04<br />
                    Santander - $1,112.00<br />
                    Banorte   - $1,112.00
                <td width="10%">
                    <span style="font-weight: bold; font-size: 10pt;">Firma de Conformidad</span><br />
                    <br />
                    <br />
                   ________________________<br />
                </td>
            </tr>
</table>
<hr>
</div>


</body>
</html>
';
//==============================================================
//==============================================================
//==============================================================
//==============================================================
//==============================================================
//==============================================================


// $mpdf = new mPDF('c', 'A4', '', '', 20, 15, 36, 25, 10, 10);
header('Content-Type: application/pdf');
$mpdf = new \Mpdf\Mpdf([
    'mode' => 'utf-8',
    'format' => 'A4',
    'margin_left' => 20,
    'margin_right' => 15,
    'margin_top' => 36,
    'margin_bottom' => 25,
    'margin_header' => 10,
    'margin_footer' => 10
]);
$mpdf->SetProtection(array('print'));
$mpdf->SetTitle("AUP-Comprobante de pago de miel");
$mpdf->SetAuthor("PCOriente");
//$mpdf->SetWatermarkText("Pagado");
$mpdf->showWatermarkText = true;
$mpdf->watermark_font = 'DejaVuSansCondensed';
$mpdf->watermarkTextAlpha = 0.1;
$mpdf->SetDisplayMode('fullpage');
$mpdf->WriteHTML($html);
$mpdf->Output('AUP-Comprobante de pago de miel.pdf', 'I');
// $mpdf->Close();
exit;

} catch (Exception $e) {
    error_log("Error generando PDF: " . $e->getMessage());
    die("Error al generar el documento: " . $e->getMessage());
}