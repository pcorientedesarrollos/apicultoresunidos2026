<?php

include_once '../../clases/consultas.php';
$dao = new consultas();
$oLaboratorio = new stdClass();

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$conexion = $pdo->conectar();

//Fecha de Exporrtacion
$fecha = date("d-m-y");

//Inicio de la insrancia de exportacion
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachmen; filename = Reporte de HMF_$fecha.xls");
header("Prafma: no-cache");
header("Expires:0");

$titulo = "Reporte de HMF";

echo '<table width="100%">';
echo '<tr>';
echo '<td width = "50%" style="color#0000;">'
 . '<span style="font-weight:bold; font-size:18pt;">' . utf8_decode($titulo) . '</span><td>';
echo '</tr>';
echo '</table>';
echo '<br>';

if(isset($_GET['sinFecha'])){
$sinFecha = $_GET['sinFecha'];
$oLaboratorio->sinFecha = $sinFecha;

} else {
    $sinFecha = 0;
    $oLaboratorio->sinFecha = $sinFecha;
    
    $fechaUno = $_GET['fechaUno'];
    $fechaDos = $_GET['fechaDos'];    
    
    $oLaboratorio->fInicial = $fechaUno;
    $oLaboratorio->fFinal = $fechaDos;
    
    echo '<table width="100%"';
    echo '<tr>';
    echo '<td width = "25%" style="color:#0000;"> <span style="font-weight: bold; font-size: 12pt;">Periodo:</span> <p> </td> ';
    echo '<td width = "25%" style="text-aligb:left;"> <span style="font-weight: bold; font-size: 12pt;">DE: ' . $oLaboratorio->fInicial . '</span> <p> </td>';
    echo '<td width = "25%" style="color:#0000;"><span style="font-weight: bold; font-size: 12pt;">HASTA: ' . $oLaboratorio->fFinal . '</span></td> ';
    echo '<td width = "25%" style="text-aling: right";> </td>';
    echo '</tr>';
    echo '</table>';
    echo '<br>';
}

//$regritrosHMF = $dao->resumenConcentrado($oLaboratorio);
$dataHMF = $dao->resumenConcentrado($oLaboratorio);
$datsHmF = $conexion->prepare($dataHMF);
$datsHmF->execute();


echo '<table  width="100%" border="1">';
echo '<tr>';
echo '<th>Resultados</th>';
echo '<th>Tambores</th>';
echo '<th>Porcentaje</th>';
echo '</tr>';

while ($detalleHMF = $datsHmF->fetch()){
    $detalleHMF1 = new stdClass();
    $detalleHMF1->aprobados = $detalleHMF['HMFapro'];
    $detalleHMF1->rechazado = $detalleHMF['HMFRech'];
    $detalleHMF1->porcApro  = $detalleHMF['HMFapropor'];
    $detalleHMF1->porcRech  = $detalleHMF['HMFrechporc'];
    
    $sumaTambores = $detalleHMF1->aprobados + $detalleHMF1->rechazado;
    $sumaPorcentaje = $detalleHMF1->porcApro +  $detalleHMF1->porcRech;
}

echo '<tr>';
echo '<td>Aprobado</td>';
echo '<td>'.$detalleHMF1->aprobados.'</td>';
echo '<td>'. number_format($detalleHMF1->porcApro,2,'.',','). '</td>';
echo '</tr>';

echo '<tr>';
echo '<td>Rechazado</td>';
echo '<td>'.$detalleHMF1->rechazado.'</td>';
echo '<td>'. number_format($detalleHMF1->porcRech,2,'.',',').  '</td>';
echo '</tr>';
echo '</table>';

echo '<table border="1">';
echo '<tr>';
echo '<td><b>Total :</b></td>';
echo '<td>'.$sumaTambores.'</td>';
echo '<td>'.$sumaPorcentaje.'</td>';
echo '</tr>';
