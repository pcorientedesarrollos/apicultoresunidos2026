<?php

include_once '../clases/consultas.php';
$dao = new consultas();
// $dao = new laboratorio21();

// $almacen = $dao->laboratorioTambos($_GET['tipoDeMiel']);
// $lab = $dao->laboratorio212($_GET['tipoDeMiel']);
$lab = $dao->laboratorioConsulta2022($_GET['tipoDeMiel']);

$fecha = date("d-m-y");

header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=Laboratorio_$fecha.xls");
header("Prafma: no-cache");
header("Expires:0");

switch ($_GET['tipoDeMiel']) {
    case '1':
        $Titulo = 'Reporte de entradas de tambores (Laboratorio) 100% pura de abeja';
        break;
    case '2':
        $Titulo = 'Reporte de entradas de tambores (Laboratorio) Miel 100% orgánica';
        break;
    case '5':
        $Titulo = 'Reporte de entradas de tambores (Laboratorio) Miel 100% mantequilla';
        break;
    case '6':
        $Titulo = 'Reporte de entradas de tambores (Laboratorio) Miel 100% altiplano';
        break;
    case '7':
        $Titulo = 'Reporte de entradas de tambores (Laboratorio) Miel 100% naranjo';
        break;
    case '8':
        $Titulo = 'Reporte de entradas de tambores (Laboratorio) Miel 100% aguacate';
        break;
    case '9':
        $Titulo = 'Reporte de entradas de tambores (Laboratorio) Miel 100% mezquite';
        break;
    default:
        return;
        break;
}

echo '<table  width="100%">';
echo '<tr>';
echo '<td width = "25%" style="text-aling:left;"> </td>';
echo '<td width = "50%" style="color:#0000;" colspan="5"> <span style="font-weight: bold; font-size: 18pt;">' . utf8_decode($Titulo) . '</span> </td> ';
echo '<td width = "25%" style="text-aling: right";> </td>';
echo '</tr>';
echo '</table>';
$aviso = '* AVISO: En caso de que te saliera el fondo BLANCO, para poner las cuadrículas al documento Excel. Tiene que ir a la pesta&#xF1;a VISTA y hacer click 
    en  recuadro de LINEAS DE CUADRICULA. (Elimina posteriormente este aviso)';
echo '<br> <br>';
echo utf8_decode($aviso);

echo '<br> <br>';
echo '<table  width="100%" style="font-family: serif; text-aling:center;" cellpadding="5">';
echo '<tr>';
echo '<th>Folio</th>';
echo '<th>Fecha</th>';
echo '<th style="text-align:center">Proveedor</th>';
echo '<th style="text-align:center">Localidad</th>';
echo '<th style="text-align:center">Floracion</th>';
echo '<th >ID</th>';
echo '<th >Bruto</th>';
echo '<th >Tara</th>';
echo '<th >Neto</th>';
// echo '<th >Exp.</th>';
// echo '<th >Lote</th>';
echo '<th > </th>';
echo '<th > </th>';
echo '<th style="text-align:center">H%</th>';
echo '<th >Color</th>';
echo '<th>SF</th>';
echo '<th>ST</th>';
echo '<th>C13</th>';
echo '<th >HMF</th>';
echo '<th >F/G</th>';
echo '<th >Resultado</th>';
echo '<th >MI</th>';
echo '<th >Comentarios</th>';
echo '</tr>';

// $labo = $dao->laboratorio();

foreach ($lab as $rsl) {
    // $laboratorio = $dao->laboratorioResultados($_GET['tipoDeMiel'], $rsl['idAlmacen']);
    // $lotes = $dao->laboratorioLotes($_GET['tipoDeMiel'], $rsl['idAlmacen']);

    $folio = $rsl['idAlmacen'];
    $clasificacion = $rsl['clasificacionMiel'];
    $proveedor = $rsl['nombre'];
    $porce = $rsl['porcentajeDescripcion'];
    $sf = $rsl['sfDescripcion'];
    $st = $rsl['stDescripcion'];
    $c13 = $rsl['adulteracionDescripcion'];
    $hmf = $rsl['procesoDescripcion'];
    $color = $rsl['color'];
    $final = $rsl['resultado'];
    $marca = $rsl['marcaInterna'];
    $fechas = $rsl['fecha'];
    $bruto = $rsl['bruto'];
    $tara = $rsl['tara'];
    $neto = $rsl['neto'];
    $localidad = $rsl['localidad'];
    $sagarpa = $rsl['idSagarpa'];
    $floracion = $rsl['floracion'];
    // $exp = $rsl['exp'];
    // $lote = $rsl['lote'];

    echo '<tr>';
    echo '<td style="text-align:center">' . $folio . ' '. $clasificacion ."</td>";
    echo '<td>' . $fechas . "</td>";
    echo '<td>' . $proveedor . "</td>";
    echo '<td>' . utf8_decode($localidad) . '</td>';
    echo '<td>' . $floracion . "</td>";
    echo '<td>' . $sagarpa . '</td>';
    echo '<td>' . $bruto . "</td>";
    echo '<td>' . $tara . "</td>";
    echo '<td>' . $neto . "</td>";
    echo '<td></td>';
    echo '<td> </td>';
    // echo '<td>' . $exp . "</td>";
    // echo '<td>' . $lote . "</td>";
    echo '<td style="text-align:center">' . utf8_decode($porce) . "</td>";
    echo '<td>' . $color . "</td>";
    echo '<td>' . $sf . "</td>";
    echo '<td>' . $st . "</td>";
    echo '<td>' . $c13 . "</td>";
    echo '<td>' . utf8_decode($hmf) . "</td>";
    echo '<td> </td>';
    echo '<td>' . $final . "</td>";
    echo '<td>' . $marca . "</td>";
    echo '<td> </td>';
    echo '</tr>';
}

echo '</table>';
