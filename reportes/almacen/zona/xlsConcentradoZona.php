<?php

include_once '../../../clases/consultas.php';
include_once '../../../DAOConeccion/conePDO.php';

$alm = new consultas();
$pdo = new conePDO();
$conexion = $pdo->conectar();

$recipiente = $_GET['recipiente'];
$tipoDeMiel = $_GET['tipoDeMiel'];
$nombreTipoDeMiel = $tipoDeMiel == '1' ? 'Miel 100% pura de abeja' : 'Miel 100% orgánica';

$oConcentradoZon = new stdClass();
$oConcentradoZon->recipiente = $recipiente;
$ambos = 0;
$oConcentradoZon ->ambos = $ambos;


//Fecha de Exportacion
$fecha = date("d-m-y");

if($recipiente == 3){
    $adicional = 'Tambores';
    
} else {
    $adicional = 'Cubetas';
}
//Inicio de la instancia de la exportacion
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachmen; filename = Reporte Concentrado de $adicional  por todas las Zonas_$fecha.xls");
header("Prafma: no-cache");
header("Expires:0");

$titulo = "Reporte Concentrado de $adicional por todas las Zonas. $nombreTipoDeMiel";

echo '<table width="100%">';
echo '<tr>';
echo '<td width = "50%" style="color#0000;">'
 . '<span style="font-weight:bold; font-size:18pt;">' . utf8_decode($titulo) . '</span></td>';
echo '</tr>';
echo '</table>';
echo '<br>';

$oConcentradoZon->tipoDeMiel = $tipoDeMiel;
$consultaZona = $alm->concentradoZona($oConcentradoZon);
$datos = $conexion->prepare($consultaZona);
$datos->execute();

if($datos){
    echo '<table>
    <tr>
        <td></td>
        <td></td>
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
    echo '<th>Comprador</th>';
    echo '<th>'.$adicional.'</th>';
    echo '<th>P.Lista</th>';
    echo '<th>Bruto</th>';
    echo '<th>Tara</th>';
    echo '<th>Neto</th>';
    echo '<th>Dif</th>';
    
    $sumTamboresZ = 0;
    $sumPlistaZ = 0;
    $sumBrutoZ = 0;
    $sumTaraZ =0;
    $sumNetoZ = 0;
    $sumDifZ = 0;
    $contZ = 0;
    while($registrosConcentradoZon= $datos->fetch()){
          $zonaZ = $registrosConcentradoZon['zona'];
          $compradorZ = $registrosConcentradoZon['comprador'];
          $tamboresZ  = $registrosConcentradoZon['Tambores'];
          $pListaZ    = $registrosConcentradoZon['pesoLista']; 
          $brutoZ     = $registrosConcentradoZon['bruto'];
          $taraZ      = $registrosConcentradoZon['tara'];
          $netoZ      = $registrosConcentradoZon['neto'];
          $difZ       = $registrosConcentradoZon['dif'];
          
          $contZ = $contZ + 1;
          $sumTamboresZ = $sumTamboresZ + $tamboresZ;
          $sumPlistaZ = $sumPlistaZ + $pListaZ;
          $sumBrutoZ = $sumBrutoZ + $brutoZ;
          $sumTaraZ = $sumTaraZ + $taraZ;
          $sumNetoZ = $sumNetoZ + $netoZ;
          $sumDifZ = $sumDifZ + $difZ;
          
          echo '<tr>';
          echo '<td>'.$contZ.'</td>';
          echo '<td>'.$zonaZ.'</td>';
          echo '<td>'.$compradorZ.'</td>';
          echo '<td>'.$tamboresZ.'</td>';
          echo '<td>'.$pListaZ.'</td>';
          echo '<td>'.$brutoZ.'</td>';
          echo '<td>'.$taraZ.'</td>';
          echo '<td>'.$netoZ.'</td>';
          echo '<td>'.$difZ.'</td>';
    }
    
    echo '</table>';
    echo '<table width="100%" style= "text-align:left;">';
    echo '<tr>';
    echo '<td></td>';
    echo '<td>Totales : </td>';
    echo '<td></td>';
    echo '<td>'. number_format($sumTamboresZ,0,'.',',').'</td>';
    echo '<td>'. number_format($sumPlistaZ,0,'.',',').'</td>';
    echo '<td>'. number_format($sumBrutoZ,0,'.',',').'</td>';
    echo '<td>'. number_format($sumTaraZ,0,'.',',').'</td>';
    echo '<td>'. number_format($sumNetoZ,0,'.',',').'</td>';
    echo '<td>'. number_format($sumDifZ,0,'.',',').'</td>';
} 
else{
     $valorNulo = 0;
    echo $json_response = json_encode($valorNulo);
}

