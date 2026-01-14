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
$titulo = $tipoDeMiel == '1' ? "Reporte Adulteración Miel 100% pura de abeja" : "Reporte Adulteración Miel 100% orgánica";

echo '<table>';
echo '<tr>';

echo '</tr>';
echo '<tr>';
echo '<td></td>';
echo '<td></td>';
echo '<td></td>';
echo '<td colspan="5" align="center">
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


$campos = array();

if ($contConcetrado > 0) {
    $c13 = [];

    foreach ($configuraciones as $conf) {
        switch ($conf['idOpcionLab']) {
            case '4':
                array_push($c13, $conf);
                if (!array_key_exists($conf["descripcion"], $campos)) {
                    $campos[$conf["descripcion"]] = 0;
                }
                break;
        }
    }
    ksort($campos);

    $localidades = [];
    foreach ($registros as $res) {

        if (!array_key_exists($res['localidad'], $localidades)) {
            $localidades[$res['localidad']] = [];
            array_push($localidades[$res['localidad']], ["idAlmacen" => $res['idAlmacen'], "neto" => $res['neto'], "c13" => $res['c13']]);
        } else {
            array_push($localidades[$res['localidad']], ["idAlmacen" => $res['idAlmacen'], "neto" => $res['neto'], "c13" => $res['c13']]);
        }
    }

    $groupLocalidad = [];
    foreach ($localidades as $k => $lc) {
        $lc['Kgs'] = 0;
        $lc['tambores'] = 0;
        $lc['Adulteracion'] = array();

        foreach ($lc as $key => $r) {

            if (!is_string($key)) {
                $lc['tambores'] ++;
                $lc['Kgs'] += $r['neto'];

                #Comparaciones C13

                foreach ($c13 as $adulteracion) {
                    switch ($adulteracion['signo']):
                        case '1':
                            if ($r['c13'] < $adulteracion['rango1']) {
                                if (array_key_exists($adulteracion['descripcion'], $lc["Adulteracion"])) {
                                    $lc["Adulteracion"][$adulteracion['descripcion']] += 1;
                                    $campos[$adulteracion["descripcion"]] ++;
                                } else {
                                    $lc["Adulteracion"][$adulteracion['descripcion']] = 1;
                                    $campos[$adulteracion["descripcion"]] ++;
                                }
                            }
                            break;
                        case '2':
                            if ($r['c13'] > $adulteracion['rango1']) {
                                if (array_key_exists($adulteracion['descripcion'], $lc["Adulteracion"])) {
                                    $lc["Adulteracion"][$adulteracion['descripcion']] += 1;
                                    $campos[$adulteracion["descripcion"]] ++;
                                } else {
                                    $lc["Adulteracion"][$adulteracion['descripcion']] = 1;
                                    $campos[$adulteracion["descripcion"]] ++;
                                }
                            }
                            break;
                        case '3':
                            if ($r['c13'] <= $adulteracion['rango1']) {
                                if (array_key_exists($adulteracion['descripcion'], $lc["Adulteracion"])) {
                                    $lc["Adulteracion"][$adulteracion['descripcion']] += 1;
                                    $campos[$adulteracion["descripcion"]] ++;
                                } else {
                                    $lc["Adulteracion"][$adulteracion['descripcion']] = 1;
                                    $campos[$adulteracion["descripcion"]] ++;
                                }
                            }
                            break;
                        case '4':
                            if ($r['c13'] >= $adulteracion['rango1']) {
                                if (array_key_exists($adulteracion['descripcion'], $lc["Adulteracion"])) {
                                    $lc["Adulteracion"][$adulteracion['descripcion']] += 1;
                                    $campos[$adulteracion["descripcion"]] ++;
                                } else {
                                    $lc["Adulteracion"][$adulteracion['descripcion']] = 1;
                                    $campos[$adulteracion["descripcion"]] ++;
                                }
                            }
                            break;
                        case '5':
                            if ($r['c13'] >= $adulteracion['rango1'] && $r['c13'] <= $adulteracion['rango2']) {
                                if (array_key_exists($adulteracion['descripcion'], $lc["Adulteracion"])) {
                                    $lc["Adulteracion"][$adulteracion['descripcion']] += 1;
                                    $campos[$adulteracion["descripcion"]] ++;
                                } else {
                                    $lc["Adulteracion"][$adulteracion['descripcion']] = 1;
                                    $campos[$adulteracion["descripcion"]] ++;
                                }
                            }
                            break;
                    endswitch;
                }
            }
        }
        ksort($lc["Adulteracion"]);
        $groupLocalidad[$k] = $lc;
    }
    #Primera Tabla
    $sumaTamb = 0;
    foreach ($campos as $field) {
        $sumaTamb = $sumaTamb + $field;
    }
    $sumaPor = 0;
    echo '<table  width="100%" border="1">
	<tr align="center">
		<td style="background:#bdc3c7; font-weight:bold">' . utf8_decode("Análisis") . '</td>
		<td style="background:#bdc3c7; font-weight:bold">TAMBORES</td>
		<td style="background:#bdc3c7; font-weight:bold">PORCENTAJE</td>
	</tr>';
    foreach($campos as $k=>$field){
        echo '<tr>
            	<td>'.$k.'</td>
		<td>'.$field.'</td>
		<td>'. number_format(($field/$sumaTamb)*100,2,'.',',').'%'.'</td>
        </tr>';
        $sumaPor = $sumaPor + (($field/$sumaTamb)*100);
    }
    echo '<tr>
    <td style="background:#bdc3c7; font-weight:bold">TOTAL</td>
    <td>'.$sumaTamb.'</td>
    <td>'.$sumaPor.'%'.'</td>
    </tr>
    </table>';
    
    echo '<br>';
    
    #Ordenamiento de los valores para la tabla
    array_multisort($groupLocalidad, SORT_DESC);

    $celdasAdulteracion = array(
        "resultados" => []
    );

    foreach ($groupLocalidad as $key => $value) {

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
    echo '<th style="background:#bdc3c7" colspan=' . count($celdasAdulteracion["resultados"]) . '>' . utf8_decode("Adulteración") . '</th>';
    echo '</tr>';

    echo '<tr>';
    echo '<th style="background:#bdc3c7">Procedencia</th>';
    echo '<th style="background:#bdc3c7">Tambores</th>';
    echo '<th style="background:#bdc3c7">Kgs</th>';

    #Imprimir las variables que se hayan generado Adulteración
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
        "Adulteracion" => []
    );

    foreach ($groupLocalidad as $loc) {
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
        "Adulteracion" => []
    );
    foreach ($sumas["Adulteracion"] as $concepto => $total) {
        $porcentaje["Adulteracion"][$concepto] = ($total / $sumaTambores) * 100;
    }

    echo '<table  width="100%" border="1">';
    echo '<tr>';
    echo '<td style="background:#bdc3c7"><b>Total :</b> </td>';
    echo '<td>' . $sumaTambores . '</td>';
    echo '<td>' . $sumaKgs . '</td>';
    foreach ($sumas["Adulteracion"] as $totalC13) {
        echo '<td>' . $totalC13 . '</td>';
    }

    echo '</tr>';
    echo '<tr>';
    echo '<td style="background:#bdc3c7" colspan="1"><b>Porcentaje :</b> </td>';
    echo '<td></td>';
    echo '<td></td>';
    foreach ($porcentaje["Adulteracion"] as $porC13) {
        echo '<td>' . number_format($porC13, 2, '.', ',') . "%" . '</td>';
    }
    echo '</tr>';
    echo '</table>';
} else {
    echo '<b>No hay Informacion de concentrado de miel en este rangon de fechas</b>';
}
    