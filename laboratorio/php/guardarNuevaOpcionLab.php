<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$miInfo = file_get_contents("php://input");
$datos = json_decode($miInfo);

$sql = "INSERT INTO opcioneslaboratorio (opciones, signo) VALUES (:opciones,:signo)";

$dats = $con->prepare($sql);
$dats->bindParam(':opciones', $datos->opciones);
$dats->bindParam(':signo', $datos->signo);
$dats->execute();
if ($dats == false) {
    echo 'Error al ingresar';
} else {
    echo 'Agregado exitosamente';
}
?>