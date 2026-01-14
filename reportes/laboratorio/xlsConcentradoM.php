<?php

include_once '../../clases/consultas.php';
include_once '../../DAOConeccion/conePDO.php';
$dao = new consultas();
$pdo = new conePDO();
$conexion = $pdo->conectar();
$oLaboratorio = new stdClass();

// Fecha de Exportacion
$fecha = date("d-m-y");

//Inicio de la instacia de la exportaion
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachmen; filename = Reporte de Concentrado de Laboratorio_$fecha.xls");
header("Prafma: no-cache");
header("Expires:0");

$titulo = "Reporte de Concentrado de Laboratorio";

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

//$registrosLab = $dao->reportesLaboratorio($oLaboratorio);
    
$consltaCons = $dao->reportesLaboratorio($oLaboratorio);
$datosalab = $conexion->prepare($consltaCons);
$datosalab->execute();
$contConcetrado = $datosalab->rowCount();

 if($contConcetrado > 0){

    echo '<table  width="100%" border="1">';
    echo '<tr>';
    echo '<th colspan="3">Datos</th>';
    echo '<th colspan="3">'. utf8_decode("Antibióticos").'</th>';
    echo '<th colspan="2">HMF</th>';   
    echo '<th colspan="3">'. utf8_decode("Adulteración").'</th>';    
    echo '</tr>';  
    echo '<tr>';
    echo '<th>Procedencia</th>';
    echo '<th>Tambores</th>';
    echo '<th>Kgs</th>';
    echo '<th>Aprobado</th>';
    echo '<th>SFRechazado</th>';
    echo '<th>STRechazados</th>';
    echo '<th>HMFAprobado</th>';
    echo '<th>HMFRechazado</th>';
    echo '<th>Delta13</th>';
    echo '<th>C4</th>';
    echo '<th>'. utf8_decode("Adulteración").'</th>';
    echo '</tr>';

    
   
//    $registroConset = mysql_num_rows($registrosLab);
   
        
        $sumaTambores = 0;
        $sumaKgs = 0;
        $sumaAprobados = 0;
        $sumaSFRechazados = 0;
        $sumaSTRechazados = 0;
        $sumaHMFapr = 0;
        $sumaHMFrepr = 0;
        $sumaDel13 = 0;
        $sumaC4 = 0;
        $sumaRechados = 0;
        
        while ($detallesLab = $datosalab->fetch()){
            $localidadLab  = $detallesLab['Localidad'];
            $tambores      = $detallesLab['Tambores'];
            $Kgs           = $detallesLab['Kgs'];
            $SFAprobados   = $detallesLab['SFAprobado'];
            $SFRechazado   = $detallesLab['SFRechazado'];
            $STRechazado   = $detallesLab['STRechazados'];
            $aprobado      = $detallesLab['HMFAprobado'];
            $rechazados    = $detallesLab['HMFRechazado'];
            $delta13       = $detallesLab['Delta13'];
            $c4            = $detallesLab['C4'];
            $adulteracion    = $detallesLab['Adulteracion'];
            
            $sumaTambores = $sumaTambores + $tambores;
            $sumaKgs = $sumaKgs + $Kgs;
            $sumaAprobados = $sumaAprobados + $SFAprobados;
            $sumaSFRechazados = $sumaSFRechazados + $SFRechazado;
            $sumaSTRechazados = $sumaSTRechazados + $STRechazado;
            $sumaHMFapr = $sumaHMFapr + $aprobado;
            $sumaHMFrepr = $sumaHMFrepr + $rechazados;
            $sumaDel13 = $sumaDel13 + $delta13;
            $sumaC4 = $sumaC4 + $c4;
            $sumaRechados = $sumaRechados + $adulteracion;
            
            $porceSFaprovado = ($sumaAprobados / $sumaTambores)*100;
            $porceSFrechazados = ($sumaSFRechazados / $sumaTambores)*100;
            $porceSTrechazado = ($sumaSTRechazados / $sumaTambores)*100;
            $porceHMFaprovado = ($sumaHMFapr / $sumaTambores)*100;
            $porceHMFrechazado = ($sumaHMFrepr / $sumaTambores)*100;
            $porceDelta13 = ($sumaDel13 / $sumaTambores)*100;
            $porceC4 = ($sumaC4 / $sumaTambores)*100;
            $porceRechazados = ($sumaRechados / $sumaTambores)*100;
            
            echo '<tr>';
            echo '<td>'. strtoupper($localidadLab).'</td>';
            echo '<td>'.$tambores.'</td>';
            echo '<td>'.$Kgs.'</td>';
            echo '<td>'.$SFAprobados.'</td>';
            echo '<td>'.$SFRechazado.'</td>';
            echo '<td>'.$STRechazado.'</td>';
            echo '<td>'.$aprobado.'</td>';
            echo '<td>'.$rechazados.'</td>';
            echo '<td>'.$delta13.'</td>';
            echo '<td>'.$c4.'</td>';
            echo '<td>'.$adulteracion.'</td>';
            echo '</tr>';
        }
        echo '</table>';
        echo '<br>';
        echo '<table  width="100%" border="1">';
        echo '<tr>';
        echo '<td><b>Total :</b> </td>';
        echo '<td>' . $sumaTambores . '</td>';
        echo '<td>' . $sumaKgs . '</td>';
        echo '<td>' . $sumaAprobados . '</td>';
        echo '<td>' . $sumaSFRechazados . '</td>';
        echo '<td>' . $sumaSTRechazados . '</td>';
        echo '<td>' . $sumaHMFapr . '</td>';
        echo '<td>' . $sumaHMFrepr . '</td>';
        echo '<td>' . $sumaDel13 . '</td>';
        echo '<td>' . $sumaC4 . '</td>';
        echo '<td>' . $sumaRechados . '</td>';
        echo '</tr>';
        
        echo '<tr>';
        echo '<td><b>Porcentaje :</b> </td>';
        echo '<td></td>';
        echo '<td></td>';
        echo '<td>' .number_format($porceSFaprovado,2,'.',','). '</td>';
        echo '<td>' .number_format($porceSFrechazados,2,'.',','). '</td>';
        echo '<td>' .number_format($porceSTrechazado,2,'.',','). '</td>';
        echo '<td>' .number_format($porceHMFaprovado,2,'.',','). '</td>';
        echo '<td>' .number_format($porceHMFrechazado,2,'.',','). '</td>';
        echo '<td>' .number_format($porceDelta13,2,'.',','). '</td>';
        echo '<td>' .number_format($porceC4,2,'.',','). '</td>';
        echo '<td>' .number_format($porceRechazados,2,'.',','). '</td>';
        echo '</tr>';
        echo '</table>';
} else {
        echo '<b>No hay Informacion de concentrado de miel en este rangon de fechas</b>';
}
