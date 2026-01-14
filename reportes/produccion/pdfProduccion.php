<?php

require_once '../../vendor/autoload.php'; //SE cambia la ruta de la librería
require_once '../../clases/consultas.php';
include_once '../../DAOConeccion/conePDO.php';
use Mpdf\Mpdf; //importamos la clase

$consulta = new produccion();
$pdo = new conePDO();
$conexion = $pdo->conectar();
$idReporteProceso = $_GET["idReporteProceso"];
$tmp = $_GET["tmp"];
$tituloProcesos = "CONTROL DE PROCESO";
if ($tmp == 1) {
    $subTitulo = "CONTROL DE PROCESO";
    $sub = " MIEL 100% PURA DE ABEJA";
} else {
    $subTitulo = "CONTROL DE PROCESO";
    $sub = " MIEL 100% ORGÁNICA";
}

$produccionPdf = $consulta->infoReporteProceso($idReporteProceso, $tmp);
$regresoInfo = $conexion->prepare($produccionPdf);
$regresoInfo->execute();

while ($datosProceso = $regresoInfo->fetch()) {
    $reporteProceso = new stdClass();
    $reporteProceso->idReporteProceso = $datosProceso["idReporteProceso"];
    $reporteProceso->fechaReporte = $datosProceso["fechaProceso"];
    $reporteProceso->idLoteInterno = $datosProceso["idLoteInterno"];
    $reporteProceso->responsable = $datosProceso["nombreRes"];
    $reporteProceso->supervisor = $datosProceso["nombreSuper"];
    $reporteProceso->horaInicio = $datosProceso["horaInicio"];
    $reporteProceso->numeroTanque = $datosProceso["numeroTanque"];
    $reporteProceso->numeroTambores = $datosProceso["numeroDeTambores"];
    ///////// Higiene de zona Inocua //////
    if ($datosProceso["cabello"] == 1) {
        $reporteProceso->cabello = "Sí";
    } else {
        $reporteProceso->cabello = "No";
    }

    if ($datosProceso["cofia"] == 1) {
        $reporteProceso->cofia = "Sí";
    } else {
        $reporteProceso->cofia = "No";
    }

    if ($datosProceso["botas"] == 1) {
        $reporteProceso->botas = "Sí";
    } else {
        $reporteProceso->botas = "No";
    }

    if ($datosProceso["unas"] == 1) {
        $reporteProceso->una = "Sí";
    } else {
        $reporteProceso->una = "No";
    }

    if ($datosProceso["cubreBoca"] == 1) {
        $reporteProceso->cubreBocas = "Sí";
    } else {
        $reporteProceso->cubreBocas = "No";
    }

    if ($datosProceso["sanitizacionManos"] == 1) {
        $reporteProceso->sanitizacionManos = "Sí";
    } else {
        $reporteProceso->sanitizacionManos = "No";
    }

    if ($datosProceso["ropa"] == 1) {
        $reporteProceso->ropa = "Sí";
    } else {
        $reporteProceso->ropa = "No";
    }

    if ($datosProceso["lavadoManos"] == 1) {
        $reporteProceso->lavadoManos = "Sí";
    } else {
        $reporteProceso->lavadoManos = "No";
    }

    if ($datosProceso["sanitizacionBotas"] == 1) {
        $reporteProceso->sanitizacionBotas = "Sí";
    } else {
        $reporteProceso->sanitizacionBotas = "No";
    }

    /////////////////////////////////////////////////////
    if ($datosProceso["lavadoHerramientas"] == 1) {
        $reporteProceso->lavadoHerramientas  = "Sí";
    } else {
        $reporteProceso->lavadoHerramientas = "No";
    }
    $reporteProceso->horaFinalReposo = $datosProceso["horaFinalReposo"];
    $reporteProceso->tiempoHomogeneizacion = $datosProceso["tiempoHomogeneizacion"];
    $reporteProceso->otrasHerramientas = $datosProceso["otrasHerramientas"];
    $reporteProceso->noProcesada = $datosProceso["noProcesada"];
    $reporteProceso->procesada = $datosProceso["procesada"];
    $reporteProceso->elProducto = $datosProceso["producto"];
    if ($datosProceso["producto"] == 1) {
        $reporteProceso->producto = "Miel 100% pura de abeja";
    } else {
        $reporteProceso->producto = "Miel 100% orgánica";
    }
    $reporteProceso->inicioHomogeneizado = $datosProceso["inicioHomogeneizado"];
    $reporteProceso->finalHomogeneizado = $datosProceso["finalHomogeneizado"];
    $reporteProceso->tiempoReposo = $datosProceso["tiempoReposo"];
    $reporteProceso->observaciones = $datosProceso["observaciones"];
    $reporteProceso->horaFinal = $datosProceso["horaFinal"];
    $reporteProceso->cantidadLlave = $datosProceso["cantidadLlave"];
    if ($datosProceso["condicionLlave"] == 1) {
        $reporteProceso->condicionLlave = "Buenas";
    } else {
        $reporteProceso->condicionLlave = "Malas";
    }

    $reporteProceso->cantidadPala = $datosProceso["cantidadPala"];
    if ($datosProceso["condicionPala"] == 1) {
        $reporteProceso->condicionPala = "Buenas";
    } else {
        $reporteProceso->condicionPala = "Malas";
    }
    $reporteProceso->cantidadOtras = $datosProceso["cantidadOtras"];
    if ($datosProceso["condicionOtras"] == 1) {
        $reporteProceso->condicionOtras = "Buenas";
    } else {
        $reporteProceso->condicionOtras = "Malas";
    }
    $reporteProceso->observacionesDes = $datosProceso["observacionesDePesos"];
}


$html = '<html>
            <body>
                <!--mpdf
                    <htmlpageheader name="myheader">
                      <div align = "right">CÓDIGO : RPR-PM-01 </div>
                      <div align = "right">REVISIÓN : 01 </div>
                        <table width="100%">
                            <tr>
                                <td width="25%" style="text-align: left;">
                                    <p><img src="../../reportes/img/oaxaca.png" ></p>
                                </td>
                                <td></td>
                                <th width="50%" style="color:#0000; text-align: center;">
                                    <span style="font-weight: bold; font-size: 18pt;">' . $tituloProcesos . '</span><br/>
                                    <span style="font-weight: bold; font-size: 12pt;">' . $sub . '</span>                                    
                                </th>
                                <th></th>
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
           <div style="font-weight: bold; font-size: 13pt;"><b>Datos del Proceso</b></div>
       <table width="100%" style="font-family: serif;" cellpadding="5">
            <tr>
                <td width="50%" >
                    Lote Interno: ' . $reporteProceso->idLoteInterno . '<br/>
                    Responsable: ' . $reporteProceso->responsable . '
                </td>
                <td width="50%">
                    Fecha de Proceso: ' . $reporteProceso->fechaReporte . '<br/>
                    Supervisor: ' . $reporteProceso->supervisor . '
                </td>
                </tr>
        </table>
        <table width="100%" style="font-family: serif;" cellpadding="5">
        <tr> 
            <td >Hora de Inicio: ' . $reporteProceso->horaInicio . '</td>
            <td>N.Tanque: ' . $reporteProceso->numeroTanque . '</td>
            <td>N.Tambores: ' . $reporteProceso->numeroTambores . '</td>
        </tr>
        </table>
        <br>
         <div style="font-weight: bold; font-size: 13pt;"><b>Higiene del Personal de la Zona Inocua</b></div>
         <br>
          <table  width="100%" style="font-family: serif;">
            <tr>
                <td  style="text-align:left">Cabello Corto : ' . $reporteProceso->cabello . '</td>
                <td  style="text-align:left">Cofía : ' . $reporteProceso->cofia . '</td>
                <td  style="text-align:left">Lavado de Botas : ' . $reporteProceso->botas . '</td>
            </tr>
             <tr>
                <td  style="text-align:left">Uñas Cortas : ' . $reporteProceso->una . '</td>
                <td  style="text-align:left">Cubrebocas : ' . $reporteProceso->cubreBocas . '</td>
                <td  style="text-align:left">Sanitización de Manos : ' . $reporteProceso->sanitizacionManos . '</td>
            </tr>
            <tr>
                <td  style="text-align:left">Ropa Limpia : ' . $reporteProceso->ropa . '</td>
                <td  style="text-align:left">Lavado de Manos : ' . $reporteProceso->lavadoManos . '</td>
                <td  style="text-align:left">Sanitización de Botas : ' . $reporteProceso->sanitizacionBotas . '</td>
            </tr>
        </table>';
$idLoteInternoPr = $reporteProceso->idLoteInterno;
$descripcionDetalle = $consulta->descrpcionLoteProcesado($idLoteInternoPr, $tmp);
$dataDescripcion = $conexion->prepare($descripcionDetalle);
$dataDescripcion->execute();

while ($infoDescripcion = $dataDescripcion->fetch()) {
    $datosDescri = new stdClass();
    $datosDescri->kilosTotales = $infoDescripcion["kilosTotales"];
    $datosDescri->brutoTotal = $infoDescripcion["brutoTotal"];
    $datosDescri->taraTotal = $infoDescripcion["taraTotal"];
    $datosDescri->netoTotal = $infoDescripcion["netoTotal"];
}
$html .= '<br>
    <div style="font-weight: bold; font-size: 13pt;"><b>Descripción</b></div>
    <br>
    <table width="100%" style="font-family: serif;" cellpadding="5">
        <tr> 
            <td>Producto: ' . $reporteProceso->producto . '</td>
            <td>Cant: ' . number_format($datosDescri->kilosTotales, 0, '.', ',') . '</td>
            <td>Bruto ' . number_format($datosDescri->brutoTotal, 0, '.', ',') . ' </td>
            <td>Tara: ' . number_format($datosDescri->taraTotal, 0, '.', ',') . '</td> 
            <td>Neto: ' . number_format($datosDescri->netoTotal, 0, '.', ',') . '</td>
        </tr> 
        <tr>
            <td>Miel No Procesada : ' . number_format($reporteProceso->noProcesada, 0, '.', ',') . '</td>
            <td>Miel Procesada:  ' . number_format($reporteProceso->procesada, 0, '.', ',') . '</td>
        </tr>
     </table>
     <br>
     <div style="font-weight: bold; font-size: 13pt;"><b>Observaciones de la Descripción</b></div>
     <br>
     <div>' . $reporteProceso->observacionesDes . '</div>
     <br/>
     <div style="font-weight: bold; font-size: 13pt;"><b>Personal</b></div>
     <br>
     <table width="100%" style="font-family: serif; text-align: left;">
           <tr>
               <th align="left">Conformación de Lote:</th>
           </tr>';
$consultaLote = $consulta->personalConforLote($idReporteProceso, $tmp);
$dataLote = $conexion->prepare($consultaLote);
$dataLote->execute();
while ($conformacionLote = $dataLote->fetch()) {
    $html .= '<tr> '
        . '<td>' . $conformacionLote["nombre"] . '</td>'
        . '<tr>';
}
$html .= '</table>   
                <br>
                <table width="100%" style="font-family: serif; text-align: left;">
            <tr>
              <th align="left">Proceso de Zona Inocua</th>             
            </tr>';
$consultaZonaIno = $consulta->personalzonainocua($idReporteProceso, $tmp);
$dataZonainocua = $conexion->prepare($consultaZonaIno);
$dataZonainocua->execute();

while ($zonaInocua = $dataZonainocua->fetch()) {

    $html .= '<tr> '
        . '<td>' . $zonaInocua["nombre"] . '</td>'
        . '</tr>';
}
$html .= '</table>  <br>
                <table width="100%" style="font-family: serif; text-align: left">
            <tr>
              <th align="left">Busqueda de Folios</th>             
            </tr>';

$consultaFolio = $consulta->personalFolio($idReporteProceso, $tmp);
$dataFolio = $conexion->prepare($consultaFolio);
$dataFolio->execute();

while ($personalFolio = $dataFolio->fetch()) {
    $html .= '<tr> '
        . '<td>' . $personalFolio["nombre"] . '</td>'
        . '</tr>';
}

$html .= '
     </table>
     <br>
     <div>Hora Final: ' . $reporteProceso->horaFinal . '</div>
         <br/><br/><br/><br/><br/><br/>
         <div style="font-weight: bold; font-size: 13pt;"><b>Horarios - Tiempos</b></div>
         <br>
    <table width="100%" style="font-family: serif;" cellpadding="5">
        <tr> 
        <td>Inicio de Homogeneizado: ' . $reporteProceso->inicioHomogeneizado . '</td>
        <td>Final de Homogeneizado : ' . $reporteProceso->finalHomogeneizado . '</td>
        <td>Hora Final de Reposo : ' . $reporteProceso->tiempoReposo . '</td>
        </tr> 
        
        <tr> 
        <td>Tiempo de Homogeneización : ' . $reporteProceso->tiempoHomogeneizacion . '</td>
        <td>Tiempo de Reposo : ' . $reporteProceso->tiempoReposo . ' </td>
        </tr>
    </table>
    <br>
    <div style="font-weight: bold; font-size: 13pt;"><b>Observaciones</b></div>
    <br/>
    <div>' . $reporteProceso->observaciones . '<div>
    <br><br><br>
    <div style="font-weight: bold; font-size: 13pt;"><b> Herramientas para Proceso </b></div>
    <br>
     <table width="100%" style="font-family: serif; text-align: center;" cellpadding="5">
        <tr>
            <th>Herramienta</th>
            <th>Cantidad</th>
            <th>Condiciones</th>
        </tr>
        <tr> 
            <td>Llave para abrir Tambores:</td>
            <td> ' . $reporteProceso->cantidadLlave . '</td>
            <td> ' . $reporteProceso->condicionLlave . '</td>
            <td></td>
        </tr>
        
        <tr> 
            <td>Pala:</td>
            <td> ' . $reporteProceso->cantidadPala . '</td>
            <td> ' . $reporteProceso->condicionPala . '</td>
            <td></td>
        </tr> 
        
        <tr> 
            <td>Otras:</td>
            <td> ' . $reporteProceso->cantidadOtras . '</td>
            <td> ' . $reporteProceso->condicionOtras . '</td>
            <td></td>
        </tr> 
     </table>
     <br>
     <div>Herramientas : ' . $reporteProceso->otrasHerramientas . '</div>
         <br>
     <div>¿Se Realizó el lavado de tuberías, fosa y tanque?   ' . $reporteProceso->lavadoHerramientas . '</div>
         <br>
         <br>
        <table width="100%">
            <tr> 
                <td width="33%">
                <span style="font-weight: bold; font-size: 12pt;">Operador de Proceso</span><br />
                    <br />
                   ________________________<br />
                   
                </td>
                <td width="33%">
                <span style="font-weight: bold; font-size: 12pt;">Auxiliar de Producción</span><br />
                    <br />
                   ________________________<br />
                   
                </td>
                <td width="33%">
                <span style="font-weight: bold; font-size: 12pt;">Jefe de Producción</span><br />
                    <br />
                   ________________________<br />
                   
                </td>
            </tr>
          </table>
            </body>
        </html>';

// $mpdf = new mPDF('c', 'A4', '', '', 20, 15, 47, 25, 10, 10);
// $mpdf->SetProtection(array('print'));
// $mpdf->SetTitle("AUP-Reporte de Proceso");
// $mpdf->SetAuthor("PCORIENTE");
// $mpdf->showWatermarkText = true;
// $mpdf->watermark_font = 'DejaVuSanCondensed';
// $mpdf->watermarkTextAlpha = 0.1;
// $mpdf->SetDisplayMode('fullpage');
// $mpdf->WriteHTML($html);
// $mpdf->Output('AUP-Conformacion del Lote.pdf', 'I');


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
$mpdf->SetTitle("AUP-Conformacion del Lote");
$mpdf->SetAuthor('PCOriente');
$mpdf->showWatermarkText = true;
$mpdf->watermark_font = 'DejaVuSansCondensed';
$mpdf->watermarkTextAlpha = 0.1;
$mpdf->SetDisplayMode('fullpage');

// No es necesario el mb_convert_encoding ya que mPDF 8.x maneja UTF-8 por defecto
$mpdf->WriteHTML($html);
$mpdf->Output('AUP-Conformacion del Loteo.pdf', 'I');
