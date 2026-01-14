<?php

include_once '../../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$miInfo = file_get_contents("php://input");
$datos = json_decode($miInfo);
$datos = (array) ($datos);

$idlocalidad = $datos['idlocalidad'];
$localidad = $datos['localidad'];
$idzona = $datos['idzona'];

if (isset($datos['idlocalidad'])) {

    $sql = "UPDATE localidades SET localidad = :localidad, idzona = :idzona WHERE localidades.idlocalidad = :idlocalidad";
    $data = $con->prepare($sql);
    $data->bindParam(':localidad', $localidad);
    $data->bindParam(':idzona', $idzona);
    $data->bindParam(':idlocalidad', $idlocalidad);
    $data->execute();

    if ($data == false) {
        echo 'Error al ingresar localidad';
    } else {
        echo 'La localidad se agrego exitosamente';
    }
}