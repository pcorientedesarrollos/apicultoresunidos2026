<?php
include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$id = $_GET["id"];

$sql = "DELETE FROM archivospdf WHERE archivospdf.idImagen = :id";
$datos = $con->prepare($sql);
$datos->bindParam(':id', $id);
$datos->execute();
if ($datos == false) {
    echo mysql_error();
} else {
    echo "Registro eliminado";
}
?>