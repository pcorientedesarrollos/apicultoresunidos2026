<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

//$idProgramacion = $_GET[""];

if (isset($_GET['idProgramacion'])) {
    $existe = "";
//    $sqlP = "SELECT  a.area, pf.idArea, e.nombre AS equipo, e.idEquipo, CONCAT('OAX-', e.idEquipo) AS codigo, e.mantto, pf.fechaProgramada, pf.idProgramacion,
//				mt.idMantenimiento, mt.fechaReal, mt.idMes, mt.descripcion, mt.observaciones, mt.tipoPersonal,
//          mt.idPersonalOM, mt.nombreTecnico
//          FROM programaciondefechas pf
//          LEFT JOIN areas a ON a.idArea = pf.idArea
//          LEFT JOIN equipos e ON e.idEquipo = pf.idEquipo
//					LEFT JOIN controlmantenimiento mt ON mt.idMes = pf.idMes
//	 WHERE pf.idProgramacion = :idProgramacion AND mt.tipo = '0'";
    $sqlP = "SELECT  a.area, pf.idArea, e.nombre AS equipo, e.idEquipo, CONCAT('AF-MID-', e.idEquipo) AS codigo, e.mantto,
        pf.fechaProgramada, pf.idProgramacion, pf.idMes
          FROM programaciondefechas pf
          LEFT JOIN areas a ON a.idArea = pf.idArea
          LEFT JOIN equipos e ON e.idEquipo = pf.idEquipo
	 WHERE pf.idProgramacion = :idProgramacion";
    $dato = $con->prepare($sqlP);
    $dato->bindParam(':idProgramacion', $_GET['idProgramacion']);
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
            $ctrlMantto->mantto = $rs["mantto"];
            $ctrlMantto->codigo = $rs["codigo"];
            $ctrlMantto->idMes = $rs["idMes"];
            $ctrlMantto->fechaProgramada = $rs["fechaProgramada"];
            $ctrlMantto->idProgramacion = $rs["idProgramacion"];

            $sqlVerifica = "SELECT idMantenimiento FROM controlmantenimiento WHERE idEquipo = :idEquipo AND idMes = :idMes AND tipo = '0'";
            $respu = $con->prepare($sqlVerifica);
            $respu->bindParam(':idEquipo', $ctrlMantto->idEquipo);
            $respu->bindParam(':idMes', $ctrlMantto->idMes);
            $respu->execute();
            while ($row = $respu->fetch()) {
                $existe = $row["idMantenimiento"];
            }

            if ($existe != null) {
                $sqlPe = "SELECT  mt.idMantenimiento, mt.fechaReal, mt.idMes, mt.descripcion, mt.observaciones, mt.tipoPersonal,
          mt.idPersonalOM, mt.nombreTecnico
          FROM controlmantenimiento mt 
	 WHERE mt.idMantenimiento = :idMantenimiento";
                $datos = $con->prepare($sqlPe);
                $datos->bindParam(':idMantenimiento', $existe);
//                $datos->bindParam(':idMes', $ctrlMantto->idMes);
                $datos->execute();
                while ($rs = $datos->fetch()) {
                    $ctrlMantto->idMantenimiento = $rs["idMantenimiento"];
                    $ctrlMantto->fechaReal = $rs["fechaReal"];
//            $ctrlMantto->idMes = $rs["idMes"];
                    $ctrlMantto->descripcion = $rs["descripcion"];
                    $ctrlMantto->observaciones = $rs["observaciones"];
                    $ctrlMantto->tipoPersonal = $rs["tipoPersonal"];
                    $ctrlMantto->idPersonalOM = $rs["idPersonalOM"];
                    $ctrlMantto->nombreTecnico = $rs["nombreTecnico"];
                }

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
            }
        }
    }
}

if (isset($_GET['idMantenimiento'])) {

    $sqlDetalle = "SELECT a.area, e.idArea, e.nombre AS equipo, e.idEquipo, e.mantto, CONCAT('AF-MID-', e.idEquipo) AS codigo,
             mt.idMantenimiento, mt.fechaReal, mt.idMes, mt.descripcion, mt.observaciones, mt.tipoPersonal,
             mt.idPersonalOM, mt.nombreTecnico
             FROM equipos e 
             LEFT JOIN areas a ON a.idArea = e.idArea
             LEFT JOIN controlmantenimiento mt ON mt.idEquipo = e.idEquipo
             WHERE mt.idMantenimiento = :idMantenimiento AND mt.tipo = 1";
    $dato = $con->prepare($sqlDetalle);
    $dato->bindParam(':idMantenimiento', $_GET['idMantenimiento']);
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
            $ctrlMantto->mantto = $rs["mantto"];
            $ctrlMantto->codigo = $rs["codigo"];
            $ctrlMantto->idMantenimiento = $rs["idMantenimiento"];
            $ctrlMantto->fechaReal = $rs["fechaReal"];
            $ctrlMantto->idMes = $rs["idMes"];
            $ctrlMantto->descripcion = $rs["descripcion"];
            $ctrlMantto->observaciones = $rs["observaciones"];
            $ctrlMantto->tipoPersonal = $rs["tipoPersonal"];
            $ctrlMantto->idPersonalOM = $rs["idPersonalOM"];
            $ctrlMantto->nombreTecnico = $rs["nombreTecnico"];

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
        }
    }
}

if (isset($_GET['idEquipo'])) {
    $sqlDetalle1 = "SELECT a.area, e.idArea, e.nombre AS equipo, e.idEquipo, e.mantto, CONCAT('AF-MID-', e.idEquipo) AS codigo
             FROM equipos e 
             LEFT JOIN areas a ON a.idArea = e.idArea
             WHERE e.idEquipo = :idEquipo";
    $dato = $con->prepare($sqlDetalle1);
    $dato->bindParam(':idEquipo', $_GET['idEquipo']);
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
            $ctrlMantto->mantto = $rs["mantto"];
            $ctrlMantto->codigo = $rs["codigo"];
        }
    }
}

echo json_encode($ctrlMantto);
?>