<?php
ob_start();
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$conexion = $pdo->conectar();

$idTamborPeso = $_GET["idTamborPeso"];
$fechaListado = date("d-m-Y");

if (isset($_GET['folios'])) {
    $mostrarFolios = true;
} else {
    $mostrarFolios = false;
}
include_once '../../clases/consultas.php';
$consulta = new listaPesos();
$datosEncabezadoPeso = $consulta->listaPesosEnca($idTamborPeso);
$datosEncabezadoPeso['totalPBruto'] = $datosEncabezadoPeso["totalBruto"];
$datosEncabezadoPeso['totalPNeto'] = $datosEncabezadoPeso["totalNeto"];

header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=Reporte de Lista de Pesos $fechaListado.xls");
header("Prafma: no-cache");
header("Expires:0");

echo '<table width="100%">
        <tr>
            <td colspan="7" style="text-align: center;">
                <span style="font-weight: bold; font-size: 16pt;">Oaxaca Miel, S.A. de C.V.</span><br/>
                <span style="font-weight: bold; font-size: 8pt;">R.F.C. OMI 950913 TV2</span><br/>
                <span style="font-size: 8pt;">2 a. CERRADA DE EMILIANO ZAPATA No. 19 </span><br/>
                <span style="font-size: 8pt;">COL.BOSQUES DEL SUR,</span><br/>
                <span style="font-size: 8pt;">' . utf8_decode('DELEGACION XOCHIMILCO, MÉXICO 16010, D.F.') . '</span><br/>
                <span style="font-size: 8pt;">SALIDA DE ALMACEN</span><br/></br/>
                <span style="font-size: 12pt;"><b>LISTA DE PRODUCTO TERMINADO </b></span><br/>
                <span style="font-size: 12pt;"><b>' . utf8_decode($datosEncabezadoPeso['tipoMiel']) . '</b></span><br/>
                <span style="font-size: 12pt;"><b>Lote ' . $datosEncabezadoPeso['lote'] . '</b></span><br/>                               
            </td>
        </tr>
        </table><br>
    <table style="font-size:11pt; border-collapse: collapse; text-align: center;">
        <tr>
            <td style="text-align: left"><b>Destino: ' . utf8_decode($datosEncabezadoPeso['destino']) . '</b></td>
            <td style="text-align: left;"><b>Codigo: RAL-LPT-01</b></td>
        </tr>
        <tr>
            <td style="text-align: left"><b>Fecha: ' . $datosEncabezadoPeso['fechaImpresion'] . '</b></td>
            <td style="text-align: left;"><b>Revision: 01</b></td>
        </tr>
        <tr>
        <td style="text-align: left"><b>Folio: ' . $datosEncabezadoPeso['idTamborPeso'] . '</b></td>
        <td></td>
        </tr>
    </table><br>';
echo '<table style="font-size: 9pt; border-collapse: collapse; text-align: center;" border="1">
        <tr> 
            <th align="center">No</th>';
if ($mostrarFolios) {
    echo '<th align="center">Folio</th>';
}
echo '<th align="center">Peso Bruto</th>
            <th align="center">Tara</th>
            <th align="center">Peso Neto</th>
            <th align="center">Humedad</th>
            <th align="center">Color</th>
            <th align="center">' . utf8_decode('Floración') . '</th>
            
        </tr>';

$consultaDetalleLista = $consulta->listaPesosDetalle($idTamborPeso);

$contLista = 0;
foreach ($consultaDetalleLista as $infoDetalleLista) {
    $contLista = $contLista + 1;

    $folio  = $infoDetalleLista["folio"];
    $bruto      = $infoDetalleLista['bruto'];
    $tara = $infoDetalleLista['tara'];
    $neto  = $infoDetalleLista['neto'];
    $humedad  = $infoDetalleLista['humedad'];
    $floracion  = $infoDetalleLista['floracion'];
    $color  = $infoDetalleLista['color'];
    echo '<tr>
            <td>' . $contLista . '</td>';
    if ($mostrarFolios) {
        echo '<td style="text-align: right">' . $folio . '</td>';
    }
    echo '<td>' . $bruto . '</td>
            <td>' . $tara . '</td>
            <td>' . $neto . '</td>
            <td>' . $humedad . '</td>
            <td>' . $color . '</td>
            <td>' . $floracion . '</td>
         </tr>';
}
echo '<tr>
            <td><b style="font-size: 12pt;">Totales :</b></td>';
if ($mostrarFolios) {
    echo '<td></td>';
    echo '<td></td>';
}
echo '<td><b style="font-size: 12pt;">' . number_format($datosEncabezadoPeso['totalPBruto'], 0, '.', ',') . '</b></td>
            <td><b style="font-size: 12pt;">' . number_format($datosEncabezadoPeso['totalTara'], 0, '.', ',') . '</b></td>
            <td><b style="font-size: 12pt;">' . number_format($datosEncabezadoPeso['totalPNeto'], 0, '.', ',') . '</b></td>
            <td></td>
            <td></td>
            <td></td>
    </tr> </table><br>';

echo '<table>
        <tr> 
            <td colspan="1">
                <span style="font-weight: bold; font-size: 10pt;">Chofer</span><br><br><br>
                ____________________
            </td>
            <td colspan="2">
                <span style="font-weight: bold; font-size: 10pt;">Gerente de Planta</span><br><br><br>
                ____________________
            </td>
            <td colspan="2">
                <span style="font-weight: bold; font-size: 10pt;">' . utf8_decode('Jefe de Producción') . '</span><br><br><br>
                ____________________
            </td>
            <td colspan="2">
                <span style="font-weight: bold; font-size: 10pt;">' . utf8_decode('Jefe de Calidad') . '</span><br><br><br>
                ____________________
            </td>
        </tr>

        <tr>
            <td colspan="2">
            <span style="font-size: 10pt;">' . utf8_decode($datosEncabezadoPeso['operador']) . '</span><br />
            <span style="font-size: 10pt;">Empresa: ' . utf8_decode($datosEncabezadoPeso['compania']) . '</span><br/>
            <span style="font-size: 10pt;">Marca: ' . utf8_decode($datosEncabezadoPeso['marca']) . '</span><br/>
            <span style="font-size: 10pt;">Modelo: ' . $datosEncabezadoPeso['modelo'] . '</span><br/>
            <span style="font-size: 10pt;">Placa: ' . $datosEncabezadoPeso['placa'] . '</span><br/>
            <span style="font-size: 10pt;">Contenedor: ' . $datosEncabezadoPeso['contenedor'] . '</span><br/>
            <span style="font-size: 10pt;">Sello: ' . $datosEncabezadoPeso['sello'] . '</span><br/>
            </td>
        </tr>
    </table>';
?>