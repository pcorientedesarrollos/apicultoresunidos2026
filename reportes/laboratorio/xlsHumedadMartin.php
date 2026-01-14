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
header("Content-Disposition: attachmen; filename = Reporte de Humedad_$fecha.xls");
header("Prafma: no-cache");
header("Expires:0");

$titulo = "Reporte de Humedad";

echo '<table width="100%">';
echo '<tr>';
echo '<td width = "50%" style="color#0000;">'
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

//$registroHum = $dao->resumenConcentrado($oLaboratorio);
$conaultasHum = $dao->resumenConcentrado($oLaboratorio);
$datosHumedad = $conexion->prepare($conaultasHum);
$datosHumedad->execute();

echo '<table  width="100%" border="1">';
echo '<tr>';
echo '<th>'. utf8_decode("Análisis") .'</th>';
echo '<th>Tambores</th>';
echo '<th>Porcentaje</th>';
echo '</tr>';

$sumaTambores = 0;
$sumaPorcentaje = 0;
while ($detalleHumed = $datosHumedad->fetch()){
    $detalleHumed1 = new stdClass();
    $detalleHumed1->menores19           = $detalleHumed['Exportmenor19'];
    $detalleHumed1->Exportacion20       = $detalleHumed['Exportacio20'];
    $detalleHumed1->Exportacion205       = $detalleHumed['Exportacion205'];
    $detalleHumed1->Exportacion21       = $detalleHumed['Exportacion21'];
    $detalleHumed1->Exportacionesmay21  = $detalleHumed['Exportacionmas21'];
    $detalleHumed1->Mayor22             = $detalleHumed['Mayores22'];
    $detalleHumed1->porcentaje19        = $detalleHumed['procentaje19'];
    $detalleHumed1->porcentaje20        = $detalleHumed['procentaje20'];
    $detalleHumed1->porcentaje205       = $detalleHumed['procentaje205'];
    $detalleHumed1->porcentaje21        = $detalleHumed['procentaje21'];
    $detalleHumed1->porcentajemas21     = $detalleHumed['procentajemas21'];
    $detalleHumed1->porcentajemas22     = $detalleHumed['procentajemas22'];
    
    $sumaTambores = $detalleHumed1->menores19  + $detalleHumed1->Exportacion20 + $detalleHumed1->Exportacion205 + $detalleHumed1->Exportacion21 + $detalleHumed1->Exportacionesmay21  + $detalleHumed1->Mayor22;
    $sumaPorcentaje = $detalleHumed1->porcentaje19 + $detalleHumed1->porcentaje20 +  $detalleHumed1->porcentaje205 + $detalleHumed1->porcentaje21 + $detalleHumed1->porcentajemas21 + $detalleHumed1->porcentajemas22;
}

echo '<tr>';
echo '<td style="text-align:center">Menos de 19.5%</td>';
echo '<td>'.$detalleHumed1->menores19.'</td>';
echo '<td>'. number_format($detalleHumed1->porcentaje19,2,'.',',').'</td>';
echo '</tr>';

echo '<tr>';
echo '<td style="text-align:center">20%</td>';
echo '<td>'.$detalleHumed1->Exportacion20.'</td>';
echo '<td>'. number_format($detalleHumed1->porcentaje20,2,'.',',').'</td>';
echo '</tr>';

echo '<tr>';
echo '<td style="text-align:center">20.50%</td>';
echo '<td>'.$detalleHumed1->Exportacion205.'</td>';
echo '<td>'. number_format($detalleHumed1->porcentaje205,2,'.',',').'</td>';
echo '</tr>';

echo '<tr>';
echo '<td style="text-align:center">21%</td>';
echo '<td>'.$detalleHumed1->Exportacion21.'</td>';
echo '<td>'. number_format($detalleHumed1->porcentaje21,2,'.',',').'</td>';
echo '</tr>';

echo '<tr>';
echo '<td style="text-align:center">21.50%</td>';
echo '<td>'.$detalleHumed1->Exportacionesmay21.'</td>';
echo '<td>'. number_format($detalleHumed1->porcentajemas21 ,2,'.',',').'</td>';
echo '</tr>';

echo '<tr>';
echo '<td style="text-align:center"> Mas de 21.5%</td>';
echo '<td>'.$detalleHumed1->Mayor22 .'</td>';
echo '<td>'. number_format($detalleHumed1->porcentajemas22 ,2,'.',',').'</td>';
echo '</tr>';

echo'</table>';

echo '<table  width="100%" border="1">';
echo '<tr>';
echo '<td><b>Totales :</b></td>';
echo '<td>'.$sumaTambores.'</td>';
echo '<td>'. number_format($sumaPorcentaje ,0,'.',',').'</td>';
echo '</tr>';
echo '</table>';
