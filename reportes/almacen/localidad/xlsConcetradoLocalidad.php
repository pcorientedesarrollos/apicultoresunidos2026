<?php

include_once '../../../clases/consultas.php';
$alm = new consultas();

include_once '../../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$conexion = $pdo->conectar();


$recipiente = $_GET['recipiente'];
$tipoDeMiel = $_GET['tipoDeMiel'];
$nombreTipoDeMiel = $tipoDeMiel == '1' ? 'Miel 100% pura de abeja' : 'Miel 100% orgánica';

$oConcentradoLoc = new stdClass();
$oConcentradoLoc->recipiente = $recipiente;
$ambos = 0;
$oConcentradoLoc ->ambos = $ambos;


//Fecha de Exportacion
$fecha = date("d-m-y");

if($recipiente == 3){
    $adicional = 'Tambores';
    
} else {
    $adicional = 'Cubetas';
}
//Inicio de la instancia de la exportacion
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachmen; filename = Reporte Concentrado de $adicional  por todas las Localidades_$fecha.xls");
header("Prafma: no-cache");
header("Expires:0");

$titulo = "Reporte Concentrado de $adicional por todas las Localidades. $nombreTipoDeMiel";
echo '<table width="100%">';
echo '<tr>';
echo '<td width = "50%" style="color#0000;">'
 . '<span style="font-weight:bold; font-size:18pt;">' . utf8_decode($titulo) . '</span><td>';
echo '</tr>';
echo '</table>';
echo '<br>';


//$detalleConcentradoLoc = $alm->concentradoLocalidad($oConcentradoLoc);
//$registroConseLoc = mysql_num_rows($detalleConcentradoLoc);

$oConcentradoLoc->tipoDeMiel = $tipoDeMiel;
$datosLocalidad = $alm->concentradoLocalidad($oConcentradoLoc);
$dataLoc = $conexion->prepare($datosLocalidad);
$dataLoc->execute();

if($dataLoc){
    echo '<table>
    <tr>
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
    echo '<th>Localidad</th>';
    echo '<th>'.$adicional.'</th>';
    echo '<th>P.Lista</th>';
    echo '<th>Bruto</th>';
    echo '<th>Tara</th>';
    echo '<th>Neto</th>';
    echo '<th>Dif</th>';
    
    $sumTamboresL = 0;
    $sumPlistaL = 0;
    $sumBrutoL = 0;
    $sumTaraL =0;
    $sumNetoL = 0;
    $sumDifL = 0;
    $contL = 0;
    while($registrosConcentradoLoc= $dataLoc->fetch()){
          $localidadL = $registrosConcentradoLoc['localidadp'];
          $tamboresL  = $registrosConcentradoLoc['Tambores'];
          $pListaL    = $registrosConcentradoLoc['pesoLista']; 
          $brutoL     = $registrosConcentradoLoc['bruto'];
          $taraL      = $registrosConcentradoLoc['tara'];
          $netoL      = $registrosConcentradoLoc['neto'];
          $difL       = $registrosConcentradoLoc['dif'];
          
          $contL = $contL + 1;
          $sumTamboresL = $sumTamboresL + $tamboresL;
          $sumPlistaL = $sumPlistaL + $pListaL;
          $sumBrutoL = $sumBrutoL + $brutoL;
          $sumTaraL = $sumTaraL + $taraL;
          $sumNetoL = $sumNetoL + $netoL;
          $sumDifL = $sumDifL + $difL;
          
          echo '<tr>';
          echo '<td>'.$contL.'</td>';
          echo '<td>'.$localidadL.'</td>';
          echo '<td>'.$tamboresL.'</td>';
          echo '<td>'.$pListaL.'</td>';
          echo '<td>'.$brutoL.'</td>';
          echo '<td>'.$taraL.'</td>';
          echo '<td>'.$netoL.'</td>';
          echo '<td>'.$difL.'</td>';
    }
    
    echo '</table>';
    echo '<table width="100%" style= "text-align:left;">';
    echo '<tr>';
    echo '<td>Totales : </td>';
    echo '<td></td>';
    echo '<td>'. number_format($sumTamboresL,0,'.',',').'</td>';
    echo '<td>'. number_format($sumPlistaL,0,'.',',').'</td>';
    echo '<td>'. number_format($sumBrutoL,0,'.',',').'</td>';
    echo '<td>'. number_format($sumTaraL,0,'.',',').'</td>';
    echo '<td>'. number_format($sumNetoL,0,'.',',').'</td>';
    echo '<td>'. number_format($sumDifL,0,'.',',').'</td>';
} 
else{
    echo 'No hay registros ';
}

