<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$empleo = $_GET['puesto'];

$sql = "INSERT INTO puestos (puesto) VALUES (:puesto)";
$datos = $con->prepare($sql);
$datos->bindParam(':puesto', $empleo);
$datos->execute();

if ($datos == false) {
    echo 'Error al ingresar Puesto';
} else {
    echo 'El puesto se agrego exitosamente';
}
?>