<?php

include_once '../../../clases/consultas.php';
require_once '../../../DAOConeccion/conePDO.php';

$pdo = new conePDO();
$conexion = $pdo->conectar();

$consultaZonas = new ReportesDeAlmacen();

$idzona = $_GET['idzona'];
$tipoDeMiel = $_GET['tipoDeMiel'];
$nombreTipoDeMiel = $tipoDeMiel == '1' ? 'Miel 100% pura de abeja' : 'Miel 100% orgánica';

$objZona = new stdClass();
$objZona->idZona = $idzona;

// Fecha de exportacion
$fecha = date("d-m-y");

if (isset($_GET['valorTodo'])) {
    $todoZonas = $_GET['valorTodo'];
    $objZona->todo = $todoZonas;

} else {
    $fInicial = $_GET['fecha1'];
    $fFinal = $_GET['fecha2'];
    $todoZonas = 0;
    
    $objZona->fInicial = $fInicial;
    $objZona->fFinal = $fFinal;
    $objZona->todo = $todoZonas;
}
$objZona->tipoDeMiel = $tipoDeMiel;
$consultaTodasZonas = $consultaZonas->ReporteAmbosporZonas($objZona);
$dataZonasAmbos = $conexion->prepare($consultaTodasZonas);
$dataZonasAmbos->execute();
$contZonasAmbos = $dataZonasAmbos->rowCount();

if ($contZonasAmbos > 0) {

    //Inicio de la instacia de exportacion
    header('Content-type: application/vnd.ms-excel');
    header("Content-Disposition: attachment; filename=Reporte de Entrada de  Tambores y Cubetas por Zona_$fecha.xls");
    header("Prafma: no-cache");
    header("Expires:0");

    $titulo = 'Reporte de Entrada de Tambores y Cubetas por Zona. ' . $nombreTipoDeMiel;

    echo '<table  width="100%">';
    echo '<tr>';
    echo '<td width = "50%" style="color:#0000;"> <span style="font-weight: bold; font-size: 18pt;">' . utf8_decode($titulo) . '</span> </td> ';
    echo '</tr>';
    echo '</table>';

    if ($objZona->todo == 2) {
        echo '';
    } else {
        echo '<br/>';
        echo '<table  width="100%">';
        echo '<tr>';
        echo '<td width = "25%" style="color:#0000;"> <span style="font-weight: bold; font-size: 12pt;">Periodo:</span> <p> </td> ';
        echo '<td width = "25%" style="text-aligb:left;"> <span style="font-weight: bold; font-size: 12pt;">DE: ' . $objZona->fInicial . '</span> <p> </td>';
        echo '<td width = "25%" style="color:#0000;"><span style="font-weight: bold; font-size: 12pt;">HASTA: ' . $objZona->fFinal . '</span></td> ';
        echo '<td width = "25%" style="text-aling: right";> </td>';
        echo '</tr>';
        echo '</table>';
    }

    while ($datosEncabezado = $dataZonasAmbos->fetch()) {
        $encabezadozona = new stdClass();
        $encabezadozona->idZona = utf8_decode($datosEncabezado['zonaCam']);
    }

    echo '<br>';
    echo '<table  width="100%">';
    echo '<tr>';
    echo '<td> 
     <span style="font-size: 12 pt; color: #555555; font-family: sans;">Zona :<b>' . $encabezadozona->idZona . '</b></span></td>';
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
    echo '<th>Proveedor</th>';
    echo '<th>Localidad</th>';
    echo '<th>Zona</th>';
    echo '<th>T/C</th>';
    echo '<th>P.Lista</th>';
    echo '<th>Bruto</th>';
    echo '<th>Tara</th>';
    echo '<th>Neto</th>';
    echo '<th>Dif</th>';
    echo '</tr>';

    $detalleTodasZonas = $consultaZonas->ReporteAmbosporZonas($objZona);
    $datosTodasZonas = $conexion->prepare($detalleTodasZonas);
    $datosTodasZonas->execute();

    $cont = 0;
    $sumaTara = 0;
    $sumBruto = 0;
    $smNe = 0;
    $sumDif = 0;
    while ($infoTodasZonas = $datosTodasZonas->fetch()) {
        $nEstradaZona = $infoTodasZonas['idAlmacenEncabezado'];
        $fchEntrdaZona = $infoTodasZonas['fecha'];
        $recipienteZona = $infoTodasZonas['idAlmacen'];
        $proveedorZona = $infoTodasZonas['nombre'];
        $localidadZona = $infoTodasZonas['localidad'];
        $pesoListaZona = $infoTodasZonas['pesoLista'];
        $brutoZona = $infoTodasZonas['bruto'];
        $taraZona = $infoTodasZonas['tara'];
        $netoZona = $infoTodasZonas['neto'];
        $difeZona = $infoTodasZonas['diferencia'];
        $precioZona = $infoTodasZonas['precio'];
        $totalCosto = $infoTodasZonas['costoTotal'];
        $zona = $infoTodasZonas['zona'];

        $sumaTara = $sumaTara + $taraZona;
        $smNe = $smNe + $netoZona;
        $sumBruto = $sumBruto + $brutoZona;
        $sumDif = $sumDif + $difeZona;
        $cont = $cont + 1;

        echo '<tr>';
        echo '<td>' . $cont . '</td>';
        echo '<td>' . $nEstradaZona . '</td>';
        echo '<td>' . $fchEntrdaZona . '</td>';
        echo '<td>' . $proveedorZona . '</td>';
        echo '<td>' . $localidadZona . '</td>';
        echo '<td>' . $zona . ' </td>';
        echo '<td>' . $recipienteZona . '</td>';
        echo '<td>' . $pesoListaZona . '</td>';
        echo '<td>' . $brutoZona . '</td>';
        echo '<td>' . $taraZona . '</td>';
        echo '<td>' . number_format($netoZona, 2, '.', ',') . '</td>';
        echo '<td>' . $difeZona . '</td>';
    }

    echo '</table>';
    echo '</table>';
    echo '<table width="100%" style="text-align:left;">';
    echo '<tr>';
    echo '<td>Totales</td>';
    echo '<td></td>';
    echo '<td></td>';
    echo '<td></td>';
    echo '<td></td>';
    echo '<td></td>';
    echo '<td></td>';
    echo '<td></td>';
    echo '<td>' . number_format($sumBruto, 0, '.', ',') . '</td>';
    echo '<td>' . number_format($sumaTara, 0, '.', ',') . '</td>';
    echo '<td>' . number_format($smNe, 0, '.', ',') . '</td>';
    echo '<td>' . number_format($sumDif, 0, '.', ',') . '</td>';
    echo '<td></td>';
    echo '<td></td>';
} else {
    $valorNulo = 0;
    echo $json_response = json_encode($valorNulo);
}