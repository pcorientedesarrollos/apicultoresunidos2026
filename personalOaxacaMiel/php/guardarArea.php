<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$miInfo = file_get_contents("php://input");
$datos = json_decode($miInfo);

//$espacio = $_GET['area'];
//if (isset($_GET['area'])) {
if (isset($datos[0]->idArea)) {
    $sql = "UPDATE areas SET area = :area WHERE areas.idArea = :idArea";
    $data = $con->prepare($sql);
    $data->bindParam(':area', $datos[0]->area);
    $data->bindParam(':idArea', $datos[0]->idArea);
    $data->execute();
    $idCreado = $datos[0]->idArea;
    if ($data == false) {
        echo 'Error al ingresar localidad';
    } else {
        echo 'El área se agregó exitosamente';
    }
} else {
    $sql = "INSERT INTO areas (area) VALUES (:area)";
    $data = $con->prepare($sql);
    $data->bindParam(':area', $datos[0]->area);
    $data->execute();
    $idCreado = $con->lastInsertId();
    if ($data == false) {
        echo 'Error al ingresar Area';
    } else {
        echo 'El area se agregó exitosamente';
    }
}

if ($datos[1] !== []) {

    foreach ($datos[1] as $subareas) {
        if ($subareas->idSubarea > 0) {
//            foreach ($datos[1] as $subareas) {
            $sqlSub = "UPDATE subareas SET subarea = :subarea, nombre = :nombre WHERE idSubarea = :idSubarea";
            $datosSub = $con->prepare($sqlSub);
            $datosSub->bindParam(':subarea', $subareas->subarea);
            $datosSub->bindParam(':nombre', $subareas->nombre);
            $datosSub->bindParam(':idSubarea', $subareas->idSubarea);
            $datosSub->execute();
//            }
        } else {
//            foreach ($datos[1] as $subareas) {
            $sqlSub = "INSERT INTO subareas (subarea, nombre, idArea) VALUES (:subarea, :nombre, :idArea)";
            $datosSub = $con->prepare($sqlSub);
            $datosSub->bindParam(':subarea', $subareas->subarea);
            $datosSub->bindParam(':nombre', $subareas->nombre);
            $datosSub->bindParam(':idArea', $idCreado);
            $datosSub->execute();
            if ($datosSub == false) {
                echo "NO HAY INSERT";
            } else {
                echo "HOLA";
            }
//            }
        }
    }
}
?>