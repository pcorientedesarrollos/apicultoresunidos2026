<?php

include_once '../../clases/consultas.php';
include_once '../../DAOConeccion/conePDO.php';
include_once '../../utilerias/php/dameNombrePersonal.php';

$consultas = new trazabilidad();
$pdo = new conePDO();
$conexion = $pdo->conectar();

$idLoteInterno = $_GET["idLoteInterno"];
$tipoMiel = $_GET["miel"];

$fechaEnvasado = date("d-m-Y");

header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=Trazabilidad de Salida_$fechaEnvasado.xls");
header("Prafma: no-cache");
header("Expires:0");

$tituloTrazabilida = "Trazabilidad de Salida";
$nombre_gerente = dameNombrePersonal(2, $conexion);
$consultaSalida = $consultas->tranzabilidadSalida($idLoteInterno, $tipoMiel);
$dataSalida = $conexion->prepare($consultaSalida);
$dataSalida->execute();
while ($informacionSalida = $dataSalida->fetch()) {
    $detallesSalida = new stdClass();
    $detallesSalida->fechaEnvasado = $informacionSalida["fechaEnvasado"];
    $detallesSalida->marcaFinalCliente = $informacionSalida["marcaFinalCliente"];
    $detallesSalida->kg = $informacionSalida["kilosSalida"];
    $detallesSalida->fchSalida = $informacionSalida["fechaSalida"];
    $detallesSalida->empresa = $informacionSalida["empresa"];
    $detallesSalida->pais = $informacionSalida["pais"];
    $detallesSalida->kgExportar = $informacionSalida["kgExportar"];
    $detallesSalida->tipoMiel = $informacionSalida["tipoDeMiel"];
    if ($informacionSalida["homogeneizado"] == 1) {
        $detallesSalida->homogeneizado = "Sí";
    } else {
        $detallesSalida->homogeneizado = "No";
    }

}

$consultaSalida1 = $consultas->tranzabilidadSalidaArray($idLoteInterno, $tipoMiel);
$dataSalida1 = $conexion->prepare($consultaSalida1);
$dataSalida1->execute();

echo '<table width="100%">';
echo '<tr>';
echo ' <td width="50%" style="color:#0000; ">
                    <span style="font-weight: bold; font-size: 18pt;">' . $tituloTrazabilida . '</span>
       </td>';
echo '</tr>';
echo '<tr>';
echo ' <td width="50%" style="color:#0000; ">
                    <span style="font-weight: bold; font-size: 18pt;">' . utf8_decode($detallesSalida->tipoMiel) . '</span>
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
        <th style="text-transform: none; font-weight: 700; text-align: center; font-size: 20px; background-color: yellowgreen" colspan="10">Trazabilidad de Salida</th>
     </tr>';
echo ' <tr>
            <td rowspan = "4" align="center">Fecha de Envasado (9)</td>
            <td colspan="2" align="center">Lote (10)</td>
            <td rowspan = "4" align="center">No. de ID de Apicultores que Conforman el Lote (11)</td>
            <td rowspan = "4" align="center">Volumen (kg) de Miel Usada por Apicultor (12)</td>
            <td></td>
            <td colspan="4" align="center">Destino y Volumen (Kg) (14)</td>
         </tr>
         <tr>
            <td align="center">Homogenizado (a)</td>
            <td align="center">' . utf8_decode($detallesSalida->homogeneizado) . '</td>
            <td rowspan = "3" align="center">Fecha de Salida (13)</td>
            <td rowspan = "3" align="center">Empresa de Destino / Pais (e)</td>
            <td rowspan = "3" align="center">Cantidad Total de  (Kg) a exportar.(f)</td>
            <td rowspan = "3" align="center">Nacional (Kg) (g)</td>
            <td rowspan = "3" align="center">Consumidor Directo (Kg) (h)</td>
         </tr>
         <tr>
         <td align="center">Sin Homogenizar (b)</td>
         <td></td>
        </tr>
        <tr>
         <td align="center">' . utf8_decode('N° (c)') . '</td>
         <td align="center">Kg (d)</td>
        </tr>';

$sumaVolumenApi = 0;
while ($infoAdicional = $dataSalida1->fetch()) {
    echo '<tr>
            <td align="center"></td>
            <td align="center"></td>
            <td align="center"></td>
            <td align="center">' . $infoAdicional["idSagarpa"] . '</td>
            <td align="center">' . $infoAdicional["volumen"] . '</td>
            <td align="center"></td>
            <td align="center"></td>
            <td align="center"></td>
            <td></td>
            <td></td>
           </tr>';
    $sumaVolumenApi = $sumaVolumenApi + $infoAdicional["volumen"];

}

echo ' <tr>
       <td align="center">' . $detallesSalida->fechaEnvasado . '</td>
       <td align="center">' . $detallesSalida->marcaFinalCliente . '</td>
       <td align="center">' . number_format($detallesSalida->kg, 0, '.', ',') . '</td>
       <td></td>
       <td align="center"><b>' . number_format($sumaVolumenApi, 0, '.', ',') . '</b></td>
       <td align="center">' . $detallesSalida->fchSalida . '</td>
       <td align="center">' . $detallesSalida->empresa . '<br>' . $detallesSalida->pais . '</td>
       <td align="center">' . number_format($detallesSalida->kgExportar, 0, '.', ',') . '</td>
       <td></td>
       <td></td>
       </tr>
        </table><br>
        <div>FO-BP-PI-PO-01/09</div><br>
        <table width="100%">
            <tr> 
                <td width="33%">
                <span style="font-weight: bold; font-size: 12pt;">Firma del Responsable</span><br />
                    <br />
                   ________________________<br />
                   
            </tr>
          </table>
        </body>
        </html>';