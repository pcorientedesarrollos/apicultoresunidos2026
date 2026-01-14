<?php

include_once '../../clases/consultas.php';
include_once '../../DAOConeccion/conePDO.php';
$consulta = new produccion();
$pdo = new conePDO();
$conexion = $pdo->conectar();

$idReporteProceso = $_GET["idReporteProceso"];
$tmp = $_GET["tmp"];
$tituloProcesos = "CONTROL DE PROCESO";
$subTitulo ="CONTROL DE PROCESO";
if($tmp == 1){
    $sub = "MIEL 100% PURA DE ABEJA";    
}else{
    $sub = "MIEL 100% ORGÁNICA";    
}

$fechaEnvasado = date("d-m-Y");

header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=Reporte de Proceso_$fechaEnvasado.xls");
header("Prafma: no-cache");
header("Expires:0");

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
    if ($datosProceso["lavadoHerramientas"]== 1){
        $reporteProceso->lavadoHerramientas  = "Sí";
    } else {
        $reporteProceso->lavadoHerramientas ="No";
    }
    $reporteProceso->horaFinalReposo = $datosProceso["horaFinalReposo"];
    $reporteProceso->tiempoHomogeneizacion = $datosProceso["tiempoHomogeneizacion"];
    $reporteProceso->otrasHerramientas = $datosProceso["otrasHerramientas"];
    $reporteProceso->noProcesada = $datosProceso["noProcesada"];
    $reporteProceso->procesada = $datosProceso["procesada"];
    $reporteProceso->elProducto = $datosProceso["producto"];
    if($datosProceso["producto"] == 1){
        $reporteProceso->producto = "Miel 100% pura de abeja";
    }else{
        $reporteProceso->producto = "Miel 100% orgánica";
    }
    $reporteProceso->inicioHomogeneizado = $datosProceso["inicioHomogeneizado"];
    $reporteProceso->finalHomogeneizado = $datosProceso["finalHomogeneizado"];
    $reporteProceso->tiempoReposo = $datosProceso["tiempoReposo"];
    $reporteProceso->observaciones = $datosProceso["observaciones"];
    $reporteProceso->horaFinal = $datosProceso["horaFinal"];
    $reporteProceso->cantidadLlave = $datosProceso["cantidadLlave"];
    if($datosProceso["condicionLlave"] == 1){
         $reporteProceso->condicionLlave = "Buenas";
    } else {
         $reporteProceso->condicionLlave = "Malas";
    }
     
    $reporteProceso->cantidadPala = $datosProceso["cantidadPala"];
    if($datosProceso["condicionPala"] == 1){
         $reporteProceso->condicionPala = "Buenas";
    } else {
         $reporteProceso->condicionPala = "Malas";
    }
    $reporteProceso->cantidadOtras = $datosProceso["cantidadOtras"];
    if($datosProceso["condicionOtras"] == 1){
         $reporteProceso->condicionOtras = "Buenas";
    } else {
         $reporteProceso->condicionOtras = "Malas";
    }
     $reporteProceso->observacionesDes = $datosProceso["observacionesDePesos"];
}
echo '<div align = "right">CÓDIGO: RPR-PM-01 </div>';
echo '<div align = "right">REVISIÓN: 01 </div><br>';
echo '<table width="100%">';
echo '<tr>';
echo '<td width = "50%" style="color:#0000;"><span style="font-weight: bold; font-size: 18pt;">' . $tituloProcesos . '</span><br/><span style="font-weight: bold; font-size: 18pt;">' . utf8_decode($sub) . '</span></td>';
echo ' </tr>';
echo '</table><br>';
echo '<div style="font-weight: bold; font-size: 13pt;"><b>Datos del Proceso</b></div>'
. '<br>';
echo '<table width="100%" style="font-family: serif;" cellpadding="5">';
echo '<tr>';
echo '<td width="50%" >'
. ' Lote Interno: ' . $reporteProceso->idLoteInterno . '<br/>'
. ' Responsable: ' .$reporteProceso->responsable . '</td>';
echo '<td width="50%">'
. ' Fecha de Proceso: ' . $reporteProceso->fechaReporte . '<br/>'
        . 'Supervisor: ' . $reporteProceso->supervisor . '</td>';
echo '</tr>';
echo '</table><br>';
echo ' <table width="100%" style="font-family: serif;" cellpadding="5">';
echo '<tr>';
echo '<td >Hora de Inicio: ' . $reporteProceso->horaInicio . '</td>';
echo ' <td>N.Tanque: ' . $reporteProceso->numeroTanque . '</td>';
echo '<td>N.Tambores: ' . $reporteProceso->numeroTambores . '</td>';
echo '</tr>';
echo '</table><br>';
echo '<div style="font-weight: bold; font-size: 13pt;"><b>Higiene del Personal de la Zona Inocua</b></div><br>';
echo '<table  width="100%" style="font-family: serif;">';
echo '<tr>';
echo '<td  style="text-align:left">Cabello Corto : ' . utf8_decode($reporteProceso->cabello) . '</td>';
echo '<td  style="text-align:left">'. utf8_decode('Cofía').' : ' . utf8_decode($reporteProceso->cofia) . '</td>';
echo '<td  style="text-align:left">Lavado de Botas : ' . utf8_decode($reporteProceso->botas) . '</td>';
echo '</tr>';
echo '<tr>';
echo '<td  style="text-align:left">'. utf8_decode('Uñas Cortas').' : ' . utf8_decode($reporteProceso->una) . '</td>';
echo '<td  style="text-align:left">Cubrebocas : ' . utf8_decode($reporteProceso->cubreBocas) . '</td>';
echo '<td  style="text-align:left">'. utf8_decode('Sanitización de Manos').' : ' . utf8_decode($reporteProceso->sanitizacionManos) . '</td>';
echo '</tr>';
echo '<tr>';
echo '<td  style="text-align:left">Ropa Limpia : ' . utf8_decode($reporteProceso->ropa) . '</td>';
echo '<td  style="text-align:left">Lavado de Manos : ' . utf8_decode($reporteProceso->lavadoManos) . '</td>';
echo '<td  style="text-align:left">'. utf8_decode('Sanitización de Botas').' : ' . utf8_decode($reporteProceso->sanitizacionBotas) . '</td>';
echo '</tr>';
echo '</table><br>';

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
echo '<div style="font-weight: bold; font-size: 13pt;"><b>'. utf8_decode('Descripción').'</b></div><br>';
echo '<table width="100%" style="font-family: serif;" cellpadding="5">';
echo '<tr>';
echo '<td>Producto: ' . utf8_decode($reporteProceso->producto) . '</td>';
echo '<td>Cant: ' . number_format($datosDescri->kilosTotales,0,'.',',') . '</td>';
echo '<td>Bruto ' . number_format($datosDescri->brutoTotal,0,'.',',') . ' </td>';
echo '<td>Tara: ' . number_format($datosDescri->taraTotal ,0,'.',','). '</td> ';
echo '<td>Neto: ' . number_format($datosDescri->netoTotal,0,'.',',') . '</td>';
echo '</tr> ';
echo '<tr>';
echo '<td>Miel No Procesada : ' . number_format($reporteProceso->noProcesada,0,'.',',') . '</td>';
echo '<td>Miel Procesada:  ' . number_format( $reporteProceso->procesada,0,'.',',') . '</td>';
echo '</tr>';
echo '</table><br>';
echo '
     <div style="font-weight: bold; font-size: 13pt;"><b>'. utf8_decode('Observaciones de la Descripción').'</b></div>
     <br>
     <div>'.$reporteProceso->observacionesDes.'</div>
     <br/>';
echo '<div style="font-weight: bold; font-size: 13pt;"><b>Personal</b></div><br>';
echo '<table width="100%" style="font-family: serif; text-align: left;">';
echo '<tr>';
echo '<th align="left">'. utf8_decode('Conformación de Lote:').'</th>';
echo '</tr>';
        $consultaLote = $consulta->personalConforLote($idReporteProceso, $tmp);
        $dataLote = $conexion->prepare($consultaLote);
        $dataLote->execute();
        while ($conformacionLote = $dataLote->fetch()){
            echo '<tr>'
                    . '<td>'. $conformacionLote["nombre"].'</td>'
                 . '</tr>';
        }
echo '</table><br>';
echo '<table width="100%" style="font-family: serif; text-align: left;">';
echo '<tr>
              <th align="left">Proceso de Zona Inocua</th>             
            </tr>';
    $consultaZonaIno = $consulta->personalzonainocua($idReporteProceso, $tmp);
        $dataZonainocua = $conexion->prepare($consultaZonaIno);
        $dataZonainocua->execute();

         while($zonaInocua= $dataZonainocua->fetch()){ 
           echo '<tr>'
                . '<td>'. $zonaInocua["nombre"].'</td>'
             . '</tr>';  
         }
echo '</table>  <br>';
echo '<table width="100%" style="font-family: serif; text-align: left">';
echo '<tr>
              <th align="left">Busqueda de Folios</th>             
            </tr>';

        $consultaFolio = $consulta->personalFolio($idReporteProceso, $tmp);
        $dataFolio = $conexion->prepare($consultaFolio);
        $dataFolio->execute();

         while($personalFolio= $dataFolio->fetch()){
            echo '<tr>'
                . '<td>'. $personalFolio["nombre"].'</td>'
             . '</tr>'; 
         }

echo '</table><br>';
echo ' <div>Hora Final: ' . $reporteProceso->horaFinal . '</div><br>';
echo '<div style="font-weight: bold; font-size: 13pt;"><b>Horarios - Tiempos</b></div><br>';
echo '<table width="100%" style="font-family: serif;" cellpadding="5">';
echo '<tr> 
        <td>Inicio de Homogeneizado: ' . $reporteProceso->inicioHomogeneizado . '</td>
        <td>Final de Homogeneizado : ' . $reporteProceso->finalHomogeneizado . '</td>
        <td>Hora Final de Reposo : ' . $reporteProceso->tiempoReposo . '</td>
        </tr>';
echo '<tr> 
        <td>'. utf8_decode('Tiempo de Homogeneización ').': ' . $reporteProceso->tiempoHomogeneizacion . '</td>
        <td>Tiempo de Reposo : ' . $reporteProceso->tiempoReposo . ' </td>
        </tr>';
echo '</table><br>';
echo '<div style="font-weight: bold; font-size: 13pt;"><b>Observaciones</b></div><br>';
echo '<div>' . $reporteProceso->observaciones . '<div><br>';
echo '<table width="100%" style="font-family: serif; text-align: center;" cellpadding="5">
        <tr>
            <th>Herramienta</th>
            <th>Cantidad</th>
            <th>Condiciones</th>
        </tr>';
echo ' <tr> 
            <td>Llave para abrir Tambores:</td>
            <td> ' . $reporteProceso->cantidadLlave . '</td>
            <td> ' . $reporteProceso->condicionLlave . '</td>
            <td></td>
        </tr>';
echo '<tr> 
            <td>Pala:</td>
            <td> ' . $reporteProceso->cantidadPala . '</td>
            <td> ' . $reporteProceso->condicionPala. '</td>
            <td></td>
        </tr> ';
echo '<tr> 
            <td>Otras:</td>
            <td> ' . $reporteProceso->cantidadOtras . '</td>
            <td> ' . $reporteProceso->condicionOtras. '</td>
            <td></td>
        </tr> ';
echo '</table><br>';
echo '<div>Herramientas : ' . $reporteProceso->otrasHerramientas.'</div><br>';
echo '<div>'. utf8_decode('¿Se Realizó el lavado de tuberías,fosa y tanque?').' ' . utf8_decode($reporteProceso->lavadoHerramientas) . '</div><br>';
echo '<table width="100%">';
echo '<tr> 
                <td width="33%">
                <span style="font-weight: bold; font-size: 12pt;">Operador de Proceso</span><br />
                    <br />
                   ________________________<br />
                   
                </td>';
echo '<td width="33%">
                <span style="font-weight: bold; font-size: 12pt;">'. utf8_decode('Auxiliar de Producción').'</span><br />
                    <br />
                   ________________________<br />
                   
                </td>';
echo '<td width="33%">
                <span style="font-weight: bold; font-size: 12pt;">'. utf8_decode('Jefe de Producción').'</span><br />
                    <br />
                   ________________________<br />
                   
                </td>
            </tr>';
echo ' </table>';