<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$miInfo = file_get_contents("php://input");
$datos = json_decode($miInfo);

if (isset($_GET['tipo'])) {
    if (isset($_GET['idMantenimiento'])) {
        $sqlUp = "UPDATE controlmantenimiento "
                . "SET idArea = :idArea, idMes = :idMes, fechaReal = :fechaReal,"
                . " descripcion = :descripcion, observaciones = :observaciones, idPersonalOM = :idPersonalOM, nombreTecnico = :nombreTecnico, tipoPersonal = :tipoPersonal"
                . " WHERE idMantenimiento = :idMantenimiento";
        $data = $con->prepare($sqlUp);
        $data->bindParam(':idArea', $datos->idArea);
        $data->bindParam(':idMes', $datos->idMes);
        $data->bindParam(':fechaReal', $datos->fechaReal);
        $data->bindParam(':descripcion', $datos->descripcion);
        $data->bindParam(':observaciones', $datos->observaciones);
        $data->bindParam(':idPersonalOM', $datos->idPersonalOM);
        $data->bindParam(':nombreTecnico', $datos->nombreTecnico);
        $data->bindParam(':tipoPersonal', $datos->tipoPersonal);
        $data->bindParam(':idMantenimiento', $datos->idMantenimiento);
        $data->execute();
    } else {
        $sql = "INSERT INTO controlmantenimiento "
                . "(idArea, idEquipo, idMes, fechaReal, descripcion, observaciones, idPersonalOM, nombreTecnico, tipoPersonal, tipo, totalPago) "
                . "VALUES (:idArea, :idEquipo, :idMes, :fechaReal, :descripcion, :observaciones, :idPersonalOM, :nombreTecnico, :tipoPersonal, '1', '0')";
        $dato = $con->prepare($sql);
        $dato->bindParam(':idArea', $datos->idArea);
        $dato->bindParam(':idEquipo', $datos->idEquipo);
        $dato->bindParam(':idMes', $datos->idMes);
        $dato->bindParam(':fechaReal', $datos->fechaReal);
        $dato->bindParam(':descripcion', $datos->descripcion);
        $dato->bindParam(':observaciones', $datos->observaciones);
        $dato->bindParam(':idPersonalOM', $datos->idPersonalOM);
        $dato->bindParam(':nombreTecnico', $datos->nombreTecnico);
        $dato->bindParam(':tipoPersonal', $datos->tipoPersonal);
        $dato->execute();
    }
} else {
    if (isset($_GET['idMantenimiento'])) {
        $sqlUp = "UPDATE controlmantenimiento "
                . "SET idArea = :idArea, idMes = :idMes, fechaReal = :fechaReal,"
                . " descripcion = :descripcion, observaciones = :observaciones, idPersonalOM = :idPersonalOM, nombreTecnico = :nombreTecnico, tipoPersonal = :tipoPersonal"
                . " WHERE idMantenimiento = :idMantenimiento";
        $data = $con->prepare($sqlUp);
        $data->bindParam(':idArea', $datos->idArea);
        $data->bindParam(':idMes', $datos->idMes);
        $data->bindParam(':fechaReal', $datos->fechaReal);
        $data->bindParam(':descripcion', $datos->descripcion);
        $data->bindParam(':observaciones', $datos->observaciones);
        $data->bindParam(':idPersonalOM', $datos->idPersonalOM);
        $data->bindParam(':nombreTecnico', $datos->nombreTecnico);
        $data->bindParam(':tipoPersonal', $datos->tipoPersonal);
        $data->bindParam(':idMantenimiento', $datos->idMantenimiento);
        $data->execute();
    } else {
        $sql = "INSERT INTO controlmantenimiento "
                . "(idArea, idEquipo, idMes, fechaReal, descripcion, observaciones, idPersonalOM, nombreTecnico, tipoPersonal, tipo) "
                . "VALUES (:idArea, :idEquipo, :idMes, :fechaReal, :descripcion, :observaciones, :idPersonalOM, :nombreTecnico, :tipoPersonal, '0')";
        $dato = $con->prepare($sql);
        $dato->bindParam(':idArea', $datos->idArea);
        $dato->bindParam(':idEquipo', $datos->idEquipo);
        $dato->bindParam(':idMes', $datos->idMes);
        $dato->bindParam(':fechaReal', $datos->fechaReal);
        $dato->bindParam(':descripcion', $datos->descripcion);
        $dato->bindParam(':observaciones', $datos->observaciones);
        $dato->bindParam(':idPersonalOM', $datos->idPersonalOM);
        $dato->bindParam(':nombreTecnico', $datos->nombreTecnico);
        $dato->bindParam(':tipoPersonal', $datos->tipoPersonal);
        $dato->execute();
    }
}

