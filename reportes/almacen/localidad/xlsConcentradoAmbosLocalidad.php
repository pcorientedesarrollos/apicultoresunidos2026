<?php

include_once '../../../clases/consultas.php';
include_once '../../../DAOConeccion/conePDO.php';

$alm = new consultas();
$pdo = new conePDO();
$conexion = $pdo->conectar();

$ambos = '5';

$tipoDeMiel = $_GET['tipoDeMiel'];
$nombreTipoDeMiel = $tipoDeMiel == '1' ? 'Miel 100% pura de abeja' : 'Miel 100% orgánica';

//$ambos = $_GET['ambos'];

$oConcentradoLoc = new stdClass();
$recipiente = 0;
$oConcentradoLoc->recipiente = $recipiente;
$oConcentradoLoc->ambos = $ambos;

//Fecha de Exportacion 
$fecha = date("d-m-y");

//Inicio de la instancia de la exportacion
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachmen; filename = Reporte Concentrado de Miel (Kg) por Localidad_$fecha.xls");
//header("Content-Disposition: attachmen; filename = Reporte Concetrado por Todas las Localidades_$fecha.xls");
header("Prafma: no-cache");
header("Expires:0");

//$titulo = "Reporte Concentrado por Todas las Localidades";
$titulo = "Reporte Concentrado de Miel (Kg) por Localidad. $nombreTipoDeMiel";
echo '<table width="100%">';
echo '<tr>';
echo '<td width = "50%" style="color#0000">'
.'<span style = "font-weight:bold; font-size:18pt;">'.utf8_decode($titulo).'</span></td>';
echo '</tr>';
echo '</table>';
echo '<br>';


//$detalleConcentradoTamCubLoc = $alm->concentradoLocalidad($oConcentradoLoc);
//$registrosConcentradoTambCubLoc = mysql_fetch_array($detalleConcentradoTamCubLoc);

$oConcentradoLoc->tipoDeMiel = $tipoDeMiel;
$consultaLoca = $alm->concentradoLocalidad($oConcentradoLoc);
$dataLoca = $conexion->prepare($consultaLoca);
$dataLoca->execute();

if($dataLoca){
    echo '<table>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td>
                <p style="font-size:22px; font-weight:bold"> <b>' . utf8_decode('Código:') . 'RCO-RG-05</b> </p>
                </td>
                </tr>
            </table>';
    echo '<table width="100%" border="1">';
    echo '<tr>';
    echo '<th>No</th>';
    echo '<th>Localidad</th>';
    //echo '<th>T/C</th>';
    echo '<th>P.Lista</th>';
    echo '<th>Bruto</th>';
    echo '<th>Tara</th>';
    echo '<th>Neto</th>';
    echo '<th>Dif</th>';
    echo'</tr>';
    
    $contLoc = 0;
    $sumaTCL = 0;
    $sumapListaTCL = 0;
    $sumabrutoTCL = 0;
    $sumaTaraTCL = 0;
    $sumaNetoTCL = 0;
    $sumadifTCL = 0;
    
    while ($registroConcentradoTamCubLoc = $dataLoca->fetch()){
           $localidadTC = $registroConcentradoTamCubLoc['localidadp'];
           //$TambCuubTC  = $registroConcentradoTamCubLoc['tambores'];
           $pListaTC    = $registroConcentradoTamCubLoc['plista'];
           $brutoTC     = $registroConcentradoTamCubLoc['bruto'];
           $taraTC      = $registroConcentradoTamCubLoc['tara'];
           $netoTC      = $registroConcentradoTamCubLoc['neto'];
           $difTC       = $registroConcentradoTamCubLoc['dif'];
           
           $contLoc = $contLoc + 1;
           //$sumaTCL = $sumaTCL + $TambCuubTC;
           $sumapListaTCL = $sumapListaTCL + $pListaTC;
           $sumabrutoTCL = $sumabrutoTCL + $brutoTC;
           $sumaTaraTCL = $sumaTaraTCL + $taraTC;
           $sumaNetoTCL = $sumaNetoTCL + $netoTC;
           $sumadifTCL  = $sumadifTCL + $difTC;
           
    echo '<tr>';
    echo '<td>'.$contLoc.'</td>';
    echo '<td>'.$localidadTC.'</td>';
    //echo '<td>'.$TambCuubTC.'</td>';
    echo '<td>'.$pListaTC.'</td>';
    echo '<td>'.$brutoTC.'</td>';
    echo '<td>'.$taraTC.'</td>';
    echo '<td>'.$netoTC.'</td>';
    echo '<td>'.$difTC.'</td>';
    echo'</tr>';
    }
    echo '</table>';
    echo '<table width="100%" style= "text-align:left;">';
    echo '<tr>';
    echo '<td>Totales : </td>';
    echo '<td></td>';
    //echo '<td>'. number_format($sumaTCL,0,'.',',').'</td>';
    echo '<td>'. number_format($sumapListaTCL,0,'.',',').'</td>';
    echo '<td>'. number_format($sumabrutoTCL,0,'.',',').'</td>';
    echo '<td>'. number_format($sumaTaraTCL,0,'.',',').'</td>';
    echo '<td>'. number_format($sumaNetoTCL,0,'.',',').'</td>';
    echo '<td>'. number_format($sumadifTCL,0,'.',',').'</td>';
    
} else
{
    echo 'No hay registros';
}