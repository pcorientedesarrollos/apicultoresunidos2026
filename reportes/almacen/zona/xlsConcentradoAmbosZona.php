<?php

include_once '../../../clases/consultas.php';
include_once '../../../DAOConeccion/conePDO.php';

$alm = new consultas();
$pdo = new conePDO();
$conexion = $pdo->conectar();

$ambos = $_GET['ambos'];
$tipoDeMiel = $_GET['tipoDeMiel'];
$nombreTipoDeMiel = $tipoDeMiel == '1' ? 'Miel 100% pura de abeja' : 'Miel 100% orgánica';

$oConcentradoZon = new stdClass();
$recipiente = 0;
$oConcentradoZon->recipiente = $recipiente;
$oConcentradoZon->ambos = $ambos;

//Fecha de Exportacion
$fecha = date("d-m-y");

//Inicio de la instancia de la exportacion
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachmen; filename = Reporte Concentrado de Miel (Kg) por Zonas_$fecha.xls");
header("Prafma: no-cache");
header("Expires:0");



$titulo = "Reporte Concentrado de Miel (Kg) por Zonas. $nombreTipoDeMiel";

echo '<table width="100%">';
echo '<tr>';
echo '<td width = "50%" style="color#0000">'
.'<span style = "font-weight:bold; font-size:18pt;">'.utf8_decode($titulo).'</span></td>';
echo '</tr>';
echo '</table>';
echo '<br>';

$oConcentradoZon->tipoDeMiel = $tipoDeMiel;
$consultaZonaTamCub = $alm->concentradoZona($oConcentradoZon);
$dataZona = $conexion->prepare($consultaZonaTamCub);
$dataZona->execute();



if($dataZona){
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
    echo '<th>Zona</th>';
    //echo '<th>T/C</th>';
    echo '<th>Comprador</th>';
    echo '<th>P.lista</th>';
    echo '<th>Bruto</th>';
    echo '<th>Tara</th>';
    echo '<th>Neto</th>';
    echo '<th>Dif</th>';
    echo '</tr>';
    
    $contaTC = 0;
    //$sumTC = 0;
    $sumPlistaTC = 0;
    $sumbrutoTC = 0;
    $sumtaraTC = 0;
    $sumnetoTC =0;
    $sumdifTC = 0;
    
    while($registroConcentradoTamCubZon = $dataZona->fetch()){
        $zonaTC   = $registroConcentradoTamCubZon['zonaCam'];
        // $tamCubTC = $registroConcentradoTamCubZon['tambores'];
        $compradorTC = $registroConcentradoTamCubZon['comprador'];
        $plistaTC = $registroConcentradoTamCubZon['plista'];
        $brutoTC  = $registroConcentradoTamCubZon['bruto'];
        $taraTC   = $registroConcentradoTamCubZon['tara'];
        $netoTC   = $registroConcentradoTamCubZon['neto'];
        $difTC    = $registroConcentradoTamCubZon['dif'];
        
        $contaTC = $contaTC + 1;
      //  $sumTC = $sumTC + $tamCubTC;
        $sumPlistaTC = $sumPlistaTC + $plistaTC;
        $sumbrutoTC = $sumbrutoTC + $brutoTC;
        $sumtaraTC = $sumtaraTC + $taraTC;
        $sumnetoTC = $sumnetoTC + $netoTC;
        $sumdifTC = $sumdifTC + $difTC;
        
    echo '<tr>';
    echo '<td>'.$contaTC.'</td>';
    echo '<td>'.$zonaTC.'</td>';
    //echo '<td>'.$tamCubTC.'</td>';
    echo '<td>'.$compradorTC.'</td>';
    echo '<td>'.$plistaTC.'</td>';
    echo '<td>'.$brutoTC.'</td>';
    echo '<td>'.$taraTC.'</td>';
    echo '<td>'.$netoTC.'</td>';
    echo '<td>'.$difTC.'</td>';
    echo '</tr>';
    }
    echo '</table>';
    echo '<table width="100%" style= "text-align:left;">';
    echo '<tr>';
    echo '<td></td>';
    echo '<td>Totales : </td>';
    echo '<td></td>';
    //echo '<td>'. number_format($sumTC,0,'.',',').'</td>';
    echo '<td>'. number_format($sumPlistaTC,0,'.',',').'</td>';
    echo '<td>'. number_format($sumbrutoTC,0,'.',',').'</td>';
    echo '<td>'. number_format($sumtaraTC,0,'.',',').'</td>';
    echo '<td>'. number_format($sumnetoTC,0,'.',',').'</td>';
    echo '<td>'. number_format($sumdifTC,0,'.',',').'</td>';
    echo '</tr>';
    echo '</table>';
    
}else{
     $valorNulo = 0;
    echo $json_response = json_encode($valorNulo);
}