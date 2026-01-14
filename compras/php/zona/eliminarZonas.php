<?php

include_once '../../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$idzona = $_GET['idzona'];

$sql = "UPDATE zonas SET estado='0' WHERE zonas.idzona = :idzona ";
$result = $con->prepare($sql);
$result->bindParam(':idzona', $idzona);
$result->execute();

if ($result == false) {
    echo 'Error al borrar la zona';
} else {
    echo 'La zona fue borrada exitosamente';
}
?>