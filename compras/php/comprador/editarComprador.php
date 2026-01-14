<?php

include_once '../../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$miInfo = file_get_contents("php://input");

$datos = json_decode($miInfo);
$datos = (array) ($datos);

$idcomprador = $datos['idcomprador'];
$nombre = $datos['nombre'];
$telefono = $datos['telefono'];

if (isset($datos['idcomprador'])) {

    $sql = "UPDATE compradores SET nombre = :nombre , telefono = :telefono WHERE compradores.idcomprador = :idcomprador";

    $data = $con->prepare($sql);
    $data->bindParam(':nombre', $nombre);
    $data->bindParam(':telefono', $telefono);
    $data->bindParam(':idcomprador', $idcomprador);
    $data->execute();

    if ($data == false) {
        echo 'Error al ingresar comprador';
    } else {
        echo 'El comprador se agrego exitosamente';
    }
}