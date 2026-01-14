<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$miInfo = file_get_contents("php://input");
$info = json_decode($miInfo);

if (isset($_GET['idEquipo'])) {
    $sqlUp = "UPDATE equipos SET idArea = :idArea, nombre = :nombre, codigo = '0', marca = :marca, modelo = :modelo, noSerie = :noSerie, caracteristicas = :caracteristicas, mantto = :mantto,  periodicidad = :periodicidad WHERE idEquipo = :idEquipo";
    $datos = $con->prepare($sqlUp);
    $datos->bindParam(':idArea', $info->idArea);
    $datos->bindParam(':nombre', $info->nombre);
//    $datos->bindParam(':codigo', $info->codigo);
    $datos->bindParam(':marca', $info->marca);
    $datos->bindParam(':modelo', $info->modelo);
    $datos->bindParam(':noSerie', $info->noSerie);
    $datos->bindParam(':caracteristicas', $info->caracteristicas);
    $datos->bindParam(':mantto', $info->mantto);
    $datos->bindParam(':periodicidad', $info->periodicidad);
    $datos->bindParam(':idEquipo', $info->idEquipo);
    $datos->execute();
} else {
    $sql = "INSERT INTO equipos (idArea, nombre, codigo, marca, modelo, noSerie, caracteristicas, mantto, periodicidad, programacion) VALUES (:idArea, :nombre, '0', :marca, :modelo, :noSerie, :caracteristicas, :mantto, :periodicidad, '0')";
    $dato = $con->prepare($sql);
    $dato->bindParam(':idArea', $info->idArea);
    $dato->bindParam(':nombre', $info->nombre);
//    $dato->bindParam(':codigo', $info->codigo);
    $dato->bindParam(':marca', $info->marca);
    $dato->bindParam(':modelo', $info->modelo);
    $dato->bindParam(':noSerie', $info->noSerie);
    $dato->bindParam(':caracteristicas', $info->caracteristicas);
    $dato->bindParam(':mantto', $info->mantto);
    $dato->bindParam(':periodicidad', $info->periodicidad);
    $dato->execute();
    if ($dato == false) {
        echo mysql_error();
    } else {
        echo 'Nuevo equipo agregado';
    }
}
?>