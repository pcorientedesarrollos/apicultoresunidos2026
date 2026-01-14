<?php

include_once '../../clases/consultas.php';
include_once '../../DAOConeccion/conePDO.php';
$dao = new consultas();
$pdo = new conePDO();
$conexion = $pdo->conectar();
$oLaboratorio = new stdClass();

// Fecha de Exportacion
$fecha = date("d-m-y");

//Inicio de la instacia de la exportaion
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachmen; filename = Reporte de Concentrado de Laboratorio_$fecha.xls");
header("Prafma: no-cache");
header("Expires:0");

$tipoDeMiel = $_GET['tipoDeMiel'];
$titulo = $tipoDeMiel == '1' ? "Reporte de Concentrado de Laboratorio Miel 100% pura de abeja" : "Reporte de Concentrado de Laboratorio Miel 100% orgánica";

echo '<table>';
echo '<tr>';

echo '</tr>';
echo '<tr>';
echo '<td></td>';
echo '<td></td>';
echo '<td></td>';
echo '<td colspan="3" align="center">
    <p style="font-size:22px; font-weight:bold">' . utf8_decode($titulo) . '</p>'
 . '<td>';
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

    echo '<table>';
    echo '<tr>';
    echo '<td width = "25%" style="text-aligb:left;">PERIODO DE: ' . $oLaboratorio->fInicial . '</td> <br>';
    echo '<td width = "25%" style="color:#0000;">HASTA: ' . $oLaboratorio->fFinal . '</td> ';
    echo '</tr>';
    echo '</table>';
    echo '<br>';
}

$consultaConfSql = $dao->configuracionLaboratorio($tipoDeMiel);
$consultaConf = $conexion->prepare($consultaConfSql);
$consultaConf->execute();
$configuraciones = $consultaConf->fetchAll(PDO::FETCH_ASSOC);


$consltaCons = $dao->reportesLaboratorio($oLaboratorio, $tipoDeMiel);
$datosalab = $conexion->prepare($consltaCons);
$datosalab->execute();
$registros = $datosalab->fetchAll(PDO::FETCH_ASSOC);
$contConcetrado = $datosalab->rowCount();

if ($contConcetrado > 0) {

    $sf = [];
    $st = [];
    $hmf = [];
    $c13 = [];

    foreach ($configuraciones as $conf) {
        switch ($conf['idOpcionLab']) {
            case '2':
                array_push($sf, $conf);
                break;
            case '3':
                array_push($st, $conf);
                break;
            case '4':
                array_push($c13, $conf);
                break;
            case '5':
                array_push($hmf, $conf);

                break;
        }
    }

    $localidades = [];
    foreach ($registros as $res) {

        if (!array_key_exists($res['localidad'], $localidades)) {
            $localidades[$res['localidad']] = [];
            array_push($localidades[$res['localidad']], ["idAlmacen" => $res['idAlmacen'], "neto" => $res['neto'], "sf" => $res['sf'], "st" => $res['st'], "hmf" => $res['hmf'], "c13" => $res['c13']]);
        } else {
            array_push($localidades[$res['localidad']], ["idAlmacen" => $res['idAlmacen'], "neto" => $res['neto'], "sf" => $res['sf'], "st" => $res['st'], "hmf" => $res['hmf'], "c13" => $res['c13']]);
        }
    }

    $groupLocalidad = [];
    foreach ($localidades as $k => $lc) {
        $lc['Kgs'] = 0;
        $lc['tambores'] = 0;
        $lc['Sulfa'] = array();
        $lc['Estrepto'] = array();
        $lc['Proceso'] = array();
        $lc['Adulteracion'] = array();

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

                #Comparaciones HMF
                if ($r['hmf'] <= 10) {
                    if (array_key_exists("Aprobados", $lc["Proceso"])) {
                        $lc["Proceso"]["Aprobados"] += 1;
                    } else {
                        $lc["Proceso"]["Aprobados"] = 1;
                    }
                } elseif ($r['hmf'] > 10) {
                    if (array_key_exists("Rechazados", $lc["Proceso"])) {
                        $lc["Proceso"]["Rechazados"] += 1;
                    } else {
                        $lc["Proceso"]["Rechazados"] = 1;
                    }
                }
                foreach ($hmf as $proceso) {
                    switch ($proceso['signo']):
                        case '1':
                            if ($r['hmf'] < $proceso['rango1']) {
                                if (array_key_exists($proceso['descripcion'], $lc["Proceso"])) {
                                    $lc["Proceso"][$proceso['descripcion']] += 1;
                                } else {
                                    $lc["Proceso"][$proceso['descripcion']] = 1;
                                }
                            }
                            break;
                        case '2':
                            if ($r['hmf'] > $proceso['rango1']) {
                                if (array_key_exists($proceso['descripcion'], $lc["Proceso"])) {
                                    $lc["Proceso"][$proceso['descripcion']] += 1;
                                } else {
                                    $lc["Proceso"][$proceso['descripcion']] = 1;
                                }
                            }
                            break;
                        case '3':
                            if ($r['hmf'] <= $proceso['rango1']) {
                                if (array_key_exists("Hmf" . $proceso['descripcion'], $lc)) {
                                    $lc["Hmf" . $proceso['descripcion']] += 1;
                                } else {
                                    $lc["Hmf" . $proceso['descripcion']] = 1;
                                }
                            }
                            break;
                        case '4':
                            if ($r['hmf'] >= $proceso['rango1']) {
                                if (array_key_exists($proceso['descripcion'], $lc["Proceso"])) {
                                    $lc["Proceso"][$proceso['descripcion']] += 1;
                                } else {
                                    $lc["Proceso"][$proceso['descripcion']] = 1;
                                }
                            }
                            break;
                        case '5':
                            if ($r['hmf'] >= $proceso['rango1'] && $r['hmf'] <= $proceso['rango2']) {
                                if (array_key_exists($proceso['descripcion'], $lc["Proceso"])) {
                                    $lc["Proceso"][$proceso['descripcion']] += 1;
                                } else {
                                    $lc["Proceso"][$proceso['descripcion']] = 1;
                                }
                            }
                            break;
                    endswitch;
                }

                #Comparaciones C13

                foreach ($c13 as $adulteracion) {
                    switch ($adulteracion['signo']):
                        case '1':
                            if ($r['c13'] < $adulteracion['rango1']) {
                                if (array_key_exists($adulteracion['descripcion'], $lc["Adulteracion"])) {
                                    $lc["Adulteracion"][$adulteracion['descripcion']] += 1;
                                } else {
                                    $lc["Adulteracion"][$adulteracion['descripcion']] = 1;
                                }
                            }
                            break;
                        case '2':
                            if ($r['c13'] > $adulteracion['rango1']) {
                                if (array_key_exists($adulteracion['descripcion'], $lc["Adulteracion"])) {
                                    $lc["Adulteracion"][$adulteracion['descripcion']] += 1;
                                } else {
                                    $lc["Adulteracion"][$adulteracion['descripcion']] = 1;
                                }
                            }
                            break;
                        case '3':
                            if ($r['c13'] <= $adulteracion['rango1']) {
                                if (array_key_exists($adulteracion['descripcion'], $lc["Adulteracion"])) {
                                    $lc["Adulteracion"][$adulteracion['descripcion']] += 1;
                                } else {
                                    $lc["Adulteracion"][$adulteracion['descripcion']] = 1;
                                }
                            }
                            break;
                        case '4':
                            if ($r['c13'] >= $adulteracion['rango1']) {
                                if (array_key_exists($adulteracion['descripcion'], $lc["Adulteracion"])) {
                                    $lc["Adulteracion"][$adulteracion['descripcion']] += 1;
                                } else {
                                    $lc["Adulteracion"][$adulteracion['descripcion']] = 1;
                                }
                            }
                            break;
                        case '5':
                            if ($r['c13'] >= $adulteracion['rango1'] && $r['c13'] <= $adulteracion['rango2']) {
                                if (array_key_exists($adulteracion['descripcion'], $lc["Adulteracion"])) {
                                    $lc["Adulteracion"][$adulteracion['descripcion']] += 1;
                                } else {
                                    $lc["Adulteracion"][$adulteracion['descripcion']] = 1;
                                }
                            }
                            break;
                    endswitch;
                }
            }
        }
        ksort($lc["Sulfa"]);
        ksort($lc["Estrepto"]);
        ksort($lc["Proceso"]);
        ksort($lc["Adulteracion"]);
        $groupLocalidad[$k] = $lc;
    }

    #Ordenamiento de los valores para la tabla
    array_multisort($groupLocalidad, SORT_DESC);
    
    $celdasSulfa = array(
        "resultados" => []
    );
    $celdasEstrepto = array(
        "resultados" => []
    );
    $celdasProceso = array(
        "resultados" => ["Aprobados", "Rechazados"]
    );
    $celdasAdulteracion = array(
        "resultados" => []
    );

    foreach ($groupLocalidad as $key => $value) {

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

        #Proceso
        foreach ($value["Proceso"] as $k => $v) {
            if ($k == "Aprobados" || $k == "Rechazados") {
                if (!in_array($k, $celdasProceso["resultados"])) {
                    array_push($celdasProceso["resultados"], $k);
                }
            }
        }

        #Adulteracion
        foreach ($value["Adulteracion"] as $k => $v) {
            if (!in_array($k, $celdasAdulteracion["resultados"])) {
                array_push($celdasAdulteracion["resultados"], $k);
            }
        }
    }

    $sumaTambores = 0;
    $sumaKgs = 0;


    echo '<table  width="100%" border="1">';
    echo '<tr>';
    echo '<th style="background:#bdc3c7" colspan="3">Datos</th>';
    echo '<th style="background:#bdc3c7" colspan=' . count($celdasSulfa["resultados"]) . '>' . utf8_decode("Antibióticos") . '</th>';
    echo '<th style="background:#bdc3c7" colspan=' . count($celdasEstrepto["resultados"]) . '>' . utf8_decode("Estrepto") . '</th>';
    echo '<th style="background:#bdc3c7" colspan=' . count($celdasProceso["resultados"]) . '>HMF</th>';
    echo '<th style="background:#bdc3c7" colspan=' . count($celdasAdulteracion["resultados"]) . '>' . utf8_decode("Adulteración") . '</th>';
    echo '</tr>';

    echo '<tr>';
    echo '<th style="background:#bdc3c7">Procedencia</th>';
    echo '<th style="background:#bdc3c7">Tambores</th>';
    echo '<th style="background:#bdc3c7">Kgs</th>';
    #Imprimir las variables que se hayan generado de Sulfa, Estrepto, Proceso y Adulteración
    foreach ($celdasSulfa["resultados"] as $variable) {
        echo '<th style="background:#bdc3c7">' . utf8_decode($variable) . '</th>';
    }
    foreach ($celdasEstrepto["resultados"] as $variable) {
        echo '<th style="background:#bdc3c7">' . utf8_decode($variable) . '</th>';
    }
    foreach ($celdasProceso["resultados"] as $variable) {
        echo '<th style="background:#bdc3c7">' . utf8_decode($variable) . '</th>';
    }
    foreach ($celdasAdulteracion["resultados"] as $variable) {
        echo '<th style="background:#bdc3c7">' . utf8_decode($variable) . '</th>';
    }
    echo '</tr>';


    foreach ($groupLocalidad as $k => $lc) {
        $localidadLab = $k;
        $tambores = $lc['tambores'];
        $Kgs = $lc['Kgs'];

        $sumaTambores = $sumaTambores + $lc['tambores'];
        $sumaKgs = $sumaKgs + $lc['Kgs'];
        echo '<tr>';
        echo '<td>' . strtoupper($localidadLab) . '</td>';
        echo '<td>' . $tambores . '</td>';
        echo '<td>' . $Kgs . '</td>';

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
        foreach ($celdasProceso['resultados'] as $celda) {
            if (array_key_exists($celda, $lc["Proceso"])) {
                echo '<td>' . $lc["Proceso"][$celda] . '</td>';
            } else {
                echo '<td>0</td>';
            }
        }
        foreach ($celdasAdulteracion['resultados'] as $celda) {
            if (array_key_exists($celda, $lc["Adulteracion"])) {
                echo '<td>' . $lc["Adulteracion"][$celda] . '</td>';
            } else {
                echo '<td>0</td>';
            }
        }
        echo '</tr>';
    }
    echo '</table>';
    echo '<br>';

    #Calcular sumas
    $sumas = array(
        "Sulfa" => [],
        "Estrepto" => [],
        "Proceso" => [],
        "Adulteracion" => []
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
        foreach ($loc["Proceso"] as $k => $v) {
            if ($k == "Aprobados" || $k == "Rechazados") {
                if (array_key_exists($k, $sumas["Proceso"])) {
                    $sumas["Proceso"][$k] += $v;
                } else {
                    $sumas["Proceso"][$k] = $v;
                }
            }
        }
        foreach ($loc["Adulteracion"] as $k => $v) {
            if (array_key_exists($k, $sumas["Adulteracion"])) {
                $sumas["Adulteracion"][$k] += $v;
            } else {
                $sumas["Adulteracion"][$k] = $v;
            }
        }
    }

    #Calcular porcentaje
    $porcentaje = array(
        "Sulfa" => [],
        "Estrepto" => [],
        "Proceso" => [],
        "Adulteracion" => []
    );

        
    foreach ($sumas["Sulfa"] as $concepto => $total) {
        $porcentaje["Sulfa"][$concepto] = ($total / $sumaTambores) * 100;
    }
    foreach ($sumas["Estrepto"] as $concepto => $total) {
        $porcentaje["Estrepto"][$concepto] = ($total / $sumaTambores) * 100;
    }
    foreach ($sumas["Proceso"] as $concepto => $total) {
        $porcentaje["Proceso"][$concepto] = ($total / $sumaTambores) * 100;
    }
    foreach ($sumas["Adulteracion"] as $concepto => $total) {
        $porcentaje["Adulteracion"][$concepto] = ($total / $sumaTambores) * 100;
    }
    
    
    
    echo '<table  width="100%" border="1">';
    echo '<tr>';
    echo '<td style="background:#bdc3c7"><b>Total :</b> </td>';
    echo '<td>' . $sumaTambores . '</td>';
    echo '<td>' . $sumaKgs . '</td>';
    foreach ($sumas["Sulfa"] as $totalSF) {
        echo '<td>' . $totalSF . '</td>';
    }
    foreach ($sumas["Estrepto"] as $totalST) {
        echo '<td>' . $totalST . '</td>';
    }
    foreach ($sumas["Proceso"] as $totalHMF) {
        echo '<td>' . $totalHMF . '</td>';
    }
    foreach ($sumas["Adulteracion"] as $totalC13) {
        echo '<td>' . $totalC13 . '</td>';
    }
    echo '</tr>';
    echo '<tr>';
    echo '<td style="background:#bdc3c7" colspan="1"><b>Porcentaje :</b> </td>';
    echo '<td></td>';
    echo '<td></td>';
    foreach ($porcentaje["Sulfa"] as $porSF) {
        echo '<td>' . number_format($porSF, 2, '.', ',') . "%" . '</td>';
    }
    foreach ($porcentaje["Estrepto"] as $porST) {
        echo '<td>' . number_format($porST, 2, '.', ',') . "%" . '</td>';
    }
    foreach ($porcentaje["Proceso"] as $porHMF) {
        echo '<td>' . number_format($porHMF, 2, '.', ',') . "%" . '</td>';
    }
    foreach ($porcentaje["Adulteracion"] as $porC13) {
        echo '<td>' . number_format($porC13, 2, '.', ',') . "%" . '</td>';
    }
    echo '</tr>';
    echo '</table>';
} else {
    echo '<b>No hay Informacion de concentrado de miel en este rangon de fechas</b>';
}
    