<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$miInfo = file_get_contents("php://input");
$info = json_decode($miInfo);
// $id = $_GET['idSolicitudCertificado'];

$sqlUp = "UPDATE solicitudcze SET numContrato = :numContrato WHERE idSolicitudCertificado = :idSolicitudCertificado";
$datos = $con->prepare($sqlUp);
$datos->bindParam(':numContrato', $info->numContrato);
$datos->bindParam(':idSolicitudCertificado', $info->idSolicitudCertificado);
$datos->execute();
if ($datos == false) {
    echo 'Hubo un error';
} else {
    echo 'Se modificó';
}

?>