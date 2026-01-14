<?php

include_once '../../../clases/consultas.php';
require_once '../../../DAOConeccion/conePDO.php';

$pdo = new conePDO();
$conexion = $pdo->conectar();

$consultaZonas = new ReportesDeAlmacen();

$valorZona = $_GET['valorZonas'];
$recipiente = $_GET['recipiente'];
$tipoDeMiel = $_GET['tipoDeMiel'];
$nombreTipoDeMiel = $tipoDeMiel == '1' ? 'Miel 100% pura de abeja' : 'Miel 100% orgánica';

$oZona = new stdClass();
$oZona->valorZona = $valorZona;
$oZona->recipiente = $recipiente;

//Fecha de exportacion
$fecha = date("d-m-y");

if (isset($_GET['valorTodo'])) {
    $todaZona = $_GET['valorTodo'];
    $oZona->todaZona = $todaZona;
    $ambos = 0;
    $oZona->ambos = $ambos;
} else {
    $todaZona = 0;
    $oZona->todaZona = $todaZona;
    $ambos = 0;
    $oZona->ambos = $ambos;

    $fInicial = $_GET['fecha1'];
    $fFinal = $_GET['fecha2'];
    $oZona->fInicial = $fInicial;
    $oZona->fFinal = $fFinal;
}

$oZona->tipoDeMiel = $tipoDeMiel;
$consultaZona = $consultaZonas->ReporteporTodasZonas($oZona);
$dataZonas = $conexion->prepare($consultaZona);
$dataZonas->execute();
$contZonas = $dataZonas->rowCount();

if ($contZonas > 0) {

    if ($oZona->recipiente == 3) {
        $ths = 'Tambor';
        $contenedorZ = "Tambores";
    } else {
        $ths = 'Cubeta';
        $contenedorZ = "Cubetas";
    }

    //Inicio de la instancia para la exportacion

    header('Content-type: application/vnd.ms-excel');
    header("Content-Disposition: attachment; filename=Reporte de entrada de $contenedorZ por Zonas_$fecha.xls");
    header("Prafma: no-cache");
    header("Expires:0");

    $Titulo = "Reporte de Entrada de $contenedorZ por Zonas. $nombreTipoDeMiel";

    echo '<table width="100%">';
    echo '<tr>';
    echo '<td witdh = "50%" style="color#0000;"><span style="font-weight:bold; font-size:18pt;">' . utf8_decode($Titulo) . '</span></td>';
    echo '</tr>';
    echo '</table>';

    if ($oZona->todaZona == 2) {
        echo '';
    } else {
        echo '<table width="100%"';
        echo '<tr>';
        echo '<td width = "25%" style="color:#0000;"> <span style="font-weight: bold; font-size: 12pt;">Periodo:</span> <p> </td> ';
        echo '<td width = "25%" style="text-aligb:left;"> <span style="font-weight: bold; font-size: 12pt;">DE: ' . $oZona->fInicial . '</span> <p> </td>';
        echo '<td width = "25%" style="color:#0000;"><span style="font-weight: bold; font-size: 12pt;">HASTA: ' . $oZona->fFinal . '</span></td> ';
        echo '<td width = "25%" style="text-aling: right";> </td>';
        echo '</tr>';
        echo '</table>';
    }

    // echo '<br>';
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
    echo '<table  width="100%" border=1">';
    echo '<tr>';
    echo '<th>No</th>';
    echo '<th>Zona del Provedor</th>';
    echo '<th>No.Entrada</th>';
    echo '<th>Fecha</th>';
    echo '<th>Proveedor</th>';
    echo '<th>ID.Sagarpa</th>';
    echo '<th>Localidad</th>';
    echo '<th>Zona</th>';
    echo '<th>' . $ths . '</th>';
    echo '<th>P.Lista</th>';
    echo '<th>Bruto</th>';
    echo '<th>Tara</th>';
    echo '<th>Neto</th>';
    echo '<th>Dif</th>';
    echo '</tr>';

    $consultaDatosZona = $consultaZonas->ReporteporTodasZonas($oZona);
    $inforZonas = $conexion->prepare($consultaDatosZona);
    $inforZonas->execute();

    $cont = 0;
    $sumTara = 0;
    $sumBrutoTZ = 0;
    $sumaNetoTZ = 0;
    $sumaDif = 0;

    while ($datosZonas = $inforZonas->fetch()) {
        $noEntraZonas = $datosZonas['idAlmacen'];
        $fchEntraZonas = $datosZonas['fecha'];
        $provedoresZonas = $datosZonas['nombre'];
        $sagarpaZonas = $datosZonas['idSagarpa'];
        $zonaCompradorZonas = $datosZonas['zonaCom'];
        $localiZonas = $datosZonas['localidad'];
        $recipienteZonas = $datosZonas['tambor'];
        $zona = $datosZonas['zona'];
        $pesoListaZonas = $datosZonas['pesoLista'];
        $brutoZonas = $datosZonas['bruto'];
        $taraZonas = $datosZonas['tara'];
        $netoZonas = $datosZonas['neto'];
        $diferZonas = $datosZonas['diferencia'];

        $cont = $cont + 1;
        $sumTara = $sumTara + $taraZonas;
        $sumBrutoTZ = $sumBrutoTZ + $brutoZonas;
        $sumaNetoTZ = $sumaNetoTZ + $netoZonas;
        $sumaDif = $sumaDif + $diferZonas;

        echo '<tr>';
        echo '<td>' . $cont . '</td>';
        echo '<td>' . $zonaCompradorZonas . "</td>";
        echo '<td>' . $noEntraZonas . "</td>";
        echo '<td>' . $fchEntraZonas . "</td>";
        echo '<td>' . $provedoresZonas . "</td>";
        echo '<td>' . $sagarpaZonas . "</td>";
        echo '<td>' . $localiZonas . "</td>";
        echo '<td>' . $zona . "</td>";
        echo '<td>' . $recipienteZonas . "</td>";
        echo '<td>' . number_format($pesoListaZonas, 0, '.', ',') . "</td>";
        echo '<td>' . number_format($brutoZonas, 0, '.', ',') . "</td>";
        echo '<td>' . number_format($taraZonas, 0, '.', ',') . "</td>";
        echo '<td>' . number_format($netoZonas) . "</td>";
        echo '<td>' . number_format($diferZonas, 0, '.', ',') . "</td>";
        echo '</tr>';
    }

    echo '</table>';
    echo '<table width="100%" style="text-align:left;">';
    echo '<tr>';
    echo '<td>Totales : </td>';
    echo '<td></td>';
    echo '<td></td>';
    echo '<td></td>';
    echo '<td></td>';
    echo '<td></td>';
    echo '<td></td>';
    echo '<td></td>';
    echo '<td></td>';
    echo '<td></td>';
    echo '<td>' . number_format($sumBrutoTZ, 0, '.', ',') . "</td>";
    echo '<td>' . number_format($sumTara, 0, '.', ',') . '</td>';
    echo '<td>' . number_format($sumaNetoTZ, 0, '.', ',') . "</td>";
    echo '<td>' . number_format($sumaDif, 0, '.', ',') . '</td>';
    echo '</tr>';
} else {
    $valorNulo = 0;
    echo $json_response = json_encode($valorNulo);
}