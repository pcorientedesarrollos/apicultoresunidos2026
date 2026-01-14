<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$idEquipo = $_GET["idEquipo"];

//$sqlP = "SELECT e.nombre AS equipo, mt.idEquipo
//         FROM equipos e 
//         INNER JOIN controlmantenimiento mt ON mt.idEquipo = e.idEquipo
//WHERE mt.idEquipo = :idEquipo";
$sqlP = "SELECT a.idArea, e.nombre AS equipo, e.idEquipo, a.area, e.periodicidad, CONCAT('OAX-', e.idEquipo) AS codigo
        FROM equipos e
        INNER JOIN areas a ON a.idArea = e.idArea
        WHERE e.idEquipo = :idEquipo";
$dato = $con->prepare($sqlP);
$dato->bindParam(':idEquipo', $idEquipo);
$dato->execute();

if ($dato == false) {
    echo mysql_error();
} else {
    $manttos = new stdClass();
    while ($rs = $dato->fetch()) {
        $manttos->idEquipo = $rs["idEquipo"];
        $manttos->equipo = $rs["equipo"];
        $manttos->idArea = $rs["idArea"];
        $manttos->area = $rs["area"];
        $manttos->periodicidad = $rs["periodicidad"];
        $manttos->codigo = $rs["codigo"];
        $manttos->mantenimientos = array();

        switch ($manttos->periodicidad) {
            case'1':
                $manttos->periodicidad = "Mensual";
                break;
            case'2':
                $manttos->periodicidad = "Trimestral";
                break;
            case'3':
                $manttos->periodicidad = "Semestral";
                break;
            case'4':
                $manttos->periodicidad = "Anual";
                break;
        }

//        $sql = "SELECT mt.idMantenimiento, pf.fechaProgramada, mt.fechaReal, mt.descripcion, mt.observaciones, mt.tipoPersonal,
//          a.area, mt.idArea, e.nombre AS equipo, m.mes, mt.idMes
//          FROM controlmantenimiento mt
//          INNER JOIN areas a ON a.idArea = mt.idArea
//          INNER JOIN equipos e ON e.idEquipo = mt.idEquipo
//	  INNER JOIN meses m ON m.idMes = mt.idMes
//          INNER JOIN programaciondefechas pf ON pf.idMes = mt.idMes 
//          WHERE mt.idEquipo = :idEquipo
//          ORDER BY mt.idMes ASC";
        $sql = "SELECT pf.idProgramacion, pf.idMes, pf.fechaProgramada, mt.idMantenimiento,
                mt.fechaReal, mt.descripcion,
                mt.observaciones, mt.tipoPersonal, m.mes
                FROM programaciondefechas pf
                LEFT JOIN controlmantenimiento mt ON mt.idMes = pf.idMes
                LEFT JOIN meses m ON m.idMes = pf.idMes
                WHERE pf.idEquipo = :idEquipo
                ORDER BY pf.idMes ASC";
        $datos = $con->prepare($sql);
        $datos->bindParam(':idEquipo', $idEquipo);
        $datos->execute();
        $cont = 0;

        if ($datos == false) {
            echo mysql_error();
        } else {
            while ($rs = $datos->fetch()) {
                $mantenimientos = new stdClass();
                $mantenimientos->idProgramacion = $rs["idProgramacion"];
                $mantenimientos->idMantenimiento = $rs["idMantenimiento"];
                $mantenimientos->mes = $rs["mes"];
                $mantenimientos->idMes = $rs["idMes"];
                $mantenimientos->fechaProgramada = $rs["fechaProgramada"];
                $mantenimientos->fechaReal = $rs["fechaReal"];
                $mantenimientos->descripcion = $rs["descripcion"];
                $mantenimientos->observaciones = $rs["observaciones"];
                $mantenimientos->tipoPersonal = $rs["tipoPersonal"];

                if ($mantenimientos->tipoPersonal == '1') {
                    $sqlPersonal = "SELECT p.nombre
                    FROM controlmantenimiento mt
                    INNER JOIN personaloaxaca p ON p.idPersonalOM = mt.idPersonalOM
                    WHERE mt.idEquipo = :idEquipo
                    ORDER BY mt.idMes ASC";
                    $dato = $con->prepare($sqlPersonal);
                    $dato->bindParam(':idEquipo', $idEquipo);
                    $dato->execute();
                    while ($rs = $dato->fetch()) {
                        $mantenimientos->nombre = $rs["nombre"];
                    }
                }
                if ($mantenimientos->tipoPersonal == '2') {
                    $sqlPersonalEx = "SELECT p.nombreProveedor AS nombre
                    FROM controlmantenimiento mt
                    INNER JOIN proveedoresmantto p ON p.idProveedorMantto = mt.idPersonalOM
                    WHERE mt.idEquipo = :idEquipo
                    ORDER BY mt.idMes ASC";
                    $dat = $con->prepare($sqlPersonalEx);
                    $dat->bindParam(':idEquipo', $idEquipo);
                    $dat->execute();
                    while ($rs = $dat->fetch()) {
                        $mantenimientos->nombre = $rs["nombre"];
                    }
                }

//                if ($mantenimientos->fechaReal == "0000-00-00") {
//                    $mantenimientos->fechaReal = " ";
//                } else {
//                    $mantenimientos->fechaReal = $rs["fechaReal"];
//                }

                $manttos->mantenimientos[$cont] = $mantenimientos;
                $cont++;
            }
        }
    }
    echo json_encode($manttos);
}
?>