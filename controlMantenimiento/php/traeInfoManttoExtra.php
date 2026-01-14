<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$idProgramacion = $_GET["idProgramacion"];

//$sqlP = "SELECT mt.idMantenimiento, mt.fechaReal, mt.descripcion, mt.observaciones, mt.tipoPersonal,
//          a.area, mt.idArea, e.nombre AS equipo, mt.idEquipo, mt.idPersonalOM
//          FROM controlmantenimiento mt
//          INNER JOIN areas a ON a.idArea = mt.idArea
//          INNER JOIN equipos e ON e.idEquipo = mt.idEquipo
//	 WHERE mt.idMantenimiento = :idMantenimiento";
$sqlP = "SELECT  a.area, pf.idArea, e.nombre AS equipo, e.idEquipo, CONCAT('AF-MID-', e.idEquipo) AS codigo, pf.fechaProgramada, pf.idProgramacion,
				mt.idMantenimiento, mt.fechaReal, mt.idMes, mt.descripcion, mt.observaciones, mt.tipoPersonal,
          mt.idPersonalOM
          FROM programaciondefechas pf
          LEFT JOIN areas a ON a.idArea = pf.idArea
          LEFT JOIN equipos e ON e.idEquipo = pf.idEquipo
					LEFT JOIN controlmantenimiento mt ON mt.idMes = pf.idMes
	 WHERE pf.idProgramacion = :idProgramacion";
$dato = $con->prepare($sqlP);
$dato->bindParam(':idProgramacion', $idProgramacion);
$dato->execute();

if ($dato == false) {
    echo mysql_error();
} else {
    $ctrlMantto = new stdClass();
    while ($rs = $dato->fetch()) {
        $ctrlMantto = new stdClass();
        $ctrlMantto->area = $rs["area"];
        $ctrlMantto->idArea = $rs["idArea"];
        $ctrlMantto->idEquipo = $rs["idEquipo"];
        $ctrlMantto->equipo = $rs["equipo"];
        $ctrlMantto->codigo = $rs["codigo"];
        $ctrlMantto->fechaProgramada = $rs["fechaProgramada"];
        $ctrlMantto->idProgramacion = $rs["idProgramacion"];
        $ctrlMantto->idMantenimiento = $rs["idMantenimiento"];
        $ctrlMantto->fechaReal = $rs["fechaReal"];
        $ctrlMantto->idMes = $rs["idMes"];
        $ctrlMantto->descripcion = $rs["descripcion"];
        $ctrlMantto->observaciones = $rs["observaciones"];
        $ctrlMantto->tipoPersonal = $rs["tipoPersonal"];
        $ctrlMantto->idPersonalOM = $rs["idPersonalOM"];

//        $sqlFP = "SELECT mt.idMes, pf.fechaProgramada
//FROM programaciondefechas pf 
//INNER JOIN controlmantenimiento mt ON mt.idMes = pf.idMes
//WHERE pf.idEquipo = :idEquipo";
//        $dt = $con->prepare($sqlFP);
//        $dt->bindParam(':idEquipo', $ctrlMantto->idEquipo);
//        $dt->execute();
//
//        while ($rs = $dt->fetch()) {
//            $ctrlMantto->idMes = $rs["idMes"];
//            $ctrlMantto->fechaProgramada = $rs["fechaProgramada"];
//        }
//    }

        if ($ctrlMantto->tipoPersonal == '1') {
            $sqlPersonal = "SELECT p.nombre, mt.idPersonalOM
          FROM controlmantenimiento mt
         INNER JOIN personaloaxaca p ON p.idPersonalOM = mt.idPersonalOM
          WHERE mt.idMantenimiento = :idMantenimiento";
            $data = $con->prepare($sqlPersonal);
            $data->bindParam(':idMantenimiento', $idMantenimiento);
            $data->execute();

            while ($rs = $data->fetch()) {
                $ctrlMantto->nombre = $rs["nombre"];
                $ctrlMantto->idPersonalOM = $rs["idPersonalOM"];
            }
        } else {
            $sqlPersonalEx = "SELECT p.nombreProveedor AS nombre, mt.idPersonalOM
          FROM controlmantenimiento mt
         INNER JOIN proveedoresmantto p ON p.idProveedorMantto = mt.idPersonalOM
          WHERE mt.idMantenimiento = :idMantenimiento";
            $dat = $con->prepare($sqlPersonalEx);
            $dat->bindParam(':idMantenimiento', $idMantenimiento);
            $dat->execute();

            while ($rs = $dat->fetch()) {
                $ctrlMantto->nombre = $rs["nombre"];
                $ctrlMantto->idPersonalOM = $rs["idPersonalOM"];
            }
        }
        echo json_encode($ctrlMantto);
    }
}
?>