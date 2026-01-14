<?php

include_once '../clases/consultas.php';

$dao = new consultas();
$idAlmacen = $_GET['idAlmacen'];
$folioEntradaTambor = $_GET['folioEntradaTambor'];
$tmp = $_GET['tmp'];

$infors = $dao->encabezadoPrecios($idAlmacen, $folioEntradaTambor, $tmp);

//Fecha de exportacion
$fecha = date("d-m-Y");

//Inicip de la instancia para ña ex´prtacion en Excel
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=Reporte de Venta_$fecha.xls");
header("Prafma: no-cache");
header("Expires:0");


//$infors = $dao->encabezadoPrecios($id);

if ($folioEntradaTambor == 99999) {
    if ($tmp == 1) {
        $Titulo = 'Comprobante de pago de Miel 100% pura de abeja (Cubetas)';
    } else if ($tmp == 5) {
        $Titulo = 'Comprobante de pago de Miel 100% mantequilla (Cubetas)';
    } else if ($tmp == 6) {
        $Titulo = 'Comprobante de pago de Miel 100% altiplano (Cubetas)';
    } else if ($tmp == 7) {
        $Titulo = 'Comprobante de pago de Miel 100% naranjo (Cubetas)';
    }  else if ($tmp == 8) {
        $Titulo = 'Comprobante de pago de Miel 100% aguacate (Cubetas)';
    } else if ($tmp == 9) {
        $Titulo = 'Comprobante de pago de Miel 100% mezquite (Cubetas)';
    } else {
        $Titulo = 'Comprobante de pago de Miel 100% orgánica (Cubetas)';
    }
    while ($resuls = $infors->fetch()) {
        $enca2 = new stdClass();
        $enca2->nombre = $resuls['nombre'];
        $enca2->localidad = $resuls['localidad'];
        $enca2->idSagarpa = $resuls['idSagarpa'];
        $enca2->folio = $resuls['idAlmacen'];
        $enca2->totalCompra = number_format($resuls['totalCompra'], 2, '.', ',');
    }
} else {
    if ($tmp == 1) {
        $Titulo = 'Comprobante de pago de Miel 100% pura de abeja';
    } else if ($tmp == 5) {
        $Titulo = 'Comprobante de pago de Miel 100% mantequilla';
    }  else if ($tmp == 6) {
        $Titulo = 'Comprobante de pago de Miel 100% altiplano';
    }  else if ($tmp == 7) {
        $Titulo = 'Comprobante de pago de Miel 100% naranjo';
    }   else if ($tmp == 8) {
        $Titulo = 'Comprobante de pago de Miel 100% aguacate';
    }  else if ($tmp == 9) {
        $Titulo = 'Comprobante de pago de Miel 100% mezquite';
    } 
    else {
        $Titulo = 'Comprobante de pago de Miel 100% orgánica';
    }
    while ($resuls = $infors->fetch()) {
        $enca2 = new stdClass();
        $enca2->nombre = $resuls['nombre'];
        $enca2->localidad = $resuls['localidad'];
        $enca2->idSagarpa = $resuls['idSagarpa'];
        $enca2->folio = $resuls['folio'];
        $enca2->totalCompra = number_format($resuls['totalCompra'], 2, '.', ',');
    }
}



echo '<table  width="100%">';
echo '<tr>';
echo '<td width = "50%" style="color:#0000;"> <span style="font-weight: bold; font-size: 18pt;">' . utf8_decode($Titulo) . '</span> </td> ';
echo '<td width = "25%" style="text-aling: right";> <span style="font-weight: bold; font-size: 12pt;">Entrada-Folio:' . $enca2->folio . '</span> </td>';
echo '</tr>';
echo '</table><br/>';


echo '<table  width="100%" border="1" style="font-family: serif;" cellpadding="5">';
echo '<tr>';
echo '<td width="49%" >
                    <span style="font-size: 7pt; color: #555555; font-family: sans;">CLIENTE:</span><br />' .
    'Nombre:<b> ' . $enca2->nombre . '</b><br />' .
    'Localidad: ' . $enca2->localidad . '<br />' .
    'ID Sagarpa: ' . $enca2->idSagarpa . '<br />
                </td>';
echo '<td width="50%" >
                    <span style="font-size: 7pt; color: #555555; font-family: sans;">COMPRADOR:</span><br />
                    <span style="font-weight: bold; font-size: 10pt;">Apicultores Unidos de la Pen&#237nsula, S.A. de C.V.</span><br />
                    Carretera M&#233rida - Canc&#250n Km 7.5 S/N<br />
                    Kanas&#237n , Yucat&#225n.<br />
                    Tel&#233fonos: (9999)880980  /  (9992)122858<br /> </td>';
echo '</tr>';
echo '</table>';

echo '<table  width="100%" border=1">';
echo '<tr>';
echo '<th width="5%">Folio</th>';
echo '<th width="10%">Zona</th>';
echo '<th width="10%">P.Lista</th>';
echo '<th width="10%">Bruto</th>';
echo '<th width="10%">Tara</th>';
echo '<th width="10%">Neto</th>';
echo '<th width="10%">Dif.</th>';
echo '<th width="10%">Humedad</th>';
echo '<th width="10%">Precio</th>';
echo '<th width="15%">Costo</th>';
echo '</tr>';

$tabla = $dao->tablaPrecios($idAlmacen, $folioEntradaTambor, $tmp);

while ($conten = $tabla->fetch()) {
    $folio = $conten['idAlmacen'];
    $zona = $conten['zona'];
    $preciolis = $conten['pesoLista'];
    $bruto = $conten['bruto'];
    $tara = $conten['tara'];
    $neto = $conten['neto'];
    $dif = $conten['diferencia'];
    $humedad = $conten['humedad'];
    $precio = $conten['precio'];
    $costo = number_format($conten['costoTotal'], 2, '.', ',');


    echo '<tr>';
    echo '<td>' . $folio . "</td>";
    echo '<td>' . $zona . "</td>";
    echo '<td>' . $preciolis . "</td>";
    echo '<td>' . $bruto . "</td>";
    echo '<td>' . $tara . "</td>";
    echo '<td>' . $neto . "</td>";
    echo '<td>' . $dif . "</td>";
    echo '<td>' . $humedad . "</td>";
    echo '<td>' . $precio . "</td>";
    echo '<td>' . $costo . "</td>";
    echo '</tr>';
}

echo '<tr>';
echo '<td class="blanktotal" colspan="9" ><b></b></td>';
echo '<td class="totals cost"><b>Total:  ' . $enca2->totalCompra . '</b></td>';
echo '</tr>';

echo '</table>';
