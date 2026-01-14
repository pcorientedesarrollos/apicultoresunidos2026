<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$conexion = $pdo->conectar();

$idContacto = $_GET["id"];
$idTipo = $_GET["idTipo"];
$telefono = $_GET["telefono"];

$sqlTelefono = "INSERT INTO telefonos (idTipo ,idContacto,telefono, extencion) VALUES (:idtipo, :idcontacto, :telefono, '0')";
$datos= $conexion->prepare($sqlTelefono);
$datos->bindParam(':idtipo', $idTipo);
$datos->bindParam(':idcontacto', $idContacto);
$datos->bindParam(':telefono', $telefono);
$datos->execute();



if ($datos == false) {
    echo mysql_error();
} else {
    echo 'Nuevo Telefono disponible';
}
