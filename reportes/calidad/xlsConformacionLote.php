<?php

include_once '../../clases/consultas.php';
$cali = new lote();

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$conexion = $pdo->conectar();

//$idLoteInterno = '1';
$idLoteInterno = $_GET['idLoteInterno'];
$tmp = $_GET["tmp"];

$fecha = date("d-m-Y");
$hoy = date("H:i:s");

header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=Conformacion de Lote_$fecha.xls");
header("Prafma: no-cache");
header("Expires:0");
if ($tmp == 1) {
    $titlo = "Conformación de lote miel 100% pura de abeja";
} else if ($tmp == 5) {
    $titlo = "Conformación de lote miel 100% mantequilla";
} else if ($tmp == 6) {
    $titlo = "Conformación de lote miel 100% altiplano";
} else if ($tmp == 7) {
    $titlo = "Conformación de lote miel 100% naranjo";
}  else if ($tmp == 8) {
    $titlo = "Conformación de lote miel 100% aguacate";
} else if ($tmp == 9) {
    $titlo = "Conformación de lote miel 100% mezquite";
} else {
    $titlo = "Conformación de lote miel 100% orgánica";
}


echo '<table width="100%">';
echo '<tr>';
echo '<td width = "50%" style="color:#0000;"> <span style="font-weight: bold; font-size: 18pt;">' . utf8_decode($titlo) . '</span></td>';
echo '</tr>';
echo '</table>';

$lote = $cali->EncabeLote($idLoteInterno, $tmp);
$datsLote = $conexion->prepare($lote);
$datsLote->execute();

while ($EncaLote = $datsLote->fetch()) {
    $encaLoteInt = new stdClass();
    $encaLoteInt->fechaEntr = $EncaLote['fecha'];
    $encaLoteInt->loteInt = $EncaLote['idLoteInterno'];
    $encaLoteInt->cliente = $EncaLote['lote'];
    // $encaLoteInt->floracion = $EncaLote['floracion'];
    $encaLoteInt->numeroTambores = $EncaLote['numeroDeTambores'];
    $encaLoteInt->observa = $EncaLote['observaciones'];
    if ($tmp == 1) {
        $encaLoteInt->iniciales = "LC26-";
    } else if ($tmp == 5) {
        $encaLoteInt->iniciales = "LM26-";
    }  else if ($tmp == 6) {
        $encaLoteInt->iniciales = "LA26-";
    }  else if ($tmp == 7) {
        $encaLoteInt->iniciales = "LN26-";
    }   else if ($tmp == 8) {
        $encaLoteInt->iniciales = "LG25-";
    }  else if ($tmp == 9) {
        $encaLoteInt->iniciales = "LZ26-";
    } else {
        $encaLoteInt->iniciales = "LO26-";
    }
}

echo '<table  width="100%"  style="font-family: serif;" cellpadding="5">';
echo '<tr>';
echo '<td width="49%" >
                    <span style="font-size: 7pt; color: #555555; font-family: sans;"></span><br />' .
    'Fecha de Entrega:<b> ' . $encaLoteInt->fechaEntr . '</b><br />' .
    'Lote Interno:<b> ' . $encaLoteInt->iniciales . '' . $encaLoteInt->loteInt . '</b><br />' .
    'Cliente:<b> ' . $encaLoteInt->cliente . '</b><br />' .
    'N&#250;mero de Tambores:<b> ' . $encaLoteInt->numeroTambores . '</b><br />' .
    'Hora :<b> ' . $hoy . '</b><br />'.
    // 'Floraci&#243n:<b> ' . $encaLoteInt->floracion . '</b><br />
            '</td>';
echo '<td width="50%" > </td>';
echo '</tr>';
echo '</table>';

echo '<br>';
echo '<table width="100%" border="1">';
echo '<tr>';
echo '<th>No</th>';
echo '<th>Folio</th>';
echo '<th>Zona</th>';
echo '<th>Peso Bruto</th>';
echo '<th>Tara</th>';
echo '<th>Peso Neto</th>';
echo '<th>Humedad</th>';
echo '</tr>';

$detaLote = $cali->detalleLoteInt($idLoteInterno, $tmp);

$contLote = 0;
$sumaBrutoLote = 0;
$sumaTaraLote = 0;
$sumaNetoLote = 0;

foreach ($detaLote as $detalleLote) {
    $folioTam  = $detalleLote['folioTambor'];
    $zona      = $detalleLote["zona"];
    $brutoLote = $detalleLote['bruto'];
    $taraLote  = $detalleLote['tara'];
    $netoLote  = $detalleLote['neto'];
    $humedad  = $detalleLote['humedad'];
    $clasificacionMiel  = $detalleLote['clasificacionMiel'];

    $contLote = $contLote + 1;
    $sumaBrutoLote = $sumaBrutoLote + $brutoLote;
    $sumaTaraLote = $sumaTaraLote + $taraLote;
    $sumaNetoLote = $sumaNetoLote + $netoLote;

    echo '<tr>';
    echo '<td>' . $contLote . '</td>';
    echo '<td style="text-align: right">' . $folioTam . ' ' . $clasificacionMiel . '</td>';
    echo '<td>' . $zona . '</td>';
    echo '<td>' . $brutoLote . '</td>';
    echo '<td>' . $taraLote . '</td>';
    echo '<td>' . $netoLote . '</td>';
    echo '<td>' . $humedad . '</td>';
    echo '</tr>';
}
echo '</table>';
echo '<br>';
echo '<table width="100%" >';
echo '</tr>';
echo '<td></td>';
echo '<td></td>';
echo '<td style="border: 1px solid black"><b>Totales:<b></td>';
echo '<td style="border: 1px solid black">' . $sumaBrutoLote . '</td>';
echo '<td style="border: 1px solid black">' . $sumaTaraLote . '</td>';
echo '<td style="border: 1px solid black">' . $sumaNetoLote . '</td>';
echo '</tr>';
echo '</table>';
echo '<br>';

echo '<table width="100%" border="1">';
echo '<th>Observaciones</th>';
echo '</tr>';
echo '<tr>';
echo '<td>' . $encaLoteInt->observa . '</td>';
echo '</tr>';
echo '</table>';


echo '<br>';
$especificacioneLote = $cali->especificaciones($idLoteInterno, $tmp);
$datosEspecificacion = $conexion->prepare($especificacioneLote);
$datosEspecificacion->execute();
$contRow = $datosEspecificacion->rowCount();

if ($contRow > 0) {
    echo '<br>';
    echo '<table  width="100%"  border="1">';
    echo '<tr>';
    echo '<th>' . utf8_decode("Parámetros") . '</th>';
    echo '<th>Especificaciones del Cliente</th>';
    echo '<th>Lab.Oaxaca Miel</th>';
    echo '</tr>';

    while ($infomacionEspecificacion = $datosEspecificacion->fetch()) {
        $tablaEspecificacion = new stdClass();
        $tablaEspecificacion->humedad       = $infomacionEspecificacion['humedad'];
        $tablaEspecificacion->color         = $infomacionEspecificacion['color'];
        $tablaEspecificacion->adulteracion  = $infomacionEspecificacion['adulteracion'];
        $tablaEspecificacion->sf            = $infomacionEspecificacion['sf'];
        $tablaEspecificacion->st            = $infomacionEspecificacion['st'];
        $tablaEspecificacion->tt            = $infomacionEspecificacion['tt'];
        $tablaEspecificacion->hmf           = $infomacionEspecificacion['hmf'];
        $tablaEspecificacion->tipo          = $infomacionEspecificacion['tipo'];

        if ($tablaEspecificacion->tipo == 1) {
            $especiCliente = $tablaEspecificacion;
        } else {
                $especiLaboratorio = $tablaEspecificacion;
            }
    }

    echo '<tr style="text-align: center">';
    echo '<td>% Humedad :</td>';
    echo '<td>' . $especiCliente->humedad . '</td>';
    echo '<td>' . $especiLaboratorio->humedad . '</td>';
    echo '</tr>';

    echo '<tr style="text-align: center">';
    echo '<td>Color :</td>';
    echo '<td>' . $especiCliente->color . '</td>';
    echo '<td>' . $especiLaboratorio->color . '</td>';
    echo '</tr>';

    echo '<tr style="text-align: center">';
    echo '<td>' . utf8_decode('Adulteración :') . '</td>';
    echo '<td>' . $especiCliente->adulteracion . '</td>';
    echo '<td>' . $especiLaboratorio->adulteracion . '</td>';
    echo '</tr>';

    echo '<tr style="text-align: center">';
    echo '<td>SF :</td>';
    echo '<td>' . $especiCliente->sf . '</td>';
    echo '<td>' . $especiLaboratorio->sf . '</td>';
    echo '</tr>';

    echo '<tr style="text-align: center">';
    echo '<td>ST :</td>';
    echo '<td>' . $especiCliente->st . '</td>';
    echo '<td>' . $especiLaboratorio->st . '</td>';
    echo '</tr>';

    echo '<tr style="text-align: center">';
    echo '<td>TT :</td>';
    echo '<td>' . $especiCliente->tt . '</td>';
    echo '<td>' . $especiLaboratorio->tt . '</td>';
    echo '</tr>';

    echo '<tr style="text-align: center">';
    echo '<td>HMF :</td>';
    echo '<td>' . $especiCliente->hmf . '</td>';
    echo '<td>' . $especiLaboratorio->hmf . '</td>';
    echo '</tr>';
    echo '</table>';
    echo '<br>';
} else {

    echo '<b>No hay especificaciones registradas en el lote</b>';
    echo '<br>';
}

echo '<br>';
echo '<table width="100%" style="font-family: serif; text-align:center" cellpadding="0">';
echo '<tr>';
echo '<td width="25%" >';
echo '<span style="font-weight: bold; font-size: 10pt;">' . utf8_decode('Jefe de Producción') . '</span><br />';
echo '<br />';
echo '________________________<br />';
echo '</td>';
echo '<td width="25%" >';
echo '<span style="font-weight: bold; font-size: 10pt;">Gerente de Planta</span><br />';
echo '<br />';
echo '________________________<br />';
echo '</td>';
echo '<td width="25%" >';
echo '<span style="font-weight: bold; font-size: 10pt;">Jefe de Calidad</span><br />';
echo '<br />';
echo '________________________<br />';
echo '</td>';
echo '</tr>';
echo '</table>';
