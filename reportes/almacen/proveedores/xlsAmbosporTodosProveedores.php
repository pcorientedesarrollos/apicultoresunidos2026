<?php

include_once '../../../clases/consultas.php';
require_once '../../../DAOConeccion/conePDO.php';

$pdo = new conePDO();
$conexion = $pdo->conectar();

$consultaTamboresCubetasProveedores = new ReportesDeAlmacen();

$valorProveedor = $_GET['valorProveedores'];
$tipoDeMiel = $_GET['tipoDeMiel'];
$nombreTipoDeMiel = $tipoDeMiel == '1' ? 'Miel 100% pura de abeja' : 'Miel 100% orgánica';

$oProvedor = new stdClass();
$oProvedor->valorProveedor = $valorProveedor;

//Fecha de exportacion 
$fecha = date("d-m-Y");

if (isset($_GET['ambos'])) {
    if (isset($_GET['valorTodo'])) {
        $ambos = $_GET['ambos'];
        $oProvedor->ambos = $ambos;
        $todoProvedor = $_GET['valorTodo'];
        $oProvedor->todoProveedor = $todoProvedor;
        $recipiente = 0;
        $oProvedor->recipiente = $recipiente;
    } else {
        $ambos = $_GET['ambos'];
        $oProvedor->ambos = $ambos;
        $todoProvedor = 0;
        $oProvedor->todoProveedor = $todoProvedor;
        $recipiente = 0;
        $oProvedor->recipiente = $recipiente;

        $fInicial = $_GET['fecha1'];
        $fFinal = $_GET['fecha2'];
        $oProvedor->fInicial = $fInicial;
        $oProvedor->fFinal = $fFinal;
    }

    $oProvedor->tipoDeMiel = $tipoDeMiel;
    $consulTamboresCubetasProveedores = $consultaTamboresCubetasProveedores->ReportePorTodosProveedores($oProvedor);
    $dataTambCubProveedores = $conexion->prepare($consulTamboresCubetasProveedores);
    $dataTambCubProveedores->execute();
    $contTamboresCubetasProveedores = $dataTambCubProveedores->rowCount();

    if ($contTamboresCubetasProveedores > 0) {

        //Inicio de la instacia de exportacion
        header('Content-type: application/vnd.ms-excel');
        header("Content-Disposition: attachmen; filename = Reporte de Entrada de  Tambores y Cubetas por Todos los Proveedores_$fecha.xls");
        header("Prafma: no-cache");
        header("Expires:0");

        $titulo = "Reporte de Entrada de Tampores y Cubetas por Todos los Proveedores. $nombreTipoDeMiel";

        echo '<table width="100%">';
        echo '<tr>';
        echo '<td width = "50%" style="color#0000;">'
        . '<span style="font-weight:bold; font-size:18pt;">' . utf8_decode($titulo) . '</span><td>';
        echo '</tr>';
        echo '</table>';

        if (isset($_GET['valorTodo'])) {
            echo '';
        } else {
            echo '<br>';
            echo '<table width ="100%">';
            echo '<tr>';
            echo '<td width="25%" style="color:#0000;"><span style="font-weight: bold; font-size: 12pt;">Periodo:</span></td>';
            echo '<td width="25%" style="text-aling:left"><span style="font-weight:bold; font-size: 12pt;">DE:' . $oProvedor->fInicial . '</span></td>';
            echo '<td width="25%" style="text-aling:left"><span style="font-weight:bold; font-size: 12pt;">HASTA:' . $oProvedor->fFinal . '</span></td>';
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
        echo '<th>Localidad</th>';
        echo '<th>Zona</th>';
        echo '<th>T/C</th>';
        echo '<th>P.Lista</th>';
        echo '<th>Bruto</th>';
        echo '<th>Tara</th>';
        echo '<th>Neto</th>';
        echo '<th>Dif</th>';
        echo '</tr>';

        $contPrs = 0;
        $sumTara = 0;
        $SumaNe = 0;
        $sumBruto = 0;
        $sumDif = 0;

        while ($infoTamboresCubetasProveedores = $dataTambCubProveedores->fetch()) {
            $noEntrPrs = $infoTamboresCubetasProveedores['idAlmacenEncabezado'];
            $fchaPrs = $infoTamboresCubetasProveedores['fecha'];
            $provedores = $infoTamboresCubetasProveedores['nombre'];
            $idSagaPrs = $infoTamboresCubetasProveedores['idSagarpa'];
            $localiPrs = $infoTamboresCubetasProveedores['localidad'];
            $recipprs = $infoTamboresCubetasProveedores['idAlmacen'];
            $zonaPrs = $infoTamboresCubetasProveedores['zona'];
            $pListaPrs = $infoTamboresCubetasProveedores['pesoLista'];
            $brutoPrs = $infoTamboresCubetasProveedores['bruto'];
            $taraPrs = $infoTamboresCubetasProveedores['tara'];
            $netoPrs = $infoTamboresCubetasProveedores['neto'];
            $diferPrs = $infoTamboresCubetasProveedores['diferencia'];

            $contPrs = $contPrs + 1;
            $sumTara = $sumTara + $taraPrs;
            $sumBruto = $sumBruto + $brutoPrs;
            $SumaNe = $SumaNe + $netoPrs;
            $sumDif = $sumDif + $diferPrs;

            echo '<tr>';
            echo '<td>' . $contPrs . '</td>';
            echo '<td>' . $noEntrPrs . '</td>';
            echo '<td>' . $fchaPrs . '</td>';
            echo '<td>' . $provedores . '</td>';
            echo '<td>' . $idSagaPrs . '</td>';
            echo '<td>' . $localiPrs . '</td>';
            echo '<td>' . $zonaPrs . '</td>';
            echo '<td>' . $recipprs . '</td>';
            echo '<td>' . number_format($pListaPrs, 0, '.', ',') . '</td>';
            echo '<td>' . number_format($brutoPrs, 0, '.', ',') . '</td>';
            echo '<td>' . number_format($taraPrs, 0, '.', ',') . '</td>';
            echo '<td>' . number_format($netoPrs, 0, '.', ',') . '</td>';
            echo '<td>' . number_format($diferPrs, 0, '.', ',') . '</td>';
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
        echo '<td>' . number_format($sumBruto, 0, '.', ',') . '</td>';
        echo '<td>' . number_format($sumTara, 0, '.', ',') . '</td>';
        echo '<td>' . number_format($SumaNe, 0, '.', ',') . '</td>';
        echo '<td>' . number_format($sumDif, 0, '.', ',') . '</td>';
        echo '</tr>';
    } else {
        $valorNulo = 0;
        echo $json_response = json_encode($valorNulo);
    }
} else {
    echo 'No se puede generar el archivo hay un error';
}