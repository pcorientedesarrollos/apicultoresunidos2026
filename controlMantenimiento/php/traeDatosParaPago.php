<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

if (isset($_GET['idMantenimiento'])) {

    $sqlDetalle = "SELECT a.area, e.idArea, e.nombre AS equipo, e.idEquipo, CONCAT('AF-MID-', e.idEquipo) AS codigo,
             mt.idMantenimiento, mt.fechaReal, mt.idMes, mt.descripcion, mt.observaciones, mt.tipoPersonal,
             mt.idPersonalOM, mt.nombreTecnico, mt.totalPago
             FROM equipos e 
             LEFT JOIN areas a ON a.idArea = e.idArea
             LEFT JOIN controlmantenimiento mt ON mt.idEquipo = e.idEquipo
             WHERE mt.idMantenimiento = :idMantenimiento";
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
            $ctrlMantto->codigo = $rs["codigo"];
            $ctrlMantto->idMantenimiento = $rs["idMantenimiento"];
            $ctrlMantto->fechaReal = $rs["fechaReal"];
            $ctrlMantto->idMes = $rs["idMes"];
            $ctrlMantto->descripcion = $rs["descripcion"];
            $ctrlMantto->observaciones = $rs["observaciones"];
            $ctrlMantto->tipoPersonal = $rs["tipoPersonal"];
            $ctrlMantto->idPersonalOM = $rs["idPersonalOM"];
            $ctrlMantto->nombreTecnico = $rs["nombreTecnico"];
            $ctrlMantto->totalPago = $rs["totalPago"];

            if ($ctrlMantto->tipoPersonal == "1") {
                $sqlPersonal = "SELECT p.nombre, mt.idPersonalOM
          FROM controlmantenimiento mt
         INNER JOIN personaloaxaca p ON p.idPersonalOM = mt.idPersonalOM
          WHERE mt.idMantenimiento = :idMantenimiento";
                $data = $con->prepare($sqlPersonal);
                $data->bindParam(':idMantenimiento', $_GET['idMantenimiento']);
                $data->execute();

                while ($rs = $data->fetch()) {
                    $ctrlMantto->nombre = $rs["nombre"];
                    $ctrlMantto->idPersonalOM = $rs["idPersonalOM"];
                }
            } else {
                $sqlPersonalEx = "SELECT p.nombreProveedor AS nombre, p.nombreContacto, p.domicilio, p.telefono, p.correo, mt.idPersonalOM
          FROM controlmantenimiento mt
         INNER JOIN proveedoresmantto p ON p.idProveedorMantto = mt.idPersonalOM
          WHERE mt.idMantenimiento = :idMantenimiento";
                $dat = $con->prepare($sqlPersonalEx);
                $dat->bindParam(':idMantenimiento', $_GET['idMantenimiento']);
                $dat->execute();

                while ($rs = $dat->fetch()) {
                    $ctrlMantto->nombre = $rs["nombre"];
                    $ctrlMantto->idPersonalOM = $rs["idPersonalOM"];
                    $ctrlMantto->nombreContacto = $rs["nombreContacto"];
                    $ctrlMantto->domicilio = $rs["domicilio"];
                    $ctrlMantto->telefono = $rs["telefono"];
                    $ctrlMantto->correo = $rs["correo"];
                }
            }
        }
    }
}

echo json_encode($ctrlMantto);
?>