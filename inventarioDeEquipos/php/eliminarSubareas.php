<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$idSubarea = $_GET["idSubarea"];

$sql = "DELETE FROM subareas WHERE idSubarea = :idSubarea";
$datos = $con->prepare($sql);
$datos->bindParam(':idSubarea', $idSubarea);
$datos->execute();
if ($datos == false) {
    echo 'Error no se pudo insertar';
} else {
    echo 'Subárea eliminada satisfactoriamente';
}
?>