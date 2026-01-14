<?php

include_once '../../../DAOConeccion/conePDO.php';
$pdo = new conePDO;
$con = $pdo->conectar();

$postdata = file_get_contents("php://input");

$request = json_decode($postdata);
$request = (array) $request;
$fechaUno = date("Y-m-d", strtotime($request['fechaUno']));
$fechaDos = date("Y-m-d", strtotime($request['fechaDos']));

$query = "SELECT SUM(totalTambores) as tamboresTotal, 
(SELECT SUM(importeTotal)) as totalImporte FROM requisicionencabezado WHERE fechaRequisicion BETWEEN :fechaUno AND :fechaDos ";
$data = $con->prepare($query);
$data->bindParam(':fechaUno', $fechaUno);
$data->bindParam(':fechaDos', $fechaDos);
$data->execute();

if ($data == false) {
    echo mysql_error();
} else {
    while ($res = $data->fetch()) {
        $fechaD = new stdClass();
        $fechaD->tamboresTotal = $res["tamboresTotal"];
        $fechaD->totalImporte = $res["totalImporte"];
    }

    $sql = "SELECT SUM(ae.totalCompra) as totalCompra, 
            (SELECT COUNT(a.idAlmacen) FROM almacen a 
            INNER JOIN almacenencabezado ae ON a.idAlmacenEncabezado = ae.idAlmacen
            WHERE ae.fecha BETWEEN :fechaUno AND :fechaDos ) as tambores
            FROM almacenencabezado ae
            WHERE ae.fecha BETWEEN :fechaUno AND :fechaDos ";
    $dataFecha = $con->prepare($sql);
    $dataFecha->bindParam(':fechaUno', $fechaUno);
    $dataFecha->bindParam(':fechaDos', $fechaDos);
    $dataFecha->execute();
    if ($dataFecha == false) {
        echo mysql_error();
    } else {
        while ($resp = $dataFecha->fetch()) {
            $fechaD->totalCompra = $resp["totalCompra"];
            $fechaD->tambores = $resp["tambores"];
        }
    }
    echo json_encode($fechaD);
}
?>
