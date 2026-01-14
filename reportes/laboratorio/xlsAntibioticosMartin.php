<?php

include_once '../../clases/consultas.php';
$dao = new consultas();
$oLaboratorio = new stdClass();

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$conexion = $pdo->conectar();

//Fecha de Exportacion
$fecha = date("d-m-y");

//Inicio de la instalacion de la exportacion
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachmen; filename = Reporte de Antibióticos_$fecha.xls");
header("Prafma: no-cache");
header("Expires:0");

$titulo = "Reporte de Antibióticos";

echo '<table width="100%">';
echo '<tr>';
echo '<td width = "50%" style="color#0000;">'
 . '<span style="font-weight:bold; font-size:18pt;">' . utf8_decode($titulo) . '</span><td>';
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

    echo '<table width="100%"';
    echo '<tr>';
    echo '<td width = "25%" style="color:#0000;"> <span style="font-weight: bold; font-size: 12pt;">Periodo:</span> <p> </td> ';
    echo '<td width = "25%" style="text-aligb:left;"> <span style="font-weight: bold; font-size: 12pt;">DE: ' . $oLaboratorio->fInicial . '</span> <p> </td>';
    echo '<td width = "25%" style="color:#0000;"><span style="font-weight: bold; font-size: 12pt;">HASTA: ' . $oLaboratorio->fFinal . '</span></td> ';
    echo '<td width = "25%" style="text-aling: right";> </td>';
    echo '</tr>';
    echo '</table>';
    echo '<br>';
}

$consultaAnti = $dao->resumenConcentrado($oLaboratorio);
$datosAnti = $conexion->prepare($consultaAnti);
$datosAnti->execute();
$contAntibioticos = $datosAnti ->rowCount();

echo '<table  width="100%" border="1">';
echo '<tr>';
echo '<th>' . utf8_decode("Análisis") . '</th>';
echo '<th>Tambores</th>';
echo '<th>Porcentaje</th>';
echo '</tr>';

while ($detallesAnt = $datosAnti->fetch()) {
    $detalleInfor = new stdClass();
    $detalleInfor->Aprobados = $detallesAnt['AprobadosSF'];
    $detalleInfor->SFRechazado = $detallesAnt['SFRechazado'];
    $detalleInfor->STRechazado = $detallesAnt['STRechazado'];
    $detalleInfor->Apro = $detallesAnt['Aprob'];
    $detalleInfor->SFRech = $detallesAnt['SFRech'];
    $detalleInfor->STRech = $detallesAnt['STRech'];
}
echo '<tr>';
echo '<td>Aprobados</td>';
echo '<td>' . $detalleInfor->Aprobados . '</td>';
echo '<td>' . number_format($detalleInfor->Apro, 2, '.', ',') . '</td>';
echo '</tr>';

echo '<tr>';
echo '<td>SFRechazado</td>';
echo '<td>' . $detalleInfor->SFRechazado . '</td>';
echo '<td>' . number_format($detalleInfor->SFRech, 2, '.', ',') . '</td>';
echo '</tr>';

echo '<tr>';
echo '<td>STRechazado</td>';
echo '<td>' . $detalleInfor->STRechazado . '</td>';
echo '<td>' . number_format($detalleInfor->STRech, 2, '.', ',') . '</td>';
echo '</tr>';
echo '</table>';
echo '<br>';

//$segundaTabla = $dao->antibioticos($oLaboratorio);
$detaAntiv = $dao->antibioticos($oLaboratorio);
$dataAnti = $conexion->prepare($detaAntiv);
$dataAnti->execute();

echo '<table  width="100%" border="1">';
echo '<tr>';
echo '<th>Procedencia</th>';
echo '<th>SFRechazado</th>';
echo '<th>Porcentaje</th>';
echo '</tr>';

$valor = "SFRechazado";

//$miSELECT = "SELECT ae.fecha, l.localidad AS Localidad, count(al.idAlmacen) AS Tambores, SUM(al.neto) AS Kgs,
//                            COUNT(IF((lab.sf >= 1980 OR lab.sf BETWEEN 1576 AND 1979),'SFAprobado',NULL)) AS SFAprobado,
//                            COUNT(IF(lab.sf < 1575,'1',NULL)) AS SFRechazado,
//                            COUNT(IF(lab.st<1281,1,NULL)) AS STRechazados,
//                            COUNT(IF(lab.hmf<=10,1,NULL)) AS HMFAprobado,
//                            COUNT(IF(lab.hmf>10,1,NULL)) AS HMFRechazado,
//                            COUNT(IF((lab.c13 < 983 OR lab.c13 BETWEEN 985 AND 100000),'Adulteracion',NULL)) AS Adulteracion,
//                            COUNT(IF((lab.c13 BETWEEN 983 AND 984.79),'Delta 13',NULL)) AS Delta13,
//                            COUNT(IF((lab.c13 BETWEEN 984.80 AND 984.99),'C4',NULL)) AS C4,
//                            COUNT(IF(porcentaje <= 19.5, 'Humedad1',NULL )) AS menor195,
//                            COUNT(IF((lab.porcentaje BETWEEN 19.6 AND 20),'Exportacion20',NULL)) AS Exportacion20,
//                            COUNT(IF((lab.porcentaje BETWEEN 20.1 AND 20.5),'Exportacion205',NULL)) AS Exportacion205,
//                            COUNT(IF((lab.porcentaje BETWEEN 20.6 AND 21),'Exportacion21',NULL)) AS Exportacion21,
//                            COUNT(IF((lab.porcentaje BETWEEN 21.1 AND 22),'Exportacion20',NULL)) AS Exportacionmas21,
//                            COUNT(IF(lab.porcentaje >22, 'RECHAZADOS',NULL )) AS Mayores22
//                            FROM almacen al
//                            LEFT JOIN almacenencabezado ae on ae.idAlmacen = al.idAlmacenEncabezado
//                            LEFT JOIN proveedor p ON p.idProveedor = ae.idProveedor 
//                            LEFT JOIN direccion d ON d.idDireccion = p.idDireccion 
//                            LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad 
//                            LEFT JOIN laboratorio lab ON lab.idAlmacen = al.idAlmacen";
//
////$GLOBALS['miSELECT'];

if ($oLaboratorio->sinFecha == 2) {
            $suma = "SELECT SUM(SFRechazado) as SumaTotalValor
                    FROM  ((SELECT ae.fecha, l.localidad AS Localidad, count(al.idAlmacen) AS Tambores, SUM(al.neto) AS Kgs,
                            COUNT(IF((lab.sf >= 1980 OR lab.sf BETWEEN 1576 AND 1979),'SFAprobado',NULL)) AS SFAprobado,
                            COUNT(IF(lab.sf < 1575,'1',NULL)) AS SFRechazado,
                            COUNT(IF(lab.st<1281,1,NULL)) AS STRechazados,
                            COUNT(IF(lab.hmf<=10,1,NULL)) AS HMFAprobado,
                            COUNT(IF(lab.hmf>10,1,NULL)) AS HMFRechazado,
                            COUNT(IF((lab.c13 < 983 OR lab.c13 BETWEEN 985 AND 100000),'Adulteracion',NULL)) AS Adulteracion,
                            COUNT(IF((lab.c13 BETWEEN 983 AND 984.79),'Delta 13',NULL)) AS Delta13,
                            COUNT(IF((lab.c13 BETWEEN 984.80 AND 984.99),'C4',NULL)) AS C4,
                            COUNT(IF(porcentaje <= 19.5, 'Humedad1',NULL )) AS menor195,
                            COUNT(IF((lab.porcentaje BETWEEN 19.6 AND 20),'Exportacion20',NULL)) AS Exportacion20,
                            COUNT(IF((lab.porcentaje BETWEEN 20.1 AND 20.5),'Exportacion205',NULL)) AS Exportacion205,
                            COUNT(IF((lab.porcentaje BETWEEN 20.6 AND 21),'Exportacion21',NULL)) AS Exportacion21,
                            COUNT(IF((lab.porcentaje BETWEEN 21.1 AND 22),'Exportacion20',NULL)) AS Exportacionmas21,
                            COUNT(IF(lab.porcentaje >22, 'RECHAZADOS',NULL )) AS Mayores22
                            FROM almacen al
                            LEFT JOIN almacenencabezado ae on ae.idAlmacen = al.idAlmacenEncabezado
                            LEFT JOIN proveedor p ON p.idProveedor = ae.idProveedor 
                            LEFT JOIN direccion d ON d.idDireccion = p.idDireccion 
                            LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad 
                            LEFT JOIN laboratorio lab ON lab.idAlmacen = al.idAlmacen WHERE lab.sf != 0
                            GROUP BY l.localidad ORDER BY Tambores DESC)) as ANALISIS                         
                            ORDER BY  SFRechazado DESC";
        } else {
            $suma = "SELECT SUM(SFRechazado) as SumaTotalValor
                    FROM  ((SELECT ae.fecha, l.localidad AS Localidad, count(al.idAlmacen) AS Tambores, SUM(al.neto) AS Kgs,
                            COUNT(IF((lab.sf >= 1980 OR lab.sf BETWEEN 1576 AND 1979),'SFAprobado',NULL)) AS SFAprobado,
                            COUNT(IF(lab.sf < 1575,'1',NULL)) AS SFRechazado,
                            COUNT(IF(lab.st<1281,1,NULL)) AS STRechazados,
                            COUNT(IF(lab.hmf<=10,1,NULL)) AS HMFAprobado,
                            COUNT(IF(lab.hmf>10,1,NULL)) AS HMFRechazado,
                            COUNT(IF((lab.c13 < 983 OR lab.c13 BETWEEN 985 AND 100000),'Adulteracion',NULL)) AS Adulteracion,
                            COUNT(IF((lab.c13 BETWEEN 983 AND 984.79),'Delta 13',NULL)) AS Delta13,
                            COUNT(IF((lab.c13 BETWEEN 984.80 AND 984.99),'C4',NULL)) AS C4,
                            COUNT(IF(porcentaje <= 19.5, 'Humedad1',NULL )) AS menor195,
                            COUNT(IF((lab.porcentaje BETWEEN 19.6 AND 20),'Exportacion20',NULL)) AS Exportacion20,
                            COUNT(IF((lab.porcentaje BETWEEN 20.1 AND 20.5),'Exportacion205',NULL)) AS Exportacion205,
                            COUNT(IF((lab.porcentaje BETWEEN 20.6 AND 21),'Exportacion21',NULL)) AS Exportacion21,
                            COUNT(IF((lab.porcentaje BETWEEN 21.1 AND 22),'Exportacion20',NULL)) AS Exportacionmas21,
                            COUNT(IF(lab.porcentaje >22, 'RECHAZADOS',NULL )) AS Mayores22
                            FROM almacen al
                            LEFT JOIN almacenencabezado ae on ae.idAlmacen = al.idAlmacenEncabezado
                            LEFT JOIN proveedor p ON p.idProveedor = ae.idProveedor 
                            LEFT JOIN direccion d ON d.idDireccion = p.idDireccion 
                            LEFT JOIN localidades l ON l.idlocalidad = d.idlocalidad 
                            LEFT JOIN laboratorio lab ON lab.idAlmacen = al.idAlmacen WHERE lab.sf != 0 AND fecha BETWEEN '$oLaboratorio->fInicial' AND '$oLaboratorio->fFinal'
                            GROUP BY l.localidad ORDER BY Tambores DESC)) as ANALISIS 
                            WHERE fecha BETWEEN '$oLaboratorio->fInicial' AND '$oLaboratorio->fFinal'
                            ORDER BY  SFRechazado DESC";
        }

//$datosSuma = $dao->sumaTotal($oLaboratorio);
$sumaAnt = $conexion->prepare($suma);
//$sumaAnt->bindParam(':valor', $valor);
$sumaAnt->execute();

while ($sumasTotal = $sumaAnt->fetch()) {
    $sumaSFTotal = new stdClass();
    $sumaSFTotal->SumaTotalValor = $sumasTotal['SumaTotalValor'];
} 
$sumaSFRechazado = 0;
while ($detalleSegun = $dataAnti->fetch()) {
    $localidad = $detalleSegun['localidad'];
    $RechazoSF = $detalleSegun['SFRechazado'];

    $sumaSFRechazado = $sumaSFRechazado + $RechazoSF;
    $porciento = (($RechazoSF / $sumaSFTotal->SumaTotalValor) * 100);
    echo '<tr>';
    echo '<td>' . strtoupper($localidad) . '</td>';
    echo '<td>' . $RechazoSF . '</td>';
    echo '<td>' . number_format($porciento, 2, '.', ',') . '</td>';
    echo '</tr>';
}

echo '</table>';

echo '<table  width="100%" border="1">';
echo '<tr>';
echo '<td>Total :</td>';
echo '<td>' . $sumaSFRechazado . '</td>';
echo '</tr>';
echo '</table>';

echo '<br>';
echo '<br>';

//$TerceraTabla= $dao->antibioticos1($oLaboratorio);
$antivioticos1 = $dao->antibioticos1($oLaboratorio);
$dats = $conexion->prepare($antivioticos1);
$dats->execute();

echo '<table  width="100%" border="1">';
echo '<tr>';
echo '<th>Procedencia</th>';
echo '<th>STRechazado</th>';
echo '</tr>';

while ($detalleTerc = $dats->fetch()) {
    $localidad = $detalleTerc['localidad'];
    $RechazoST = $detalleTerc['STRechazados'];


    echo '<tr>';
    echo '<td>' . strtoupper($localidad) . '</td>';
    echo '<td>' . $RechazoST . '</td>';
    echo '</tr>';
}
echo '</table>';
