<?php

require_once '../../clases/consultas.php';
require_once '../../DAOConeccion/conePDO.php';

$pdo = new conePDO();
$conexion = $pdo->conectar();

$dao = new consultas();

$tipoCosecha = $_GET['tipoCosecha'];
$fecha1 = $_GET['fecha1'];
$fecha2 = $_GET['fecha2'];
$fechas = new stdClass();
$fechas->fecha1 = $fecha1;
$fechas->fecha2 = $fecha2;


$fecha = date("d-m-Y");

header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename= Reporte de Entrada de Tambores al Almacen_$fecha.xls");
header("Prafma: no-cache");
header("Expires:0");

if($tipoCosecha == 1){
    $tituloEntrada = "REPORTE DE ENTRADA DE MIEL 100% PURA DE ABEJA";    
}else if($tipoCosecha == 2){
    $tituloEntrada = "REPORTE DE ENTRADA DE MIEL 100% ORGÁNICA";
}else if($tipoCosecha == 6){
    $tituloEntrada = "REPORTE DE ENTRADA DE MIEL 100% ALTIPLANO";
}else if($tipoCosecha == 7){
    $tituloEntrada = "REPORTE DE ENTRADA DE MIEL 100% NARANJO";
}else if($tipoCosecha == 8){
    $tituloEntrada = "REPORTE DE ENTRADA DE MIEL 100% AGUACATE";
}else if($tipoCosecha == 9){
    $tituloEntrada = "REPORTE DE ENTRADA DE MIEL 100% MEZQUITE";
}
else{
    $tituloEntrada = "REPORTE DE ENTRADA DE MIEL 100% MANTEQUILLA";
}

echo '<table width="100%">';
echo '<tr>';
echo '<td width = "50%" style="color#0000;">'
 . '<span style="font-weight:bold; font-size:18pt;">' . utf8_decode($tituloEntrada) . '</span><td>';
echo '</tr>';
echo '</table>';

echo '<table  width="100%">';
echo '<tr>';
echo '<td width = "25%" style="color:#0000;"> <span style="font-weight: bold; font-size: 12pt;">Periodo:</span> <p> </td> ';
echo '<td width = "25%" style="text-aligb:left;"> <span style="font-weight: bold; font-size: 12pt;">DE: ' . $fechas->fecha1 . '</span> <p> </td>';
echo '<td width = "25%" style="color:#0000;"><span style="font-weight: bold; font-size: 12pt;">HASTA: ' . $fechas->fecha2 . '</span></td> ';
echo '<td width = "25%" style="text-aling: right";> </td>';
echo '<tr>';
echo '</tr>';
echo '</table>';

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
echo '<th width="5%">Fecha</th>';
echo '<th width="10%">Nombre</th>';
echo '<th width="10%">Localidad</th>';
echo '<th width="10%">idSagarpa</th>'; //daap
echo '<th width="10%">Tambor</th>';
echo '<th width="10%">Zona</th>';
echo '<th width="10%">P.Lista</th>';
echo '<th width="10%">Bruto</th>';
echo '<th width="10%">Tara</th>';
echo '<th width="10%">Neto</th>';
echo '<th width="10%">Dif</th>';
echo '</tr>';

$inform = $dao->EntradaTambores($fechas, $tipoCosecha);
$datosAlmacen = $conexion->prepare($inform);
$datosAlmacen->execute();

while ($resul = $datosAlmacen->fetch()) {
    $fecha        = $resul['fecha'];
    $nombre       = $resul['nombre'];
    $localidad    = $resul['localidad'];
    $idSagarpa    = $resul['idSagarpa']; //daap
    $tambor       = $resul['tambor'];
    $zona         = $resul['zona'];
    $pesoLista    = $resul['pesoLista'];
    $bruto        = $resul['bruto'];
    $tara         = $resul['tara'];
    $neto         = $resul['neto'];
    $dif          = $resul['diferencia'];


    echo '<tr>';
    echo '<td>' . $fecha . "</td>";
    echo '<td>' . $nombre . "</td>";
    echo '<td>' . $localidad . "</td>";
    echo '<td>' . $idSagarpa . "</td>";
    echo '<td>' . $tambor . "</td>";
    echo '<td>' . $zona . "</td>";
    echo '<td>' . $pesoLista . "</td>";
    echo '<td>' . $bruto . "</td>";
    echo '<td>' . $tara . "</td>";
    echo '<td>' . $neto . "</td>";
    echo '<td>' . $dif . "</td>";
    echo '</tr>';
}

echo '</table>';
