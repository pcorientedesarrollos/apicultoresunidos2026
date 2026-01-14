<?php

include_once '../../../clases/consultas.php';
require_once '../../../DAOConeccion/conePDO.php';

$pdo = new conePDO();
$conexion = $pdo->conectar();

$consultaLocalidad = new ReportesDeAlmacen();

$idLocalidad = $_GET['idlocalidad'];
$tipoDeMiel = $_GET['tipoDeMiel'];
$nombreTipoDeMiel = $tipoDeMiel == '1' ? 'Miel 100% pura de abeja' : 'Miel 100% orgánica';
$oLocalidad = new stdClass();
$oLocalidad->idLocalidad = $idLocalidad;

//Fecha de exportacion
$fecha = date("d-m-Y");

if (isset($_GET['ambos'])) {
    if (isset($_GET['valorTodo'])) {
        $ambos = $_GET['ambos'];
        $todasLocalidades = $_GET['valorTodo'];
        $recipiente = 0;

        $oLocalidad->ambos = $ambos;
        $oLocalidad->todasLocalidades = $todasLocalidades;
        $oLocalidad->recipiente = $recipiente;

//        $localidadEnca = $dao->repxLocalidad($oLocalidad);
//        $tablaConte = $dao->repxLocalidad($oLocalidad);
    } else {
        $ambos = $_GET['ambos'];
        $todasLocalidades = 0;
        $recipiente = 0;
        $fInicial = $_GET['fecha1'];
        $fFinal = $_GET['fecha2'];

        $oLocalidad->ambos = $ambos;
        $oLocalidad->todasLocalidades = $todasLocalidades;
        $oLocalidad->recipiente = $recipiente;
        $oLocalidad->fInicial = $fInicial;
        $oLocalidad->fFinal = $fFinal;

//        $localidadEnca = $dao->repxLocalidad($oLocalidad);
//        $tablaConte = $dao->repxLocalidad($oLocalidad);
    }
    
    $oLocalidad->tipoDeMiel = $tipoDeMiel;

    $consultaAmbosporLocalidad = $consultaLocalidad->reporteLocalidad($oLocalidad);
    $dataAmbosLocalidad = $conexion->prepare($consultaAmbosporLocalidad);
    $dataAmbosLocalidad->execute();
    $contAmbosLocalidad = $dataAmbosLocalidad->rowCount();

    if ($contAmbosLocalidad > 0) {

        //Inicio de la instancia de exportacion
        header('Content-type: application/vnd.ms-excel');
        header("Content-Disposition: attachment; filename=Reporte de Entrada de  Tambores y Cubetas por Localida_$fecha.xls");
        header("Prafma: no-cache");
        header("Expires:0");

        $titulo = 'Reporte de Entrada de Tambores y Cubetas por Localidad. ' . $nombreTipoDeMiel;

        echo '<table width="100%">';
        echo '<tr>';
        echo '<td width = "50%" style="color#0000;">'
        . '<span style="font-weight:bold; font-size:18pt;">' . utf8_decode($titulo) . '</span><td>';
        echo '</tr>';
        echo '</table>';
        
        if($oLocalidad->todasLocalidades == 2){
            echo '';
        }else {
            
        // echo '<br/>';
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
        echo '<table width ="100%">';
        echo '<tr>';
        echo '<td width="25%" style="color:#0000;"><span style="font-weight: bold; font-size: 12pt;">Periodo:</span></td>';
        echo '<td width="25%" style="text-aling:left"><span style="font-weight:bold; font-size: 12pt;">DE:' . $oLocalidad->fInicial . '</span></td>';
        echo '<td width="25%" style="text-aling:left"><span style="font-weight:bold; font-size: 12pt;">HASTA:' . $oLocalidad->fFinal . '</span></td>';
        echo '</tr>';
        echo '</table>';
        }
        while ($datosAmbosLocalida = $dataAmbosLocalidad->fetch()) {
            $nomLocalidad = new stdClass();
            $nomLocalidad->nomLocalidad = utf8_decode($datosAmbosLocalida['localidad']);
        }

        echo '<br>';
        echo '<table width="100%">';
        echo '<td><span style="font-size:12 pt; color: #555555; font-family: sans;">Zona:<b>' . $nomLocalidad->nomLocalidad . '</b></span></td>';
        echo '</tr>';
        echo '</table>';

        echo '<br>';
        echo '<table width="100%" border="1">';
        echo '<tr>';
        echo '<th>No</th>';
        echo '<th>No.Entrada</th>';
        echo '<th>Fecha</th>';
        echo '<th>Proveedor</th>';
        echo '<th>Sagarpa</th>';
        echo '<th>T/C</th>';
        echo '<th>Zona</th>';
        echo '<th>P.Lista</th>';
        echo '<th>Bruto</th>';
        echo '<th>Tara</th>';
        echo '<th>Neto</th>';
        echo '<th>Dif</th>';
        echo '</tr>';

        $consultaAmbosEnca = $consultaLocalidad->reporteLocalidad($oLocalidad);
        $infoAmbosLocalida = $conexion->prepare($consultaAmbosEnca);
        $infoAmbosLocalida->execute();

        $contador = 0;
        $sumTara = 0;
        $sumaBruto = 0;
        $sumaNeto = 0;
        $sumDif = 0;
        while ($contenidoAmbos = $infoAmbosLocalida->fetch()) {
            $nEntraL = $contenidoAmbos['idAlmacenEncabezado'];
            $fchaL = $contenidoAmbos['fecha'];
            $recpL = $contenidoAmbos['idAlmacen'];
            $provL = $contenidoAmbos['nombre'];
            $idSaL = $contenidoAmbos['idSagarpa'];
            $zonaL = $contenidoAmbos['zona'];
            $pLisL = $contenidoAmbos['pesoLista'];
            $brutoL = $contenidoAmbos['bruto'];
            $taraL = $contenidoAmbos['tara'];
            $netoL = $contenidoAmbos['neto'];
            $diferL = $contenidoAmbos['diferencia'];

            $contador = $contador + 1;
            $sumTara = $sumTara + $taraL;
            $sumaBruto = $sumaBruto + $brutoL;
            $sumaNeto = $sumaNeto + $netoL;
            $sumDif = $sumDif + $diferL;

            echo '<tr>';
            echo '<td>' . $contador . '</td>';
            echo '<td>' . $nEntraL . '</td>';
            echo '<td>' . $fchaL . '</td>';
            echo '<td>' . $provL . '</td>';
            echo '<td>' . $idSaL . '</td>';
            echo '<td>' . $recpL . '</td>';
            echo '<td>' . $zonaL . '</td>';
            echo '<td>' . number_format($pLisL, 0, '.', ',') . '</td>';
            echo '<td>' . number_format($brutoL, 0, '.', ',') . '</td>';
            echo '<td>' . number_format($taraL, 0, '.', ',') . '</td>';
            echo '<td>' . number_format($netoL, 0, '.', ',') . '</td>';
            echo '<td>' . number_format($diferL, 0, '.', ',') . '</td>';
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
        echo '<td>' . number_format($sumaBruto, 0, '.', ',') . '</td>';
        echo '<td>' . number_format($sumTara, 0, '.', ',') . '</td>';
        echo '<td>' . number_format($sumaNeto, 0, '.', ',') . '</td>';
        echo '<td>' . number_format($sumDif, 0, '.', ',') . '</td>';
    } else {
        $valorNulo = 0;
        echo $json_response = json_encode($valorNulo);
    }
} else {
    $valorNulo = 0;
    echo $json_response = json_encode($valorNulo);
}