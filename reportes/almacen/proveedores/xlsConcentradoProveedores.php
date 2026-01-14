<?php

include_once '../../../clases/consultas.php';
require_once '../../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$conexion = $pdo->conectar();

$alm = new consultas();

$recipiente = $_GET['recipiente'];
$tipoDeMiel = $_GET['tipoDeMiel'];
$nombreTipoDeMiel = $tipoDeMiel == '1' ? 'Miel 100% pura de abeja' : 'Miel 100% orgánica';

$oConcentradoProve = new stdClass();
$oConcentradoProve->recipiente = $recipiente;
$ambos = 0;
$oConcentradoProve ->ambos = $ambos;


//Fecha de Exportacion
$fecha = date("d-m-y");

if($recipiente == 3){
    $adicional = 'Tambores';
    
} else {
    $adicional = 'Cubetas';
}
//Inicio de la instancia de la exportacion
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachmen; filename = Reporte Concentrado de $adicional  por todos los Proveedores_$fecha.xls");
header("Prafma: no-cache");
header("Expires:0");

$titulo = "Reporte Concentrado de $adicional por todos los proveedores. $nombreTipoDeMiel";

echo '<table width="100%">';
echo '<tr>';
echo '<td width = "50%" style="color#0000;">'
 . '<span style="font-weight:bold; font-size:18pt;">' . utf8_decode($titulo) . '</span><td>';
echo '</tr>';
echo '</table>';
echo '<br>';
    

//$detalleConcentradoPro = $alm->concentradoProveedores($oConcentradoProve);
//$registroConse = mysql_num_rows($detalleConcentradoPro);
$oConcentradoProve->tipoDeMiel = $tipoDeMiel;
$consulta = $alm->concentradoProveedores($oConcentradoProve);

 $datos = $conexion->prepare($consulta);
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
        <td>
                <p style="font-size:22px; font-weight:bold"> <b>' . utf8_decode('Código:') . 'RCO-RG-05</b> </p>
                </td>
                </tr>
            </table>';
    echo '<table width="100%" border="1">';
    echo '<tr>';
    echo '<th>No</th>';
    echo '<th>Proveedor</th>';
    echo '<th>Localidad</th>';
    echo '<th>'.$adicional.'</th>';
    echo '<th>P.Lista</th>';
    echo '<th>Bruto</th>';
    echo '<th>Tara</th>';
    echo '<th>Neto</th>';
    echo '<th>Dif</th>';
    
    $sumTambores = 0;
    $sumPlistaC = 0;
    $sumBrutoC = 0;
    $sumTaraC =0;
    $sumNetoC = 0;
    $sumDifC = 0;
    $contC = 0;
    while($registrosConcentrado= $datos->fetch()){
          $nombreC    = $registrosConcentrado['nombrep'];
          $localidadC = $registrosConcentrado['localidadp'];
          $tamboresC  = $registrosConcentrado['Tambores'];
          $pListaC    = $registrosConcentrado['pesoLista']; 
          $brutoC     = $registrosConcentrado['bruto'];
          $taraC      = $registrosConcentrado['tara'];
          $netoC      = $registrosConcentrado['neto'];
          $difC       = $registrosConcentrado['dif'];
          
          $contC = $contC + 1;
          $sumTambores = $sumTambores + $tamboresC;
          $sumPlistaC = $sumPlistaC + $pListaC;
          $sumBrutoC = $sumBrutoC + $brutoC;
          $sumTaraC = $sumTaraC + $taraC;
          $sumNetoC = $sumNetoC + $netoC;
          $sumDifC = $sumDifC + $difC;
          
          echo '<tr>';
          echo '<td>'.$contC.'</td>';
          echo '<td>'.utf8_decode($nombreC).'</td>';
          echo '<td>'.strtoupper($localidadC).'</td>';
          echo '<td>'.$tamboresC.'</td>';
          echo '<td>'.$pListaC.'</td>';
          echo '<td>'.$brutoC.'</td>';
          echo '<td>'.$taraC.'</td>';
          echo '<td>'.$netoC.'</td>';
          echo '<td>'.$difC.'</td>';
    }
    
    echo '</table>';
    echo '<table width="100%" style= "text-align:left;">';
    echo '<tr>';
    echo '<td>Totales : </td>';
    echo '<td></td>';
    echo '<td></td>';
    echo '<td>'. number_format($sumTambores,0,'.',',').'</td>';
    echo '<td>'. number_format($sumPlistaC,0,'.',',').'</td>';
    echo '<td>'. number_format($sumBrutoC,0,'.',',').'</td>';
    echo '<td>'. number_format($sumTaraC,0,'.',',').'</td>';
    echo '<td>'. number_format($sumNetoC,0,'.',',').'</td>';
    echo '<td>'. number_format($sumDifC,0,'.',',').'</td>';
    echo '</table>';
} 
else{
    echo 'No hay registros ';
}


