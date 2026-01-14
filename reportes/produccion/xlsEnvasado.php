<?php

include_once '../../clases/consultas.php';
include_once '../../DAOConeccion/conePDO.php';
$consulta = new produccion();
$pdo = new conePDO();
$conexion = $pdo->conectar();

$idReporteEnvasado = $_GET["idReporteEnvasado"];
$tipoDeMiel = $_GET['tipoDeMiel'];
$fechaEnvasado = date("d-m-Y");

header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=Reporte de Envasado de Lotes_$fechaEnvasado.xls");
header("Prafma: no-cache");
header("Expires:0");

$tituloEnvasado = "CONTROL DE ENVASADO";
$subTituloEnvasado = "REPORTE DE ENVASADO DE LOTES";

$consultaDetalleEn = $consulta->reporteEnvasado($idReporteEnvasado, $tipoDeMiel);
$dataEnvasado = $conexion->prepare($consultaDetalleEn);
$dataEnvasado->execute();

while ($indfoDeatlle = $dataEnvasado->fetch()) {
    $detalleEnvasado = new stdClass();
    $detalleEnvasado->idReporteEnvasado = $indfoDeatlle["idReporteEnvasado"];
    $detalleEnvasado->fechaEnvasado = $indfoDeatlle["fechaEnvasado"];
    $detalleEnvasado->idLoteInterno = $indfoDeatlle["idLoteInterno"];
    $detalleEnvasado->numeroTanque = $indfoDeatlle["numeroTanque"];
    $detalleEnvasado->horaInicio = $indfoDeatlle["horaInicio"];
    $detalleEnvasado->horaFinal = $indfoDeatlle["horaFinal"];
    $detalleEnvasado->tiempo = $indfoDeatlle["tiempo"];
    $detalleEnvasado->observaciones = utf8_encode($indfoDeatlle["observaciones"]);
    $detalleEnvasado->kilosProcesados = $indfoDeatlle["kilosProcesados"];
    $detalleEnvasado->netosEnvasados = $indfoDeatlle["netosEnvasados"];
    $detalleEnvasado->merma = $indfoDeatlle["merma"];
    $detalleEnvasado->faltante = $indfoDeatlle["faltante"];
    $detalleEnvasado->observacionesPeso = $indfoDeatlle["observacionesDePesos"];
}
echo '<div align = "right">CODIGO: RPR-EL-01 </div>';
echo '<div align = "right">REVISION: 01</div><br>';
echo '<table width="100%">';
echo '<tr>';
echo '<td width = "50%" style="color:#0000;"> <span style="font-weight: bold; font-size: 18pt;">' . utf8_decode($tituloEnvasado) . '</span><br>'
    . '<span style="font-weight: bold; font-size: 12pt;">' . utf8_decode($subTituloEnvasado) . '</span></td>';
echo '</tr>';
echo '</table> '
    . '<br>';

echo '<div style ="font-weight: bold; font-size: 13pt;"><b>Datos de Proceso</b></div>'
    . '<br>';
echo '<table width="100%" style="font-family: serif;" cellpadding="5">';
echo '<tr>';
echo '<td> Lote Interno: ' . $detalleEnvasado->idLoteInterno . '</td>';
echo '<td>Fecha Envasado: ' . $detalleEnvasado->fechaEnvasado . '</td>';
echo '<td>Tanque Utilizado: ' . $detalleEnvasado->numeroTanque . '<td>';
echo '</tr>';

echo '<tr>';
echo '<td>Hora Inicial: ' . $detalleEnvasado->horaInicio . '</td>';
echo '<td>Hora Final: ' . $detalleEnvasado->horaFinal . '</td>';
echo '<td>Tiempo: ' . $detalleEnvasado->tiempo . '</td>';
echo '</tr>';
echo '</table> <br>';

echo '<div style="font-weight: bold; font-size: 13pt;"><b>Personal Operativo</b></div>';
echo '<table width="100%" style="font-family: serif; text-align: left">';
echo '<tr>';
echo '<th align="left">Envasado</th>';
echo '</tr>';
$consultaPersonalEnvasado = $consulta->personalEnvasado($idReporteEnvasado, $tipoDeMiel);
$infoPersonalEn = $conexion->prepare($consultaPersonalEnvasado);
$infoPersonalEn->execute();

while ($dataPersonalEn = $infoPersonalEn->fetch()) {
    $nombreEnvasador = $dataPersonalEn["nombre"];
}
echo '<tr>';
echo '<td>' . $nombreEnvasador . '</td>';
echo '</tr>';
echo '</table> <br>';

echo '<table width="100%" style="font-family: serif; text-align: left">';
echo '<tr>';
echo '<th align="left">Ayudante Interno</th>';
echo '</tr>';

$consultaAyudaInt = $consulta->ayudanteInterno($idReporteEnvasado, $tipoDeMiel);
$infoAyudInt = $conexion->prepare($consultaAyudaInt);
$infoAyudInt->execute();

while ($dataPersonalIn = $infoAyudInt->fetch()) {
    $nombreAyudatesInt = $dataPersonalIn["nombre"];
}

echo '<tr>';
echo '<td>' . $nombreAyudatesInt . '</td>';
echo '</tr>';
echo '<table> <br>';

echo '<table width="100%" style="font-family: serif; text-align: left">';
echo '<tr>';
echo '<th align="left">Ayudante Externo</th>';
echo '</tr>';

$consultaAyudaExt = $consulta->ayudanteExterno($idReporteEnvasado, $tipoDeMiel);
$infoAyudExt = $conexion->prepare($consultaAyudaExt);
$infoAyudExt->execute();

while ($dataPersonalExt = $infoAyudExt->fetch()) {
    $nombreAyudatesExt = $dataPersonalExt["nombre"];
}

echo '<tr>';
echo '<td>' . $nombreAyudatesExt . '</td>';
echo '</tr>';
echo '</table> <br>';

echo '<div style ="font-weight: bold; font-size: 13pt;"><b>Tambores</b></div>';
echo '<br>';
echo '<div style ="font-color:red; font-size: 10pt;"><b><font color = "red">ESPECIFICACIONES: Envasar el tambo dejando unos 80cm de espacio en la tapa , poner la tara correcta del tambo.</font></b></div>';
echo '<br>';
echo '<table width="100%" style="font-family: serif; text-align: center" cellpadding="5" border="1">';
echo '<tr>';
echo '<th>No.</th> ';
echo '<th>P.Bruto</th> ';
echo '<th>Tara</th>';
echo '<th>P.Neto</th>';
echo '</tr>';
$consultaTamboresEn = $consulta->TamboresEnvasado($idReporteEnvasado, $tipoDeMiel);
$dataTamboresEn = $conexion->prepare($consultaTamboresEn);
$dataTamboresEn->execute();
$cont = "0";
while ($datosdelTambor = $dataTamboresEn->fetch()) {
    $bruto = $datosdelTambor["bruto"];
    $tara = $datosdelTambor["tara"];
    $neto = $datosdelTambor["neto"];
    $cont = $cont + 1;

    echo '<tr>';
    echo '<td>' . $cont . '</td>';
    echo '<td>' . number_format($bruto, 0, '.', '.') . '</td>';
    echo '<td>' . number_format($tara, 0, '.', ',') . '</td>';
    echo '<td>' . number_format($neto, 0, '.', ',') . '</td>';
    echo '</tr>';
}

echo '</table><br>';
echo '<div style ="font-weight: bold; font-size: 13pt;"><b>Observaciones</b></div>';
echo '<br>';
echo ' <div>' . $detalleEnvasado->observaciones . '</div><br/>';

echo '<div style ="font-weight: bold; font-size: 13pt;"><b>' . utf8_decode('Conciliación de Mercancía') . '</b></div><br/>';
echo '<table width="100%" style="font-family: serif;">';
echo '<tr>';
echo '<th align="left">Kilogramos realmente procesados</th>';
echo '<td>' . number_format($detalleEnvasado->kilosProcesados, 0, '.', ',') . '</td>';
echo '</tr>';
echo '<tr>';
echo '<th align="left">Kilogramos Netos Envasados</th>';
echo '<td>' . number_format($detalleEnvasado->netosEnvasados, 0, '.', ',') . '</td>';
echo '</tr>';
echo '<tr>';
echo '<th align="left">' . utf8_decode('Merma Física') . '</th>';
echo '<td>' . number_format($detalleEnvasado->merma, 0, '.', ',') . '</td>';
echo '</tr>';
echo '<tr>';
echo '<th align="left">Faltante o Sobrante</th>';
echo '<td>' . number_format($detalleEnvasado->faltante, 0, '.', ',') . '</td>';
echo ' </tr>';
echo '</table><br/><br/>';
echo '<div style ="font-weight: bold; font-size: 13pt;"><b>' . utf8_decode('Observaciones de Conciliación de Mercancía') . '</b></div> <br>';
echo '<div>' . $detalleEnvasado->observacionesPeso . '</div>';
echo '<br/><br/>';
echo '<table width="100%">';
echo '<tr> ';
echo ' <td width="33%">';
echo '<span style="font-weight: bold; font-size: 12pt;">Responsable de Envasado</span><br /> <br />';
echo '________________________<br />';
echo '</td>';
echo '<td width="33%">';
echo '<span style="font-weight: bold; font-size: 12pt;">' . utf8_decode('Auxiliar de Producción') . '</span><br /><br />';
echo ' ________________________<br />';
echo ' </td>';
echo '<td width="33%">';
echo ' <span style="font-weight: bold; font-size: 12pt;">' . utf8_decode('Jefe de Producción') . '</span><br /> <br />';
echo ' ________________________<br />';
echo '</td>';
echo '</tr>';
echo '</table>';
echo '</body>';
echo '</html>';
