<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$idProveedor = $_GET["idProveedor"];

$sql = "SELECT nombre FROM proveedor WHERE idProveedor = :idProveedor";
$datos = $con->prepare($sql);
$datos->bindParam(':idProveedor', $idProveedor);
$datos->execute();

if ($datos == false) {
    echo mysql_error();
} else {
    $gasto = new stdClass();
    while ($rs = $datos->fetch()) {
        $gasto->nombre = $rs["nombre"];
        $gasto->gastosDetalle = array();

        $sqlgastosDetalle = "SELECT * FROM gastosrealizados WHERE idProveedor = :idProveedor";
        $dato = $con->prepare($sqlgastosDetalle);
        $dato->bindParam(':idProveedor', $idProveedor);
        $dato->execute();
        $cont = 0;
        while ($rsgastosDetalle = $dato->fetch()) {
            $gastoDetalle = new stdClass();
            $gastoDetalle->idGasto = $rsgastosDetalle["idGasto"];
            $gastoDetalle->idProveedor = $rsgastosDetalle["idProveedor"];
            $gastoDetalle->concepto = $rsgastosDetalle["concepto"];                        
            $gastoDetalle->fecha = $rsgastosDetalle["fecha"];
            $gastoDetalle->idMes = $rsgastosDetalle["idMes"];         
            $gastoDetalle->cantidad = $rsgastosDetalle["cantidad"];
            $gasto->gastosDetalle[] = $gastoDetalle;
        }
    }
    echo json_encode($gasto);
}