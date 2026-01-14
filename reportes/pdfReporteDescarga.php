<?php

require_once '../vendor/autoload.php'; //SE cambia la ruta de la librería
include_once '../clases/consultas.php';
require_once '../DAOConeccion/conePDO.php';
use Mpdf\Mpdf; //importamos la clase


$consultasReporte = new consultas();
$pdo = new conePDO();
$conexion = $pdo->conectar();

$idReporte = $_GET['idReporte'];
$tipoDeMiel = $_GET['tipoDeMiel'];
$tipo = $_GET['tipo'];
//$idReporte = 4//;

$consultaReporteDesCar = $consultasReporte->reportesdDescargayCarga($tipoDeMiel, $idReporte, $tipo);
$dataReporteDesCar = $conexion->prepare($consultaReporteDesCar);
$dataReporteDesCar->execute();
while ($contDescarga = $dataReporteDesCar->fetch()) {
    $detalleDescarga = new stdClass();
    $detalleDescarga->fchImpresion = $contDescarga['fechaImpresion'];
    $detalleDescarga->hInicio = $contDescarga['horaInicio'];
    $detalleDescarga->operador = $contDescarga['operador'];
    $detalleDescarga->licencia = $contDescarga['licencia'];
    $detalleDescarga->vigencia = $contDescarga['vigencia'];
    $detalleDescarga->compania = $contDescarga['compania'];
    $detalleDescarga->contenedor = $contDescarga['contenedor'];
    $detalleDescarga->sello = $contDescarga['sello'];
    $detalleDescarga->idLoteInterno = $contDescarga["idLoteInterno"];
    $detalleDescarga->lote = $contDescarga['lote'];
    $detalleDescarga->tipo = $contDescarga["tipo"];
    $detalleDescarga->placa = $contDescarga["placa"];
    $detalleDescarga->marca = $contDescarga["marca"];
    $detalleDescarga->modelo = $contDescarga["modelo"];
    $detalleDescarga->remolque = $contDescarga['remolque'];
    $detalleDescarga->marcaRemolque = $contDescarga["marcaRemolque"];
    $detalleDescarga->modeloRemolque = $contDescarga["modeloRemolque"];
    $detalleDescarga->placaRemolque = $contDescarga["placaRemolque"];
    $detalleDescarga->cabello = $contDescarga["cabello"];
    $detalleDescarga->unas = $contDescarga["unas"];
    $detalleDescarga->ropa = $contDescarga["ropa"];

    $detalleDescarga->clasificacion = $contDescarga["clasificacion"];
    $detalleDescarga->producto = $contDescarga['producto'];
    if ($detalleDescarga->producto == '1' || $detalleDescarga->producto == '0') {
        $detalleDescarga->producto = 'Convencional';
    } else if($detalleDescarga->producto == '2'){
        $detalleDescarga->producto = 'Orgánico';
    } else if($detalleDescarga->producto == '6'){
        $detalleDescarga->producto = 'Altiplano';
    } else if($detalleDescarga->producto == '7'){
        $detalleDescarga->producto = 'Naranjo';
    }  else if($detalleDescarga->producto == '8'){
        $detalleDescarga->producto = 'Aguacate';
    } else if($detalleDescarga->producto == '9'){
        $detalleDescarga->producto = 'Mezquite';
    }  else{
        $detalleDescarga->producto = 'Mantequilla';
    }
    $detalleDescarga->clasificacionMiel = $contDescarga['clasificacionMiel'];

    $detalleDescarga->cantidad = $contDescarga["cantidad"];
    $detalleDescarga->pesoBruto = $contDescarga["pesoBruto"];
    $detalleDescarga->pesoTara = $contDescarga["pesoTara"];
    $detalleDescarga->pesoNeto = $contDescarga["pesoNeto"];
    $detalleDescarga->miel = $contDescarga["miel"];
    $detalleDescarga->tambor = $contDescarga["tambor"];
    $detalleDescarga->cubeta = $contDescarga["cubeta"];
    $detalleDescarga->cera = $contDescarga['cera'];
    $detalleDescarga->apicolas = $contDescarga["apicolas"];
    $detalleDescarga->mp = $contDescarga["mp"];
    $detalleDescarga->traspaso = $contDescarga["traspaso"];
    $detalleDescarga->envasesFrascos = $contDescarga["envasesFrascos"];
    $detalleDescarga->productosDerivados = $contDescarga["productosDerivados"];
    $detalleDescarga->entradaCubeta = $contDescarga["entradaCubeta"];
    $detalleDescarga->entradaCera = $contDescarga["entradaCera"];
    $detalleDescarga->entradaApicola = $contDescarga["entradaApicola"];
    $detalleDescarga->entradaMP = $contDescarga["entradaMP"];
    $detalleDescarga->entradaTraspaso = $contDescarga["entradaTraspaso"];
    $detalleDescarga->entradaEnvasesFrascos = $contDescarga["entradaEnvasesFrascos"];
    $detalleDescarga->entradaProductosDerivados = $contDescarga["entradaProductosDerivados"];

    if ($contDescarga['roto'] == 1) {
        $detalleDescarga->roto = "Sí";
    } else {
        $detalleDescarga->roto = "No";
    }

    if ($contDescarga['abolladuras'] == 1) {
        $detalleDescarga->abollado = "Sí";
    } else {
        $detalleDescarga->abollado = "No";
    }

    if ($contDescarga['recipienteAdecuado'] == 1) {
        $detalleDescarga->recipiente = "Sí";
    } else {
        $detalleDescarga->recipiente = "No";
    }

    if ($contDescarga['lavadoExterior'] == 1) {
        $detalleDescarga->lavadoEx = "Sí";
    } else {
        $detalleDescarga->lavadoEx = "No";
    }

    $detalleDescarga->tipo = $contDescarga['tipo'];

    if ($contDescarga['limpieza'] == 1) {
        $detalleDescarga->limpieza = "Sí";
    } else {
        $detalleDescarga->limpieza = "No";
    }

    if ($contDescarga['materialExtrano'] == 1) {
        $detalleDescarga->materialEx = "Sí";
    } else {
        $detalleDescarga->materialEx = "No";
    }

    if ($contDescarga['vehiculoAdecuado'] == 1) {
        $detalleDescarga->vehiculoAd = "Sí";
    } else {
        $detalleDescarga->vehiculoAd = "No";
    }
    $detalleDescarga->observa = $contDescarga['observaciones'];
    $detalleDescarga->horaFinal = $contDescarga['horaFinal'];
    $detalleDescarga->estado = $contDescarga['estado'];
}

if ($detalleDescarga->estado == 0) {
    $titulo = 'REPORTE DE DESCARGA';
    $subtitulo = 'DATOS GENERALES DE LA ENTRADA';
    $personal = 'PERSONAL DE DESCARGA';
    $tituloExpo = 'AUP-Reporte de Descarga';
    $codigo = 'CÓDIGO: RAL-RD-01  ';
    $revision = 'REVISIÓN: 01';
} else {
    $titulo = 'REPORTE DE CARGA';
    $subtitulo = 'DATOS GENERALES DE LA SALIDA';
    $personal = 'PERSONAL DE CARGA';
    $tituloExpo = 'AUP-Reporte de Carga';
    $codigo = 'CÓDIGO: RAL-RC-01 ';
    $revision = 'REVISIÓN: 01';
}

$consultaResponsable = $consultasReporte->nombreEncargadoReporteDesCar( $tipoDeMiel ,$idReporte);
$dataResponsable = $conexion->prepare($consultaResponsable);
$dataResponsable->execute();
while ($nombreResponsable = $dataResponsable->fetch()) {
    $ResponsanbleEm = new stdClass();
    $ResponsanbleEm->responsable = $nombreResponsable['nombre'];
}

$consultaSupervisor = $consultasReporte->nombreSupervisorReporteDesCar($tipoDeMiel ,$idReporte);
$dataSupervisor = $conexion->prepare($consultaSupervisor);
$dataSupervisor->execute();
while ($nombreSupervisor = $dataSupervisor->fetch()) {
    $supervisorEm = new stdClass();
    $supervisorEm->supervisor = $nombreSupervisor['nombre'];
}

$consultaLimpieza = $consultasReporte->nombrePersonalLimpieza($tipoDeMiel, $idReporte);
$dataLimpieza = $conexion->prepare($consultaLimpieza);
$dataLimpieza->execute();
while ($nombreLimpieza = $dataLimpieza->fetch()) {
    $limpiezaEm = new stdClass();
    $limpiezaEm->Plimpieza = $nombreLimpieza['nombre'];
}

$consultaMarcacion = $consultasReporte->nombrePersonalMarcacion($tipoDeMiel, $idReporte);
$dataMarcacion = $conexion->prepare($consultaMarcacion);
$dataMarcacion->execute();
while ($nombreMarcacion = $dataMarcacion->fetch()) {
    $MarcacionEm = new stdClass();
    $MarcacionEm->PMarcacion = $nombreMarcacion['nombre'];
}

$consultaRotulacon = $consultasReporte->nombrePersonalRotulacion($tipoDeMiel, $idReporte);
$dataRotulacion = $conexion->prepare($consultaRotulacon);
$dataRotulacion->execute();
while ($nombreRotulacion = $dataRotulacion->fetch()) {
    $RotulacionEm = new stdClass();
    $RotulacionEm->PRotulacion = $nombreRotulacion['nombre'];
}

$consultaMontacarga = $consultasReporte->nombreMontacargas($tipoDeMiel, $idReporte);
$dataMontacargas = $conexion->prepare($consultaRotulacon);
$dataMontacargas->execute();
while ($nombreMontacargas = $dataMontacargas->fetch()) {
    $montacargasEm = new stdClass();
    $montacargasEm->Pmontacargas = $nombreMontacargas['nombre'];
}
$html = '<html>
    <body>
        
    <!--mpdf    
    <htmlpageheader name="myheader">
        <table width="100%">
            <tr>
                <td width="25%" style="text-align: left;">
                    <p><img src="../reportes/img/oaxaca.png"></p>
                </td>
                <th width="35%" style="#0000; text-align: center">
                    <span style="font-weight: bold; font-size: 18pt;">' . $titulo . '</span><br>
                </th>
                 <th width="25%" style="#0000; text-align: center">    
                </th>
            </tr>
            <tr>
                <td width="25%" style="text-align: left;"></td>
                <th width="35%" style="#0000; text-align: center"></th>
                 <th width="25%" style="#0000; text-align: right">   
                 <span style="font-weight: bold; font-size: 10pt;">' . $codigo . '</span><br>
                </th>
            </tr>
            <tr>
                <td width="25%" style="text-align: left;"></td>
                <th width="35%" style="#0000; text-align: center"></th>
                <th width="25%" style="#0000; text-align: right">   
                <span style="font-weight: bold; font-size: 10pt;">' . $revision . '</span><br>
                </th>
            </tr> 
        </table>
    </htmlpageheader>
    
    <htmlpagefooter name="myfooter">
        <div style="border-top: 1px solid #000000; font-size: 9pt; text-align: center; padding-top: 3mm;">
            Página {PAGENO} de {nb}
        </div>
    </htmlpagefooter>   
<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
<sethtmlpagefooter name="myfooter" value="on" />

mpdf-->
<br><br>
<div style="font-weight: bold; font-size: 13pt; text-aling:center"> <b>' . $subtitulo . '</b></div>
<br>
    <table width="100%" style="font-family : serif;">
        <tr>      
            <td width="50%" style="text-align : left">
                        Fecha: ' . $detalleDescarga->fchImpresion . '<br />
            </td>
            <td width="50%" style="text-align:left">
            Responsable : ' . $ResponsanbleEm->responsable . '
                </td>
        </tr>
    </table>
    <div><b>Personal operativo</b></div>
    <table  width="100%" style="font-family: serif;">
        <tr>
        <td width="50%" style="text-align:left">
            Limpieza: ' . $limpiezaEm->Plimpieza . '
        </td>
        <td width="50%" style="text-align:left">
            Marcación: ' . $MarcacionEm->PMarcacion . '
        </td>
        </tr>';
if ($detalleDescarga->estado == 1) {
    $html .= '<tr>
<td width="50%" style="text-align:left">
    Rotulación: ' . $RotulacionEm->PRotulacion . '
</td>
<td width="50%" style="text-align:left">
    Montacargas: ' . $montacargasEm->Pmontacargas . '
</td>
</tr>';
}

$html .= '</table>
    <div>Carga / Descarga :</div>
    <table  width="100%" style="font-family: serif;">';
$consultaPersonalDesCar = $consultasReporte->personalDesCar( $tipoDeMiel, $idReporte);
$dataPerosnal = $conexion->prepare($consultaPersonalDesCar);
$dataPerosnal->execute();

while ($infoNombreDesCar = $dataPerosnal->fetch()) {
    $html .= '<tr>
                  <td>' . $infoNombreDesCar["nombre"] . '</td>
                </tr>';
}

$html .= ' </table>

    <br>
    <div style="font-weight: bold; font-size: 13pt; text-align:center"> <b>DATOS DEL OPERADOR Y TRANSPORTE</b></div>
    <br>
    <div> <b>Datos personales</b></div>
    ';

$html .= '<table  width="100%" style="font-family: serif;">
        <tr>
        <td width="50%" style="text-align:left">
        Operador : ' . $detalleDescarga->operador . '<br />
        Licencia : ' . $detalleDescarga->licencia . '<br />
        </td>
        <td width="50%" style="text-align:left">
        Compañía : ' . $detalleDescarga->compania . '<br />
        Vigencia : ' . $detalleDescarga->vigencia . '<br/>  
        </td>
        </tr>
    </table>    
        <div> <b>Higiene personal</b></div>
        <table  width="100%" style="font-family: serif;">
            <tr>
                <td  style="text-align:left"> Cabello corto : ' . $detalleDescarga->cabello . '</td>
                <td  style="text-align:left"> Uñas cortas : ' . $detalleDescarga->unas . ' </td>
                <td  style="text-align:left"> Ropa limpia : ' . $detalleDescarga->ropa . '</td>
            </tr>
        </table> 
        <div> <b>Datos del vehículo</b></div>
        <table  width="100%" style="font-family: serif;">
            <tr>
                <td width="25%">
                    Tipo: ' . $detalleDescarga->tipo . '<br />
                </td>
                <td width="25%">
                    Marca: ' . $detalleDescarga->marca . '<br />
                </td>
                <td width="25%">
                    Modelo: ' . $detalleDescarga->modelo . '<br/>  
                </td>
                <td width="25%">
                    Placas: ' . $detalleDescarga->placa . '<br />
                </td>
            </tr>
        </table>
        <table  width="100%" style="font-family: serif;">
            <tr>
                <td width="25%">
                    Remolque: ' . $detalleDescarga->remolque . '<br />
                </td>   
                <td width="25%">
                    Marca: ' . $detalleDescarga->marcaRemolque . '<br />
                </td>
                <td width="25%">
                    Modelo: ' . $detalleDescarga->modeloRemolque . '<br/>  
                </td>
                <td width="25%">
                    Placas: ' . $detalleDescarga->placaRemolque . '<br />
                </td>
            </tr>
        </table>  
    <div> <b>Condiciones de la Unidad</b></div>
        <table  width="100%" style="font-family: serif;">
            <tr>
                <td style="text-align:left">Limpieza : ' . $detalleDescarga->limpieza . '</td>
                <td style="text-align:left">Mats. Extraño : ' . $detalleDescarga->materialEx . '</td>
                <td style="text-align:left">Adecuado : ' . $detalleDescarga->vehiculoAd . '</td>
            </tr>
        </table>  
        <br>
        <div style="font-weight: bold; font-size: 13pt; text-align:center "> <b>DATOS DEL PRODUCTO</b></div>
<br>
<div style="text-align:left">Tipo: ' . $detalleDescarga->producto . '  ' . $detalleDescarga->clasificacionMiel . '</div>
';


if ($detalleDescarga->estado == 1) {

    $html .= '<div>

                                                                <table>
                                               <tr>
                                                    <th>Producto</th>
                                                    <th>Salida</th>
                                                </tr>';
    if ($detalleDescarga->miel == 1) {
        $html .= '<tr>
                                                    <td>Miel (T) </td>
                                                    <td style="text-align:right">' . $detalleDescarga->idLoteInterno . '</td>
                                                </tr>';
    }
    if ($detalleDescarga->cera == 1) {
        $html .= '<tr>
                                                    <td>Cera </td>
                                                    <td style="text-align:right">' . $detalleDescarga->entradaCera . '</td>
                                                </tr>';
    }
    if ($detalleDescarga->apicolas == 1) {
        $html .= '<tr>
                                                    <td>Productos apícolas </td>
                                                    <td style="text-align:right">' . $detalleDescarga->entradaApicola . '</td>
                                                </tr>';
    }
    if ($detalleDescarga->mp == 1) {
        $html .= '<tr>
                                                    <td>Empaques (T, C y B) </td>
                                                    <td style="text-align:right">' . $detalleDescarga->entradaMP . '</td>
                                                </tr>';
    }
    if ($detalleDescarga->traspaso == 1) {
        $html .= '<tr>
                                                    <td>Traspaso </td>
                                                    <td style="text-align:right">' . $detalleDescarga->entradaTraspaso . '</td>
                                                </tr>';
    }
    if ($detalleDescarga->envasesFrascos == 1) {
        $html .= '<tr>
                                                    <td>Envases y frascos </td>
                                                    <td style="text-align:right">' . $detalleDescarga->entradaEnvasesFrascos . '</td>
                                                </tr>';
    }
    if ($detalleDescarga->productosDerivados == 1) {
        $html .= '<tr>
                                                    <td>Productos derivados </td>
                                                    <td style="text-align:right">' . $detalleDescarga->entradaProductosDerivados . '</td>
                                                </tr>';
    }
    $html .= ' 
    </table>
    <br>
    <div style="font-weight: bold; font-size: 9pt;"> <b>*Aplica únicamente en caso de miel</b></div>
        <table width="100%"  style="font-family: serif;"> 
           <tr>
                <td width="15%">Tambos:  ' . $detalleDescarga->cantidad . '</td>
                <td width="30%">Clasificación:  ' . $detalleDescarga->clasificacion . '</td>
                <td width="20%">Lote Interno: ' . $detalleDescarga->idLoteInterno . '</td>
                <td width="35%">Marcación Final: ' . $detalleDescarga->lote . '</td>
           </tr>
        </table>
        <table width="100%" style="font-family: serif;"> 
            <tr>
                <td width="50%">Contenedor: ' . $detalleDescarga->contenedor . '</td>
                <td width="50%">Sello:  ' . $detalleDescarga->sello . '</td>
            </tr>
        </table>
    <div> <b>Condiciones Fisicas Externas del Tambor</b></div>
    <table  width="100%" style="font-family: serif;">
        <tr>
        <td  style="text-align:left"> Roto : ' . $detalleDescarga->roto . '</td>
        <td  style="text-align:left">Abollado : ' . $detalleDescarga->abollado . '</td>
        <td  style="text-align:left">Adecuado : ' . $detalleDescarga->recipiente . '</td>
        <td  style="text-align:left">Lavado Ext : ' . $detalleDescarga->lavadoEx . '</td>
        </tr>
    </table>
           <br>
     <!-- <br><table style="page-break-after:always;"></br></table><br> -->
    <div> <b>Observaciones</b></div>
   <!-- <hr> -->
    <table  width="100%" style="font-family: serif;">
        <tr>
        <td width="50%" style="text-align:left">
                    ' . $detalleDescarga->observa . '
       
        </td>
        </tr>
    </table>
    <br>
    <br>

    <!-- <div style="font-weight: bold; font-size: 13pt;"> <b>Firmas</b></div> -->
        <!-- <hr> -->
    <table width="100%" style="font-family: serif; text-align:center" cellpadding="10">
            <tr>
                <td width="25%" >
                    <br />
                   ________________________<br />
                   <span style="font-weight: bold; font-size: 10pt;">Gerente de Planta</span>
                </td>

                <td width="25%">
                   <br />
                   ________________________<br />
                <span style="font-weight: bold; font-size: 10pt;">Jefe de Producción</span>
                </td> 

                <td width="25%">
                <br>
                    <br />
                   ________________________<br />
                 <span style="font-weight: bold; font-size: 10pt;">Jefe de Aseguramiento de Calidad</span>
                </td>
            </tr>
</table>
   
</body>
</html> ';
} else {

    $html .= '<div>

                                                                <table>
                                               <tr>
                                                    <th>Producto</th>
                                                    <th>Entrada/Salida</th>
                                                </tr>';
    if ($detalleDescarga->tambor == 1) {
        $html .= '<tr>
                                                    <td>Miel (T) </td>
                                                    <td style="text-align:center">' . $detalleDescarga->lote . '</td>
                                                </tr>';
    }
    if ($detalleDescarga->cubeta == 1) {
        $html .= '<tr>
                                                    <td>Miel (C) </td>
                                                    <td style="text-align:center">' . $detalleDescarga->entradaCubeta . '</td>
                                                </tr>';
    }
    if ($detalleDescarga->cera == 1) {
        $html .= '<tr>
                                                    <td>Cera </td>
                                                    <td style="text-align:center">' . $detalleDescarga->entradaCera . '</td>
                                                </tr>';
    }
    if ($detalleDescarga->apicolas == 1) {
        $html .= '<tr>
                                                    <td>Productos apícolas </td>
                                                    <td style="text-align:center">' . $detalleDescarga->entradaApicola . '</td>
                                                </tr>';
    }
    if ($detalleDescarga->mp == 1) {
        $html .= '<tr>
                                                    <td>Empaques (T, C y B) </td>
                                                    <td style="text-align:center">' . $detalleDescarga->entradaMP . '</td>
                                                </tr>';
    }
    if ($detalleDescarga->traspaso == 1) {
        $html .= '<tr>
                                                    <td>Traspaso </td>
                                                    <td style="text-align:center">' . $detalleDescarga->entradaTraspaso . '</td>
                                                </tr>';
    }
    if ($detalleDescarga->envasesFrascos == 1) {
        $html .= '<tr>
                                                    <td>Envases y frascos </td>
                                                    <td style="text-align:center">' . $detalleDescarga->entradaEnvasesFrascos . '</td>
                                                </tr>';
    }
    if ($detalleDescarga->productosDerivados == 1) {
        $html .= '<tr>
                                                    <td>Productos derivados </td>
                                                    <td style="text-align:center">' . $detalleDescarga->entradaProductosDerivados . '</td>
                                                </tr>';
    }
    $html .= '</table>
                               
                            </div>
                            <div style="font-weight: bold; font-size: 9pt;"> <b>*Aplica únicamente en caso de miel</b></div>
    <div> <b>Condiciones Fisicas Externas del Tambor</b></div>
    <table  width="100%" style="font-family: serif;">
        <tr>
        <td  style="text-align:left"> Roto: ' . $detalleDescarga->roto . '</td>
        <td  style="text-align:left">Abollado: ' . $detalleDescarga->abollado . '</td>
        <td  style="text-align:left">Adecuado: ' . $detalleDescarga->recipiente . '</td>
        <td  style="text-align:left">Lavado Ext: ' . $detalleDescarga->lavadoEx . '</td>
        </tr>
    </table>
    <!--  <br>
       <div> <b>Condiciones de la Unidad</b></div>
     <hr>
    <br>
    <table  width="100%" style="font-family: serif;">
        <tr>
        <td style="text-align:left"> Tipo: ' . $detalleDescarga->tipo . '</td>
        <td style="text-align:left">Limpieza: ' . $detalleDescarga->limpieza . '</td>
        <td style="text-align:left">Mats. Extraños: ' . $detalleDescarga->materialEx . '</td>
        <td style="text-align:left">Adecuado: ' . $detalleDescarga->vehiculoAd . '</td>
        </tr>
    </table>
    <br>
    <br> 
       <div> <b>Personal Operativo</b></div>
    <hr>
    <br>
    <table  width="100%" style="font-family: serif;">
        <tr>
        <td width="50%" style="text-align:left">
        Limpieza: ' . $limpiezaEm->Plimpieza . '<br />
        Marcación : ' . $MarcacionEm->PMarcacion . '
        </td>
        </tr>
    </table> <br>
       <div> <b>' . $personal . '</b></div>
     <hr> -->
    <br>
 
     <!-- <br><table style="page-break-after:always;"></br></table><br> -->
    <div> <b>Observaciones</b></div>
   <!-- <hr> -->
    <table  width="100%" style="font-family: serif;">
        <tr>
        <td width="50%" style="text-align:left">
                    ' . $detalleDescarga->observa . '
       
        </td>
                </tr>
    </table>
    <br>
    <br>

    <!-- <div style="font-weight: bold; font-size: 13pt;"> <b>Firmas</b></div> -->
        <!-- <hr> -->
        <table width="100%" style="font-family: serif; text-align:center" cellpadding="10">
        <tr>
            <td width="50%" >
                <br />
               ________________________<br />
               <span style="font-weight: bold; font-size: 10pt; text-align:center">Jefe de almacén</span><br />               
            </td>
            <td width="50%">
                            <br />
               ________________________<br />
               <span style="font-weight: bold; font-size: 10pt; text-align:center">Operador de vehículo</span><br />
            
               <br>
            </td>

        </tr>
</table>
   
</body>
</html> ';
}

// $mpdf = new mPDF('c', 'A4', '', '', 20, 15, 40, 25, 10, 10);
// $mpdf->SetProtection(array('print'));
// $mpdf->SetTitle($tituloExpo);
// $mpdf->SetAuthor("PCOriente");
// $mpdf->showWatermarkText = true;
// $mpdf->watermark_font = 'DejaVuSansCondensed';
// $mpdf->watermarkTextAlpha = 0.1;
// $mpdf->SetDisplayMode('fullpage');
// $mpdf->WriteHTML($html);
// $mpdf->Output('AUP-Reporte de Descarga.pdf', 'I');


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
$mpdf->SetTitle("AUP-Reporte de Descarga");
$mpdf->SetAuthor('PCOriente');
$mpdf->showWatermarkText = true;
$mpdf->watermark_font = 'DejaVuSansCondensed';
$mpdf->watermarkTextAlpha = 0.1;
$mpdf->SetDisplayMode('fullpage');

// No es necesario el mb_convert_encoding ya que mPDF 8.x maneja UTF-8 por defecto
$mpdf->WriteHTML($html);
$mpdf->Output('AUP-Reporte de Descarga.pdf', 'I');