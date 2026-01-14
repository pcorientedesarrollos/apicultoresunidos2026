<?php
require_once '../../vendor/autoload.php'; //SE cambia la ruta de la librería
include_once './consultas.php';
use Mpdf\Mpdf; //importamos la clase

$dao = new consultas();


$id = 2;
$infot = $dao->encabezadoPrecios($id);


$html = '<header >
      <div id="logo">
        <img src="img/oaxaca.png">
      </div>
      <h1>APICULTORES UNIDOS DE LA PENINSULA S.A DE C.V</h1>
      <div id="company" class="clearfix">
        <div>2a. Cda de Emiliano Zapata #19</div>

      </div>';


while ($resps = mysql_fetch_array($infot)) {
    $enca = new stdClass();
    $enca->nombre = $resps['nombre'];
    $enca->localidad = $resps['localidad'];
    $enca->id = $resps['idSagarpa'];
    $enca->folio = $resps['folio'];
    $enca->totalCompra = number_format($resps['totalCompra'], 2, '.', ',');
}
$html .= "
  <tr>
    <td>NOMBRE:</td>
    <td> $enca->nombre </td>
  </tr>
  <tr>
    <td>LOCALIDAD: </td>
    <td> $enca->localidad </td>
  <tr>
    <td>SAGARPA: </td>
    <td> $enca->id </td>
  <tr>
    <td>FOLIO: </td>
    <td> $enca->folio </td>
  </tr>";




$html .= ' <main class="clearfix">
      <table>
        <thead>
          <tr>
            <th class="service">Folio Tambores</th>
            <th class="desc">Zona</th>
            <th>PesoLista</th>
            <th>Bruto</th>
            <th>Tara</th>
            <th>Neto</th>
            <th>Diferencia</th>
            <th>Humedad</th>
            <th>Precio</th>
            <th>Costo Total</th>
          </tr>
        </thead>
        <tbody>';


$infr = $dao->tablaPrecios($id);
while ($resultp = mysql_fetch_array($infr)) {
    $html .= "<tr>"
            . "<td>" . $resultp['idAlmacen'] . "</td>"
            . "<td>" . $resultp['zona'] . "</td>"
            . "<td>" . number_format($resultp['pesoLista'], 0, '.', ',') . "</td>"
            . "<td>" . number_format($resultp['bruto'], 0, '.', ',') . "</td>"
            . "<td>" . number_format($resultp['tara'], 0, '.', ',') . "</td>"
            . "<td>" . number_format($resultp['neto'], 2, '.', ',') . "</td>"
            . "<td>" . number_format($resultp['diferencia'], 2, '.', ',') . "</td>"
            . "<td>" . number_format($resultp['humedad'], 2, '.', ',') . "</td>"
            . "<td>" . number_format($resultp['precio'], 2, '.', ',') . "</td>"
            . "<td>" . number_format($resultp['costoTotal'], 2, '.', ',') . "</td>"
            . ""
            . "</tr>";
}

$html .= "<tr>
        <td colspan=10> Total: $enca->totalCompra  </td>
        </tr>";



$html .= '    
        </tbody>
      </table>
      <div id="notices">
        <div><p><strong><u>NOTA IMPORTANTE:<u></strong></div>
        <div class="notice"> Favor de <strong>NO COBRAR EL CHEQUE hasta ser CONFIRMADO</strong> por el personal autorizado de la empresa; en caso de que el <strong>CHEQUE SE COBRE SIN AUTORIZACION</strong> y éste sea <strong>REBOTADO</strong>, se le <strong>DESCONTARÁ</strong> la cantidad correspondiente a la <strong>SANCION</strong> que el banco aplique por la Comisión de Cheque sin Fondos. /<strong>ES OBLIGACION DEL PROVEEDOR DE MIEL CONFIRMAR EL CHEQUE ANTES DE PASARLO AL BANCO</strong> para evitar problemas y cargos futuros.</p></div>
        
        <div><p>Descuento por Cheque Rebotados segun el Banco</p></div>
        </div>
    </main>
    <footer>
     
    </footer>';



// $mpdf = new mPDF('c', 'A4');
// $css = file_get_contents('css/style.css');
// $mpdf->WriteHTML($css, 1);
// $mpdf->WriteHTML($html);
// $mpdf->Output('reportePrecios.pdf', 'I');


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
$mpdf->SetTitle("AreportePrecios");
$mpdf->SetAuthor('PCOriente');
$mpdf->showWatermarkText = true;
$mpdf->watermark_font = 'DejaVuSansCondensed';
$mpdf->watermarkTextAlpha = 0.1;
$mpdf->SetDisplayMode('fullpage');

// No es necesario el mb_convert_encoding ya que mPDF 8.x maneja UTF-8 por defecto
$mpdf->WriteHTML($html);
$mpdf->Output('reportePrecios.pdf', 'I');


?>