<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$idArea = $_GET["idArea"];

$sql = "SELECT cm.idMantenimiento, cm.fechaReal, e.nombre AS equipo, e.idEquipo, cm.tipoPersonal, cm.totalPago
    FROM controlmantenimiento cm 
            LEFT JOIN equipos e ON e.idEquipo = cm.idEquipo
            WHERE cm.idArea = :idArea
            ORDER BY cm.fechaReal DESC";
$datos = $con->prepare($sql);
$datos->bindParam(':idArea', $idArea);
$datos->execute();
$arreglo = array();
if ($datos == false) {
    echo mysql_error();
} else {
    while ($rs = $datos->fetch()) {
        $mantenimientosArea = new stdClass();
        $mantenimientosArea->idMantenimiento = $rs["idMantenimiento"];
        $mantenimientosArea->fechaReal = $rs["fechaReal"];
        $mantenimientosArea->equipo = $rs["equipo"];
        $mantenimientosArea->idEquipo = $rs["idEquipo"];
        $mantenimientosArea->tipoPersonal = $rs["tipoPersonal"];
        $mantenimientosArea->totalPago = $rs["totalPago"];

        if ($mantenimientosArea->tipoPersonal == '1') {
            $sqlPersonal = "SELECT p.nombre, mt.idPersonalOM
          FROM controlmantenimiento mt
         INNER JOIN personaloaxaca p ON p.idPersonalOM = mt.idPersonalOM
          WHERE mt.idMantenimiento = :idMantenimiento";
            $data = $con->prepare($sqlPersonal);
            $data->bindParam(':idMantenimiento', $mantenimientosArea->idMantenimiento);
            $data->execute();

            while ($rs = $data->fetch()) {
                $mantenimientosArea->nombre = $rs["nombre"];
                $mantenimientosArea->idPersonalOM = $rs["idPersonalOM"];
                $mantenimientosArea->tipo = "Interno";
            }
        } else {
            $sqlPersonalEx = "SELECT p.nombreProveedor AS nombre, mt.idPersonalOM
          FROM controlmantenimiento mt
         INNER JOIN proveedoresmantto p ON p.idProveedorMantto = mt.idPersonalOM
          WHERE mt.idMantenimiento = :idMantenimiento";
            $dat = $con->prepare($sqlPersonalEx);
            $dat->bindParam(':idMantenimiento', $mantenimientosArea->idMantenimiento);
            $dat->execute();

            while ($rs = $dat->fetch()) {
                $mantenimientosArea->nombre = $rs["nombre"];
                $mantenimientosArea->idPersonalOM = $rs["idPersonalOM"];
                $mantenimientosArea->tipo = "Externo";
            }
        }

        $arreglo[] = $mantenimientosArea;
    }
    echo json_encode($arreglo);
}
?>