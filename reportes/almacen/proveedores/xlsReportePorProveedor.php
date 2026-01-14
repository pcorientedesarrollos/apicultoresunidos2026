<?php

include_once '../../../clases/consultas.php';
require_once '../../../DAOConeccion/conePDO.php';

$pdo = new conePDO();
$conexion = $pdo->conectar();

$consultaProveedor = new ReportesDeAlmacen();

$recipien    = $_GET['recipiente'];
$idProveedor = $_GET['idProveedor'];
$tipoDeMiel = $_GET['tipoDeMiel'];
$nombreTipoDeMiel = $tipoDeMiel == '1' ? 'Miel 100% pura de abeja' : 'Miel 100% orgánica';
$fecha = date("d-m-y");
$objetProveedor = new stdClass();
$objetProveedor->recipiente = $recipien;
$objetProveedor->idProveedor = $idProveedor;

if (isset($_GET['valorTodo'])) {
    $todo = $_GET['valorTodo'];
    $objetProveedor->todos = $todo;
} else {
    $todo = 0;
    $fInicial = $_GET['fecha1'];
    $fFinal = $_GET['fecha2'];
    $objetProveedor->todos = $todo;
    $objetProveedor->fInicial = $fInicial;
    $objetProveedor->fFinal = $fFinal;
}

$objetProveedor->tipoDeMiel = $tipoDeMiel;
$consultasProveedor = $consultaProveedor->ReportePorProveedor($objetProveedor);
$dataProveedor = $conexion->prepare($consultasProveedor);
$dataProveedor->execute();
$contProveedor = $dataProveedor->rowCount();

if ($contProveedor > 0) {

    if ($objetProveedor->recipiente == 3) {
        $EnvaseContenedor = 'Tambor';
    } else {
        $EnvaseContenedor = 'Cubetas';
    }

    header('Content-type: application/vnd.ms-excel');
    header("Content-Disposition: attachment; filename=Reporte de Entrada de $EnvaseContenedor por Proveedor_$fecha.xls");
    header("Prafma: no-cache");
    header("Expires:0");

    $tituloProveedor = "Reporte de Entrada de $EnvaseContenedor por Proveedor. $nombreTipoDeMiel";

    echo '<table  width="100%">';
    echo '<tr>';
    echo '<td width = "50%" style="color:#0000;"> <span style="font-weight: bold; font-size: 18pt;">' . utf8_decode($tituloProveedor) . '</span> </td> ';
    echo '</tr>';
    echo '</table>';
    
        if (isset($_GET['valorTodo'])) {
        echo '';
    } else {
        echo '<table  width="100%">';
        echo '<tr>';
        echo '<td width = "25%" style="color:#0000;"> <span style="font-weight: bold; font-size: 12pt;">Periodo:</span> <p> </td> ';
        echo '<td width = "25%" style="text-aligb:left;"> <span style="font-weight: bold; font-size: 12pt;">DE: ' . $objetProveedor->fInicial . '</span> <p> </td>';
        echo '<td width = "25%" style="color:#0000;"><span style="font-weight: bold; font-size: 12pt;">HASTA: ' . $objetProveedor->fFinal . '</span></td> ';
        echo '<td width = "25%" style="text-aling: right";> </td>';
        echo '</tr>';
        echo '</table>';
    }
    
    while($encabezadoProveedor = $dataProveedor->fetch()){
        $encabezadoData = new stdClass();
        $encabezadoData->proveedor    = $encabezadoProveedor['nombre'];
        $encabezadoData->localidad    = $encabezadoProveedor['localidad'];
        $encabezadoData->sagarpa      = $encabezadoProveedor['idSagarpa'];
    }
    echo '<table  width="100%"  style="font-family: serif;" cellpadding="5">';
    echo '<tr>';
    echo '<td width="49%" >
                    <span style="font-size: 7pt; color: #555555; font-family: sans;"></span><br />' .
    'Proveedor:<b> ' . $encabezadoData->proveedor . '</b><br />' .
    'Localidad:<b> ' . $encabezadoData->localidad . '</b><br />' .
    'ID Sagarpa:<b> ' . $encabezadoData->sagarpa . '</b><br />
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
    echo '<table width="100%" border="1">
            <tr> 
                <th>No.</th>
                <th>No.Entrada</th>
                <th>Fecha</th>
                <th>Zona</th>
                <th>'.$EnvaseContenedor.'</th>
                <th>P.Lista</th>
                <th>Bruto</th>
                <th>Tara</th>
                <th>Neto</th>
                <th>Dif</th>
            </tr>';
   
    $conts = 0;
    $sumBruto = 0;
    $sumTara = 0;
    $sumaNeto = 0;
    $sumDif = 0;
    
    $consultasProveedor = $consultaProveedor->ReportePorProveedor($objetProveedor);
    $infoProveedor = $conexion->prepare($consultasProveedor);
    $infoProveedor->execute();
    
     while($datosCliente = $infoProveedor->fetch()){
         
        $noEntradaProveedor = $datosCliente['idAlmacen'];
        $fchEntrada         = $datosCliente['fecha'];
        $tambor             = $datosCliente['tambor'];
        $zonaProveedor      = $datosCliente['zona'];
        $pesoListaProve     = $datosCliente['pesoLista'];
        $brutoProveedor     = $datosCliente['bruto'];
        $taraProveedor      = $datosCliente['tara'];
        $netoProveedor      = $datosCliente['neto'];
        $diferProveedor     = $datosCliente['diferencia'];
        
        $conts = $conts + 1;
        $sumBruto = $sumBruto + $brutoProveedor;
        $sumTara = $sumTara + $taraProveedor;
        $sumaNeto = $sumaNeto + $netoProveedor;
        $sumDif = $sumDif + $diferProveedor;
        
        echo '<tr>';
        echo '<td>' . $conts . '</td>';
        echo '<td>' . $noEntradaProveedor . '</td>';
        echo '<td>' . $fchEntrada . '</td>';
        echo '<td>' . $zonaProveedor . '</td>';
        echo '<td>' . $tambor . '</td>';
        echo '<td>' . $pesoListaProve . '</td>';
        echo '<td>' . $brutoProveedor . '</td>';
        echo '<td>' . $taraProveedor . '</td>';
        echo '<td>' . $netoProveedor . '</td>';
        echo '<td>' . $diferProveedor . '</td>';
        echo '</tr>';
    }
    
    
//    while($datosCliente = $dataProveedor->fetch()){
//        
//    }
    
    echo '</table>';
    echo '<table width="100%" style="text-align:left;">';
    echo '<tr>';
    echo '<td>Totales :</td>';
    echo '<td></td>';
    echo '<td></td>';
    echo '<td></td>';
    echo '<td></td>';
    echo '<td></td>';
    echo '<td>' . number_format($sumBruto, 0, '.', ',') . "</td>";
    echo '<td>' . number_format($sumTara, 0, '.', ',') . ' </td>';
    echo '<td>' . number_format($sumaNeto, 0, '.', ',') . "</td>";
    echo '<td>' . number_format($sumDif, 0, '.', ',') . '</td>';
    echo '</tr>';
} else {
    $valorNulo = 0;
    echo $json_response = json_encode($valorNulo);
}