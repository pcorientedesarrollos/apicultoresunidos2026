<?php

include_once '../../clases/consultas.php';
$dao = new consultas();
$oLaboratorio = new stdClass();

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$conexion = $pdo->conectar();

//Fecha de Exporrtacion
$fecha = date("d-m-y");

//Inicio de la insrancia de exportacion
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachmen; filename = Reporte de HMF_$fecha.xls");
header("Prafma: no-cache");
header("Expires:0");

$tipoDeMiel = $_GET['tipoDeMiel'];

$titulo = $tipoDeMiel == '1' ? "Reporte de HMF Miel 100% pura de abeja" : "Reporte de HMF Miel 100% orgánica";

echo '<table width="100%">';
echo '<tr>';
echo '<td colspan="3">'
 . '<span style="font-weight:bold; font-size:18pt;">' . utf8_decode($titulo) . '</span><td>';
echo '</tr>';
echo '</table>';
echo '<br>';

if (isset($_GET['sinFecha'])) {
    $sinFecha = $_GET['sinFecha'];
    $oLaboratorio->sinFecha = $sinFecha;
} else {
    $sinFecha = 0;
    $oLaboratorio->sinFecha = $sinFecha;

    $fechaUno = $_GET['fechaUno'];
    $fechaDos = $_GET['fechaDos'];

    $oLaboratorio->fInicial = $fechaUno;
    $oLaboratorio->fFinal = $fechaDos;

    echo '<table width="100%"';
    echo '<tr>';
    echo '<td width = "25%" style="color:#0000;"> <span style="font-weight: bold; font-size: 12pt;">Periodo:</span> <p> </td> ';
    echo '<td width = "25%" style="text-aligb:left;"> <span style="font-weight: bold; font-size: 12pt;">DE: ' . $oLaboratorio->fInicial . '</span> <p> </td>';
    echo '<td width = "25%" style="color:#0000;"><span style="font-weight: bold; font-size: 12pt;">HASTA: ' . $oLaboratorio->fFinal . '</span></td> ';
    echo '<td width = "25%" style="text-aling: right";> </td>';
    echo '</tr>';
    echo '</table>';
    echo '<br>';
}

//$regritrosHMF = $dao->resumenConcentrado($oLaboratorio);
$dataHMF = $dao->reporteHmf($oLaboratorio, $tipoDeMiel);
$datsHmF = $conexion->prepare($dataHMF);
$datsHmF->execute();
$registros = $datsHmF->fetchAll(PDO::FETCH_ASSOC);

$totalTambores = count($registros);
$aprobados = 0;
$rechazados = 0;


foreach ($registros as $tambor) {
    if ($tambor['hmf'] <= 10) {
        $aprobados++;
    } else {
        $rechazados++;
    }
}
if($totalTambores > 0) {
    $porcentajeAprobados = number_format(($aprobados / $totalTambores)*100, 2, '.', ',');
    $porcentajeRechazados = number_format(($rechazados / $totalTambores)*100, 2, '.', ',');
    
    $total = $aprobados + $rechazados;
    $totalPor = $porcentajeAprobados + $porcentajeRechazados;
    
    echo '<table  width="100%" border="1">
        <tr align="center">
            <td style="background:#bdc3c7; font-weight:bold">RESULTADOS</td>
            <td style="background:#bdc3c7; font-weight:bold">TAMBORES</td>
            <td style="background:#bdc3c7; font-weight:bold">PORCENTAJE</td>
        </tr>
                <tr>
            <td>APROBADOS</td>
            <td>' . $aprobados . '</td>
            <td>' . number_format(($aprobados / $totalTambores) * 100, 2, '.', ',') . '%' . '</td>
        </tr>
        </tr>
                <tr>
            <td>RECHAZADOS</td>
            <td>' . $rechazados . '</td>
            <td>' . number_format(($rechazados / $totalTambores) * 100, 2, '.', ',') . '%' . '</td>
        </tr>
                </tr>
                <tr>
            <td>TOTAL</td>
            <td>' . $total . '</td>
            <td>' . $totalPor . '%' . '</td>
        </tr>
    </table>';
} else {
    echo '<b>No hay Informacion</b>';
}
