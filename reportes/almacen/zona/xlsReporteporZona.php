<?php

include_once '../../../clases/consultas.php';
require_once '../../../DAOConeccion/conePDO.php';

$pdo = new conePDO();
$conexion = $pdo->conectar();

$consultaZona = new ReportesDeAlmacen();

$idzona = $_GET['idzona'];
$recipiente = $_GET['recipiente'];
$tipoDeMiel = $_GET['tipoDeMiel'];
$nombreTipoDeMiel = $tipoDeMiel == '1' ? 'Miel 100% pura de abeja' : 'Miel 100% orgánica';

$objeZona = new stdClass();
$objeZona->idZona = $idzona;
$objeZona->recipiente = $recipiente;

//Fecha de exportacion
$fecha = date("d-m-y");

if (isset($_GET['valorTodo'])) {
    $todoZona = $_GET['valorTodo'];
    $objeZona->todoZona = $todoZona;

} else {
    $todoZona = 0;
    $fInicial = $_GET['fecha1'];
    $fFinal = $_GET['fecha2'];

    $objeZona->todoZona = $todoZona;
    $objeZona->fInicial = $fInicial;
    $objeZona->fFinal = $fFinal;
}

$objeZona->tipoDeMiel = $tipoDeMiel;
$consultasZona = $consultaZona->ReportePorZona($objeZona);
$datasZona = $conexion->prepare($consultasZona);
$datasZona->execute();
$contReporteZona = $datasZona->rowCount();

if ($contReporteZona > 0) {

    if ($objeZona->recipiente == 3) {
        $ths = 'Tambor';
        $contenedorZ = "Tambores";
    } else {
        $ths = 'Cubeta';
        $contenedorZ = "Cubetas";
    }

//Inicio de la instancia para la exportacion

    header('Content-type: application/vnd.ms-excel');
    header("Content-Disposition: attachment; filename=Reporte de entrada de $contenedorZ por Zona_$fecha.xls");
    header("Prafma: no-cache");
    header("Expires:0");

    $Titulo = "Reporte de Entrada de $contenedorZ por Zona. $nombreTipoDeMiel";

    echo '<table width="100%">';
    echo '<tr>';
    echo '<td witdh = "50%" style="color#0000;"><span style="font-weight:bold; font-size:18pt;">' . utf8_decode($Titulo) . '</span></td>';
    echo '</tr>';
    echo '</table>';

    if ($objeZona->todoZona == 2) {
        echo '';
    } else {
        echo '<br/>';
        echo '<table width="100%"';
        echo '<tr>';
        echo '<td width = "25%" style="color:#0000;"> <span style="font-weight: bold; font-size: 12pt;">Periodo:</span> <p> </td> ';
        echo '<td width = "25%" style="text-aligb:left;"> <span style="font-weight: bold; font-size: 12pt;">DE: ' . $objeZona->fInicial . '</span> <p> </td>';
        echo '<td width = "25%" style="color:#0000;"><span style="font-weight: bold; font-size: 12pt;">HASTA: ' . $objeZona->fFinal . '</span></td> ';
        echo '<td width = "25%" style="text-aling: right";> </td>';
        echo '</tr>';
        echo '</table>';
    }

    while ($infoZonas = $datasZona->fetch()) {
        $zonaEncabezado = new stdClass();
        $zonaEncabezado->zona = $infoZonas['zonaCom'];
    }

    echo '<br>';
    echo '<table  width="100%">';
    echo '<tr>';
    echo '<td> 
     <span style="font-size: 12 pt; color: #555555; font-family: sans;">Zona :<b>' . $zonaEncabezado->zona . '</span></b></td>';
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
    echo '<th>ID.Sagarpa</th>';
    echo '<th>Localidad</th>';
    echo '<th>Zona</th>';
    echo '<th>' . $ths . '</th>';
    echo '<th>P.Lista</th>';
    echo '<th>Bruto</th>';
    echo '<th>Tara</th>';
    echo '<th>Neto</th>';
    echo '<th>Dif</th>';
    echo '</tr>';

    $sqlZona = $consultaZona->ReportePorZona($objeZona);
    $datosZona = $conexion->prepare($sqlZona);
    $datosZona->execute();

    $cont = 0;
    $sumTara = 0;
    $smNeto = 0;
    $smBruto = 0;
    $sumDif = 0;
    while ($informacionZona = $datosZona->fetch()) {
        $noEntradaZona = $informacionZona['idAlmacen'];
        $fchEntradaZona = $informacionZona['fecha'];
        $proveedorZona = $informacionZona['nombre'];
        $sagarpaZona = $informacionZona['idSagarpa'];
        $localidadZona = $informacionZona['localidad'];
        $tamborZona = $informacionZona['tambor'];
        $zona = $informacionZona['zona'];
        $pesoListaZona = $informacionZona['pesoLista'];
        $brutoZona = $informacionZona['bruto'];
        $taraZona = $informacionZona['tara'];
        $netoZona = $informacionZona['neto'];
        $diferenZona = $informacionZona['diferencia'];

        $cont = $cont + 1;
        $sumTara = $sumTara + $taraZona;
        $smBruto = $smBruto + $brutoZona;
        $smNeto = $smNeto + $netoZona;
        $sumDif = $sumDif + $diferenZona;
        
        echo '<tr>';
        echo '<td>' . $cont . '</td>';
        echo '<td>' . $noEntradaZona . "</td>";
        echo '<td>' . $fchEntradaZona . "</td>";
        echo '<td>' . $proveedorZona . "</td>";
        echo '<td>' . $sagarpaZona . "</td>";
        echo '<td>' . $localidadZona . "</td>";
        echo '<td>' . $zona . "</td>";
        echo '<td>' . $tamborZona . "</td>";
        echo '<td>' . $pesoListaZona . "</td>";
        echo '<td>' . $brutoZona . "</td>";
        echo '<td>' . $taraZona . "</td>";
        echo '<td>' . $netoZona . "</td>";
        echo '<td>' . $diferenZona . "</td>";
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
    echo '<td>' . number_format($smBruto,0,'.',',') . "</td>";
    echo '<td>'. number_format($sumTara,0,'.',',').'</td>';
    echo '<td>' . number_format($smNeto,0,'.',',')  . "</td>";
    echo '<td>'. number_format($sumDif,0,'.',',').'</td>';
} else {
    $valorNulo = 0;
    echo $json_response = json_encode($valorNulo);
}
