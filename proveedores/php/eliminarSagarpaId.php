<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$conexion = $pdo->conectar();

$id = $_GET["id"];

$sql = "DELETE FROM archivossagarpa WHERE archivossagarpa.idSagarpaId =:id";
$datos = $conexion->prepare($sql);
$datos->bindParam(':id', $id);
$datos->execute();

if ($datos == false) {
    echo mysql_error();
} else {
    echo "Registro eliminado";
}

?>