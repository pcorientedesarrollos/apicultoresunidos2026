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
header("Content-Disposition: attachmen; filename = Reporte de Adulteración_$fecha.xls");
header("Prafma: no-cache");
header("Expires:0");

$titulo = "Reporte de Adulteración";

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

$consultaAdul = $dao->resumenConcentrado($oLaboratorio);
$dataAdul = $conexion->prepare($consultaAdul);
$dataAdul->execute();
$contAdulteracion = $dataAdul->rowCount();


echo '<table  width="100%" border="1">';
echo '<tr>';
echo '<th>'. utf8_decode("Análisis").'</th>';
echo '<th>Tambores</th>';
echo '<th>Porcentaje</th>';
echo '</tr>';

while ($detalleAdult = $dataAdul->fetch()){
    $detalleAdult1 = new stdClass();
    $detalleAdult1->Delta13 = $detalleAdult['delta13'];
    $detalleAdult1->C4 = $detalleAdult['C4'];
    $detalleAdult1->Adulteracion = $detalleAdult['Adulteracion'];
    $detalleAdult1->porceDelta13 = $detalleAdult['porcDelta13'];
    $detalleAdult1->porceC4 = $detalleAdult['porcC4'];
    $detalleAdult1->porceAdulte = $detalleAdult['porcAdult'];
}

echo '<tr>';
echo '<td>Delta 13</td>';
echo '<td>'.$detalleAdult1->Delta13.'</td>';
echo '<td>'. number_format($detalleAdult1->porceDelta13,2,'.',',').'</td>';
echo '</tr>';

echo '<tr>';
echo '<td>C4</td>';
echo '<td>'.$detalleAdult1->C4.'</td>';
echo '<td>'. number_format($detalleAdult1->porceC4,2,'.',',').'</td>';
echo '</tr>';

echo '<tr>';
echo '<td>'. utf8_decode('Adulteración').'</td>';
echo '<td>'.$detalleAdult1->Adulteracion.'</td>';
echo '<td>'. number_format($detalleAdult1->porceAdulte,2,'.',',').'</td>';
echo '</tr>';
echo '</table>';

echo '<br>';

$consultaResumen = $dao->reportesLaboratorio($oLaboratorio);
$datsAdult = $conexion->prepare($consultaResumen);
$datsAdult->execute();
$contAdulteracion1 = $datsAdult->rowCount();


echo '<table  width="100%" border="1">';
    echo '<tr>';
    echo '<th colspan="3">Datos</th>';
    echo '<th colspan="3">'. utf8_decode("Adulteración").'</th>';    
    echo '</tr>';
    
    echo '<tr>';
    echo '<th>Procedencia</th>';
    echo '<th>Tambores</th>';
    echo '<th>Kgs</th>';
    echo '<th>Delta13</th>';
    echo '<th>C4</th>';
    echo '<th>'. utf8_decode("Adulteración").'</th>';
    echo '</tr>';
    
    if($contAdulteracion1 > 0) {

        $sumaTambores = 0;
        $sumaKgs = 0;
        $sumaDel13 = 0;
        $sumaC4 = 0;
        $sumaRechados = 0;
        
        while ($detallesLab = $datsAdult->fetch()){
            $localidadLab  = $detallesLab['Localidad'];
            $tambores      = $detallesLab['Tambores'];
            $Kgs           = $detallesLab['Kgs'];
            $delta13       = $detallesLab['Delta13'];
            $c4            = $detallesLab['C4'];
            $adulteracion    = $detallesLab['Adulteracion'];
            
            $sumaTambores = $sumaTambores + $tambores;
            $sumaKgs = $sumaKgs + $Kgs;
            $sumaDel13 = $sumaDel13 + $delta13;
            $sumaC4 = $sumaC4 + $c4;
            $sumaRechados = $sumaRechados + $adulteracion;
            
           
            $porceDelta13 = ($sumaDel13 / $sumaTambores)*100;
            $porceC4 = ($sumaC4 / $sumaTambores)*100;
            $porceRechazados = ($sumaRechados / $sumaTambores)*100;
            
             echo '<tr>';
            echo '<td>'. strtoupper($localidadLab).'</td>';
            echo '<td>'.$tambores.'</td>';
            echo '<td>'.$Kgs.'</td>';
            echo '<td>'.$delta13.'</td>';
            echo '<td>'.$c4.'</td>';
            echo '<td>'.$adulteracion.'</td>';
            echo '</tr>';
        }
        
        echo '</table>';
        echo '<br>';
        echo '<table  width="100%" border="1">';
        echo '<tr>';
        echo '<td><b>Total : </b></td>';
        echo '<td>' . $sumaTambores . '</td>';
        echo '<td>' . $sumaKgs . '</td>';
        echo '<td>' . $sumaDel13 . '</td>';
        echo '<td>' . $sumaC4 . '</td>';
        echo '<td>' . $sumaRechados . '</td>';
        echo '</tr>';
        
        echo '<tr>';
        echo '<td><b>Porcentaje : </b></td>';
        echo '<td></td>';
        echo '<td></td>';
        echo '<td>' .number_format($porceDelta13,2,'.',','). '</td>';
        echo '<td>' .number_format($porceC4,2,'.',','). '</td>';
        echo '<td>' .number_format($porceRechazados,2,'.',','). '</td>';
        echo '</tr>';
        echo '</table>';
    } else 
    {
        echo '';
    }