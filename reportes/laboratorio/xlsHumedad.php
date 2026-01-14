<?php

include_once '../../clases/consultas.php';
$dao = new consultas();
$oLaboratorio = new stdClass();

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$conexion = $pdo->conectar();

//Fecha de Exportacion
$fecha = date("d-m-y");

//Inicio de la instalacion de la exportacion
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachmen; filename = Reporte de Humedad_$fecha.xls");
header("Prafma: no-cache");
header("Expires:0");
$tipoDeMiel = $_GET['tipoDeMiel'];
$titulo = $tipoDeMiel == '1' ? "Reporte de Humedad Miel 100% pura de abeja" : "Reporte de Humedad Miel 100% orgánica";

echo '<table width="100%">';
echo '<tr>';
echo '<td colspan="5" width = "50%" style="color#0000;">'
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

$conaultasHum = $dao->reportesLaboratorioHumedad($oLaboratorio, $tipoDeMiel);
$datosHumedad = $conexion->prepare($conaultasHum);
$datosHumedad->execute();
$registros = $datosHumedad->fetchAll(PDO::FETCH_ASSOC);

$confHumedad = $dao->configuracionLaboratorioHumedad($tipoDeMiel);
$configuraciones = $conexion->prepare($confHumedad);
$configuraciones->execute();
$configuraciones = $configuraciones->fetchAll(PDO::FETCH_ASSOC);


$totalTambores = 0;
$totalPor = 0;
$campos = array();

foreach ($configuraciones as $conf) {
    if (!array_key_exists($conf["descripcion"], $campos)) {
        $campos[$conf["descripcion"]] = 0;
    }
}
ksort($campos);

foreach ($registros as $k => $value) {
    foreach ($configuraciones as $conf) {
        switch ($conf["signo"]):
            case '1':
                if ($value["porcentaje"] < $conf["rango1"]) {
                    $campos[$conf["descripcion"]] ++;
                }
                break;
            case '2':
                if ($value["porcentaje"] > $conf["rango1"]) {
                    $campos[$conf["descripcion"]] ++;
                }
                break;
            case '3':
                if ($value["porcentaje"] <= $conf["rango1"]) {
                    $campos[$conf["descripcion"]] ++;
                }
                break;
            case '4':
                if ($value["porcentaje"] >= $conf["rango1"]) {
                    $campos[$conf["descripcion"]] ++;
                }
                break;
            case '5':
                if ($value["porcentaje"] >= $conf["rango1"] && $value["porcentaje"] <= $conf["rango2"]) {
                    $campos[$conf["descripcion"]] ++;
                }
                break;
        endswitch;
    }
}

foreach ($campos as $field) {
    $totalTambores = $totalTambores + $field;
}
foreach ($campos as $field) {
    $totalPor = $totalPor + (($field / $totalTambores) * 100);
}

echo '<table  width="100%" border="1">
	<tr align="center">
		<td style="background:#bdc3c7; font-weight:bold">' . utf8_decode("Análisis") . '</td>
		<td style="background:#bdc3c7; font-weight:bold">TAMBORES</td>
		<td style="background:#bdc3c7; font-weight:bold">PORCENTAJE</td>
	</tr>';
foreach ($campos as $k => $field) {
    echo '<tr>
        <td>' . $k . '</td>
        <td>' . $field . '</td>
        <td>' . number_format(($field / $totalTambores) * 100, 2, '.', ',') .'%'. '</td>
</tr>';
}

echo '<tr>
        <td style="background:#bdc3c7; font-weight:bold">Totales</td>
        <td>' . $totalTambores . '</td>
        <td>' . number_format($totalPor, 2, '.', ',').'%'. '</td>
</tr>';

echo '</table>';
