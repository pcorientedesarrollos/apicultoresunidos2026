<?php

include_once '../clases/consultas.php';
$dao =new consultas();

$fchInicial ="2017-01-04";
$fchFinal ="2017-01-25";

//Fecha de exportacion
$fecha = date("d-m-y");

//Inicio de la instancia para la exportacion

header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=Reporte de Entrada por Fecha_$fecha.xls");
header("Prafma: no-cache");
header("Expires:0");

$Titulo = 'Reporte de Entrada por Fecha';

echo '<table  width="100%">';
echo '<tr>';
echo '<td width = "50%" style="color:#0000;"> <span style="font-weight: bold; font-size: 18pt;">'. utf8_decode($Titulo).'</span> </td> ';
echo '</tr>';
echo '</table>';



echo '<br>';
echo '<table  width="100%" border=1">';
echo '<tr>';
echo '<th>No.Entrada</th>';
echo '<th>Fecha</th>';
echo '<th>Proveedor</th>';
echo '<th>ID.Sagarpa</th>';
echo '<th>Localidad</th>';
echo '<th>Zona</th>';
echo '<th>Tambor</th>';
echo '<th>P.Lista</th>';
echo '<th>Bruto</th>';
echo '<th>Tara</th>';
echo '<th>Neto</th>';
echo '<th>Dif</th>';
echo '</tr>';

$rePers = $dao->repFecha($fchInicial, $fchFinal);
while ($rxper = mysql_fetch_array($rePers)){
    $noEn     = $rxper['idAlmacen'];
    $fchEntra = $rxper['fecha'];
    $pro      = $rxper['nombre'];
    $sag      = $rxper['idSagarpa'];
    $loc      = $rxper['localidad'];
    $tam      = $rxper['tambor'];
    $zns      = $rxper['zona'];
    $pl       = $rxper['pesoLista'];
    $br       = $rxper['bruto'];
    $tar      = $rxper['tara'];
    $net      = $rxper['neto'];
    $dif      = $rxper['diferencia'];
    
    
    echo '<tr>';
    echo '<td>' . $noEn . "</td>";
    echo '<td>' . $fchEntra . "</td>";
    echo '<td>' . $pro . "</td>";
    echo '<td>' . $sag . "</td>";
    echo '<td>' . $loc . "</td>";
    echo '<td>' . $zns . "</td>";
    echo '<td>' . $tam . "</td>";
    echo '<td>' . $pl . "</td>";
    echo '<td>' . $br . "</td>";
    echo '<td>' . $tar . "</td>";
    echo '<td>' . $net . "</td>";
    echo '<td>' . $dif . "</td>";
    echo '</tr>';
}