<?php

include_once '../../../clases/consultas.php';
require_once '../../../DAOConeccion/conePDO.php';

$pdo = new conePDO();
$conexion = $pdo->conectar();

$consultaAmbosTodasZonas = new ReportesDeAlmacen();

$valorZonas = $_GET['valorZonas'];
$tipoDeMiel = $_GET['tipoDeMiel'];
$nombreTipoDeMiel = $tipoDeMiel == '1' ? 'Miel 100% pura de abeja' : 'Miel 100% orgánica';

$oZona = new stdClass();
$oZona->valorZonas = $valorZonas;

//Fecha de exportacion
$fecha = date("d-m-Y");

if (isset($_GET['ambos'])) {
    if (isset($_GET['valorTodo'])) {
        $ambos = $_GET['ambos'];
        $oZona->ambos = $ambos;
        $todoZona = $_GET['valorTodo'];
        $oZona->todaZona = $todoZona;
        $recipiente = 0;
        $oZona->recipiente = $recipiente;
    } else {
        $ambos = $_GET['ambos'];
        $oZona->ambos = $ambos;
        $todoZona = 0;
        $oZona->todaZona = $todoZona;
        $recipiente = 0;
        $oZona->recipiente = $recipiente;
        $fInicial = $_GET['fecha1'];
        $fFinal = $_GET['fecha2'];
        $oZona->fInicial = $fInicial;
        $oZona->fFinal = $fFinal;
    }

    $oZona->tipoDeMiel = $tipoDeMiel;
    $consultaAmbosporTodasZonas = $consultaAmbosTodasZonas->ReporteporTodasZonas($oZona);
    $dataAmbosTodasZonas = $conexion->prepare($consultaAmbosporTodasZonas);
    $dataAmbosTodasZonas->execute();
    $contAmbosZonas = $dataAmbosTodasZonas->rowCount();

    if ($contAmbosZonas > 0) {

        //Inicio de la instancia de exportacion
        header('Content-type: application/vnd.ms-excel');
        header("Content-Disposition: attachmen; filename = Reporte de Entrada de Tambores y Cubetas por Todas las Zonas_$fecha.xls");
        header("Prafma: no-cache");
        header("Expires:0");

        $titulo = "Reporte de Entrada de Tambores y Cubetas  por Todas las Zonas. $nombreTipoDeMiel";

        echo '<table width="100%">';
        echo '<tr>';
        echo '<td width = "50%" style="color#0000;">'
        . '<span style="font-weight:bold; font-size:18pt;">' . utf8_decode($titulo) . '</span><td>';
        echo '</tr>';
        echo '</table>';

        if ($oZona->todaZona == 2) {
            echo '';
        } else {
            echo '<br/>';
            echo '<table width ="100%">';
            echo '<tr>';
            echo '<td width="25%" style="color:#0000;"><span style="font-weight: bold; font-size: 12pt;">Periodo:</span></td>';
            echo '<td width="25%" style="text-aling:left"><span style="font-weight:bold; font-size: 12pt;">DE:' . $oZona->fInicial . '</span></td>';
            echo '<td width="25%" style="text-aling:left"><span style="font-weight:bold; font-size: 12pt;">HASTA:' . $oZona->fFinal . '</span></td>';
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
        echo '<table width="100%" border="1">';
        echo '<tr>';
        echo '<th>No</th>';
        echo '<th>No.Entrada</th>';
        echo '<th>Fecha</th>';
        echo '<th>Proveedor</th>';
        echo '<th>Sagarpa</th>';
        echo '<th>Zona del Proveedor</th>';
        echo '<th>Localidad</th>';
        echo '<th>Zona</th>';
        echo '<th>T/C</th>';
        echo '<th>P.Lista</th>';
        echo '<th>Bruto</th>';
        echo '<th>Tara</th>';
        echo '<th>Neto</th>';
        echo '<th>Dif</th>';
        echo '</tr>';

        $contZona = 0;
        $sumaTara = 0;
        $sumaBruto = 0;
        $SumaNeto = 0;
        $sumaDif = 0;
        while ($datosAmbosporZonas = $dataAmbosTodasZonas->fetch()) {
            $noEntradaZonas     = $datosAmbosporZonas['idAlmacenEncabezado'];
            $fchaEntradaZonas   = $datosAmbosporZonas['fecha'];
            $provedorZonas      = $datosAmbosporZonas['nombre'];
            $idSagaZonas        = $datosAmbosporZonas['idSagarpa'];
            $recipienZonas      = $datosAmbosporZonas['idAlmacen'];
            $zonaProveedorZonas = $datosAmbosporZonas['zonaCam'];
            $localidadZonas     = $datosAmbosporZonas['localidad'];
            $zona               = $datosAmbosporZonas['zona'];
            $pesoListaZonas     = $datosAmbosporZonas['pesoLista'];
            $brutoZonas         = $datosAmbosporZonas['bruto'];
            $taraZonas          = $datosAmbosporZonas['tara'];
            $netoZonas          = $datosAmbosporZonas['neto'];
            $difereZonas        = $datosAmbosporZonas['diferencia'];

            $contZona = $contZona + 1;
            $sumaTara = $sumaTara + $taraZonas;
            $sumaBruto = $sumaBruto + $brutoZonas;
            $SumaNeto = $SumaNeto + $netoZonas;
            $sumaDif  = $sumaDif + $difereZonas;

            echo '<tr>';
            echo '<td>' . $contZona . '</td>';
            echo '<td>' . $noEntradaZonas . '</td>';
            echo '<td>' . $fchaEntradaZonas . '</td>';
            echo '<td>' . $provedorZonas . '</td>';
            echo '<td>' . $idSagaZonas . '</td>';
            echo '<td>' . $zonaProveedorZonas . '</td>';
            echo '<td>' . $localidadZonas . '</td>';
            echo '<td>' . $zona . '</td>';
            echo '<td>' . $recipienZonas . '</td>';
            echo '<td>' . number_format($pesoListaZonas, 0, '.', ',') . '</td>';
            echo '<td>' . number_format($brutoZonas, 0, '.', ',') . '</td>';
            echo '<td>' . number_format($taraZonas, 0, '.', ',') . '</td>';
            echo '<td>' . number_format($netoZonas, 0, '.', ',') . '</td>';
            echo '<td>' . number_format($difereZonas, 0, '.', ',') . '</td>';
            echo '</tr>';
        }

        echo '</table>';
        echo '<table width="100%" style="text-align:left;">';
        echo '<tr>';
        echo '<td>Totales :</td>';
        echo '<td></td>';
        echo '<td></td>';
        echo '<td></td>';
        echo '<td></td>';
        echo '<td></td>';
        echo '<td></td>';
        echo '<td></td>';
        echo '<td></td>';
        echo '<td></td>';
        echo '<td>' . number_format($sumaBruto, 0, '.', ',') . '</td>';
        echo '<td>' . number_format($sumaTara, 0, '.', ',') . '</td>';
        echo '<td>' . number_format($SumaNeto, 0, '.', ',') . '</td>';
        echo '<td>' . number_format($sumaDif, 0, '.', ',') . '</td>';
        echo '</tr>';
    } else {
        $valorNulo = 0;
        echo $json_response = json_encode($valorNulo);
    }
}