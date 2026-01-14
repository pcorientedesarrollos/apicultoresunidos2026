<?php

include_once '../../../clases/consultas.php';
require_once '../../../DAOConeccion/conePDO.php';

$pdo = new conePDO();
$conexion = $pdo->conectar();

$consultaLocalidad = new ReportesDeAlmacen();

$idLocalidad = $_GET['idlocalidad'];
$recipiente = $_GET['recipiente'];
$tipoDeMiel = $_GET['tipoDeMiel'];
$nombreTipoDeMiel = $tipoDeMiel == '1' ? 'Miel 100% pura de abeja' : 'Miel 100% orgánica';

$oLocalidad = new stdClass();
$oLocalidad->idLocalidad = $idLocalidad;
$oLocalidad->recipiente = $recipiente;

//Fecha de exportacion 
$fecha = date("d-m-Y");

if (isset($_GET['valorTodo'])) {
    $todasLocalidades = $_GET['valorTodo'];
    $oLocalidad->todasLocalidades = $todasLocalidades;
    $ambos = 0;
    $oLocalidad->ambos = $ambos;

//    $encTabla = $dao->repxLocalidad($oLocalidad);
//    $contTabla = $dao->repxLocalidad($oLocalidad);
} else {
    $todasLocalidades = 0;
    $ambos = 0;
    $fInicial = $_GET['fecha1'];
    $fFinal = $_GET['fecha2'];
    $oLocalidad->todasLocalidades = $todasLocalidades;
    $oLocalidad->ambos = $ambos;
    $oLocalidad->fInicial = $fInicial;
    $oLocalidad->fFinal = $fFinal;

//    $encTabla = $dao->repxLocalidad($oLocalidad);
//    $contTabla = $dao->repxLocalidad($oLocalidad);
}

$oLocalidad->tipoDeMiel = $tipoDeMiel;
$consultaLocalidadEnca = $consultaLocalidad->reporteLocalidad($oLocalidad);
$dataLocalidad = $conexion->prepare($consultaLocalidadEnca);
$dataLocalidad->execute();
$contLocalidad = $dataLocalidad->rowCount();

if ($contLocalidad > 0) {

    if ($oLocalidad->recipiente == 3) {
        $thLoca = 'Tambor';
        $contenedor = 'Tambores';
    } else {
        $thLoca = 'Cubeta';
        $contenedor = 'Cubetas';
    }

//Inicio de la instacia de exportacion
    header('Content-type: application/vnd.ms-excel');
    header("Content-Disposition: attachmen; filename = Reportes de Entradas de $contenedor por Localidad_$fecha.xls");
    header("Prafma: no-cache");
    header("Expires:0");

    $titulo = "Reporte de Entrada de $contenedor por Localidad. $nombreTipoDeMiel";

    echo '<table width="100%">';
    echo '<tr>';
    echo '<td width = "50%" style="color#0000;">'
    . '<span style="font-weight:bold; font-size:18pt;">' . utf8_decode($titulo) . '</span><td>';
    echo '</tr>';
    echo '</table>';

    if ($oLocalidad->todasLocalidades == 2) {
        echo '';
    } else {
        echo '<br/>';
        echo '<table width ="100%">';
        echo '<tr>';
        echo '<td width="25%" style="color:#0000;"><span style="font-weight: bold; font-size: 12pt;">Periodo:</span></td>';
        echo '<td width="25%" style="text-aling:left"><span style="font-weight:bold; font-size: 12pt;">DE:' . $oLocalidad->fInicial . '</span></td>';
        echo '<td width="25%" style="text-aling:left"><span style="font-weight:bold; font-size: 12pt;">HASTA:' . $oLocalidad->fFinal . '</span></td>';
        echo '</tr>';
        echo '</table>';
    }

    while ($encaLocalidad = $dataLocalidad->fetch()) {
        $Encabezado = new stdClass();
        $Encabezado->localidad = utf8_decode($encaLocalidad['localidad']);
    }

    echo '<br>';
    echo '<table width = "100%">';
    echo '<tr>';
    echo '<td><span style="font-size: 12pt; color: #555555; font-family: sans"><span>Localidad:<b>' . $Encabezado->localidad . '</span></b></td>';
    echo '</tr>';
    echo '</table>';


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
    echo '<th>No.Entradad</th>';
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

    $consultaLocalidadInfor = $consultaLocalidad->reporteLocalidad($oLocalidad);
    $datosLocalidad = $conexion->prepare($consultaLocalidadInfor);
    $datosLocalidad->execute();
    
    $cont = 0;
    $sumTara = 0;
    $sumBrutoL = 0;
    $sumNetoL = 0;
    $sumDif = 0;
    while ($infoLocalidad = $datosLocalidad->fetch()){
        $noEntradaLocalidad    = $infoLocalidad['idAlmacen'];
        $fchaEntradaLocalidad  = $infoLocalidad['fecha'];
        $proveedorLocalidad    = $infoLocalidad['nombre'];
        $sargapa               = $infoLocalidad['idSagarpa'];
        $zonaLocalidad         = $infoLocalidad['zona'];
        $contendor             = $infoLocalidad['tambor'];
        $pesoListaLocalidad    = $infoLocalidad['pesoLista'];
        $brutoLocalidad        = $infoLocalidad['bruto'];
        $taraLocalidad         = $infoLocalidad['tara'];
        $netoLocalidad         = $infoLocalidad['neto'];
        $difeLocalidad         = $infoLocalidad['diferencia'];

        $cont = $cont + 1;
        $sumTara = $sumTara + $taraLocalidad;
        $sumBrutoL = $sumBrutoL + $brutoLocalidad;
        $sumNetoL = $sumNetoL + $netoLocalidad;
        $sumDif = $sumDif + $difeLocalidad;
        
        echo '<tr>';
        echo '<td>' . $cont . '</td>';
        echo '<td>' . $noEntradaLocalidad . "</td>";
        echo '<td>' . $fchaEntradaLocalidad . "</td>";
        echo '<td>' . $proveedorLocalidad . "</td>";
        echo '<td>' . $sargapa . "</td>";
        echo '<td>' . $zonaLocalidad . "</td>";
        echo '<td>' . $contendor . "</td>";
        echo '<td>' . $pesoListaLocalidad . "</td>";
        echo '<td>' . $brutoLocalidad . "</td>";
        echo '<td>' . $taraLocalidad . "</td>";
        echo '<td>' . $netoLocalidad . "</td>";
        echo '<td>' . $difeLocalidad . "</td>";
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
    echo '<td>' . number_format($sumBrutoL, 0, '.', ',') . "</td>";
    echo '<td>' . number_format($sumTara, 0, '.', ',') . '</td>';
    echo '<td>' . number_format($sumNetoL, 0, '.', ',') . "</td>";
    echo '<td>' . number_format($sumDif, 0, '.', ',') . '</td>';
    echo '</tr>';
    echo '</table>';
} else {
        $valorNulo = 0;
    echo $json_response = json_encode($valorNulo);
}
        