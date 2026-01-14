<?php

include_once '../../../clases/consultas.php';
require_once '../../../DAOConeccion/conePDO.php';

$pdo = new conePDO();
$conexion = $pdo->conectar();

$consultaTodoProveedores = new ReportesDeAlmacen();

$recipiente = $_GET['recipiente'];
$valorProveedor = $_GET['valorProveedores'];
$tipoDeMiel = $_GET['tipoDeMiel'];
$nombreTipoDeMiel = $tipoDeMiel == '1' ? 'Miel 100% pura de abeja' : 'Miel 100% orgánica';
//Fecha de exportacion 
$fecha = date("d-m-Y");

$oProvedor = new stdClass();
$oProvedor->valorProveedor = $valorProveedor;
$oProvedor->recipiente = $recipiente;

if (isset($_GET['valorTodo'])) {
    $todoProvedor = $_GET['valorTodo'];
    $oProvedor->todoProveedor = $todoProvedor;
    $ambos = 0;
    $oProvedor->ambos = $ambos;
} else {
    $todoProvedor = 0;
    $oProvedor->todoProveedor = $todoProvedor;
    $ambos = 0;
    $oProvedor->ambos = $ambos;

    $fInicial = $_GET['fecha1'];
    $fFinal = $_GET['fecha2'];
    $oProvedor->fInicial = $fInicial;
    $oProvedor->fFinal = $fFinal;
}
$oProvedor->tipoDeMiel = $tipoDeMiel;
$consultaTodosProveedores = $consultaTodoProveedores->ReportePorTodosProveedores($oProvedor);
$dataProveedores = $conexion->prepare($consultaTodosProveedores);
$dataProveedores->execute();
$contTodosProveedores = $dataProveedores->rowCount();

if ($contTodosProveedores > 0) {

    if ($oProvedor->recipiente == 3) {
        $EnvasadoContenedor = 'Tambor';
        $AnexoTitulo = 'Tambores';
    } else {
        $EnvasadoContenedor = 'Cubeta';
        $AnexoTitulo = 'Cubetas';
    }

    //Inicio de la instacia de exportacion
    header('Content-type: application/vnd.ms-excel');
    header("Content-Disposition: attachmen; filename = Reporte de Entrada de $AnexoTitulo  por todos los Proveedores_$fecha.xls");
    header("Prafma: no-cache");
    header("Expires:0");

    $titulo = "Reporte de Entrada de $EnvasadoContenedor por Todos los Proveedores. $nombreTipoDeMiel";

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
    echo '<th>' . $EnvasadoContenedor . '</th>';
    echo '<th>P.Lista</th>';
    echo '<th>Bruto</th>';
    echo '<th>Tara</th>';
    echo '<th>Neto</th>';
    echo '<th>Dif</th>';
    echo '</tr>';

    $contProveedores = 0;
    $sumConte = 0;
    $sumPlista = 0;
    $sumBruto = 0;
    $sumTara = 0;
    $sumaNeto = 0;
    $sumDif = 0;

    while ($infoProveedores = $dataProveedores->fetch()) {

        $noEntradaProveedores = $infoProveedores['idAlmacen'];
        $fchEntradaProveedores = $infoProveedores['fecha'];
        $provedores = $infoProveedores['nombre'];
        $idSagarpaProveedores = $infoProveedores['idSagarpa'];
        $localidadProveedores = $infoProveedores['localidad'];
        $contenedorProveedores = $infoProveedores['tambor'];
        $zonaProveedores = $infoProveedores['zona'];
        $pesoLidstaProvedores = $infoProveedores['pesoLista'];
        $brutoProveedores = $infoProveedores['bruto'];
        $taraProveedores = $infoProveedores['tara'];
        $netoProveedores = $infoProveedores['neto'];
        $diferProveedores = $infoProveedores['diferencia'];

        $contProveedores = $contProveedores + 1;
        $sumBruto = $sumBruto + $brutoProveedores;
        $sumTara = $sumTara + $taraProveedores;
        $sumaNeto = $sumaNeto + $netoProveedores;
        $sumDif = $sumDif + $diferProveedores;

        echo '<tr>';
        echo '<td>' . $contProveedores . '</td>';
        echo '<td>' . $noEntradaProveedores . "</td>";
        echo '<td>' . $fchEntradaProveedores . "</td>";
        echo '<td>' . $provedores . "</td>";
        echo '<td>' . $idSagarpaProveedores . "</td>";
        echo '<td>' . $localidadProveedores . "</td>";
        echo '<td>' . $zonaProveedores . "</td>";
        echo '<td>' . $contenedorProveedores . "</td>";
        echo '<td>' . $pesoLidstaProvedores . "</td>";
        echo '<td>' . $brutoProveedores . "</td>";
        echo '<td>' . $taraProveedores . "</td>";
        echo '<td>' . $netoProveedores . "</td>";
        echo '<td>' . $diferProveedores . "</td>";
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
    echo '<td>' . number_format($sumBruto, 0, '.', ',') . '</td>';
    echo '<td>' . number_format($sumTara, 0, '.', ',') . '</td>';
    echo '<td>' . number_format($sumaNeto, 0, '.', ',') . '</td>';
    echo '<td>' . number_format($sumDif, 0, '.', ',') . '</td>';
    echo '</tr>';
} else {
    $valorNulo = 0;
    echo $json_response = json_encode($valorNulo);
}

