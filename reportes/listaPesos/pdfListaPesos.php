<?php

require_once '../../vendor/autoload.php'; //SE cambia la ruta de la librería
require_once '../../clases/consultas.php';
include_once '../../DAOConeccion/conePDO.php';
use Mpdf\Mpdf; //importamos la clase

$consulta = new listaPesos();
$pdo = new conePDO();
$conexion = $pdo->conectar();

$idTamborPeso = $_GET["idTamborPeso"];

$datosEncabezadoPeso = $consulta->listaPesosEnca($idTamborPeso);
$datosEncabezadoPeso['totalPBruto'] = $datosEncabezadoPeso["totalBruto"];
$datosEncabezadoPeso['totalPNeto'] = $datosEncabezadoPeso["totalNeto"];

$html = '<html> 
            <body>
                <!--mpdf
                     <htmlpageheader name="myheader">
                        <table width="100%">
                        
                            <tr>
                                <td width="30%" style="text-align: left;">
                                <img src="../../reportes/img/oaxaca.png" >
                                </td>
                                <td width="70%" style="color:#0000; text-align: center;">
                                <span style="text-align: center; font-weight: bold; font-size: 10pt;">OAXACA MIEL, S.A. DE C.V.</span><br/>
                                <span style="text-align: center; font-weight: bold; font-size: 6pt;">R.F.C. OMI 950913 TV2</span><br/>
                                <span style="text-align: center; font-size: 6pt;">2a. CERRADA DE EMILIANO ZAPATA No. 19 </span><br/>
                                <span style="text-align: center; font-size: 6pt;">COL.BOSQUES DEL SUR,</span><br/>
                                <span style="text-align: center; font-size: 6pt;">DELEGACION XOCHIMILCO, MÉXICO 16010, D.F.</span><br/>
                                <span style="text-align: center; font-size: 6pt;">SALIDA DE ALMACEN</span><br/></br/></br/><br>
                                <span style="text-align: center; font-size: 8pt;"><b>LISTA DE PRODUCTO TERMINADO LOTE ' . $datosEncabezadoPeso['lote'] . ' </b></span><br/>
                                <span style="text-align: center; font-size: 8pt;"><b>' . strtoupper($datosEncabezadoPeso['tipoMiel']) .  ' ' . strtoupper($datosEncabezadoPeso['clasificacionMiel']) .'</b></span><br/>  
                                <span style="text-align: center; font-size: 8pt;"><b>' . $datosEncabezadoPeso['floracion'] . '</b></span><br/>                                                               
                                </td>
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
                         <table width="100%" style="font-size:10pt; border-collapse: collapse;" cellpadding="3">
                        <tr>
                            <td style="text-align: left;"><b>Destino: ' . $datosEncabezadoPeso['destino'] . '</b></td>
                            <td style="text-align: left;"><b>Código: RAL-LPT-01</b></td>
                        </tr>
                        <tr>
                            <td style="text-align: left;"><b>Fecha: ' . $datosEncabezadoPeso['fechaImpresion'] . '</b></td>
                            <td style="text-align: left;"><b>Revisión: 01</b></td>
                        </tr>
                        <tr>
                            <td style="text-align: left;"><b>Número de lote: ' . $datosEncabezadoPeso['idLoteInterno'] . '</b></td>
                            <td style="text-align: right;"><b>Folio: ' . $datosEncabezadoPeso['idTamborPeso'] . '</b></td>                        
                        </tr>
                     </table>
                     <br/>';

if ($datosEncabezadoPeso['mielHomogeneizada'] == '2' && isset($_GET['conFolios'])) {
    $html .= '<table width="100%" style="font-size: 8pt; border-collapse: collapse; text-align: center;" cellpadding="4" border="1">
                        <tr> 
                            <th align="center">No</th>
                            <th align="center">Folio</th>                                      
                            <th align="center"> Bruto</th>
                            <th align="center">Tara</th>
                            <th align="center">Peso Neto</th>
                            <th align="center">Humedad</th>
                            <th align="center">Color</th>
                            <th align="center">Floración</th>
                        </tr>';
} else {
    $html .= '<table width="100%" style="font-size: 8pt; border-collapse: collapse; text-align: center;" cellpadding="4" border="1">
    <tr> 
        <th align="center">No</th>      
        <th align="center">Peso Bruto</th>
        <th align="center">Tara</th>
        <th align="center">Peso Neto</th>
        <th align="center">Humedad</th>
        <th align="center">Color</th>
        <th align="center">Floración</th>
    </tr>';
}

$consultaDetalleLista = $consulta->listaPesosDetalle($idTamborPeso);

$contLista = 1;
foreach ($consultaDetalleLista as $infoDetalleLista) {
    $folio  = $infoDetalleLista["folio"];
    $bruto      = $infoDetalleLista['bruto'];
    $tara = $infoDetalleLista['tara'];
    $neto  = $infoDetalleLista['neto'];
    $humedad  = $infoDetalleLista['humedad'];
    $color  = $infoDetalleLista['color'];
    $floracion  = $infoDetalleLista['floracion'];
    if ($folio != '0' && isset($_GET['conFolios'])) {
        $html .= '<tr>'
            . '<td>' . $contLista . '</td>'
            . '<td>' . $folio . '</td>'
            . '<td>' . $bruto . '</td>'
            . '<td>' . $tara . '</td>'
            . '<td>' . $neto . '</td>'
            . '<td>' . $humedad . '</td>'
            . '<td>' . $color . '</td>'
            . '<td>' . $floracion . '</td>'
            . '</tr>';
        $contLista = $contLista + 1;
    } else {
        $html .= '<tr>'
            . '<td>' . $contLista . '</td>'
            . '<td>' . $bruto . '</td>'
            . '<td>' . $tara . '</td>'
            . '<td>' . $neto . '</td>'
            . '<td>' . $humedad . '</td>'
            . '<td>' . $color . '</td>'
            . '<td>' . $floracion . '</td>'
            . '</tr>';
        $contLista = $contLista + 1;
    }
}
if ($datosEncabezadoPeso['mielHomogeneizada'] == 2 && isset($_GET['conFolios'])) {
    $html .= ' <tr>
    <td colspan="2"><b style="font-size: 8pt;">Totales :</b></td>
    <td><b style="font-size: 8pt;">' . number_format($datosEncabezadoPeso['totalPBruto'], 0, '.', ',') . '</b></td>
    <td><b style="font-size: 8pt;">' . number_format($datosEncabezadoPeso['totalTara'], 0, '.', ',') . '</b></td>
    <td><b style="font-size: 8pt;">' . number_format($datosEncabezadoPeso['totalPNeto'], 0, '.', ',') . '</b></td>
 </tr>
</table>';
} else {
    $html .= ' <tr>
    <td><b style="font-size: 8pt;">Totales :</b></td>
    <td><b style="font-size: 8pt;">' . number_format($datosEncabezadoPeso['totalPBruto'], 0, '.', ',') . '</b></td>
    <td><b style="font-size: 8pt;">' . number_format($datosEncabezadoPeso['totalTara'], 0, '.', ',') . '</b></td>
    <td><b style="font-size: 8pt;">' . number_format($datosEncabezadoPeso['totalPNeto'], 0, '.', ',') . '</b></td>
 </tr>
</table>';
}

$html .= '<br> 
        <table width="100%">
            <tr> 
                <td width="33%">
                <br />
                <br />
                   ________________________<br />
                   <span style="text-align: center; align-content: center; font-weight: bold; font-size: 10pt; ">Chofer</span>                   
                </td>
                <td width="33%">
                <br />
                    <br />
                   ________________________<br />
                   <span style="text-align: center; align-content: center; font-weight: bold; font-size: 10pt;">Jefe de Producción</span>
                </td>
                <td width="33%">
                <br />
                    <br />
                   ________________________<br />
                   <span style="text-align: center; align-content: center; font-weight: bold; font-size: 10pt;">Jefe de Calidad</span>
                </td>
                <td width="33%">
                <br />
                    <br />
                   ________________________<br />
                   <span style="text-align: center; align-content: center; font-weight: bold; font-size: 10pt;">Gerente de Planta</span>
                </td>
            </tr>
            </table>
            <br>
            <table width="100%">
            <tr>
            <td width="33%">
            <span style="font-size: 10pt;">Operador: ' . $datosEncabezadoPeso['operador'] . '</span><br />
                <span style="font-size: 10pt;">Empresa: ' . $datosEncabezadoPeso['compania'] . '</span><br/>
                <span style="font-size: 10pt;">Marca: ' . $datosEncabezadoPeso['marca'] . '</span><br/>
                <span style="font-size: 10pt;">Modelo: ' . $datosEncabezadoPeso['modelo'] . '</span><br/>
                <span style="font-size: 10pt;">Placa: ' . $datosEncabezadoPeso['placa'] . '</span><br/>
                <span style="font-size: 10pt;">Contenedor: ' . $datosEncabezadoPeso['contenedor'] . '</span><br/>
                <span style="font-size: 10pt;">Sello: ' . $datosEncabezadoPeso['sello'] . '</span><br/>
            </td>
            </tr>
          </table>
            </body>
       </html>';

// $mpdf = new mPDF('c', 'A4', '', '', 20, 15, 50, 25, 10, 10);
// $mpdf->SetProtection(array('print'));
// $mpdf->SetTitle('AUP-Lista de Pesos');
// $mpdf->SetAuthor("PCOriente");
// $mpdf->showWatermarkText = true;
// $mpdf->watermark_font = 'DejaVuSansCondensed';
// $mpdf->watermarkTextAlpha = 0.1;
// $mpdf->SetDisplayMode('fullpage');
// $mpdf->WriteHTML($html);
// $mpdf->Output('AUP-Lista de Pesos.pdf', 'I');


//creamos el objeto mpdf
$mpdf = new Mpdf([
    'mode' => 'utf-8',
    'format' => 'A4',
    'margin_left' => 12,
    'margin_right' => 15,
    'margin_top' => 50,
    'margin_bottom' => 25,
    'margin_header' => 10,
    'margin_footer' => 10
]);

// Configurar propiedades del PDF
$mpdf->SetTitle("AUP-Lista de Pesos");
$mpdf->SetAuthor('PCOriente');
$mpdf->showWatermarkText = true;
$mpdf->watermark_font = 'DejaVuSansCondensed';
$mpdf->watermarkTextAlpha = 0.1;
$mpdf->SetDisplayMode('fullpage');

// No es necesario el mb_convert_encoding ya que mPDF 8.x maneja UTF-8 por defecto
$mpdf->WriteHTML($html);
$mpdf->Output('AUP-Lista de Pesos.pdf', 'I');