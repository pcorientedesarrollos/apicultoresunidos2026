<?php

require_once '../vendor/autoload.php'; //SE cambia la ruta de la librería ok
require_once '../clases/consultas.php';
include_once '../DAOConeccion/conePDO.php';
include_once '../utilerias/php/dameNombrePersonal.php';
use Mpdf\Mpdf; //importamos la clase ok

$consultas = new trazabilidad();
$pdo = new conePDO();
$conexion = $pdo->conectar();

$idLoteInterno = $_GET["idLoteInterno"];
$tipoMiel = $_GET["miel"];
if($tipoMiel == '1'){
    $tituloSalida = "Trazabilidad de Salida Miel 100% Pura de Abeja";
}else if($tipoMiel == '2'){
    $tituloSalida = "Trazabilidad de Salida Miel 100% Orgánica";
}else if($tipoMiel == '5'){
    $tituloSalida = "Trazabilidad de Salida Miel 100% Mantequilla";
}else if($tipoMiel == '6'){
    $tituloSalida = "Trazabilidad de Salida Miel 100% Altiplano";
}else if($tipoMiel == '7'){
    $tituloSalida = "Trazabilidad de Salida Miel 100% Naranjo";
}else if($tipoMiel == '8'){
    $tituloSalida = "Trazabilidad de Salida Miel 100% Aguacate";
}else if($tipoMiel == '9'){
    $tituloSalida = "Trazabilidad de Salida Miel 100% Mezquite";
}

$consultaSalida = $consultas->tranzabilidadSalida($idLoteInterno, $tipoMiel);
$dataSalida = $conexion->prepare($consultaSalida);
$dataSalida->execute();
$nombre_gerente = dameNombrePersonal(2, $conexion);
while ($informacionSalida = $dataSalida->fetch()) {
    $detallesSalida = new stdClass();
    $detallesSalida->fechaEnvasado = $informacionSalida["fechaEnvasado"];
//    $detallesSalida->lote = $informacionSalida["lote"];
    $detallesSalida->marcaFinalCliente = $informacionSalida["marcaFinalCliente"];
    $detallesSalida->kg = $informacionSalida["kilosSalida"];
    $detallesSalida->fchSalida = $informacionSalida["fechaSalida"];
    $detallesSalida->empresa = $informacionSalida["empresa"];
    $detallesSalida->pais = $informacionSalida["pais"];
    $detallesSalida->kgExportar = $informacionSalida["kgExportar"];
    $detallesSalida->tipoMiel = utf8_decode($informacionSalida["tipoDeMiel"]);
    if ($informacionSalida["homogeneizado"] == 1) {
        $detallesSalida->homogeneizado = "Sí";
    } else {
        $detallesSalida->homogeneizado = "No";
    }
}


$consultaSalida1 = $consultas->tranzabilidadSalidaArray($idLoteInterno, $tipoMiel);
$dataSalida1 = $conexion->prepare($consultaSalida1);
$dataSalida1->execute();

$html = '<html>  
            <body>
                <!--mpdf
                    <htmlpageheader name="myheader">
                        <table width="100%">
                            <tr>
                                <td style="text-align: left;">
                                   <!-- <p ><img src="../../reportes/img/oaxaca.png" > </p>-->
                                      <p ><img src="../../reportes/img/encabezadoTrazabilidad700.png" > </p>
                             <!--    </td>
                                <td width="50%" style="color:#0000; ">
                                    <!--<span style="font-weight: bold; font-size: 18pt;">' . $tituloSalida . '</span>-->
                                     
                                </td>-->
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
                        
                  <!-- <table width="100%" style="font-family: serif;" cellpadding="5">
            <tr>
                   <td width="50%" >
                1.- RAZON SOCIAL: <u>OaxacaMiel S.A de S.V</u><br>
                3.- DOMICILIO DEL ESTABLECIMIENTO: <br><u>Carretera Mérida-Cancún K.m 7.5 Sn Pedro Noh Pat</u><br>
                5.- MUNICIPIO: <u>Kanasín</u><br>
                7.- TELEFONOS: <u>01-999-9880990</u>
                    
                </td>
                <td width="50%">
                2.- No de ID: 3108771I<br>
                4.- ESTADO : <u>Yucatán</u><br>
                6.- ENCARGADO O RESPONSABLE DEL ACOPIO DE MIEL: <u>' . $nombre_gerente['nombre_completo'] . '</u><br>
                8.- CORREO: <u>' . $nombre_gerente['correo'] . '</u>
                <td>
            </tr>
         </table>-->
            <br>
        <table width="100%" style="font-size: 9pt; border-collapse: collapse; text-align: center; " cellpadding="4" border="1">
         <tr> 
             <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 20px; background-color: yellowgreen" colspan="10">' . utf8_decode($tituloSalida) . '</th>
         </tr>
         <tr>
            <td rowspan = "4">Fecha de Envasado(9)</td>
            <td colspan="2">Lote (10)</td>
            <td rowspan = "4">No. de ID de Apicultores que Conforman el Lote (11)</td>
            <td rowspan = "4">Volumen (kg) de Miel Usada por Apicultor (12)</td>
            <td></td>
            <td colspan="4">Destino y Volumen (Kg) (14)</td>
         </tr>
         <tr>
            <td>Homogenizado (a)</td>
            <td align="center">' . $detallesSalida->homogeneizado . '</td>
            <td rowspan = "3">Fecha de Salida (13)</td>
            <td rowspan = "3">Empresa de Destino / Pais (e)</td>
            <td rowspan = "3">Cantidad Total de  (Kg) a Exportar.(f)</td>
            <td rowspan = "3">Nacional (Kg) (g)</td>
            <td rowspan = "3">Consumidor Directo (Kg) (h)</td>
         </tr>
         <tr>
         <td>Sin Homogenizar (b)</td>
         <td></td>
        </tr>
        <tr>
         <td align="center">N° (c)</td>
         <td align="center">Kg (d)</td>
        </tr>
         ';

while ($infoAdicional = $dataSalida1->fetch()) {
    $html .= '<tr>'
        . '<td></td>'
        . '<td></td>'
        . '<td></td>'
        . '<td>' . $infoAdicional["idSagarpa"] . '</td>'
        . '<td>' . $infoAdicional["volumen"] . '</td>'
        . '<td></td>'
        . '<td></td>'
        . '<td></td>'
        . '<td></td>'
        . '<td></td>'
        . '</tr>';
    $sumaVolumenApi = $sumaVolumenApi + $infoAdicional["volumen"];
}

$html .= '
            <tr>
                <td>' . $detallesSalida->fechaEnvasado . '</td>
                    <td>' . $detallesSalida->marcaFinalCliente . '</td>
                <td>' . number_format($detallesSalida->kg, 0, '.', ',') . '</td>
                <td></td>
                <td>' . number_format($sumaVolumenApi, 0, '.', ',') . '</td>
                <td>' . $detallesSalida->fchSalida . '</td>
                <td>' . $detallesSalida->empresa . '<br>' . $detallesSalida->pais . '</td>
                <td>' . number_format($detallesSalida->kgExportar, 0, '.', ',') . '</td>
                <td></td>
                <td></td>
            </tr>
        </table><br>
        <div>FO-BP-PI-PO-01/09</div><br>
        <table width="100%">
            <tr> 
                <td width="33%">
                <span style="font-weight: bold; font-size: 12pt;">Firma del Responsable</span><br />
                    <br />
                   ________________________<br />
                   
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
        $mpdf->SetTitle("AUP-Trazabilidad Salida");
        $mpdf->SetAuthor('PCOriente');
        $mpdf->showWatermarkText = true;
        $mpdf->watermark_font = 'DejaVuSansCondensed';
        $mpdf->watermarkTextAlpha = 0.1;
        $mpdf->SetDisplayMode('fullpage');
        
        // No es necesario el mb_convert_encoding ya que mPDF 8.x maneja UTF-8 por defecto
        $mpdf->WriteHTML($html);
        $mpdf->Output('AUP-Trazabilidad Salida.pdf', 'I');