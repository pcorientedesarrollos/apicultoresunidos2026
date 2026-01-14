<?php

include_once '../clases/consultas.php';
$dao = new consultas();
$id = $_GET['idRequisicion'];

//Fecha de exportacion
$fecha = date("d-m-y");

//Inicio de la instancia para la exportacion

$TabR = $dao->RTablaDeposito($id);
$totls = $dao->Rencabezado($id);
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=Requerimientos de Depósito de Compra_$fecha.xls");
header("Prafma: no-cache");
header("Expires:0");

$Titulo = 'Requerimientos de Depósito de Compra';

echo '<table  width="100%">';
echo '<tr>';
//echo '<td width = "25%" style="text-aligb:left;"> <p><img src="./reportes/img/oaxaca.png"> <p> </td>';
echo '<td width = "50%" style="color:#0000;"> <span style="font-weight: bold; font-size: 18pt;">' . utf8_decode($Titulo) . '</span> </td> ';
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
    <td>
            <p style="font-size:22px; font-weight:bold"> <b>' . utf8_decode('Código:') . 'DCO-RD-06</b> </p>
            </td>
            </tr>
        </table>';
echo '<table  width="100%" border=1">';
echo '<tr>';
echo '<th>Proveedor</th>';
echo '<th>Localidad</th>';
echo '<th>Miel</th>';
echo '<th>Tambores</th>';
echo '<th>Peso</th>';
echo '<th>Precio</th>';
echo '<th>Importe</th>';
echo '<th>Banco</th>';
echo '<th>Observacion</th>';
echo '</tr>';


$cont = 1;
//while ($Tabs = mysql_fetch_array($TabR)) {
while ($Tabs = $TabR->fetch()) {

    $pr =  utf8_decode($Tabs['nombre']);
    $loc =   utf8_decode($Tabs['localidad']);
    $miel =  utf8_decode($Tabs['tipoDeMiel']);    
    $noT = $Tabs['noTambores'];
    $pes = number_format($Tabs['peso'], 2, '.', ',');
    $pre = number_format($Tabs['precio'], 2, '.', ',');
    $imp = number_format($Tabs['importe'], 2, '.', ',');
    $ban =  utf8_decode($Tabs['banco']);
    $obs =  utf8_decode($Tabs['observaciones']);



    echo '<tr>';
    echo '<td>' . $pr . "</td>";
    echo '<td>' . $loc . "</td>";
    echo '<td>' . $miel . "</td>";    
    echo '<td>' . $noT . "</td>";
    echo '<td>' . $pes . "</td>";
    echo '<td>' . $pre . "</td>";
    echo '<td>' . $imp . "</td>";
    echo '<td>' . $ban . "</td>";
    echo '<td>' . $obs . "</td>";
    echo '</tr>';
}


echo '</table>';

echo '<table width="100%" style="text-align:left;">';




//while ($tol = mysql_fetch_array($totls)) {
while ($tol = $totls->fetch()) {
    $importe = number_format($tol['importeTotal'], 2, '.', ',');
    $tambores = number_format($tol['totalTambores'], 2, '.', ',');
    $kilos = number_format($tol ['totalKilos'], 2, '.', ',');
}

echo '<tr>';
echo "<td>TOTALES:</td>";
echo "<td></td>";
echo "<td></td>";
echo "<td>" . $tambores . "</td>";
echo "<td>" . $kilos . "</td>";
echo "<td></td>";
echo "<td>$" . $importe . "</td>";
echo '</tr>';
