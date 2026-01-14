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
header("Content-Disposition: attachmen; filename = Reporte de Antibióticos_$fecha.xls");
header("Prafma: no-cache");
header("Expires:0");    

$tipoDeMiel = $_GET['tipoDeMiel'];

$titulo = $tipoDeMiel == '1' ? "Reporte de Antibióticos Miel 100% pura de abeja" : "Reporte de Antibióticos Miel 100% orgánica";

echo '<table width="100%">';
echo '<tr>';
echo '<td>'
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

$consultaAnti = $dao->reporteAntibioticos($oLaboratorio, $tipoDeMiel);
$datosAnti = $conexion->prepare($consultaAnti);
$datosAnti->execute();
$registros = $datosAnti->fetchAll(PDO::FETCH_ASSOC);

$confLab = $dao->configuracionLaboratorioAntibioticos($tipoDeMiel);
$confLab = $conexion->prepare($confLab);
$confLab->execute();
$configuraciones = $confLab->fetchAll(PDO::FETCH_ASSOC);

$sf = [];
$st = [];

foreach ($configuraciones as $conf) {
    switch ($conf['idOpcionLab']) {
        case '2':
            array_push($sf, $conf);
            break;
        case '3':
            array_push($st, $conf);
            break;
    }
}

$localidades = [];
foreach ($registros as $res) {

    if (!array_key_exists($res['localidad'], $localidades)) {
        $localidades[$res['localidad']] = [];
        array_push($localidades[$res['localidad']], ["idAlmacen" => $res['idAlmacen'], "neto" => $res['neto'], "sf" => $res['sf'], "st" => $res['st']]);
    } else {
        array_push($localidades[$res['localidad']], ["idAlmacen" => $res['idAlmacen'], "neto" => $res['neto'], "sf" => $res['sf'], "st" => $res['st']]);
    }
}

$groupLocalidad = [];
foreach ($localidades as $k => $lc) {
    $lc['Kgs'] = 0;
    $lc['tambores'] = 0;
    $lc['Sulfa'] = array();
    $lc['Estrepto'] = array();

    foreach ($lc as $key => $r) {

        if (!is_string($key)) {
            $lc['tambores'] ++;
            $lc['Kgs'] += $r['neto'];

            #Comparaciones SF
            foreach ($sf as $sulfa) {
                switch ($sulfa['signo']):
                    case '1':
                        if ($r['sf'] < $sulfa['rango1']) {
                            if (array_key_exists($sulfa['descripcion'], $lc["Sulfa"])) {
                                $lc["Sulfa"][$sulfa['descripcion']] += 1;
                            } else {
                                $lc["Sulfa"][$sulfa['descripcion']] = 1;
                            }
                        }
                        break;
                    case '2':
                        if ($r['sf'] > $sulfa['rango1']) {
                            if (array_key_exists($sulfa['descripcion'], $lc["Sulfa"])) {
                                $lc["Sulfa"][$sulfa['descripcion']] += 1;
                            } else {
                                $lc["Sulfa"][$sulfa['descripcion']] = 1;
                            }
                        }
                        break;
                    case '3':
                        if ($r['sf'] <= $sulfa['rango1']) {
                            if (array_key_exists($sulfa['descripcion'], $lc["Sulfa"])) {
                                $lc["Sulfa"][$sulfa['descripcion']] += 1;
                            } else {
                                $lc["Sulfa"][$sulfa['descripcion']] = 1;
                            }
                        }
                        break;
                    case '4':
                        if ($r['sf'] >= $sulfa['rango1']) {
                            if (array_key_exists($sulfa['descripcion'], $lc["Sulfa"])) {
                                $lc["Sulfa"][$sulfa['descripcion']] += 1;
                            } else {
                                $lc["Sulfa"][$sulfa['descripcion']] = 1;
                            }
                        }
                        break;
                    case '5':
                        if ($r['sf'] >= $sulfa['rango1'] && $r['sf'] <= $sulfa['rango2']) {
                            if (array_key_exists($sulfa['descripcion'], $lc["Sulfa"])) {
                                $lc["Sulfa"][$sulfa['descripcion']] += 1;
                            } else {
                                $lc["Sulfa"][$sulfa['descripcion']] = 1;
                            }
                        }
                        break;
                endswitch;
            }

            #Comparaciones ST

            foreach ($st as $estrepto) {
                switch ($estrepto['signo']):
                    case '1':
                        if ($r['st'] < $estrepto['rango1']) {
                            if (array_key_exists($estrepto['descripcion'], $lc["Estrepto"])) {
                                $lc["Estrepto"][$estrepto['descripcion']] += 1;
                            } else {
                                $lc["Estrepto"][$estrepto['descripcion']] = 1;
                            }
                        }
                        break;
                    case '2':
                        if ($r['st'] > $estrepto['rango1']) {
                            if (array_key_exists($estrepto['descripcion'], $lc["Estrepto"])) {
                                $lc["Estrepto"][$estrepto['descripcion']] += 1;
                            } else {
                                $lc["Estrepto"][$estrepto['descripcion']] = 1;
                            }
                        }
                        break;
                    case '3':
                        if ($r['st'] <= $estrepto['rango1']) {
                            if (array_key_exists($estrepto['descripcion'], $lc["Estrepto"])) {
                                $lc["Estrepto"][$estrepto['descripcion']] += 1;
                            } else {
                                $lc["Estrepto"][$estrepto['descripcion']] = 1;
                            }
                        }
                        break;
                    case '4':
                        if ($r['st'] >= $estrepto['rango1']) {
                            if (array_key_exists($estrepto['descripcion'], $lc["Estrepto"])) {
                                $lc["Estrepto"][$estrepto['descripcion']] += 1;
                            } else {
                                $lc["Estrepto"][$estrepto['descripcion']] = 1;
                            }
                        }
                        break;
                    case '5':
                        if ($r['st'] >= $estrepto['rango1'] && $r['st'] <= $estrepto['rango2']) {
                            if (array_key_exists($estrepto['descripcion'], $lc["Estrepto"])) {
                                $lc["Estrepto"][$estrepto['descripcion']] += 1;
                            } else {
                                $lc["Estrepto"][$estrepto['descripcion']] = 1;
                            }
                        }
                        break;
                endswitch;
            }
        }

        ksort($lc["Sulfa"]);
        ksort($lc["Estrepto"]);
        $groupLocalidad[$k] = $lc;
    }
}

array_multisort($groupLocalidad, SORT_DESC);

$celdasSulfa = array(
    "resultados" => []
);
$celdasEstrepto = array(
    "resultados" => []
);

$sumaTambores = 0;

foreach ($groupLocalidad as $key => $value) {
    $sumaTambores = $sumaTambores + $value["tambores"];
    #Sulfa
    foreach ($value["Sulfa"] as $k => $v) {
        if (!in_array($k, $celdasSulfa["resultados"])) {
            array_push($celdasSulfa["resultados"], $k);
        }
    }

    #Estrepto
    foreach ($value["Estrepto"] as $k => $v) {
        if (!in_array($k, $celdasEstrepto["resultados"])) {
            array_push($celdasEstrepto["resultados"], $k);
        }
    }
}

arsort($celdasSulfa["resultados"]);
arsort($celdasEstrepto["resultados"]);

#Calcular sumas
$sumas = array(
    "Sulfa" => [],
    "Estrepto" => [],
);

foreach ($groupLocalidad as $loc) {
    foreach ($loc["Sulfa"] as $k => $v) {

        if (array_key_exists($k, $sumas["Sulfa"])) {
            $sumas["Sulfa"][$k] += $v;
        } else {
            $sumas["Sulfa"][$k] = $v;
        }
    }
    foreach ($loc["Estrepto"] as $k => $v) {
        if (array_key_exists($k, $sumas["Estrepto"])) {
            $sumas["Estrepto"][$k] += $v;
        } else {
            $sumas["Estrepto"][$k] = $v;
        }
    }
}

#Calcular porcentaje
$porcentaje = array(
    "Sulfa" => [],
    "Estrepto" => [],
);

foreach ($sumas["Sulfa"] as $concepto => $total) {
    $porcentaje["Sulfa"][$concepto] = ($total / $sumaTambores) * 100;
}
foreach ($sumas["Estrepto"] as $concepto => $total) {
    $porcentaje["Estrepto"][$concepto] = ($total / $sumaTambores) * 100;
}

arsort($sumas["Sulfa"]);
arsort($sumas["Estrepto"]);

#TABLAS DE ANALISIS
echo '<table width="100%" border="1">
	<tr align="center">
		<td style="background:#bdc3c7; font-weight:bold">' . utf8_decode("Análisis Sulfa") . '</td>
		<td style="background:#bdc3c7; font-weight:bold">Tambores</td>
		<td style="background:#bdc3c7; font-weight:bold">Porcentaje</td>
	</tr>';

foreach ($sumas["Sulfa"] as $k => $value) {
    echo '<tr>
	<td> Sulfa ' . $k . '</td>
            <td>' . $value . '</td>
                <td>' . number_format($porcentaje["Sulfa"][$k], 2, '.', ',') . '%' . '</td>
</tr>';
}
echo '</table>';
echo '<br>';

echo '<table width="100%" border="1">
	<tr align="center">
		<td style="background:#bdc3c7; font-weight:bold">' . utf8_decode("Análisis Estrepto") . '</td>
		<td style="background:#bdc3c7; font-weight:bold">Tambores</td>
		<td style="background:#bdc3c7; font-weight:bold">Porcentaje</td>
	</tr>';


foreach ($sumas["Estrepto"] as $k => $value) {
    echo '<tr>
	<td> ST ' . $k . '</td>
            <td>' . $value . '</td>
                <td>' . number_format($porcentaje["Estrepto"][$k], 2, '.', ',') . '%' . '</td>
</tr>';
}
echo '</table>';
echo '<br>';


//Segunda tabla

echo '<table width="100%">';
echo '<tr>';
echo '<td>'
 . '<span style="font-weight:bold; font-size:18pt;">' . utf8_decode("Por cantidad") . '</span><td>';
echo '</tr>';
echo '</table>';
echo '<br>';


echo '<table width="100%" border="1">
	<tr align="center">
            <td style="background:#bdc3c7; font-weight:bold" rowspan="2">' . utf8_decode("Procedencia") . '</td>
            <td style="background:#bdc3c7; font-weight:bold" colspan="' . count($celdasSulfa["resultados"]) . '">Sulfa</td>
            <td style="background:#bdc3c7; font-weight:bold" colspan="' . count($celdasEstrepto["resultados"]) . '">Estrepto</td>
        </tr>';
echo '<tr align="center">';
foreach ($celdasSulfa["resultados"] as $celda) {
    echo '<td style="background:#bdc3c7; font-weight:bold">' . $celda . '</td>';
}
foreach ($celdasEstrepto["resultados"] as $celda) {
    echo '<td style="background:#bdc3c7; font-weight:bold">' . $celda . '</td>';
}
echo '</tr>';
foreach ($groupLocalidad as $k => $lc) {
    echo '<tr>';
    echo '<td>' . $k . '</td>';
    foreach ($celdasSulfa['resultados'] as $celda) {
        if (array_key_exists($celda, $lc["Sulfa"])) {
            echo '<td>' . $lc["Sulfa"][$celda] . '</td>';
        } else {
            echo '<td>0</td>';
        }
    }

    foreach ($celdasEstrepto['resultados'] as $celda) {
        if (array_key_exists($celda, $lc["Estrepto"])) {
            echo '<td>' . $lc["Estrepto"][$celda] . '</td>';
        } else {
            echo '<td>0</td>';
        }
    }
    echo '</tr>';
}

echo '</table>';
echo '<br>';

#Tabla de porcentajes
echo '<table width="100%">';
echo '<tr>';
echo '<td>'
 . '<span style="font-weight:bold; font-size:18pt;">' . utf8_decode("En porcentaje") . '</span><td>';
echo '</tr>';
echo '</table>';
echo '<br>';

echo '<table width="100%" border="1">
	<tr align="center">
            <td style="background:#bdc3c7; font-weight:bold" rowspan="2">' . utf8_decode("Procedencia") . '</td>
            <td style="background:#bdc3c7; font-weight:bold" colspan="' . count($celdasSulfa["resultados"]) . '">Sulfa Porcentaje</td>
            <td style="background:#bdc3c7; font-weight:bold" colspan="' . count($celdasEstrepto["resultados"]) . '">Estrepto Porcentaje</td>
        </tr>';
echo '<tr align="center">';
foreach ($celdasSulfa["resultados"] as $celda) {
    echo '<td style="background:#bdc3c7; font-weight:bold">' . $celda . '</td>';
}
foreach ($celdasEstrepto["resultados"] as $celda) {
    echo '<td style="background:#bdc3c7; font-weight:bold">' . $celda . '</td>';
}
echo '</tr>';

foreach ($groupLocalidad as $k => $lc) {
    echo '<tr>';
    echo '<td>' . $k . '</td>';
    foreach ($celdasSulfa["resultados"] as $celda) {
        if (array_key_exists($celda, $lc["Sulfa"])) {
            echo '<td>' . number_format((($lc["Sulfa"][$celda] / $sumas["Sulfa"][$celda]) * 100), 2, '.', ',') . '%' . '</td>';
        } else {
            echo '<td>0%</td>';
        }
    }
    foreach ($celdasEstrepto["resultados"] as $celda) {
        if (array_key_exists($celda, $lc["Estrepto"])) {
            echo '<td>' . number_format((($lc["Estrepto"][$celda] / $sumas["Estrepto"][$celda]) * 100), 2, '.', ',') . '%' . '</td>';
        } else {
            echo '<td>0%</td>';
        }
    }
}
echo '</tr>';
echo '</table>';
