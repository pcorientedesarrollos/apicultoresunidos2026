<?php

include_once '../../clases/consultas.php';
include_once '../../DAOConeccion/conePDO.php';
include_once '../../utilerias/php/dameNombrePersonal.php';

$consulta = new trazabilidad();
$pdo = new conePDO();
$conexion = $pdo->conectar();

$idLoteInterno = $_GET["idLoteInterno"];
$tipo = $_GET['tipo'];
if($tipo == '1'){
    $tituloTrazabilida = "Trazabilidad de Entrada Miel 100% Pura de Abeja";
}else if($tipo == '2'){
    $tituloTrazabilida = "Trazabilidad de Entrada Miel 100% Orgánica";
}else if($tipo == '5'){
    $tituloTrazabilida = "Trazabilidad de Entrada Miel 100% Mantequilla";
}else if($tipo == '6'){
    $tituloTrazabilida = "Trazabilidad de Entrada Miel 100% Altiplano";
}else if($tipo == '7'){
    $tituloTrazabilida = "Trazabilidad de Entrada Miel 100% Naranjo";
}else if($tipo == '8'){
    $tituloTrazabilida = "Trazabilidad de Entrada Miel 100% Aguacate";
}else if($tipo == '9'){
    $tituloTrazabilida = "Trazabilidad de Entrada Miel 100% Mezquite";
}

$fechaEnvasado = date("d-m-Y");

header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=Trazabilidad de Entrada_$fechaEnvasado.xls");
header("Prafma: no-cache");
header("Expires:0");

// $tituloTrazabilida = "Trazabilidad de Entrada";

$nombre_gerente = dameNombrePersonal(2, $conexion);

$consultaTrazabilidadEn = $consulta->trazabilidadEntrada($tipo, $idLoteInterno);
$dataTrazabilidaEn = $conexion->prepare($consultaTrazabilidadEn);
$dataTrazabilidaEn->execute();
echo '<table width="100%">';
echo '<tr>';
echo ' <td width="50%" style="color:#0000; ">
                    <span style="font-weight: bold; font-size: 18pt;">' . utf8_decode($tituloTrazabilida) . '</span>
       </td>';
echo '</tr>';
echo '</table> <br>';
echo '<table width="100%" style="font-family: serif;" cellpadding="5">';
echo '<tr>';
echo '
                <td width="50%" >
                1.- RAZON SOCIAL: <u>OaxacaMiel S.A de C.V</u><br>
                3.- DOMICILIO DEL ESTABLECIMIENTO: <br>' . utf8_decode('<u>Carretera Mérida-Cancún K.m 7.5 Sn Pedro Noh Pat</u>') . '<br>
                5.- MUNICIPIO: ' . utf8_decode('<u>Kanasín</u><br>') . '
                7.- TELEFONOS: <u>01-999-9880990</u>              
                </td>';
echo '</td>
                <td width="50%">
                2.- No de ID: 3108771I<br>
                4.- ESTADO : <u>' . utf8_decode('Yucatán') . '</u><br>
                6.- ENCARGADO O RESPONSABLE DEL ACOPIO DE MIEL: <u>' . $nombre_gerente['nombre_completo'] . '</u><br>
                8.- CORREO: <u>' . $nombre_gerente['correo'] .'</u>
                <td>';
echo '</tr>';
echo '</table> <br>';
echo ' <table width="100%" style="font-size: 9pt; border-collapse: collapse; text-align: center; " cellpadding="4" border="1">
                    <tr> 
                       <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 20px;" colspan="6">Trazabilidad de Entrada</th>
                    </tr>
                    <tr> 
                        <th style="text-transform: none; font-weight: 600; background: none" align="center">'. utf8_decode('Fecha de Recepción (9)').'</th>
                        <th style="text-transform: none; font-weight: 600; background: none" align="center">'. utf8_decode('N° de Lote (10)').'</th>
                        <th style="text-align: right; text-transform: none; font-weight: 600; background: none" align="center">'. utf8_decode('Número de ID del Proveedor de Miel (11)').'</th>
                        <th style="text-align: right; text-transform: none; font-weight: 600; background: none" align="center">'. utf8_decode('Dirección del Proveedor (12)').'</th>
                        <th style="text-align: right; text-transform: none; font-weight: 600; background: none" align="center">Volumen Kg (13)</th>
                        <th style="text-align: right; text-transform: none; font-weight: 600; background: none" align="center">'. utf8_decode('N° de Muestra (14)').'</th>
                    </tr>';
$sumaVolumen = 0;
while ($infoTrazabilidadEn = $dataTrazabilidaEn->fetch()) {
    $fecha = $infoTrazabilidadEn["fecha"];
    $marcaCliente = $infoTrazabilidadEn["marcaFinalCliente"];
    $idSagarpaTranz = $infoTrazabilidadEn["idSagarpa"];
    $Direccion = $infoTrazabilidadEn["domicilio"];
    $kilos = $infoTrazabilidadEn["kilos"];
    
    $sumaVolumen = $sumaVolumen + $kilos;

    echo '<tr>';
    echo '<td align="center">' . $fecha . '</td>';
    echo '<td align="center">' . $marcaCliente . '</td>';
    echo '<td align="center">' . $idSagarpaTranz . '</td>';
    echo '<td align="center">' . $Direccion . '</td>';
    echo '<td align="center">' . $kilos . '</td>';
    echo '<td></td>';
    echo '</tr>';
    
}

  echo '<tr>';
    echo '<td align="center"></td>';
    echo '<td align="center"></td>';
    echo '<td align="center"></td>';
    echo '<td align="center"></td>';
    echo '<th align="center">' . number_format($sumaVolumen,0,'.',',') . '</th>';
    echo '<td></td>';
    echo '</tr>';
echo '</table>';