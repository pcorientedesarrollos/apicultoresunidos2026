<?php

include_once '../../../clases/consultas.php';
include_once '../../../DAOConeccion/conePDO.php';

$alm = new consultas();
$pdo = new conePDO();
$conexion = $pdo->conectar();

$ambos = $_GET['ambos'];
$tipoDeMiel = $_GET['tipoDeMiel'];
$nombreTipoDeMiel = $tipoDeMiel == '1' ? 'Miel 100% pura de abeja' : 'Miel 100% orgánica';

$oConcentradoProve = new stdClass();
$recipiente = 0;
$oConcentradoProve->recipiente = $recipiente;
$oConcentradoProve->ambos = $ambos;

//Fecha de Exportacion
$fecha = date("d-m-y");

//Inicio de la instancia de la exportacion
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachmen; filename = Reporte Concentrado de Miel (Kg) por Proveedor_$fecha.xls");
header("Prafma: no-cache");
header("Expires:0");

$titulo = "Reporte Concentrado de Miel (Kg) por Proveedor. $nombreTipoDeMiel";

echo '<table width="100%">';
echo '<tr>';
echo '<td width = "50%" style="color#0000">'
.'<span style = "font-weight:bold; font-size:18pt;">'.utf8_decode($titulo).'</span></td>';
echo '</tr>';
echo '</table>';
echo '<br>';

$oConcentradoProve->tipoDeMiel = $tipoDeMiel;
$consultaProveTamCub = $alm->concentradoProveedores($oConcentradoProve);
$dta = $conexion->prepare($consultaProveTamCub);
$dta->execute();

if($dta){
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
    echo '<th>Proveedor</th>';
    echo'<th>Localidad</th>';
    //echo '<th>T/C</th>';
    echo '<th>P.Lista</th>';
    echo '<th>Bruto</th>';
    echo '<th>Tara</th>';
    echo '<th>Neto</th>';
    echo '<th>Dif</th>';
    echo '</tr>';
    
    $sumaTC = 0;
    $sumaPlistaTC = 0;
    $sumaBrutoTC = 0;
    $sumataraTC = 0;
    $sumanetoTC = 0;
    $sumadifTC = 0;
    $contTC = 0;
    
    while ($resgistroConcentradoTambCub = $dta->fetch()){
        $nombretc    = $resgistroConcentradoTambCub['nombrep'];
        $localidadtc = $resgistroConcentradoTambCub['localidadp'];
       // $tamcub      = $resgistroConcentradoTambCub['tambores'];
        $pListatc    = $resgistroConcentradoTambCub['plista'];
        $brutotc     = $resgistroConcentradoTambCub['bruto']; 
        $taratc      = $resgistroConcentradoTambCub['tara'];
        $netotc      = $resgistroConcentradoTambCub['neto'];
        $diftc       = $resgistroConcentradoTambCub['dif'];
        
        $contTC = $contTC + 1;
        //$sumaTC = $sumaTC + $tamcub;
        $sumaPlistaTC = $sumaPlistaTC + $pListatc;
        $sumaBrutoTC = $sumaBrutoTC + $brutotc;
        $sumataraTC = $sumataraTC + $taratc;
        $sumanetoTC = $sumanetoTC + $netotc;
        $sumadifTC = $sumadifTC + $diftc;
        
    echo '<tr>';
    echo '<td>'.$contTC.'</td>';
    echo '<td>'. utf8_decode($nombretc).'</td>';
    echo '<td>'. utf8_decode($localidadtc).'</td>';
    //echo '<td>'.$tamcub.'</th>';
    echo '<td>'.$pListatc.'</td>';
    echo '<td>'.$brutotc.'</td>';
    echo '<td>'.$taratc.'</td>';
    echo '<td>'.$netotc.'</td>';
    echo '<td>'.$diftc.'</td>';
    echo '</tr>';
    }
    echo '</table>';
    echo '<table width="100%" style= "text-align:left;">';
    echo '<tr>';
    echo '<td></td>';
    echo '<td>Totales : </td>';
    echo '<td></td>';
    //echo '<td>'. number_format($sumaTC,0,'.',',').'</td>';
    echo '<td>'. number_format($sumaPlistaTC,0,'.',',').'</td>';
    echo '<td>'. number_format($sumaBrutoTC,0,'.',',').'</td>';
    echo '<td>'. number_format($sumataraTC,0,'.',',').'</td>';
    echo '<td>'. number_format($sumanetoTC,0,'.',',').'</td>';
    echo '<td>'. number_format($sumadifTC,0,'.',',').'</td>';
    echo '</table>';
}
 else {
    $valorNulo = 0;
    echo $json_response = json_encode($valorNulo); 
}