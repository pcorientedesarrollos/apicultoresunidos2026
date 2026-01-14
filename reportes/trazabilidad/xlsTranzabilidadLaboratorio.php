<?php

include_once '../../clases/consultas.php';
include_once '../../DAOConeccion/conePDO.php';
include_once '../../utilerias//php/dameNombrePersonal.php';

$consulta = new trazabilidad();
$pdo = new conePDO();
$conexion = $pdo->conectar();

$idLoteInterno = $_GET["idLoteInterno"];
//$idLoteInterno = 2;
$fechaEnvasado = date("d-m-Y");
$tipo = $_GET['tipoMiel'];
if($tipo == '1'){
    $tituloTrazabilida = "Trazabilidad de Laboratorio Miel 100% Pura de Abeja";
}else if($tipo == '2'){
    $tituloTrazabilida = "Trazabilidad de Laboratorio Miel 100% Orgánica";
}else if($tipo == '5'){
    $tituloTrazabilida = "Trazabilidad de Laboratorio Miel 100% Mantequilla";
}else if($tipo == '6'){
    $tituloTrazabilida = "Trazabilidad de Laboratorio Miel 100% Altiplano";
}else if($tipo == '7'){
    $tituloTrazabilida = "Trazabilidad de Laboratorio Miel 100% Naranjo";
}else if($tipo == '8'){
    $tituloTrazabilida = "Trazabilidad de Laboratorio Miel 100% Aguacate";
}else if($tipo == '9'){
    $tituloTrazabilida = "Trazabilidad de Laboratorio Miel 100% Mezquite";
}

header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=Trazabilidad de Laboratorio_$fechaEnvasado.xls");
header("Prafma: no-cache");
header("Expires:0");

// $tituloTrazabilida = "Trazabilidad de Laboratorio";

$nombre_gerente = dameNombrePersonal(2, $conexion);
$consultaLab = $consulta->trazabilidadAnalisis($tipo, $idLoteInterno);
$dataLab = $conexion->prepare($consultaLab);
$dataLab->execute();
echo '<table width="100%">';
echo '<tr>';
echo ' <td width="50%" style="color:#0000; ">
                    <span style="font-weight: bold; font-size: 18pt;">' . utf8_decode($tituloTrazabilida) . '</span>
       </td>';
echo '</tr>';
echo '</table> <br>';
echo '<table width="100%" style="font-family: serif;" cellpadding="5">';
echo '<tr>';
echo '         <td width="50%" >
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
                8.- CORREO: <u>' . $nombre_gerente['correo'] . '</u>
                <td>';
echo '</tr>';
echo '</table> <br>';
echo '<table width="100%" style="font-size: 9pt; border-collapse: collapse; text-align: center; " cellpadding="4" border="1">';
echo '<tr> 
        <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 20px; background-color: gold" colspan="6">Trazabilidad de Laboratorio</th>
      </tr>';
echo '<tr> 
             <th style="text-transform: none; font-weight: 600; background: none" align="center">'. utf8_decode('N° de Muestra (9)').'</th>
             <th style="text-transform: none; font-weight: 600; background: none" align="center">'. utf8_decode('N° de Lote (10)').'</th>
             <th style="text-align: right; text-transform: none; font-weight: 600; background: none" align="center">'. utf8_decode('N° de ID del Proveedor (11)').'</th>
             <th style="text-align: right; text-transform: none; font-weight: 600; background: none" align="center">Nombre del Laboratorio (12)</th>
             <th style="text-align: right; text-transform: none; font-weight: 600; background: none" align="center">Fecha de Protocolo (13)</th>
             <th style="text-align: right; text-transform: none; font-weight: 600; background: none" align="center">'. utf8_decode('N° de Folio de la Constancia del Protocolo (14)').'</th>
      </tr>';
while ($infoTransLab = $dataLab->fetch()){
    
    $marcaCliente = $infoTransLab["marcaFinalCliente"];
    $idSagarpaTranz = $infoTransLab["idSagarpa"];
    $nombreLab = $infoTransLab["nombreLaboratorio"];
    $fechaProto = $infoTransLab["fechaProtocolo"];
    $folioProto = $infoTransLab["folioProtocolo"];

    echo '<tr>';
    echo '<td align="center"></td>';
    echo '<td align="center">' . $marcaCliente . '</td>';
    echo '<td align="center">' . $idSagarpaTranz . '</td>';
    echo '<td align="center">' . $nombreLab . '</td>';
    echo '<td align="center">' . $fechaProto . '</td>';
    echo '<td align="center">' . $folioProto.'</td>';
    echo '</tr>';
}