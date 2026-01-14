<?php

include_once '../clases/consultas.php';
$dao = new consultas();

$valorLocalidades = $_GET['valorLocalidades'];

$oLocalidad1 = new stdClass();
$oLocalidad1->valorLocalidad = $valorLocalidades;

//Fecha de exportacion
$fecha = date("d-m-Y");

//Inicio de la instacia de exportacion
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachmen; filename = Reporte de Entrada de Tambores y Cubetas por Todas las Localidades_$fecha.xls");
header("Prafma: no-cache");
header("Expires:0");

$titulo = "Reporte de Entrada de Tambores y Cubetas  por Todas las Localidades";

echo '<table width="100%">';
echo '<tr>';
echo '<td width = "50%" style="color#0000;">'
 . '<span style="font-weight:bold; font-size:18pt;">' . utf8_decode($titulo) . '</span><td>';
echo '</tr>';
echo '</table>';

if (isset($_GET['ambos'])) {
    if (isset($_GET['valorTodo'])) {
        $ambos = $_GET['ambos'];
        $oLocalidad1->ambos = $ambos;
        $todasLocalidades = $_GET['valorTodo'];
        $oLocalidad1->todasLocalidades = $todasLocalidades;
        $recipiente = 0;
        $oLocalidad1->recipiente = $recipiente;

        $detalleCubTamLoc = $dao->repxLocalidades($oLocalidad1);
    } else {
        $ambos = $_GET['ambos'];
        $oLocalidad1->ambos = $ambos;
        $todasLocalidades = 0;
        $oLocalidad1->todasLocalidades = $todasLocalidades;
        $recipiente = 0;
        $oLocalidad1->recipiente = $recipiente;
//        $fecha1 = $_GET['fecha1'];
//        $conve = DateTime::createFromFormat('d/m/Y', $fecha1);
//        $fInicial = $conve->format('Y-m-d');
//        $fecha2 = $_GET['fecha2'];
//        $conve1 = DateTime::createFromFormat('d/m/Y', $fecha2);
//        $fFinal = $conve1->format('Y-m-d');
        $fInicial = $_GET['fecha1'];
        $fFinal = $_GET['fecha2'];
        $oLocalidad1->fInicial = $fInicial;
        $oLocalidad1->fFinal = $fFinal;

        echo '<br>';
        echo '<table width ="100%">';
        echo '<tr>';
        echo '<td width="25%" style="color:#0000;"><span style="font-weight: bold; font-size: 12pt;">Periodo:</span></td>';
        echo '<td width="25%" style="text-aling:left"><span style="font-weight:bold; font-size: 12pt;">DE:' . $oLocalidad1->fInicial . '</span></td>';
        echo '<td width="25%" style="text-aling:left"><span style="font-weight:bold; font-size: 12pt;">HASTA:' . $oLocalidad1->fFinal . '</span></td>';
        echo '</tr>';
        echo '</table>';

        $detalleCubTamLoc = $dao->repxLocalidades($oLocalidad1);
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
    $filaLoc = mysql_num_rows($detalleCubTamLoc);
    if ($filaLoc > 0) {
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

        $contLoc = 0;
        $sumTara = 0;
        $sumBruto = 0;
        $SumaNeto = 0;
        $sumDif = 0;
        while ($detalleCubTamLoc1 = mysql_fetch_array($detalleCubTamLoc)) {
            $noEntraLoc = $detalleCubTamLoc1['idAlmacenEncabezado'];
            $fchaLoc = $detalleCubTamLoc1['fecha'];
            $provedorLoc = $detalleCubTamLoc1['nombre'];
            $idSagaLoc = $detalleCubTamLoc1['idSagarpa'];
            $recipienLoc = $detalleCubTamLoc1['idAlmacen'];
            $localidaLoc = $detalleCubTamLoc1['localidad'];
            $zonaLoc = $detalleCubTamLoc1['zona'];
            $pListaLoc = $detalleCubTamLoc1['pesoLista'];
            $brutoLoc = $detalleCubTamLoc1['bruto'];
            $taraLoc = $detalleCubTamLoc1['tara'];
            $netoLoc = $detalleCubTamLoc1['neto'];
            $diferLoc = $detalleCubTamLoc1['diferencia'];

            $contLoc = $contLoc + 1;
            $sumTara = $sumTara + $taraLoc;
            $sumBruto = $sumBruto + $brutoLoc;
            $SumaNeto = $SumaNeto + $netoLoc;
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
    echo '<td>'. number_format($sumTara,0,'.',',').'</td>';
    echo '<td>' . number_format($SumaNeto, 0, '.', ',') . '</td>';
    echo '<td>'. number_format($sumDif,0,'.',',').'</td>';
    echo '</tr>';
} else {
    echo 'No hay registros de Tambores y Cubetas de ninguna Zona';
}