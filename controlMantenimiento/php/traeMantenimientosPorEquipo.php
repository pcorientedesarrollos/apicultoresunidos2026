<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$idEquipo = $_GET["idEquipo"];

if (isset($_GET['extra'])) {
    $sql = "SELECT  mt.idMantenimiento,mt.idMes,
                mt.fechaReal, mt.descripcion,
                mt.observaciones, mt.tipoPersonal, m.mes
                FROM controlmantenimiento mt
                INNER JOIN meses m ON m.idMes = mt.idMes
                WHERE mt.idEquipo = :idEquipo AND mt.tipo = 1
                ORDER BY mt.idMes ASC";
    $datos = $con->prepare($sql);
    $datos->bindParam(':idEquipo', $idEquipo);
    $datos->execute();
    $arreglo = array();
    if ($datos == false) {
        echo mysql_error();
    } else {
        while ($rs = $datos->fetch()) {
            $mantenimientos = new stdClass();
            $mantenimientos->idMantenimiento = $rs["idMantenimiento"];
            $mantenimientos->mes = $rs["mes"];
            $mantenimientos->idMes = $rs["idMes"];
//            $mantenimientos->fechaProgramada = $rs["fechaProgramada"];
            $mantenimientos->fechaReal = $rs["fechaReal"];
            $mantenimientos->descripcion = $rs["descripcion"];
            $mantenimientos->observaciones = $rs["observaciones"];
            $mantenimientos->tipoPersonal = $rs["tipoPersonal"];

            if ($mantenimientos->tipoPersonal == '1') {
                $sqlPersonal = "SELECT p.nombre
                    FROM controlmantenimiento mt
                    INNER JOIN personaloaxaca p ON p.idPersonalOM = mt.idPersonalOM
                    WHERE mt.idEquipo = :idEquipo AND mt.idMantenimiento = :idMantenimiento
                    ORDER BY mt.idMes ASC";
                $dato = $con->prepare($sqlPersonal);
                $dato->bindParam(':idEquipo', $idEquipo);
                $dato->bindParam(':idMantenimiento', $mantenimientos->idMantenimiento);
                $dato->execute();
                while ($rs = $dato->fetch()) {
                    $mantenimientos->nombre = $rs["nombre"];
                }
            }
            if ($mantenimientos->tipoPersonal == '2') {
                $sqlPersonalEx = "SELECT p.nombreProveedor AS nombre
                    FROM controlmantenimiento mt
                    INNER JOIN proveedoresmantto p ON p.idProveedorMantto = mt.idPersonalOM
                    WHERE mt.idEquipo = :idEquipo AND mt.idMantenimiento = :idMantenimiento
                    ORDER BY mt.idMes ASC";
                $dat = $con->prepare($sqlPersonalEx);
                $dat->bindParam(':idEquipo', $idEquipo);
                $dat->bindParam(':idMantenimiento', $mantenimientos->idMantenimiento);
                $dat->execute();
                while ($rs = $dat->fetch()) {
                    $mantenimientos->nombre = $rs["nombre"];
                }
            }
            $arreglo[] = $mantenimientos;
        }
    }
} else {
//    $sql = "SELECT pf.idProgramacion, pf.idMes, pf.fechaProgramada, mt.idMantenimiento,
//                mt.fechaReal, mt.descripcion,
//                mt.observaciones, mt.tipoPersonal, m.mes
//                FROM programaciondefechas pf
//                LEFT JOIN controlmantenimiento mt ON mt.idMes = pf.idMes
//                LEFT JOIN meses m ON m.idMes = pf.idMes
//                WHERE pf.idEquipo = :idEquipo AND mt.tipo = 0
//                ORDER BY pf.idMes ASC";
    $sql = "SELECT pf.idProgramacion, pf.idMes, pf.fechaProgramada, m.mes FROM programaciondefechas pf 
            LEFT JOIN meses m ON m.idMes = pf.idMes
            WHERE pf.idEquipo = :idEquipo";
    $datos = $con->prepare($sql);
    $datos->bindParam(':idEquipo', $idEquipo);
    $datos->execute();
    $arreglo = array();
    if ($datos == false) {
        echo mysql_error();
    } else {
        while ($rs = $datos->fetch()) {
            $mantenimientos = new stdClass();
            $mantenimientos->idProgramacion = $rs["idProgramacion"];
//            $mantenimientos->idMantenimiento = $rs["idMantenimiento"];
            $mantenimientos->mes = $rs["mes"];
            $mantenimientos->idMes = $rs["idMes"];
            $mantenimientos->fechaProgramada = $rs["fechaProgramada"];
//            $mantenimientos->fechaReal = $rs["fechaReal"];
//            $mantenimientos->descripcion = $rs["descripcion"];
//            $mantenimientos->observaciones = $rs["observaciones"];
//            $mantenimientos->tipoPersonal = $rs["tipoPersonal"];

            $sqlMan = "SELECT mt.idMantenimiento,
                mt.fechaReal, mt.descripcion,
                mt.observaciones, mt.tipoPersonal
                FROM controlmantenimiento mt 
                WHERE mt.idEquipo = :idEquipo AND mt.idMes = :idMes AND mt.tipo = '0'";
            $res = $con->prepare($sqlMan);
            $res->bindParam(':idEquipo', $idEquipo);
            $res->bindParam(':idMes', $mantenimientos->idMes);
            $res->execute();
            while ($rs = $res->fetch()) {
                $mantenimientos->idMantenimiento = $rs["idMantenimiento"];
                $mantenimientos->fechaReal = $rs["fechaReal"];
                $mantenimientos->descripcion = $rs["descripcion"];
                $mantenimientos->observaciones = $rs["observaciones"];
                $mantenimientos->tipoPersonal = $rs["tipoPersonal"];

                if ($mantenimientos->tipoPersonal == '1') {
                    $sqlPersonal = "SELECT p.nombre
                    FROM controlmantenimiento mt
                    INNER JOIN personaloaxaca p ON p.idPersonalOM = mt.idPersonalOM
                    WHERE mt.idEquipo = :idEquipo AND mt.idMantenimiento = :idMantenimiento
                    ORDER BY mt.idMes ASC";
                    $dato = $con->prepare($sqlPersonal);
                    $dato->bindParam(':idEquipo', $idEquipo);
                    $dato->bindParam(':idMantenimiento', $mantenimientos->idMantenimiento);
                    $dato->execute();
                    while ($rs = $dato->fetch()) {
                        $mantenimientos->nombre = $rs["nombre"];
                    }
                }
                if ($mantenimientos->tipoPersonal == '2') {
                    $sqlPersonalEx = "SELECT p.nombreProveedor AS nombre
                    FROM controlmantenimiento mt
                    INNER JOIN proveedoresmantto p ON p.idProveedorMantto = mt.idPersonalOM
                    WHERE mt.idEquipo = :idEquipo AND mt.idMantenimiento = :idMantenimiento
                    ORDER BY mt.idMes ASC";
                    $dat = $con->prepare($sqlPersonalEx);
                    $dat->bindParam(':idEquipo', $idEquipo);
                    $dat->bindParam(':idMantenimiento', $mantenimientos->idMantenimiento);
                    $dat->execute();
                    while ($rs = $dat->fetch()) {
                        $mantenimientos->nombre = $rs["nombre"];
                    }
                }
            }


            $arreglo[] = $mantenimientos;
        }
    }
}
echo json_encode($arreglo);
?>