<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$miInfo = file_get_contents("php://input");
$info = json_decode($miInfo);
// $id = $_GET['idLoteContratado'];

$sqlUp = "UPDATE lotescontratados SET fechaAprobacion = :fechaAprobacion WHERE idLoteContratado = :idLoteContratado";
$datos = $con->prepare($sqlUp);
$datos->bindParam(':fechaAprobacion', $info->fechaAprobacion);
$datos->bindParam(':idLoteContratado', $info->idLoteContratado);
$datos->execute();
if ($datos == false) {
    echo 'Hubo un error';
} else {
    echo 'Se modificó';
}

?>