<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$miInfo = file_get_contents("php://input");
$datos = json_decode($miInfo);


$clasificacion = $_GET['clasificacion'];

if (isset($_GET['clasificacion'])) {

    $sql = "INSERT INTO clasificaciones (clasificacion) VALUES (:clasificacion)";
    $data = $con->prepare($sql);
    $data->bindParam(':clasificacion', $_GET['clasificacion']);
    $data->execute();

    if ($data == false) {
        echo 'Error al ingresar localidad';
    } else {
        echo 'La localidad se agrego exitosamente';
    }
} else {
    $sql = "UPDATE clasificaciones SET clasificacion = :clasificacion WHERE clasificaciones.idClasificacion = :idClasificacion";
    $data = $con->prepare($sql);
    $data->bindParam(':clasificacion', $datos->clasificacion);
    $data->bindParam(':idClasificacion', $datos->idClasificacion);
    $data->execute();

    if ($data == false) {
        echo 'Error al ingresar localidad';
    } else {
        echo 'La localidad se agrego exitosamente';
    }
}
?>