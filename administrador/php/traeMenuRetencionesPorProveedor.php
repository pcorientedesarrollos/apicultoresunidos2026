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
    throw new Exception('No existen datos');
} else {
    $retencion = new stdClass();
    while ($rs = $datos->fetch()) {
        $retencion->nombre = $rs["nombre"];
        $retencion->retencionesDetalle = array();

        $sqlRetencionesDetalle = "SELECT * FROM retencionesisr WHERE idProveedor = :idProveedor";
        $dato = $con->prepare($sqlRetencionesDetalle);
        $dato->bindParam(':idProveedor', $idProveedor);
        $dato->execute();
        $cont = 0;
        while ($rsRetencionesDetalle = $dato->fetch()) {
            $retencionDetalle = new stdClass();
            $retencionDetalle->idRetencion = $rsRetencionesDetalle["idRetencion"];
            $retencionDetalle->idProveedor = $rsRetencionesDetalle["idProveedor"];
            $retencionDetalle->concepto = $rsRetencionesDetalle["concepto"];                        
            $retencionDetalle->fecha = $rsRetencionesDetalle["fecha"];
            $retencionDetalle->idMes = $rsRetencionesDetalle["idMes"];         
            $retencionDetalle->cantidad = $rsRetencionesDetalle["cantidad"];
            $retencion->retencionesDetalle[] = $retencionDetalle;
        }
    }
    echo json_encode($retencion);
}