<?php

include_once '../../DAOConeccion/conePDO.php';
$pdo = new conePDO();
$con = $pdo->conectar();

$miInfo = file_get_contents("php://input");
// $info = json_decode($miInfo);
$id = $_GET['idSolicitudCertificado'];

$sqlUp = "UPDATE solicitudcze SET datosAnexo = :datosAnexo WHERE idSolicitudCertificado = :idSolicitudCertificado";
$datos = $con->prepare($sqlUp);
$datos->bindParam(':datosAnexo', $miInfo);
$datos->bindParam(':idSolicitudCertificado', $id);
$datos->execute();
if ($datos == false) {
    echo 'Hubo un error';
} else {
    echo 'Se modificó';
}

?>