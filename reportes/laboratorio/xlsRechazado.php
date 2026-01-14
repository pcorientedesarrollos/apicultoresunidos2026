<?php

include_once '../../clases/consultas.php';
$dao = new consultas();
$oLaboratorio = new stdClass();

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$conexion = $pdo->conectar();

//Fecha de Exportacion
$fecha = date("d-m-y");

//Inicio de la instalacion de la exportacion
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachmen; filename = Reporte de Rechazados_$fecha.xls");
header("Prafma: no-cache");
header("Expires:0");
$tipoDeMiel = $_GET['tipoDeMiel'];
$titulo = $tipoDeMiel == '1' ? "Reporte de Rechazados Miel 100% pura de abeja" : "Reporte de Rechazados Miel 100% orgánica";

echo '<table width="100%">';
echo '<tr>';
echo '<td colspan="6" width = "50%" style="color#0000;">'
 . '<span style="font-weight:bold; font-size:18pt;">' . utf8_decode($titulo) . '</span><td>';
echo '</tr>';
echo '</table>';
echo '<br>';

if (isset($_GET['sinFecha'])){
    $sinFecha = $_GET['sinFecha'];
    $oLaboratorio ->sinFecha = $sinFecha;
}else
{
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

$consultasRechazo = $dao->porcentajeRechazo($oLaboratorio, $tipoDeMiel);
$datsRecha = $conexion->prepare($consultasRechazo);
$datsRecha->execute();
$contRechazado = $datsRecha->rowCount();

if($contRechazado > 0){
    
echo '<table  width="100%" border="1">';
echo '<tr>';
echo '<th>Apicultor</th>';
echo '<th>Procedencia</th>';
echo '<th>Tambores</th>';
echo '<th>SF</th>';
echo '<th>ST</th>';
echo '<th>HMF</th>';
echo '<th>C13</th>';
echo '<th>Humedad</th>';
echo '<th>Porcentaje</th>';
echo '</tr>';

$sumaTodo = 0;
$porcentajeST = 0;
$porcentajeSF = 0;
$porcentajeHMF = 0;
$porcentajeC13 = 0;
$porcentajeHumedad = 0;

$sumaTambores = 0;
$sumaSFRech = 0;
$sumaSTRech = 0;
$sumaHMRech = 0;
$sumaC13 = 0;
$sumaHumedad = 0;
while ($detallesRechazo = $datsRecha->fetch()){
    $apicul = $detallesRechazo['nombre'];
    $procedencia = $detallesRechazo['localidad'];
    $Tambores   = $detallesRechazo['Tambores'];
    $SFRech     = $detallesRechazo['SFRechazado'];
    $STRech     = $detallesRechazo['STRechazados'];
    $HMFRech    = $detallesRechazo['HMFRechazado'];
    $C13        = $detallesRechazo['Adulteracion'];
    $humedadRech = $detallesRechazo['Rechazado'];
    
   $sumaTambores = $sumaTambores + $Tambores;
   $sumaSFRech = $sumaSFRech + $SFRech;
   $sumaSTRech = $sumaSTRech + $STRech;
   $sumaHMRech = $sumaHMRech + $HMFRech;
   $sumaC13 = $sumaC13 + $C13;
   $sumaHumedad = $sumaHumedad + $humedadRech;
     
   $sumaTodo = ($SFRech + $STRech + $HMFRech + $C13 + $humedadRech);
   
  
    if($sumaTodo >= $Tambores){
        $porcentaje = 100; 
    }
    else {
        $porcentaje = ($sumaTodo/$Tambores)*100;
    }
     
echo '<tr>';
echo '<td>'. utf8_decode($apicul).'</td>';
echo '<td>'. strtoupper($procedencia).'</td>';
echo '<td>'.$Tambores.'</td>';
echo '<td>'.$SFRech.'</td>';
echo '<td>'.$STRech.'</td>';
echo '<td>'.$HMFRech.'</td>';
echo '<td>'.$C13.'</td>';
echo '<td>'.$humedadRech.'</td>';
echo '<td>'. number_format($porcentaje,2,'.',',').'</td>';
echo '</tr>';
}

echo '</table>';
echo '<table  width="100%" border="1">';
echo '<tr>';
echo '<td><b>Totales: </b></td>';
echo '<td></td>';
echo '<td>'.$sumaTambores.'</td>';
echo '<td>'.$sumaSFRech.'</td>';
echo '<td>'.$sumaSTRech.'</td>';
echo '<td>'.$sumaHMRech.'</td>';
echo '<td>'.$sumaC13.'</td>';
echo '<td>'.$sumaHumedad.'</td>';
//echo '<td>'. number_format($porcentaje,2,'.',',').'</td>';
echo '</tr>';
echo '</table>';
} else {
    echo '<b>No Hay Registros de Rechazados</b>';    
}
