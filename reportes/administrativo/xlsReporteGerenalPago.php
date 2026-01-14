<?php

include_once '../../clases/consultas.php';
include_once '../../DAOConeccion/conePDO.php';
$consulta = new adminitrativo();
$pdo = new conePDO();
$conexion = $pdo->conectar();

$idProveedor = $_GET["idProveedor"];
$tmp = $_GET["tmp"];
if($tmp == 1){
    $tituloReporte = "Reporte General de Precios Miel 100% Pura de Abeja";    
} else if($tmp == 5){
    $tituloReporte = "Reporte General de Precios Miel 100% Mantequilla";    
} else if($tmp == 6){
    $tituloReporte = "Reporte General de Precios Miel 100% Altiplano";    
} else if($tmp == 7){
    $tituloReporte = "Reporte General de Precios Miel 100% Naranjo";    
} else if($tmp == 8){
    $tituloReporte = "Reporte General de Precios Miel 100% Aguacate";    
} else if($tmp == 9){
    $tituloReporte = "Reporte General de Precios Miel 100% Mezquite";    
} else{
    $tituloReporte = "Reporte General de Precios Miel 100% Orgánica";    
}
// Fecha de creacion del archivo xmls
$fechaReporte = date("d-m-Y");

//Se empiza a elaborar la creacion del xls
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename = Reporte General de Precios por Proveedor_$fechaReporte.xls");
header("Prafma: no-cache");
header("Expires:0");

$consultaReportePrecios = $consulta->reporteGeneralPrecios($idProveedor, $tmp);
$dataReportePrecios = $conexion->prepare($consultaReportePrecios);
$dataReportePrecios->execute();

while ($nombreApicultor = $dataReportePrecios->fetch()) {
      $nombreDelApiculor = new stdClass();
      $nombreDelApiculor->apicultor = $nombreApicultor['nombre'];
}

echo '
<table>
        <tr>
            <td width = "50%" style="color:#0000;"><span style="font-weight: bold; font-size: 18pt;">' . utf8_decode($tituloReporte) . '</span></td>
        </tr>
        </table>
        
        <br/>
        <th>
        <p >
        <b style="text-aling: right;">
        '. utf8_decode('Código: ') .' RCO-PTM-02
        </b>
        </p>
        </th>

        <div><b style="font-size: 13pt;">Apicultor: '.$nombreDelApiculor->apicultor.'</b> </div><br/>
        <table width="100%" border=1">
            <thead>
                <th>' . utf8_decode('N°') . '</th> 
                <th>' . utf8_decode('N°Entrada') . '</th> 
                <th>Fecha</th>
                <th>Localidad</th>
                <th>Tambores</th>
                <th>Kgs</th>
                <th>Total</th>
            <thead>
            <tbody>';
$consultaReportePreciosTabla = $consulta->reporteGeneralPrecios($idProveedor, $tmp);
$dataReportePreciosTabla = $conexion->prepare($consultaReportePreciosTabla);
$dataReportePreciosTabla->execute();
$contPrecios = 1;
$sumaKgs = 0;
$sumaRegistros = 0;
$sumaTotal = 0;
while ($infoReportePrecios = $dataReportePreciosTabla->fetch()) {
    echo '<tr>';
    echo '<td>' . $contPrecios . '</td>';
    echo '<td>' . $infoReportePrecios['idAlmacen'] . '</td>';
    echo '<td>' . $infoReportePrecios['fecha'] . '</td>';
    echo '<td>' . $infoReportePrecios['localidad'] . '</td>';
    echo '<td>' . $infoReportePrecios['registros'] . '</td>';
    echo '<td>' . number_format($infoReportePrecios['kgs'], 0, '.', ',') . '</td>';
    echo '<td>' . number_format($infoReportePrecios['totalCompra'], 1, '.', ',') . '</td>';
    echo '</tr>';

    $contPrecios = $contPrecios + 1;
    $sumaRegistros = $sumaRegistros + $infoReportePrecios['registros'];
    $sumaKgs = $sumaKgs + $infoReportePrecios['kgs'];
    $sumaTotal = $sumaTotal + $infoReportePrecios['totalCompra'];
}
echo ' <tr>
           <td><b style="font-size: 12pt;">Totales: </b></td>
           <td></td>
           <td></td>
           <td></td>
           <td><b style="font-size: 12pt;">' . number_format($sumaRegistros, 0, '.', ',') . '</b></td>
           <td><b style="font-size: 12pt;">' . number_format($sumaKgs, 0, '.', ',') . '</b></td>
           <td><b style="font-size: 12pt;">' . number_format($sumaTotal, 1, '.', ',') . '</b></td>
       </tr>     
       </tbody>
        </table
        <table></';
