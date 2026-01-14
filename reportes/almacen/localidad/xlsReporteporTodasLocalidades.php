<?php

include_once '../../../clases/consultas.php';
require_once '../../../DAOConeccion/conePDO.php';

$pdo = new conePDO();
$conexion = $pdo->conectar();

$consultaLocalidad = new ReportesDeAlmacen();

$valorLocalidades = $_GET['valorLocalidades'];
$recipiente = $_GET['recipiente'];
$tipoDeMiel = $_GET['tipoDeMiel'];
$nombreTipoDeMiel = $tipoDeMiel == '1' ? 'Miel 100% pura de abeja' : 'Miel 100% orgánica';

$oLocalidad1 = new stdClass();
$oLocalidad1->valorLocalidades = $valorLocalidades;
$oLocalidad1->recipiente = $recipiente;

//Fecha de exportacion 
$fecha = date("d-m-Y");

if (isset($_GET['valorTodo'])) {
    $todasLocalidades = $_GET['valorTodo'];
    $oLocalidad1->todasLocalidades = $todasLocalidades;
    $ambos = 0;
    $oLocalidad1->ambos = $ambos;


//    $contTabla = $dao->repxLocalidades($oLocalidad1);
} else {
    $todasLocalidades = 0;
    $ambos = 0;
    $fInicial = $_GET['fecha1'];
    $fFinal = $_GET['fecha2'];
    $oLocalidad1->todasLocalidades = $todasLocalidades;
    $oLocalidad1->ambos = $ambos;
    $oLocalidad1->fInicial = $fInicial;
    $oLocalidad1->fFinal = $fFinal;

//    $contTabla = $dao->repxLocalidades($oLocalidad1);
}

$oLocalidad1->tipoDeMiel = $tipoDeMiel;
$consultaTodasLocalidades = $consultaLocalidad->reportePorLocalidades($oLocalidad1);
$dataLocalidadTodas = $conexion->prepare($consultaTodasLocalidades);
$dataLocalidadTodas->execute();
$contTodasLocalidades = $dataLocalidadTodas->rowCount();

if ($contTodasLocalidades > 0) {
    if ($oLocalidad1->recipiente == 3) {
        $thLoca = 'Tambor';
        $contenedor = 'Tambores';
    } else {
        $thLoca = 'Cubeta';
        $contenedor = 'Cubetas';
    }

    //Inicio de la instacia de exportacion
    header('Content-type: application/vnd.ms-excel');
    header("Content-Disposition: attachmen; filename = Reportes de Entradas de $contenedor por Localidades_$fecha.xls");
    header("Prafma: no-cache");
    header("Expires:0");

    $titulo = "Reporte de Entrada de $contenedor por Localidades. $nombreTipoDeMiel";

    echo '<table width="100%">';
    echo '<tr>';
    echo '<td width = "50%" style="color#0000;">'
    . '<span style="font-weight:bold; font-size:18pt;">' . utf8_decode($titulo) . '</span><td>';
    echo '</tr>';
    echo '</table>';

    if ($oLocalidad1->todasLocalidades == 2) {
        echo '';
    } else {
        echo '<br/>';
        echo '<table width ="100%">';
        echo '<tr>';
        echo '<td width="25%" style="color:#0000;"><span style="font-weight: bold; font-size: 12pt;">Periodo:</span></td>';
        echo '<td width="25%" style="text-aling:left"><span style="font-weight:bold; font-size: 12pt;">DE:' . $oLocalidad1->fInicial . '</span></td>';
        echo '<td width="25%" style="text-aling:left"><span style="font-weight:bold; font-size: 12pt;">HASTA:' . $oLocalidad1->fFinal . '</span></td>';
        echo '</tr>';
        echo '</table>';
    }

    //Construccion de la tabla de reporte de entrada por  Localidad
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
    echo '<th>No.</th>';
    echo '<th>Localidad</th>';
    echo '<th>No.Entrada</th>';
    echo '<th>Fecha</th>';
    echo '<th>Proveedor</th>';
    echo '<th>ID Sagarpa</th>';
    echo '<th>Zona</th>';
    echo '<th>' . $thLoca . '</th>';
    echo '<th>P.Lista</th>';
    echo '<th>Bruto</th>';
    echo '<th>Tara</th>';
    echo '<th>Neto</th>';
    echo '<th>Dif</th>';
    echo '</tr>';

    $cont = 0;
    $sumaTara = 0;
    $sumBrutoTL = 0;
    $sumNetoTL = 0;
    $sumaDif = 0;
    while ($infoLocalidadTodas = $dataLocalidadTodas->fetch()) {
        $noEntrada      = $infoLocalidadTodas['idAlmacen'];
        $fchaEntrada    = $infoLocalidadTodas['fecha'];
        $proveedor      = $infoLocalidadTodas['nombre'];
        $sargapa        = $infoLocalidadTodas['idSagarpa'];
        $localidad      = $infoLocalidadTodas['localidad'];
        $zona           = $infoLocalidadTodas['zona'];
        $contendor      = $infoLocalidadTodas['tambor'];
        $pesoLista      = $infoLocalidadTodas['pesoLista'];
        $bruto          = $infoLocalidadTodas['bruto'];
        $tara           = $infoLocalidadTodas['tara'];
        $neto           = $infoLocalidadTodas['neto'];
        $dife           = $infoLocalidadTodas['diferencia'];
        
        $cont = $cont + 1;
        $sumaTara = $sumaTara + $tara;
        $sumBrutoTL = $sumBrutoTL + $bruto;
        $sumNetoTL = $sumNetoTL + $neto;
        $sumaDif = $sumaDif + $dife;

        echo '<tr>';
        echo '<td>' . $cont . '</td>';
        echo '<td>' . $localidad . "</td>";
        echo '<td>' . $noEntrada . "</td>";
        echo '<td>' . $fchaEntrada . "</td>";
        echo '<td>' . $proveedor . "</td>";
        echo '<td>' . $sargapa . "</td>";
        echo '<td>' . $zona . "</td>";
        echo '<td>' . $contendor . "</td>";
        echo '<td>' . $pesoLista . "</td>";
        echo '<td>' . $bruto . "</td>";
        echo '<td>' . $tara . "</td>";
        echo '<td>' . $neto . "</td>";
        echo '<td>' . $dife . "</td>";
        echo '</tr>';
    }
    
    echo '</table>';
    echo '<table width="100%" style="text-align:left;">';
    echo '<tr>';
    echo '<td> Totales :</td>';
    echo '<td></td>';
    echo '<td></td>';
    echo '<td></td>';
    echo '<td></td>';
    echo '<td></td>';
    echo '<td></td>';
    echo '<td></td>';
    echo '<td></td>';
    echo '<td>' . number_format($sumBrutoTL, 0, '.', ',') . "</td>";
    echo '<td>' . number_format($sumaTara, 0, '.', ',') . '</td>';
    echo '<td>' . number_format($sumNetoTL, 0, '.', ',') . "</td>";
    echo '<td>' . number_format($sumaDif, 0, '.', ',') . '</td>';
    echo '</tr>';
} else {
       $valorNulo = 0;
    echo $json_response = json_encode($valorNulo);
}