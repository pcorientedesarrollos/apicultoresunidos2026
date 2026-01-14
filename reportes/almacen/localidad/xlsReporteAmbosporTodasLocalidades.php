<?php

include_once '../../../clases/consultas.php';
require_once '../../../DAOConeccion/conePDO.php';

$pdo = new conePDO();
$conexion = $pdo->conectar();

$consultaLocalidad = new ReportesDeAlmacen();

$valorLocalidades = $_GET['valorLocalidades'];
$tipoDeMiel = $_GET['tipoDeMiel'];
$nombreTipoDeMiel = $tipoDeMiel == '1' ? 'Miel 100% pura de abeja' : 'Miel 100% orgánica';

$oLocalidad1 = new stdClass();
$oLocalidad1->valorLocalidad = $valorLocalidades;

//Fecha de exportacion
$fecha = date("d-m-Y");

if (isset($_GET['ambos'])) {
    if (isset($_GET['valorTodo'])) {
        $ambos = $_GET['ambos'];
        $todasLocalidades = $_GET['valorTodo'];
        $recipiente = 0;

        $oLocalidad1->ambos = $ambos;
        $oLocalidad1->todasLocalidades = $todasLocalidades;
        $oLocalidad1->recipiente = $recipiente;

//        $detalleCubTamLoc = $dao->repxLocalidades($oLocalidad1);
    } else {
        $ambos = $_GET['ambos'];
        $todasLocalidades = 0;
        $recipiente = 0;
        $fInicial = $_GET['fecha1'];
        $fFinal = $_GET['fecha2'];

        $oLocalidad1->ambos = $ambos;
        $oLocalidad1->todasLocalidades = $todasLocalidades;
        $oLocalidad1->recipiente = $recipiente;
        $oLocalidad1->fInicial = $fInicial;
        $oLocalidad1->fFinal = $fFinal;
//        $detalleCubTamLoc = $dao->repxLocalidades($oLocalidad1);
    }

    $oLocalidad1->tipoDeMiel = $tipoDeMiel;
    $consultasAmbosToda = $consultaLocalidad->reportePorLocalidades($oLocalidad1);
    $datosContenido = $conexion->prepare($consultasAmbosToda);
    $datosContenido->execute();
    $contAmbosTodas = $datosContenido->rowCount();

    if ($contAmbosTodas > 0) {
        // Inicio de la instacia de exportacion
        header('Content-type: application/vnd.ms-excel');
        header("Content-Disposition: attachmen; filename = Reporte de Entrada de Tambores y Cubetas por Todas las Localidades_$fecha.xls");
        header("Prafma: no-cache");
        header("Expires:0");

        $titulo = "Reporte de Entrada de Tambores y Cubetas  por Todas las Localidades. $nombreTipoDeMiel";

        echo '<table width="100%">';
        echo '<tr>';
        echo '<td width = "50%" style="color#0000;">'
        . '<span style="font-weight:bold; font-size:18pt;">' . utf8_decode($titulo) . '</span><td>';
        echo '</tr>';
        echo '</table>';

        if ($oLocalidad1->todasLocalidades == 2) {
            echo '';
        } else {
            echo '<br>';
            echo '<table width ="100%">';
            echo '<tr>';
            echo '<td width="25%" style="color:#0000;"><span style="font-weight: bold; font-size: 12pt;">Periodo:</span></td>';
            echo '<td width="25%" style="text-aling:left"><span style="font-weight:bold; font-size: 12pt;">DE:' . $oLocalidad1->fInicial . '</span></td>';
            echo '<td width="25%" style="text-aling:left"><span style="font-weight:bold; font-size: 12pt;">HASTA:' . $oLocalidad1->fFinal . '</span></td>';
            echo '</tr>';
            echo '</table>';
        }
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
        // echo '<br>';
        echo '<table width="100%" border="1">';
        echo '<tr>';
        echo '<th>No</th>';
        echo '<th>No.Entrada</th>';
        echo '<th>Fecha</th>';
        echo '<th>Proveedor</th>';
        echo '<th>Sagarpa</th>';
        echo '<th>Localidad</th>';
        echo '<th>Zona</th>';
        echo '<th>T/C</th>';
        echo '<th>P.Lista</th>';
        echo '<th>Bruto</th>';
        echo '<th>Tara</th>';
        echo '<th>Neto</th>';
        echo '<th>Dif</th>';
        echo '</tr>';

        $consultaDatosAmbos = $consultaLocalidad->reportePorLocalidades($oLocalidad1);
        $datosAmbosTabla = $conexion->prepare($consultaDatosAmbos);
        $datosAmbosTabla->execute();

        $contLoc = 0;
        $sumTara = 0;
        $sumBruto = 0;
        $sumaNeto = 0;
        $sumDif = 0;

        while ($infoDatosAmbos = $datosAmbosTabla->fetch()) {
            $noEntraLoc  = $infoDatosAmbos['idAlmacenEncabezado'];
            $fchaLoc     = $infoDatosAmbos['fecha'];
            $provedorLoc = $infoDatosAmbos['nombre'];
            $idSagaLoc   = $infoDatosAmbos['idSagarpa'];
            $recipienLoc = $infoDatosAmbos['idAlmacen'];
            $localidaLoc = $infoDatosAmbos['localidad'];
            $zonaLoc     = $infoDatosAmbos['zona'];
            $pListaLoc   = $infoDatosAmbos['pesoLista'];
            $brutoLoc    = $infoDatosAmbos['bruto'];
            $taraLoc     = $infoDatosAmbos['tara'];
            $netoLoc     = $infoDatosAmbos['neto'];
            $diferLoc    = $infoDatosAmbos['diferencia'];

            $contLoc = $contLoc + 1;
            $sumTara = $sumTara + $taraLoc;
            $sumBruto = $sumBruto + $brutoLoc;
            $sumaNeto = $sumaNeto + $netoLoc;
            $sumDif = $sumDif + $diferLoc;

            echo '<tr>';
            echo '<td>' . $contLoc . '</td>';
            echo '<td>' . $noEntraLoc . '</td>';
            echo '<td>' . $fchaLoc . '</td>';
            echo '<td>' . $provedorLoc . '</td>';
            echo '<td>' . $idSagaLoc . '</td>';
            echo '<td>' . $localidaLoc . '</td>';
            echo '<td>' . $zonaLoc . '</td>';
            echo '<td>' . $recipienLoc . '</td>';
            echo '<td>' . number_format($pListaLoc, 0, '.', ',') . '</td>';
            echo '<td>' . number_format($brutoLoc, 0, '.', ',') . '</td>';
            echo '<td>' . number_format($taraLoc, 0, '.', ',') . '</td>';
            echo '<td>' . number_format($netoLoc, 0, '.', ',') . '</td>';
            echo '<td>' . number_format($diferLoc, 0, '.', ',') . '</td>';
            echo '</tr>';
        }

        echo '</table>';
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
        echo '<td>' . number_format($sumBruto, 0, '.', ',') . '</td>';
        echo '<td>' . number_format($sumTara, 0, '.', ',') . '</td>';
        echo '<td>' . number_format($sumaNeto, 0, '.', ',') . '</td>';
        echo '<td>' . number_format($sumDif, 0, '.', ',') . '</td>';
        echo '</tr>';
    } else {
        $valorNulo = 0;
        echo $json_response = json_encode($valorNulo);
    }
} else {
    $valorNulo = 0;
    echo $json_response = json_encode($valorNulo);
}