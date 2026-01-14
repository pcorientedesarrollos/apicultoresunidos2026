<?php

include_once '../../../clases/consultas.php';
require_once '../../../DAOConeccion/conePDO.php';

$pdo = new conePDO();
$conexion = $pdo->conectar();

$consultaAmbosProveedor = new ReportesDeAlmacen();

$idProveedor = $_GET['idProveedor'];
$tipoDeMiel = $_GET['tipoDeMiel'];
$nombreTipoDeMiel = $tipoDeMiel == '1' ? 'Miel 100% pura de abeja' : 'Miel 100% orgánica';

$objeAmbosProveedor = new stdClass();
$objeAmbosProveedor->idProveedor = $idProveedor;

if (isset($_GET['valorTodo'])) {
    $todo = $_GET['valorTodo'];
    $objeAmbosProveedor->todo = $todo;
} else {

    $fInicial = $_GET['fecha1'];
    $fFinal = $_GET['fecha2'];
    $todo = 0;
    $objeAmbosProveedor->todo = $todo;
    $objeAmbosProveedor->fInicial = $fInicial;
    $objeAmbosProveedor->fFinal = $fFinal;
}

// Fecha de exportacion
$fecha = date("d-m-y");

$objeAmbosProveedor->tipoDeMiel = $tipoDeMiel;
$sqlAmbosProveedor = $consultaAmbosProveedor->ambosPorProveedor($objeAmbosProveedor);
$dataAmbosProveedor = $conexion->prepare($sqlAmbosProveedor);
$dataAmbosProveedor->execute();
$contAmbosProveedor = $dataAmbosProveedor->rowCount();

if ($contAmbosProveedor > 0) {

    header('Content-type: application/vnd.ms-excel');
    header("Content-Disposition: attachment; filename=Reporte de Entrada de Tambores y Cubetas por Proveedor_$fecha.xls");
    header("Prafma: no-cache");
    header("Expires:0");

    $titulo = 'Reporte de Entrada de Tambores y Cubetas por Proveedor. ' + $nombreTipoDeMiel;

    echo '<table  width="100%">';
    echo '<tr>';
    echo '<td width = "50%" style="color:#0000;"> <span style="font-weight: bold; font-size: 18pt;">' . utf8_decode($titulo) . '</span> </td> ';
    echo '</tr>';
    echo '</table>';
    if ($objeAmbosProveedor->todo == 2) {
        echo '';
    } else {
        echo '<br/>';
        echo '<table  width="100%">';
        echo '<tr>';
        echo '<td width = "25%" style="color:#0000;"> <span style="font-weight: bold; font-size: 12pt;">Periodo:</span> <p> </td> ';
        echo '<td width = "25%" style="text-aligb:left;"> <span style="font-weight: bold; font-size: 12pt;">DE: ' . $objeAmbosProveedor->fInicial . '</span> <p> </td>';
        echo '<td width = "25%" style="color:#0000;"><span style="font-weight: bold; font-size: 12pt;">HASTA: ' . $objeAmbosProveedor->fFinal . '</span></td> ';
        echo '<td width = "25%" style="text-aling: right";> </td>';
        echo '</tr>';
        echo '</table>';
    }

    while ($infoEncabezadoAmbos = $dataAmbosProveedor->fetch()) {
        $encabezadoAmbos = new stdClass();
        $encabezadoAmbos->proveedor = $infoEncabezadoAmbos['nombre'];
        $encabezadoAmbos->localidad = $infoEncabezadoAmbos['localidad'];
        $encabezadoAmbos->sagarpa = $infoEncabezadoAmbos['idSagarpa'];
    }

    echo '<table  width="100%"  style="font-family: serif;" cellpadding="5">';
    echo '<tr>';
    echo '<td width="49%" >
                    <span style="font-size: 7pt; color: #555555; font-family: sans;"></span><br />' .
    'Proveedor:<b> ' . $encabezadoAmbos->proveedor . '</b><br />' .
    'Localidad:<b> ' . $encabezadoAmbos->localidad . '</b><br />' .
    'ID Sagarpa:<b> ' . $encabezadoAmbos->sagarpa . '</b><br />
                </td>';
    echo '<td width="50%" > </td>';
    echo '</tr>';
    echo '</table>';

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
        <td>
                <p style="font-size:22px; font-weight:bold"> <b>' . utf8_decode('Código:') . 'RCO-RG-05</b> </p>
                </td>
                </tr>
            </table>';
    echo '<table  width="100%" border=1">';
    echo '<tr>';
    echo '<th>No</th>';
    echo '<th>No.Entrada</th>';
    echo '<th>Fecha</th>';
    echo '<th>Zona</th>';
    echo '<th>T/C</th>';
    echo '<th>P.Lista</th>';
    echo '<th>Bruto</th>';
    echo '<th>Tara</th>';
    echo '<th>Neto</th>';
    echo '<th>Dif</th>';
    echo '</tr>';


    $con = 0;
    $sumaTara = 0;
    $sumaBruto = 0;
    $SumaNe = 0;
    $sumaDif = 0;
    $sqlInfoAmbos = $consultaAmbosProveedor->ambosPorProveedor($objeAmbosProveedor);
    $datosAmbosProveedor = $conexion->prepare($sqlInfoAmbos);
    $datosAmbosProveedor->execute();
    while ($infoAmbos = $datosAmbosProveedor->fetch()) {
        $nEntrada = $infoAmbos['idAlmacenEncabezado'];
        $fchaEntrada = $infoAmbos['fecha'];
        $recipienteAmbos = $infoAmbos['idAlmacen'];
        $zonaAmbos = $infoAmbos['zona'];
        $pListaAmbos = $infoAmbos['pesoLista'];
        $brutoAmbos = $infoAmbos['bruto'];
        $taraAmbos = $infoAmbos['tara'];
        $netoAmbos = $infoAmbos['neto'];
        $diferenciaAmb = $infoAmbos['diferencia'];


        $sumaTara = $sumaTara + $taraAmbos;
        $SumaNe = $SumaNe + $netoAmbos;
        $sumaBruto = $sumaBruto + $brutoAmbos;
        $sumaDif = $sumaDif + $diferenciaAmb;
        $con = $con + 1;

        echo '<tr>';
        echo '<td>' . $con . '</td>';
        echo '<td>' . $nEntrada . '</td>';
        echo '<td>' . $fchaEntrada . '</td>';
        echo '<td>' . $zonaAmbos . '</td>';
        echo '<td>' . $recipienteAmbos . '</td>';
        echo '<td>' . $pListaAmbos . '</td>';
        echo '<td>' . $brutoAmbos . '</td>';
        echo '<td>' . $taraAmbos . '</td>';
        echo '<td>' . number_format($netoAmbos, 2, '.', ',') . '</td>';
        echo '<td>' . $diferenciaAmb . '</td>';
    }

    echo '</table>';
    echo '<table width="100%" style="text-align:left;">';
    echo '<tr>';
    echo '<td>Totales:</td>';
    echo '<td></td>';
    echo '<td></td>';
    echo '<td></td>';
    echo '<td></td>';
    echo '<td></td>';
    echo '<td>' . number_format($sumaBruto, 0, '.', ',') . '</td>';
    echo '<td>' . number_format($sumaTara, 0, '.', ',') . '</td>';
    echo '<td>' . number_format($SumaNe, 0, '.', ',') . '</td>';
    echo '<td>' . number_format($sumaDif, 0, '.', ',') . '</td>';
    echo '<td></td>';
    echo '</tr>';
} else {
     
        $valorNulo = 0;
    echo $json_response = json_encode($valorNulo);
}