<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$conexion = $pdo->conectar();

$miInfo = file_get_contents("php://input");
$datos = json_decode($miInfo);

$sql = "INSERT INTO localidades (localidad, idzona, estado) VALUES (:localidad,:datos,'1')";

$result = $conexion->prepare($sql);
$result->bindParam(':localidad', $datos->localidad);
$result->bindParam(':datos', $datos->idzona);
$result->execute();

if ($result == false) {
    echo 'Error al ingresar localidad';
} else {
    echo 'La localidad se agrego exitosamente';
}
